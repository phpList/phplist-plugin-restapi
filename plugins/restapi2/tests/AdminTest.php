<?php

require_once 'vendor/autoload.php';

/**
 * @note Tests currently fail due to headers management conflict
 */
class TestAdmin extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Mock necessary globals
        $response = new \Rapi\Response();
        $this->admin = new \Rapi\Admin( $response );
    }

    public function testLogin()
    {
        // $response = $this->admin->login();
        // // Check that a response object was returned
        // $this->assertInstanceOf( '\Rapi\Response', $response );
    }

    public function testProcessQueue()
    {
        // $response = $this->admin->processQueue();
        // // Check that a response object was returned
        // $this->assertInstanceOf( '\Rapi\Response', $response );
    }
}
