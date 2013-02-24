<?php
/**
 * Plugin that tests the REST API
 * Andreas Ek, 2012-12-26
 */
class restapi extends phplistPlugin {

    var $name = "RESTAPI";

    function restapi() {
        $this->coderoot = dirname(__FILE__) . '/restapi/';
    }

    function adminmenu() {
        return array(
            "main" => "RESTAPI"
        );
    }

}
?>
