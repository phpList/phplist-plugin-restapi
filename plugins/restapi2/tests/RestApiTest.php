<?php

/**
  * Class for high level testing of API calls
  */
class TestRestapi extends \PHPUnit_Framework_TestCase
{

    public $userId;
    public $loginName;
    public $password;
    public $url;
    public $tmpPath;

    public function setUp()
    {
        // Set values from constants stored in phpunit.xml
        $this->loginName = API_LOGIN_USERNAME;
        $this->password = API_LOGIN_PASSWORD;
        $this->url = API_URL_BASE_PATH;
        $this->tmpPath = TMP_PATH;
    }

    public function tearDown() {
    }

    /**
     * Make a call to the API using cURL
     * @return string result of the CURL execution
     */
    private function callApi( $className, $method, array $params, $decode = true )
    {
        // Serialise and encode query
        $postParams = http_build_query( $params );

        // Prepare cURL
        $c = curl_init();
        curl_setopt( $c, CURLOPT_URL,            $this->url );
        curl_setopt( $c, CURLOPT_HEADER,         0 );
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $c, CURLOPT_POST,           1 );
        curl_setopt( $c, CURLOPT_POSTFIELDS,     $postParams);
        // FIXME: this tmp path mustn't be hardcoded
        curl_setopt( $c, CURLOPT_COOKIEFILE,     $this->tmpPath.'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt( $c, CURLOPT_COOKIEJAR,      $this->tmpPath.'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt( $c, CURLOPT_HTTPHEADER,     array( 'Connection: Keep-Alive', 'Keep-Alive: 60' ) );

        // Execute the call
        $result = curl_exec( $c );
print_r($result);
        // Check if decoding of result is required
        if ( $decode === true )
        {
            $result = json_decode( $result );
        }

        return $result;
    }

    public function testSubscriberManagerGetSubscriber()
    {
        $params = array(
            'id' => 2
        );

        $className = 'subscriberManager';
        $method = 'getSubscriber';

        $result = $this->callApi( $className, $method, $params );

        var_dump($result);
    }

    // /**
    //  * Use a real login to test login api call
    //  * @return bool true if user exists and login successful
    //  */
    // public function testLogin()
    // {
    //     // Set the username and pwd to login with
    //     $post_params = array(
    //         'login' => $this->loginName,
    //         'password' => $this->password
    //     );
    //
    //     // Execute the login with the credentials as params
    //     $result = $this->callApi( 'login', $post_params );
    //
    //     // Check if the login was successful
    //     $this->assertEquals( 'success', $result->status );
    //
    // }

