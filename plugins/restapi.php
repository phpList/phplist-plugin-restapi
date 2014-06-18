<?php
/**
 * Plugin that implements a REST API
 * 
 * Documentation: http://resources.phplist.com/plugin/restapi
 * 
 * version history:
 * 
 * v 2 * phpList Api Team https://github.com/orgs/phpList/teams/api
 * - renamed plugin repository to phplist-plugin-restapi
 * - https://github.com/phpList/phplist-plugin-restapi
 * 
 * v 1 * Andreas Ek, 2012-12-26
 * https://github.com/EkAndreas/phplistapi
 */
defined('PHPLISTINIT') || die;

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
