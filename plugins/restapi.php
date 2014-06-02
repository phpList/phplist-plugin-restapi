<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class restapi extends phplistPlugin {

    public $name = "RESTAPI";
    public $description = 'Implements a REST API interface to phpList';
    public $topMenuLinks = array(
      'main' => array('category' => 'system'),
    ); 

    function restapi() {
      parent::phplistplugin();
      $this->coderoot = dirname(__FILE__) . '/restapi/';
      // Need to find a better time to strip slashes. This is already done for the web interface in storemessage.php
      // recursively strip slashes from an array
      function stripslashes_r($array) {
      	foreach ($array as $key => $value) {
      		$array[$key] = is_array($value) ?
      		stripslashes_r($value) :
      		stripslashes($value);
      	}
      	return $array;
      }
      
      if (get_magic_quotes_gpc()) {
      	$_GET     = stripslashes_r($_GET);
      	$_POST    = stripslashes_r($_POST);
      	$_COOKIE  = stripslashes_r($_COOKIE);
      	$_REQUEST = stripslashes_r($_REQUEST);
      }
    }

    function adminmenu() {
        return array(
            "main" => "RESTAPI"
        );
    }

}