    // /**
    // * Test for simple success of fetching of a list
    // */
    // public function testListGet()
    // {
    //     // Create empty params array
    //     $listId = 2;
    //
    //     // Execute the api call
    //     $result = $this->callApi( 'listGet', $listId );
    //
    //     // Check if the lists were fetched successfully
    //     $this->assertEquals( 'success', $result->status );
    // }
    //
    // /**
    //  * Test for simple success of fetching of all lists
    //  * @note Only the 'status' property is tested
    //  */
    // public function testMultiListGet()
    // {
    //     // Create empty params array
    //     $post_params = array();
    //
    //     // Execute the api call
    //     $result = $this->callApi( 'multiListGet', $post_params );
    //
    //     // Check if the lists were fetched successfully
    //     $this->assertEquals( 'success', $result->status );
    // }
    //
    // /**
    //  * Test creation of a new list
    //  * @note Created list is deleted in later test, barring errors
    //  */
    // public function testListAdd()
    // {
    //     // Create minimal params for api call
    //     $post_params = array(
    //         'name' => 'testList',
    //         'description' => 'listDescription',
    //         'listorder' => '0',
    //         'prefix' => '',
    //         'rssfeed' => '',
    //         'active' => '1'
    //     );
    //
    //     // Execute the api call
    //     $result = $this->callAPI( 'listAdd', $post_params );
    //
    //     // Check if the list was created successfully
    //     $this->assertEquals( 'success', $result->status );
    //
    //     // Check that the list has a numeric ID
    //     $this->assertTrue( is_numeric( $result->data->id ) );
    //
    //     // Check the new list data is what we requested
    //     $this->assertEquals( 'testList', $result->data->name );
    //     $this->assertEquals( 'listDescription', $result->data->description );
    //     $this->assertEquals( '0', $result->data->listorder );
    //     $this->assertEquals( '', $result->data->prefix );
    //     $this->assertEquals( '', $result->data->rssfeed );
    //     #$this->assertEquals( '2014-06-15 15:27:22', $result->data->modified );
    //     $this->assertEquals( '1', $result->data->active );
    //
    //     $listId = $result->data->id;
    //
    //     // Pass on the new list ID so other tests can reuse it
    //     return $listId;
    // }
    //
    // /**
    //  * Test updating an existing list
    //  * @depends testListAdd
    //  */
    // public function testListUpdate( $listId )
    // {
    //     // Create minimal params for api call
    //     $post_params = array(
    //         'id' => $listId,
    //         'name' => 'updatedTestList',
    //         'description' => 'updatedListDescription',
    //         'listorder' => '1',
    //         'prefix' => '_',
    //         'rssfeed' => '1',
    //         'active' => '0'
    //     );
    //
    //     // Execute the api call
    //     $result = $this->callAPI( 'listUpdate', $post_params);
    //
    //     // Check if the list was updated successfully
    //     $this->assertEquals( 'success', $result->status );
    //
    //     // Check that the list has a numeric ID
    //     $this->assertTrue( is_numeric( $result->data->id ) );
    //
    //     // Check the new list data is what we requested
    //     $this->assertEquals( 'updatedTestList', $result->data->name );
    //     $this->assertEquals( 'updatedListDescription', $result->data->description );
    //     $this->assertEquals( '1', $result->data->listorder );
    //     $this->assertEquals( '_', $result->data->prefix );
    //     $this->assertEquals( '1', $result->data->rssfeed );
    //     #$this->assertEquals( '2014-06-15 15:27:22', $result->data->modified );
    //     $this->assertEquals( '0', $result->data->active );
    // }
    //
    // /**
    //  * Test deleting an existing list
    //  * @note Simply trusts the status returned from the API. Deeper testing required
    //  * @depends testListAdd
    //  */
    // public function testListDelete( $listId )
    // {
    //     // Create minimal params for api call
    //     $post_params = array(
    //         'id' => $listId
    //     );
    //
    //     // Execute the api call
    //     $result = $this->callAPI( 'listDelete', $post_params);
    //
    //     // Check if the list was deleted successfully
    //     $this->assertEquals( 'success', $result->status );
    //     $this->assertEquals( 'Item with ' . $listId . ' is successfully deleted!', $result->data );
    // }
    //
    // /**
    //  * Test adding a new subscriber
    //  * @todo add another test to delete the user later on
    //  * @depends testListAdd
    //  */
    // public function testSubscriberAdd( $listId )
    // {
    //     // Set the user details as parameters
    //     $post_params = array(
    //         'email' => 'test_' . rand( 100, 999 ), // rand() works around 'email' being a primary key and therefore unique
    //         'confirmed' => 1,
    //         'htmlemail' => 1,
    //         'password' => 'password',
    //         'disabled' => 0,
    //         'rssfrequency' => 1
    //     );
    //
    //     // Execute the api call
    //     $result = $this->callAPI( 'subscriberAdd', $post_params);
    //
    //     // Test if the user was created successfully
    //     $this->assertEquals( 'success', $result->status );
    //
    //     $subscriberId = $result->data->id;
    //
    //     // Pass on the newly created userid to other tests
    //     return $subscriberId;
    // }
    //
    // /**
    //  * Test adding a subscriber to an existing list
    //  * @todo check subscriber is actually added, don't trust return status
    //  * @depends testListAdd
    //  * @depends testSubscriberAdd
    //  */
    // public function testListSubscriberAdd( $listId, $subscriberId )
    // {
    //     // Set list and subscriber vars
    //     $post_params = array(
    //         'list_id' => $listId,
    //         'subscriber_id' => $subscriberId
    //     );
    //
    //     // Execute the api call
    //     $result = $this->callAPI( 'listSubscriberAdd', $post_params);
    //
    //     // Test if the user was added to the list successfully
    //     $this->assertEquals( 'success', $result->status );
    // }
}
