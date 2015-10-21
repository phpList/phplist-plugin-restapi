<?php

/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26.
 */
class restapi_test extends phplistPlugin
{
    public $name = 'RESTAPI Test';
    public $description = 'Functionality tests for the REST API plugin for phpList';
    public $topMenuLinks = array(
    'main' => array('category' => 'develop'),
  );
    public $settings = array(
    'restapi_test_login' => array(
      'value'       => 'admin',
      'description' => 'Login name for REST API testing',
      'type'        => 'text',
      'allowempty'  => 0,
      'category'    => 'develop',
    ),
    'restapi_test_password' => array(
      'value'       => 'phplist',
      'description' => 'Login password for REST API testing',
      'type'        => 'text',
      'category'    => 'develop',
    ),
  );

    public function restapi_test()
    {
        parent::phplistplugin();
        $this->coderoot = dirname(__FILE__).'/restapi_test/';
    }

    public function adminmenu()
    {
        return array(
      'main' => 'Test RESTAPI',
    );
    }
}
