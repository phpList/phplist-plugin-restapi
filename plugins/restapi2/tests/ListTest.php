<?php

require_once 'vendor/autoload.php';

class TestLists extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Instantiate necessary objects
        // TODO: Consider mocking these
        $this->pdoEx = new Rapi\PdoEx(
            $GLOBALS['database_host']
            , $GLOBALS['database_user']
            , $GLOBALS['database_password']
            , $GLOBALS['database_name']
        );
        $this->response = new \Rapi\Response();
        $this->common = new \Rapi\Common( $this->pdoEx, $this->response );
        $this->lists = new \Rapi\Lists( $this->common, $this->pdoEx, $this->response );
    }

    public function testListGet()
    {
        // Set arbitrary list ID to retrieve
        $params = array( 'id' => 2 );
        // Set expected data type
        $type = 'List';

        // Retrieve list
        $response = $this->lists->listGet( $params );

        // Check correct object was returned
        $this->assertInstanceOf( '\Rapi\Response', $this->response );
        // Map the private result array to a readable variable
        $responseArray = PHPUnit_Framework_Assert::readAttribute( $response, 'result' );
        // Check that status exists
        $this->assertTrue( array_key_exists( 'status', $responseArray ) );
        // Check call was successful
        $this->assertEquals( 'success', $responseArray['status'] );
        // Check that type exists
        $this->assertTrue( array_key_exists( 'type', $responseArray ) );
        // Check correct response type was set
        $this->assertEquals( $type, $responseArray['type'] );
        // Check that data exists
        $this->assertTrue( array_key_exists( 'data', $responseArray ) );
        // Check correct data was set
        $this->assertInstanceOf( '\StdClass', $responseArray['data'] );

        $propertiesTest = array(
            'id'
            , 'name'
            , 'description'
            , 'entered'
            , 'listorder'
            , 'prefix'
            , 'rssfeed'
            , 'modified'
            , 'active'
            , 'owner'
            , 'category'
        );

        // Check that all the correct properties are set
        foreach ( $propertiesTest as $property ) {
            $this->assertTrue( property_exists( $responseArray['data'], $property ) );
        }
    }

    public function testMultiListGet()
    {
    }

    public function testListAdd()
    {
    }

    public function testListUpdate()
    {
    }

    public function testListDelete()
    {
    }

    public function testListsSubscriber()
    {
    }

    public function testListSubscriberAdd()
    {
    }

    public function testListSubscriberDelete()
    {
    }

    public function testListMessageAdd()
    {
    }

    public function testListMessageDelete()
    {
    }
}
