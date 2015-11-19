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
defined( 'PHPLISTINIT' ) || die;

class restapi2 extends phplistPlugin {

    // Set plugin name presented in admin pages
    public $name = 'RESTAPI2';
    // Description of the app as displayed in admin pages
    public $description = 'REST API interface to phpList4, work in progress';

    function restapi2() {
      parent::phplistplugin();
      // Set path to plugin folder
      $this->coderoot = dirname( __FILE__ ) . '/restapi2/';
    }

    // Set header nav link label, url, and category
    public $topMenuLinks = array(
        // Array key determines both label of admin menu item, & php file name
        // of page
        'main' => array( 'category' => 'system' ),
    );

    // Set dashboard link label and url
    function adminmenu() {
        return array(
            // Array key determines link URL in dashboard; value sets link label
            'main' => 'RESTAPI'
        );
    }

    // Add settings to admin interface
    // Note: stock text, ready for editing / customisation
    // public $settings = array(
    //     "myplugin_setting1" => array (
    //         'value' => "some default",
    //         'description' => 'Description of this setting',
    //         'type' => "text",
    //         'allowempty' => 0,
    //         "max" => 1000,
    //         "min" => 0,
    //         'category'=> 'general',
    //     ),
    // );

}
