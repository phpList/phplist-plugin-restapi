<?php

// Phplist 4 namespaces
use phpList\Config;
use phpList\EmailUtil;
use phpList\SubscriberManager;
use phpList\Entity\SubscriberEntity;
use phpList\Model\SubscriberModel;
use phpList\helper\Database;
use phpList\Pass;
use phpList\phpList;
use phpList\Subscriber;
use phpList\helper\Util;

// Symfony namespaces
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Tests access to phplist 4 classes. Duplicates cases from that package.
 */
class Pl4Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Create a randomised email addy to register with
        $this->emailAddress = 'unittest-' . rand( 0, 999999 ) . '@example.com';
        $this->plainPass = 'easypassword';

        // Create Symfony DI service container object for use by other classes
        $this->container = new ContainerBuilder();
        // Create new Symfony file loader to handle the YAML service config file
        $loader = new YamlFileLoader( $this->container, new FileLocator(__DIR__) );
        // Load the service config file, which is in YAML format
        $loader->load( '../services.yml' );
        $this->subscriberManager = $this->container->get( 'SubscriberManager' );
    }

    /**
    * @note This belongs in a test class for SubscriberEntity, not here
    */
    public function testAdd()
    {
        // Add new subscriber properties to the entity
        $scrEntity = new SubscriberEntity;
        $scrEntity->emailAddress = $this->emailAddress;
        $scrEntity->plainPass = $this->plainPass;

        // Copy the email address to test it later
        $emailCopy = $this->emailAddress;
        // Save the subscriber
        $newSubscriberId = $this->subscriberManager->add( $scrEntity );

        // Test that an ID was returned
        $this->assertNotEmpty( $newSubscriberId );
        $this->assertTrue( is_numeric( $newSubscriberId ) );

        // Pass on to the next test
        return array( 'id' => $newSubscriberId, 'email' => $emailCopy, 'encPass' => $scrEntity->encPass );
    }

    /**
    * @depends testAdd
    */
    public function testGetSubscriberById( array $vars )
    {
        $scrEntity = $this->subscriberManager->getSubscriberById( $vars['id'] );

        // Check that the correct entity was returned
        $this->assertInstanceOf( '\phpList\Entity\SubscriberEntity', $scrEntity );
        // Check that the saved password isn't in plain text
        $this->assertNotEquals( $this->plainPass, $scrEntity->encPass );
        // Check that retrieved email matches what was set
        $this->assertEquals( $vars['email'] , $scrEntity->emailAddress );

        return $scrEntity;
    }
}
