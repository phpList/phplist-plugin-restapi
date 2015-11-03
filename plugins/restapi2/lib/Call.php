<?php

namespace Rapi;

/**
 * Class to handle API call functionality and execution
 * @note This class is ignorant of call output and forwards it transparently --
 * e.g. returned values aren't formatted
 */
class Call {

    /**
     * Constructor requires all classes that may handle calls as arguments
     * @note To add support for an API call, add it's parent class to arguments
     * @param Lists             $lists
     * @param SubscriberHandler $subscriberHandler
     */
    public function __construct(
        \Rapi\Handler\ListHandler $listHandler
        , \Rapi\Handler\SubscriberHandler $subscriberHandler
    )
    {
        $this->listHandler = $listHandler;
        $this->subscriberHandler = $subscriberHandler;#
    }
    /**
     * Validate a requested call by checking characters and syntax
     * @param string $className Name of class to validate
     * @param string $method Name of method to validate
     * @return bool $result
     */
    public function validateCall( $className, $method )
    {
        // Default result to true / pass
        $result = true;
        $subjects = array( $className, $method );

        // Loop through items to be tested
        foreach ( $subjects as $subject ) {
            // Check for non-word characters
            if ( preg_match( '/\W/', $subject ) ) {
                $result = false;
            }
        }

        // Check that the name of the method uses valid syntax
        if ( ! is_callable( $method, true ) ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get API call configuraiton whitelist
     * @param string $whitelistPath Path to whitelist configuration file
     */
    public function getWhitelistConfig( $whitelistPath = NULL )
    {
        // If a whitelist config file path was not specified, use default
        if ( NULL === $whitelistPath ) {
            $whitelistPath =  dirname( __FILE__ ) . '/../whitelist.php';
        }

        // Load the whitelist config file
        // NOTE: This should result in var $whitelist being initialised
        if ( ! include( $whitelistPath ) ) {
            // If whitelist config file couldn't be found
            throw new \Exception( 'Call action whitelist configuration file could not be loaded' );
        } elseif( ! isset( $whitelist ) ) {
            // If the file was loaded but contained no whitelist variable
            throw new \Exception( 'Whitelist config file does not contain variable $whitelist' );
        } elseif( ! is_array( $whitelist ) ) {
            // If whitelist is set but is not an array
            throw new \Exception( 'Whitelist config file variable $whitelist is not an array' );
        }

        // Return the whitelist
        return $whitelist;
    }

    /**
     * Check if the supplied class name is permitted by the whitelist
     * @param string $className Class name to check
     */
    public function isCallWhitelisted( $className, $method )
    {
        // Get whitelisted classnames
        $whitelistArray = $this->getWhitelistConfig();

        if (
            ! isset( $whitelistArray[$className] )
            || true !== $whitelistArray[$className]['enabled']
        ) {
            // If requested class is disabled
            return false;
        } elseif( true !== $whitelistArray[$className]['methods'][$method] ) {
            // if requested method is disabled
            return false;
        }

        // All checks passed
        return true;
    }

    /**
     * Validate an API call and execute the requested method on handler object
     * @param string $className to execute method on
     * @param string $method name of method to execute
     * @param array $argumentsArray arguments to pass to method
     * @return \phpList\Entity\SubscriberEntity Data object
     */
    public function doCall( $className, $method, array $argumentsArray )
    {
        // NOTE: Consider adding use of isCallable() here and making that method
        // private

        // Check if desired class is accessible as a property
        if ( ! property_exists( $this, $className ) ) {
            throw new \Exception(
                "Object '$className' is not an available handler object"
            );
        }
        // Check that desired method is callable
        if ( ! is_callable( array( $this->$className, $method ) ) ) {
            throw new \Exception( "API call method '$method' not callable on object '$className'" );
        }
        // Check that desired call is whitelisted
        if ( ! $this->isCallWhitelisted( $className, $method ) ) {
            throw new \Exception( "Requested call '$className::$method' is not whitelisted on this server" );
        }

        // Format the parameters
        $formattedParams = $this->formatParams( $argumentsArray );

        // Execute the desired action
        $result = call_user_func_array( array( $this->$className, $method ), $formattedParams );

        return $result;
    }

    /**
     * Format raw user-privded API call parameters for passing to handler object
     * @param array $argumentsArray user-supplied API call parameters
     */
    public function formatParams( array $argumentsArray ) {

        // Remove unnecessary params
        unset( $argumentsArray['className'] );
        unset( $argumentsArray['method'] );

        // Sort the parameters alphbetically
        ksort( $argumentsArray );

        return $argumentsArray;
    }

    /**
     * Convert any var type to an array suitable for passing to a response
     * @param mixed $callResult Returned value of an executed API call
     */
    public function callResultToArray( $callResult )
    {
        $varType = gettype( $callResult );

        switch( $varType ) {
            case 'array':
                // Nothing to do, var is already correct type
                return $callResult;
            case 'object':
                // Convert object to array
                $objectToArray = $this->objectToArray( $callResult );
                return $objectToArray;
            case 'resource':
                // Resource vars probably aren't useful; generate error
                throw new \Exception( 'Forbidden variable type returned by call: \'' . $varType . '\'' );
        }

        // Looks like the the var must be simple (string/int/bool etc.)
        // Put the var in a simple array
        $callResultArray = array( $callResult );

        return $callResultArray;
    }

    /**
    * Convert an object into an associative array
    *
    * This function converts an object into an associative array by iterating
    * over its public properties. Because this function uses the foreach
    * construct, Iterators are respected. It also works on arrays of objects.
    * @param object $object The object to be converted
    * @return array $result Converted object
    */
    function objectToArray( $object )
    {
        $result = array();
        $references = array();

        // loop over elements/properties
        foreach ( $object as $key => $value ) {
            // recursively convert objects
            if ( is_object( $value ) || is_array( $value ) ) {
                // but prevent cycles
                if ( !in_array( $value, $references ) ) {
                    $result[$key] = $this->objectToArray( $value );
                    $references[] = $value;
                }
            } else {
                // simple values are untouched
                $result[$key] = utf8_encode( $value );
            }
        }
        return $result;
    }
}
