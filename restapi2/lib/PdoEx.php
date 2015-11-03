<?php

namespace Rapi;

/**
 * Wrapper class for stock PDO{}
 * @return PDO Configured PDO connection
 */
class PdoEx extends \PDO {

    /**
     * Connect to database with minimal validation
     * @param string $hostname Database hostname
     * @param string $username Username for database login
     * @param string $pass     Pasword for database login
     * @param string $dbname   Name of the database to connect to
     */
    public function __construct( $hostname, $username, $pass, $dbname )
    {
        // Check that DSN values are not set to default values
        if (
            $hostname == 'hostname'
            || $username == 'username'
            || $pass == 'pass'
            || $dbname == 'dbname'
        ) {
            throw new \Exception( 'Database connection parameters were not set before accessing object from container' );
        }

        // NOTE: If this constructor isn't present, the parent class'
        // constructor will automatically be called.

        // Create a PDO object
        parent::__construct( "mysql:host=$hostname;dbname=$dbname", $username, $pass );
        // Configure connection parameters
        $this->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
    }

    /**
     * Take a query result of unknown quantity and return only the first record
     * @param array $result Result of a successfully executed query
     */
    public function trimFirstRecord( array $result )
    {
        // Check if there is more than one record
        if (
            is_array( $result )
            && isset( $result[0] )
        ) {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Execute a query, optionionally return first record only
     * @param string  $sql      SQL to execute
     * @param boolean $isSingle Flag indicating how many records to return
     */
    public function doQuery( $sql, $isSingle = false )
    {
        $stmt = $this->query( $sql );
        $result = $stmt->fetchAll( \PDO::FETCH_OBJ );

        // If just one result is requested and more are available, use 1st
        if ( $isSingle == true ) {
            $result = $this->trimFirstRecord( $result );
        }

        return $result;
    }

    /**
    * Execute an SQL select query and generate Response object
    * @param  string $type   [description]
    * @param  string $sql SQL string to execute
    * @param  bool $isSingle Whether only one record should be returned
    * @return Response $response Generated Response object
    */
    public function doQueryResponse( Response $response, $sql, $type, $isSingle = false )
    {
        try {
            // Execute the query
            $result = $this->doQuery( $sql, $isSingle );
            // Save the data to the response object
            $response->setData( $type, $result );

        } catch( \Rapi\PDOException $e ) {
            // Save the PDO error to the response
            $response->setError( $e->getCode(), $e->getMessage() );
        }

        return $response;
    }
}
