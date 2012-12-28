<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class api extends phplistPlugin {

    var $name = "API";
    var $coderoot = "plugins/api/";

    function api() {
    }

    function adminmenu() {
        return array(
            "main" => "API"
        );
    }

}
?>
