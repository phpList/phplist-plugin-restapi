<?php

namespace Rapi;

/**
 * Class to handle generation of responses to API calls over HTTP
 */
class Response {

    private $result;

    /**
     * Initialise empty vars
     */
    function __construct()
    {
        $this->result = array();
    }

    /**
     * Save error and error code inside response
     * @param string $code error code to store
     * @param string $message error message to store
     */
    function setError( $code, $message )
    {
        $this->result['status'] = 'error';
        $this->result['type'] = 'Error';
        $this->result['data'] = array (
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * Save data inside response
     * @param string $type data type
     * @param string $data data to be stored
     * @return NULL
     */
    function setData( $type, $data )
    {
        $this->result['status'] = 'success';
        $this->result['type'] = $type;
        $this->result['data'] = $data;
    }

    /**
     * Print error message as JSON and die
     * @return NULL
     */
    function output()
    {
        header( 'Content-Type: application/json' );
        echo $this->jsonEncodeIm( $this->result );
        die( 0 );
    }

    /**
     * Convert a value to JSON - improved implementation over stock PHP
     *
     * This function returns a JSON representation of $param. It uses json_encode
     * to accomplish this, but converts objects and arrays containing objects to
     * associative arrays first. This way, objects that do not expose (all) their
     * properties directly but only through an Iterator interface are also encoded
     * correctly.
     */
    function jsonEncodeIm( array $param )
    {
        return json_encode( $param );
    }

    /**
     * Take an Exception and output it to an error response
     * @param Exception $e Exception object
     */
    static function outputError( \Exception $e ){
        $response = new Response();
        $response->setError( $e->getCode(), $e->getMessage() );
        $response->output();
    }

    /**
     * Generate and output an error response from an error message
     * @note Wraps other error handling methods for convenience
     * @param string $message Error message
     */
    static function outputErrorMessage( $message ){
        $response = new Response();
        $response->setError( 0, $message );
        $response->output();
    }

    /**
     * Generate and output a response for successful deletion of something
     * @param [type] $type [description]
     * @param [type] $id   [description]
     */
    static function outputDeleted( $type, $id ){
        $response = new Response();
        $response->setData( $type, 'Item with ' . $id . ' is successfully deleted!' );
        $response->output();
    }

    /**
     * Generate and output a generic system message as a response
     * @param string $message System message
     */
    static function outputMessage( $message ){
        $response = new Response();
        $response->setData( 'SystemMessage', $message );
        $response->output();
    }

}
