<?php

require_once 'vendor/autoload.php';

/**
 * @note Tests currently fail due to headers management conflict
 */
class TestPdoEx extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Access the Symfony DI container object which was setup during bootstrap
        global $container;
        $this->container = $container;

        // Get objects
        $this->pdoEx = $this->container->get( 'PdoEx');
        $this->response =  $this->container->get( 'Response');
    }

    public function testDoQuery()
    {
        $result = $this->pdoEx->doQuery(
            'SELECT * FROM ' . $GLOBALS['usertable_prefix'] . 'user LIMIT 1'
        );
        // Check that a PDO object was returned
        $this->assertTrue( is_array( $result ) );
        // Check that record ID is set
        $this->assertTrue( is_numeric( $result[0]->id ) );
        // Check that record pass is set
        $this->assertTrue( strlen( $result[0]->password ) >= 10 );
    }

    public function testDoQueryResonse()
    {
        $response = $this->pdoEx->doQueryResponse(
            $this->response
            , 'SELECT * FROM ' . $GLOBALS['usertable_prefix'] . 'user LIMIT 1'
            , 'Users'
        );
        // Check that a response object was returned
        $this->assertInstanceOf( '\Rapi\Response', $response );
        // Get private property: response status
        $resultStatus = PHPUnit_Framework_Assert::readAttribute( $response, 'result' );
        // Check that response status indicates success
        $this->assertEquals( 'success', $resultStatus['status'] );
        // Check that the user ID is numeric
        $this->assertTrue( is_numeric( $resultStatus['data'][0]->id ) );
    }

}
