<?php

/**
 * Class Test_
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
    private function callApi( $command, $post_params, $decode = true ) 
    {
        $post_params['cmd'] = $command;

        // Serialise and encode query
        $post_params = http_build_query( $post_params );
        
        // Prepare cURL
        $c = curl_init();
        curl_setopt( $c, CURLOPT_URL,            $this->url );
        curl_setopt( $c, CURLOPT_HEADER,         0 );
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $c, CURLOPT_POST,           1 );
        curl_setopt( $c, CURLOPT_POSTFIELDS,     $post_params);
        // FIXME: this tmp path mustn't be hardcoded
        curl_setopt( $c, CURLOPT_COOKIEFILE,     $this->tmpPath.'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt( $c, CURLOPT_COOKIEJAR,      $this->tmpPath.'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt( $c, CURLOPT_HTTPHEADER,     array( 'Connection: Keep-Alive', 'Keep-Alive: 60' ) );
        
        // Execute the call
        $result = curl_exec( $c );

        // Check if decoding of result is required
        if ( $decode === true ) 
        {
            $result = json_decode( $result );
        }

        return $result;
    }

    /**
     * Use a real login to test login api call
     * @return bool true if user exists and login successful
     */
    public function testLogin() 
    {
        // Set the username and pwd to login with
        $post_params = array(
            'login' => $this->loginName,
            'password' => $this->password
        );

        // Execute the login with the credentials as params
        $result = $this->callApi( 'login', $post_params );
        
        // Check if the login was successful
        $this->assertEquals( 'success', $result->status );
        
    }
    
    /**
     * Test for simple success of fetching of all lists
     * @note Only the 'status' property is tested
     */
    public function testListsGet() 
    {
        // Create empty params array
        $post_params = array();

        // Execute the api call
        $result = $this->callApi( 'listsGet', $post_params );

        // Check if the lists were fetched successfully
        $this->assertEquals( 'success', $result->status );
    }
    
    /**
     * Test creation of a new list
     * @note Currently the created list is not deleted
     */
    public function testListAdd() 
    {
        // Create minimal params for api call
        $post_params = array(
            'name' => 'testList',
            'description' => 'creted by unit testing',
            'listorder' => 0,
            'prefix' => '',
            'rssfeed' => '',
            'active' => 1
        );

        // Execute the api call
        $result = $this->callAPI( 'listAdd', $post_params );

        // Check if the list was created successfully
        $this->assertEquals( 'success', $result->status );
        
        // Check that the list has a numeric ID
        $this->assertTrue( is_numeric( $result->data->id ) );
        
        // Check the new list data is what we requested
        $this->assertEquals( 'testList', $result->data->name );
        $this->assertEquals( 'creted by unit testing', $result->data->description );
        $this->assertEquals( '0', $result->data->listorder );
        $this->assertEquals( '', $result->data->prefix );
        $this->assertEquals( '', $result->data->rssfeed );
        #$this->assertEquals( '2014-06-15 15:27:22', $result->data->modified );
        $this->assertEquals( '1', $result->data->active );
    }
}

?>
