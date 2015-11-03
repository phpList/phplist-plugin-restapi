<?php

namespace rapi\Test\Handler;

class TestSubscriberHandler extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Access the Symfony DI container object which was setup during bootstrap
        global $container;
        $this->container = $container;
        // Get objects from container
        $this->scrHandler = $this->container->get( 'SubscriberHandler' );
        $this->scrEntity = $this->container->get( 'SubscriberEntity' );
        $this->subscriberManager = $this->container->get( 'SubscriberManager' );

        // Create a randomised email addy to register with
        $this->emailAddress = 'unittest-' . rand( 0, 999999 ) . '@example.com';
    }

    public function testAddEmailOnly()
    {
        // Insert a new subscriber
        $scrId = $this->scrHandler->addEmailOnly( $this->emailAddress );

        // Check that a valid ID was returned
        $this->assertTrue( is_numeric( $scrId ) );

        return $scrId;
    }

    /**
     * @depends testAddEmailOnly
     */
    public function testDelete( $scrId )
    {
        $result = $this->scrHandler->delete( $scrId );

        $this->assertTrue( $result );
    }
}
