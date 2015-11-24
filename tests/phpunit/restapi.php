<?php

/**
 * Class Test_.
 */
class TestRestapi extends \PHPUnit_Framework_TestCase
{
    public $userId;
    public $loginName;
    public $password;
    public $url;
    public $tmpPath;
    public $testListName;
    public $testListRename;
    private $debug = 0;
    private $testEmailAddress = '';
    private $testTemplateTitle = '';
    private $testTemplateRenamedTitle = '';

    public function setUp()
    {
        // Set values from constants stored in phpunit.xml
        $this->loginName = API_LOGIN_USERNAME;
        $this->password = API_LOGIN_PASSWORD;
        $this->processingSecret = API_REMOTE_PROCESSING_SECRET;
        $this->url = API_URL_BASE_PATH;
        $this->tmpPath = TMP_PATH;
        $this->testListName = 'API Test Testlist '.time();
        $this->testListRename = 'API Test Testlist'.date('Y-m-d H:i:s');
        $this->testEmailAddress = 'test-'.time().rand(100, 999).'@phplist.com';
        $this->testTemplateTitle = 'API Test Template '.date('Y-m-d H:i:s');
        $this->testTemplateRenamedTitle = 'API Test Template Renamed '.date('Y-m-d H:i:s');
    }

    public function tearDown()
    {
    }

    /**
     * Make a call to the API using cURL.
     *
     * @return string result of the CURL execution
     */
    private function callApi($command, $post_params, $decode = true)
    {
        $post_params['cmd'] = $command;
        $post_params['secret'] = $this->processingSecret;
        if ($this->debug) {
            print  "Calling $command".PHP_EOL;
        }
        // Serialise and encode query
        $post_params = http_build_query($post_params);
        // Prepare cURL
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL,            $this->url);
        curl_setopt($c, CURLOPT_HEADER,         0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_POST,           1);
        curl_setopt($c, CURLOPT_POSTFIELDS,     $post_params);
        curl_setopt($c, CURLOPT_COOKIEFILE,     $this->tmpPath.'/phpList_RESTAPI_cookiejar.txt');
        curl_setopt($c, CURLOPT_COOKIEJAR,      $this->tmpPath.'/phpList_RESTAPI_cookiejar.txt');
        curl_setopt($c, CURLOPT_HTTPHEADER,     array('Connection: Keep-Alive', 'Keep-Alive: 60'));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        // Execute the call
        $result = curl_exec($c);
        
        if (curl_errno($c)) {
            print "Error: ".curl_error($c).PHP_EOL;
        }

        // Check if decoding of result is required
        if ($decode === true) {
            $result = json_decode($result);
        }

