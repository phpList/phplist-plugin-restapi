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
    }

    function adminmenu() {
        return array(
            "main" => "RESTAPI"
        );
    }

}
