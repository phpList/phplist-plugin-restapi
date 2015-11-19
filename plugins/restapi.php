<?php

/**
 * Plugin that implements a REST API.
 * 
 * Documentation: http://resources.phplist.com/plugin/restapi
 * 
 * version history:
 * 
 * v 3 2015-11-19
 * - updated some calls
 * - added more security checks
 * - added phpunit tests
 * - removed obsolete fields
 * - added limit to subscriber retrieval
 * - added limit to campaign retrieval
 * - renamed messages to campaigns
 * - renamed users to subscribers
 * 
 * v 2 * phpList Api Team https://github.com/orgs/phpList/teams/api
 * - renamed plugin repository to phplist-plugin-restapi
 * - https://github.com/phpList/phplist-plugin-restapi
 * 
 * v 1 * Andreas Ek, 2012-12-26
 * https://github.com/EkAndreas/phplistapi
 */
defined('PHPLISTINIT') || die;

class restapi extends phplistPlugin
{
    public $name = 'RESTAPI';
    public $description = 'Implements a REST API interface to phpList';
    public $version = 3;
    public $documentationUrl = 'https://resources.phplist.com/plugin/restapi';
    public $topMenuLinks = array(
      'main' => array('category' => 'system'),
    );

    public $DBstruct = array(
        'request_log' => array(
            'id' => array('integer not null primary key auto_increment', 'ID'),
            'url' => array('text not null', ''),
            'cmd' => array('varchar(150) not null',''),
            'ip' => array('varchar(15) not null',''),
            'request' => array('text not null', ''),
            'date' => array('timestamp not null', ''),
            'index_1' => array('dateidx (date)',''),
            'index_2' => array('cmdidx (cmd)',''),
            'index_3' => array('ipidx (ip)',''),
        ),
    );

    public $settings = array(
        'restapi_limit' => array(
            'description' => 'Maximum number of RESTAPI requests per minute',
            'type' => 'integer',
            'value' => 60,
            'allowempty' => false,
            'min' => 1,
            'max' => 1200,
            'category' => 'Security',
        ),
        'restapi_enforcessl' => array(
            'description' => 'Require SSL on Rest API calls',
            'type' => 'boolean',
            'allowempty' => true,
            'value' => false,
            'category' => 'Security',
        ),
        'restapi_ipaddress' => array(
            'description' => 'IP Address that is allowed to access the API',
            'type' => 'text',
            'allowempty' => true,
            'value' => '',
            'category' => 'Security',
        ),
        'restapi_usesecret' => array(
            'description' => 'Require the secret code for Rest API calls',
            'type' => 'boolean',
            'allowempty' => true,
            'value' => false,
            'category' => 'Security',
        ),
    );
    public function __construct()
    {
        $this->coderoot = dirname(__FILE__).'/'.__CLASS__.'/';
        parent::__construct();
        if (!Sql_Table_exists($GLOBALS['table_prefix'].'restapi_request_log')) {
            saveConfig(md5('plugin-restapi-initialised'), false, 0);
            $this->initialise();
        }
    }

    public function adminmenu()
    {
        return array(
            'main' => 'RESTAPI Main',
        );
    }
}