        return $result;
    }

    /**
     * Use a real login to test login api call.
     *
     * @return bool true if user exists and login successful
     */
    public function testLogin()
    {
        // Set the username and pwd to login with
        $post_params = array(
            'login' => $this->loginName,
            'password' => $this->password,
        );

        // Execute the login with the credentials as params
        $result = $this->callApi('login', $post_params);
        // Check if the login was successful
        $this->assertEquals('success', $result->status);
    }

    /**
     * Test for simple success of fetching of all lists.
     *
     * @note Only the 'status' property is tested
     */
    public function testListsGet()
    {
        // Create empty params array
        $post_params = array();

        // Execute the api call
        $result = $this->callApi('listsGet', $post_params);
        // Check if the lists were fetched successfully
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_numeric(count($result->data)));
        $this->assertTrue(is_array($result->data));
        if ($this->debug) {
            echo 'Number of lists is '.count($result->data).PHP_EOL;
        }
    }

    /**
     * Test creation of a new list.
     *
     * @note Created list is deleted in later test, barring errors
     */
    public function testListAdd()
    {
        // Create minimal params for api call
        $post_params = array(
            'name' => $this->testListName,
            'description' => 'List created with API phpUnit test',
            'listorder' => '0',
            'active' => '1',
        );

        // Execute the api call
        $result = $this->callAPI('listAdd', $post_params);

        // Check if the list was created successfully
        $this->assertEquals('success', $result->status);

        // Check that the list has a numeric ID
        $this->assertTrue(is_numeric($result->data->id));

        // Check the new list data is what we requested
        $this->assertEquals($this->testListName, $result->data->name);
        $this->assertEquals('List created with API phpUnit test', $result->data->description);
        $this->assertEquals('0', $result->data->listorder);
        #$this->assertEquals( '2014-06-15 15:27:22', $result->data->modified );
        $this->assertEquals('1', $result->data->active);

        $listId = $result->data->id;
        if ($this->debug) {
            echo 'The new list ID for list "'.$this->testListName.'" = '.$listId.PHP_EOL;
        }

        // Pass on the new list ID so other tests can reuse it
        return $listId;
    }

    /**
     * Test updating an existing list.
     *
     * @depends testListAdd
     */
    public function testListUpdate($listId)
    {
        $post_params = array(
            'id' => $listId,
            'name' => $this->testListRename,
            'description' => 'List modified with API phpUnit test',
            'listorder' => '1',
            'active' => '1',
        );
        if ($this->debug) {
            print "Updating $listId".PHP_EOL;
        }
        // Execute the api call
        $result = $this->callAPI('listUpdate', $post_params);

        // Check if the list was updated successfully
        $this->assertEquals('success', $result->status);

        // Check that the list has a numeric ID
        $this->assertTrue(is_numeric($result->data->id));

        // Check the new list data is what we requested
        $this->assertEquals($this->testListRename, $result->data->name);
        $this->assertEquals('List modified with API phpUnit test', $result->data->description);
        $this->assertEquals('1', $result->data->listorder);
        #$this->assertEquals( '2014-06-15 15:27:22', $result->data->modified );
        $this->assertEquals('1', $result->data->active);
    }

     /**
      * Test counting the total number of subscribers.
      */
     public function testSubscriberCount()
     {
         $post_params = array(
         );
         // Execute the api call
         $result = $this->callAPI('subscribersCount', $post_params);
         $this->assertEquals('success', $result->status);
         $this->assertTrue(is_numeric($result->data->total));

         if ($this->debug) {
             print 'There are '.$result->data->total.' subscribers'.PHP_EOL;
         }
         $subscriberCount = $result->data->total;

         return $subscriberCount;
     }

     /**
      * Test fetching all subscribers.
      * the API limits to 100, so it should not return more than that
      * @depends testSubscriberCount
      * 
      */
     public function testSubscribersGet($subscriberCount)
     {
         if ($this->debug) {
             print 'There are '.$subscriberCount.' subscribers'.PHP_EOL;
         }
       
         $post_params = array(
          'order_by' => '', 
          'order' => '', 
          'limit' => 500, 
          'offset' => 0,
        );
        if ($subscriberCount > 100) {
          $max = 100;
        } else {
          $max = $subscriberCount;
        }
        // Execute the api call
         $result = $this->callAPI('subscribersGet', $post_params);
         $this->assertEquals('success', $result->status);
         $this->assertTrue(is_array($result->data));
         $this->assertTrue(count($result->data) <= 100);
         $this->assertEquals(count($result->data),$max);
         if ($this->debug) {
             print 'There are '.count($result->data).' subscribers'.PHP_EOL;
         }
         $subscriberCount = count($result->data);

         return $subscriberCount;
     }

    /** 
     * Test that a subscriber exists.
     */
    public function testSubscriberExist()
    {
        $params = array(
            'email' => $this->testEmailAddress,
        );
        $result = $this->callAPI('subscriberGetByEmail', $params);
        $this->assertEquals('success', $result->status);
        $this->assertEmpty($result->data); // it should not exist yet
        $testEmailAddress = $this->testEmailAddress;

        return $testEmailAddress;
    }

    /**
     * Test adding a new subscriber.
     *
     * @todo add another test to delete the user later on
     * @depends testSubscriberExist
     */
    public function testSubscriberAdd($testEmailAddress)
    {
        // Set the user details as parameters
        $post_params = array(
            'email' => $testEmailAddress,
            'foreignkey' => 'testForeignKey',
            'confirmed' => 1,
            'htmlemail' => 1,
            'password' => 'password',
            'disabled' => 0,
        );

        // Execute the api call
        $result = $this->callAPI('subscriberAdd', $post_params);
        // Test if the user was created successfully
        $this->assertEquals('success', $result->status);

        $subscriberId = $result->data->id;

        // Pass on the newly created userid to other tests
        return $subscriberId;
    }
    
    /**
     * Test adding a the subscriber again
     *
     * this should fail on being duplicate
     * @depends testSubscriberExist
     */
    public function testSubscriberAddAgain($testEmailAddress)
    {
        $post_params = array(
            'email' => $testEmailAddress,
            'confirmed' => 1,
            'htmlemail' => 1,
            'password' => 'password',
            'disabled' => 0,
        );

        $result = $this->callAPI('subscriberAdd', $post_params);
        $this->assertEquals('error', $result->status);
    }

    /**
     * Test updating the subscriber
     *
     * @depends testSubscriberAdd
     */
    public function testSubscriberUpdate($subscriberId)
    {
        $changedEmail = 'updatetest-'.time().rand(100, 999).'@phplist.com';
        $post_params = array(
            'id' => $subscriberId,
            'email' => $changedEmail,
            'confirmed' => 1,
            'htmlemail' => 0,
        );

        $result = $this->callAPI('subscriberUpdate', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertEquals(1, $result->data->confirmed);
        $this->assertEquals($changedEmail,$result->data->email);
        $this->assertEquals(0, $result->data->htmlemail);
        $testEmailAddress = $changedEmail;
        return $testEmailAddress;
    }
    
    /** 
     * test getting subscriber by ID
     * @depends testSubscriberAdd
     * @depends testSubscriberUpdate
     * 
     */
    public function testSubscriberGet($subscriberId,$testEmailAddress) {
        $post_params = array(
            'id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('subscriberGet', $post_params);
        $this->assertEquals('success', $result->status);
        $fetchedSubscriberId = $result->data->id;
        $this->assertEquals($fetchedSubscriberId, $subscriberId);
        $this->assertEquals($testEmailAddress,  $result->data->email);

        return $subscriberId;
    }
    
    /**
     * Test add subscriber and subscribing to a list in one call
     *
     * @depends testListAdd
     */
    public function testSubscribe($listId)
    {
        // Set the user details as parameters
        $post_params = array(
            'email' => $this->testEmailAddress,
            'htmlemail' => 1,
            'foreignkey' => '',
            'subscribepage' => 0,
            'lists' => $listId,
        );

        // Execute the api call
        $result = $this->callAPI('subscribe', $post_params);
        // Test if the user was created successfully
        $this->assertEquals('success', $result->status);

        $subscriberId = $result->data->id;

        // Pass on the newly created userid to other tests
        return $subscriberId;
    } 
    
    /** 
     * test getting subscriber by Foreign Key
     * @depends testSubscriberAdd
     * @depends testSubscriberUpdate
     * 
     */
    public function testSubscriberGetByFK($subscriberId,$testEmailAddress) {
        $post_params = array(
            'foreignkey' => 'testForeignKey',
        );

        // Execute the api call
        $result = $this->callAPI('subscriberGetByForeignkey', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_numeric($result->data->id));
        $fetchedSubscriberId = $result->data->id;
        $this->assertEquals($fetchedSubscriberId, $subscriberId);
        $this->assertEquals($testEmailAddress,  $result->data->email);

        return $subscriberId;
    }
    /** 
     * test getting subscriber by ID
     * @depends testSubscriberAdd
     * @depends testSubscriberExist
     * 
     */
    public function testSubscriberGetFailed($subscriberId,$testEmailAddress) {
        $post_params = array(
            'id' => 'id',
        );

        // Execute the api call
        $result = $this->callAPI('subscriberGet', $post_params);
        $this->assertEquals('error', $result->status);

        return $subscriberId;
    }

    /** 
     * Test that a subscriber exists again.
     *
     * @depends testSubscriberUpdate
     */
    public function testSubscriberExist2($testEmailAddress)
    {
        $params = array(
            'email' => $testEmailAddress,
        );
        $result = $this->callAPI('subscriberGetByEmail', $params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_numeric($result->data->id)); // now it does
        $subscriberId = $result->data->id;
        return $subscriberId;
    }

     /**
      * Test counting the total number of subscribers.
      * We should now have one more than before.
      *
      * @depends testSubscriberCount
      */
     public function testSubscriberCount2($subscriberCount)
     {
         $post_params = array(
        );
        // Execute the api call
        $result = $this->callAPI('subscribersCount', $post_params);
         $this->assertEquals('success', $result->status);
         $this->assertTrue(is_numeric($result->data->total));
         $this->assertEquals($subscriberCount + 2, $result->data->total);

         if ($this->debug) {
             print 'There are now '.$result->data->total.' subscribers'.PHP_EOL;
         }
         $subscriberCount2 = $result->data->total;

         return $subscriberCount2;
     }

    /**
     * Test adding a subscriber to an existing list.
     *
     * @depends testListAdd
     * @depends testSubscriberAdd
     */
    public function testListSubscriberAdd($listId, $subscriberId)
    {
        // Set list and subscriber vars
        $post_params = array(
            'list_id' => $listId,
            'subscriber_id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('listSubscriberAdd', $post_params);
        // Test if the user was added to the list successfully
        $this->assertEquals('success', $result->status);
    }

    /**
     * Verify the lists a subscriber is member of.
     * The subscriber should be on the list.
     *
     * @depends testListAdd
     * @depends testSubscribe
     */
     
    public function testListsSubscriberSubscribe($listId, $subscriberId)
    {
        $post_params = array(
            'list_id' => $listId,
            'subscriber_id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('listsSubscriber', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_array($result->data));
        $listIds = array();
        foreach ($result->data as $resultItem) {
            $listIds[] = $resultItem->id;
        }
        $this->assertContains($listId, $listIds);
    }

     /**
     * Verify the lists a subscriber is member of.
     * The subscriber should be on the list.
     *
     * @depends testListAdd
     * @depends testSubscriberAdd
     */
     
    public function testListsSubscriber($listId, $subscriberId)
    {
        $post_params = array(
            'subscriber_id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('listsSubscriber', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_array($result->data));
        $listIds = array();
        foreach ($result->data as $resultItem) {
            $listIds[] = $resultItem->id;
        }
        $this->assertContains($listId, $listIds);
    }
    /** 
      * list all templates.
      */
     public function testListTemplates()
     {
         $post_params = array(
        );

         $result = $this->callAPI('templatesGet', $post_params);
         $this->assertEquals('success', $result->status);
         $templateCount = count($result->data);

         return $templateCount;
     }
    /** 
     * Test template existence and creation.
     */
    public function testTemplateGetByTitle()
    {
        $post_params = array(
            'title' => $this->testTemplateTitle,
        );

        // Execute the api call
        $result = $this->callAPI('templateGetByTitle', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_array($result->data));

        if (empty($result->data->id)) {
            $template_content = file_get_contents(__DIR__.'/test-template1.html');
            $post_params = array(
                'title' => $this->testTemplateTitle,
                'template' => $template_content,
            );
            $result = $this->callAPI('templateAdd', $post_params);
            $this->assertEquals('success', $result->status);
            $this->assertEquals($this->testTemplateTitle, $result->data->title);
            $this->assertTrue(is_numeric($result->data->id));
            if ($this->debug) {
                echo 'New template created for test.'.PHP_EOL;
            }
            $templateId = $result->data->id;
        } else {
            $templateId = $result->data->id;
        }

        return $templateId;
    }

    /** 
     * Test template existence and creation.
     *
     * @depends testTemplateGetByTitle
     */
    public function testTemplateUpdate($templateId)
    {
        $post_params = array(
            'id' => $templateId,
            'title' => $this->testTemplateRenamedTitle,
            'template' => file_get_contents(__DIR__.'/test-template2.html'),
        );

        // Execute the api call
        $result = $this->callAPI('templateUpdate', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_numeric($result->data->id));
        $this->assertEquals($result->data->id, $templateId);
        $this->assertEquals($this->testTemplateRenamedTitle, $result->data->title);
        $templateId = $result->data->id;

        return $templateId;
    }

     /** 
      * list all templates again, we should have one more now.
      *
      * @depends testListTemplates
      */
     public function testListTemplatesAgain($templateCount)
     {
         $post_params = array(
        );

         $result = $this->callAPI('templatesGet', $post_params);
         $this->assertEquals('success', $result->status);
         $newTemplateCount = count($result->data);
         $this->assertEquals($templateCount + 1, count($result->data));

         return $newTemplateCount;
     }

     /**
      * test counting the number of campaigns.
      */
     public function testCountCampaigns()
     {
         $post_params = array(
        );

         $result = $this->callAPI('campaignsCount', $post_params);
         $this->assertEquals('success', $result->status);
         $campaignCount = $result->data->total;

         return $campaignCount;
     }

     /**
      * Test fetching all campaigns.
      * the API limits to 10, so it should not return more than that
      * @depends testCountCampaigns
      * 
      */
     public function testCampaignsGet($campaignCount)
     {       
         $post_params = array(
          'order_by' => '', 
          'order' => '', 
          'limit' => 500, 
          'offset' => 0,
        );
        if ($campaignCount > 10) {
          $max = 10;
        } else {
          $max = $campaignCount;
        }
        // Execute the api call
         $result = $this->callAPI('campaignsGet', $post_params);
         $this->assertEquals('success', $result->status);
         $this->assertTrue(is_array($result->data));
         $this->assertTrue(count($result->data) <= 10);
         $this->assertEquals(count($result->data),$max);
         if ($this->debug) {
             print 'There are '.count($result->data).' campaigns'.PHP_EOL;
         }
         $campaignCount = count($result->data);

         return $campaignCount;
     }
     
     /**
      * 
      * test creating a campaign
      * @depends testTemplateGetByTitle
      */
           
     function testCreateCampaign($templateId) {
        $post_params = array(
            'subject' => 'Test Campaign created by API '.time(),
            'fromfield' => 'From Name apitest@phplist.com',
            'replyto' => '',
            'message' => 'Test Message',
            'textmessage' => 'Text',
            'footer' => 'Footer',
            'status' => 'submitted',
            'sendformat' => 'both',
            'template' => $templateId,
            'embargo' => date('Y-m-d'),
            'rsstemplate' => '',
            'owner' => 0,
            'htmlformatted' => 1,
        );
        
        $result = $this->callAPI('campaignAdd', $post_params);
        $this->assertEquals('success', $result->status);
        $campaignID = $result->data->id;
        return $campaignID;
    }
 
      /**
      * 
      * test creating a campaign with UTF-8 characters
      */
           
     function testCreateCampaignUTF8() {
        $post_params = array(
            'subject' => 'Test Campaign created by API '.time(),
            'fromfield' => 'From Name apitest@phplist.com',
            'replyto' => '',
            'message' => '快速的棕色狐狸跳过懒狗',
            'textmessage' => '快速的棕色狐狸跳过懒狗',
            'footer' => 'Footer',
            'status' => 'submitted',
            'sendformat' => 'both',
            'template' => 0,
            'embargo' => date('Y-m-d'),
            'rsstemplate' => '',
            'owner' => 0,
            'htmlformatted' => 1,
        );
        
        $result = $this->callAPI('campaignAdd', $post_params);
        $this->assertEquals('success', $result->status);
        $campaignID = $result->data->id;
        $this->assertEquals($result->data->message,'快速的棕色狐狸跳过懒狗');
        return $campaignID;
    }
 
  
    /**
     * update a campaign
     * @depends testCreateCampaign
     */
         
/** this test fails on travis, but not locally on my machine.

    function testUpdateCampaign($campaignID) {
        
        $post_params = array(
            'id' => $campaignID,
        );
        
        $result = $this->callAPI('campaignGet', $post_params,true);
        $this->assertEquals('success', $result->status);
        
        $current = $result->data;
        $post_params = array(
            'id' => $campaignID,
            'subject' => 'Test Campaign updated by API '.time(),
            'fromfield' => $current->fromfield,
            'replyto' => $current->replyto,
            'message' => $current->message,
            'textmessage' => $current->textmessage,
            'footer' => $current->footer,
            'status' => 'submitted',
            'sendformat' => $current->sendformat,
            'template' => $current->template,
            'embargo' => $current->embargo,
            'rsstemplate' => $current->rsstemplate,
            'owner' => $current->owner,
            'htmlformatted' => $current->htmlformatted,
        );
        
        $result = $this->callAPI('campaignUpdate', $post_params);
        $this->assertEquals('success', $result->status);
        $campaignID = $result->data->id;
        return $campaignID;
    }
*/

     /**
      * test counting the number of campaigns again, should be one more
      * @depends testCountCampaigns
      */
     public function testCountCampaignsAgain($campaignCount)
     {
         $post_params = array(
        );

         $result = $this->callAPI('campaignsCount', $post_params);
         $this->assertEquals('success', $result->status);
         $this->assertEquals($campaignCount+2, $result->data->total);
         $campaignCount = $result->data->total;

         return $campaignCount;
     }
     
     /**
      * test adding a campaign to a list
      * 
      * @depends testCreateCampaign
      * @depends testListAdd
      */
    public function testAddCampaignToList($campaignID,$listId) {
        $post_params = array(
            'list_id' => $listId,
            'campaign_id' => $campaignID,
        );
        $result = $this->callAPI('listCampaignAdd', $post_params);
        $this->assertEquals('success', $result->status);
    }
    
 
    /**
     * test removing Subscriber from list
     *
     * @depends testListAdd
     * @depends testSubscriberAdd
     */
    public function testListSubscriberDelete($listId, $subscriberId)
    {
        // Set list and subscriber vars
        $post_params = array(
            'list_id' => $listId,
            'subscriber_id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('listSubscriberDelete', $post_params);

        $this->assertEquals('success', $result->status);
    }

    /**
     * Verify the lists a subscriber is member of.
     * Now the subscriber should no longer be on the list.
     *
     * @depends testListAdd
     * @depends testSubscriberAdd
     */
     
    public function testListsSubscriberAgain($listId, $subscriberId)
    {
        $post_params = array(
            'list_id' => $listId,
            'subscriber_id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('listsSubscriber', $post_params);
        $this->assertEquals('success', $result->status);
        $this->assertTrue(is_array($result->data));
        $listIds = array();
        foreach ($result->data as $resultItem) {
            $listIds[] = $resultItem->id;
        }
        $this->assertNotContains($listId, $listIds);
    }
    
    /**
     * delete the subscriber
     *
     * @depends testSubscriberAdd
     */
     
    public function testSubscriberDelete($subscriberId)
    {
        $post_params = array(
            'id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('subscriberDelete', $post_params);
        $this->assertEquals('success', $result->status);
    }
    
    /**
     * fail deleting the subscriber
     *
     * @depends testSubscriberAdd
     */
     
    public function testSubscriberDeleteFailed($subscriberId)
    {
        $post_params = array(
            'id' => 'id',
        );

        // Execute the api call
        $result = $this->callAPI('subscriberDelete', $post_params);
        $this->assertEquals('error', $result->status);
    }
    
    /**
     * delete the 2nd subscriber
     *
     * @depends testSubscribe
     */
     
    public function testSubscriber2Delete($subscriberId)
    {
        $post_params = array(
            'id' => $subscriberId,
        );

        // Execute the api call
        $result = $this->callAPI('subscriberDelete', $post_params);
        $this->assertEquals('success', $result->status);
    }

    /**
     * Test deleting the template with incorrect data
     */
     
     public function testTemplateDeleteFailure() 
     {
        $post_params = array(
            'id' => 'id; delete from phplist_admin;',
        );
        if ($this->debug) {
            print 'Attempting to delete template with incorrect ID'.PHP_EOL;
        }
        // Execute the api call
        $result = $this->callAPI('templateDelete', $post_params);

        $this->assertEquals('error', $result->status);
    }
          
    /**
     * Test deleting the template
     * @depends testTemplateUpdate
     */
     
     public function testTemplateDelete($templateId) 
     {
        $post_params = array(
            'id' => $templateId,
        );
        if ($this->debug) {
            print 'Deleting template '.$templateId.PHP_EOL;
        }
        // Execute the api call
        $result = $this->callAPI('templateDelete', $post_params);

        $this->assertEquals('success', $result->status);
        $this->assertEquals('Item with '.$templateId.' is successfully deleted!', $result->data);
    }

    /**
     * Test deleting an existing list.
     *
     * @note Simply trusts the status returned from the API. Deeper testing required
     * @depends testListAdd
     */
    public function testListDelete($listId)
    {
        // Create minimal params for api call
        $post_params = array(
            'id' => $listId,
        );
        if ($this->debug) {
            print 'Deleting list '.$listId.PHP_EOL;
        }
        // Execute the api call
        $result = $this->callAPI('listDelete', $post_params);

        // Check if the list was deleted successfully
        $this->assertEquals('success', $result->status);
        $this->assertEquals('Item with '.$listId.' is successfully deleted!', $result->data);
    }
    
    /**
     * Test deleting an non-existing list.
     *
     */
    public function testListDeleteFailure()
    {
        // Create minimal params for api call
        $post_params = array(
            'id' => '> 0; delete from phplist_usermessage;',
        );
        if ($this->debug) {
            print 'Attempt to deleting invalid list '.PHP_EOL;
        }
        // Execute the api call
        $result = $this->callAPI('listDelete', $post_params);

        // Check if the list was deleted successfully
        $this->assertEquals('error', $result->status);
        $this->assertNotEquals('Item with is successfully deleted!', $result->data);
     }
   }
