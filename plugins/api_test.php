<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class api_test extends phplistPlugin {

  var $name = "API Test";

  function api_test() {
      $this->coderoot = dirname(__FILE__) . '/api_test/';
  }

  function adminmenu() {
    return array(
      "main" => "Test API"
    );
  }

}
?>
