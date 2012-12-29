<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class api extends phplistPlugin {

    var $name = "API";

    function api() {
        $this->coderoot = dirname(__FILE__) . '/api/';
    }

    function adminmenu() {
        return array(
            "main" => "API"
        );
    }

}
?>
