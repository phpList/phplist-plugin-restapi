<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class restapi_test extends phplistPlugin {

  var $name = "RESTAPI Test";

  function restapi_test() {
      $this->coderoot = dirname(__FILE__) . '/restapi_test/';
  }

  function adminmenu() {
    return array(
      "main" => "Test RESTAPI"
    );
  }

}
?>
