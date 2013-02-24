<?php

//No HTML-output, please!
ob_end_clean();

//Getting phpList globals for this plugin
$plugin = $GLOBALS["plugins"][$_GET["pi"]];

include 'includes/response.php';
include 'includes/pdo.php';

include 'includes/common.php';

include 'includes/actions.php';
include 'includes/lists.php';
include 'includes/users.php';
include 'includes/templates.php';
include 'includes/messages.php';

include 'doc/doc.php';


//Check if this is called outside phpList auth, this should never occur!
if ( empty( $plugin->coderoot ) ){
    phpList_RESTAPI_Response::outputErrorMessage( 'Not authorized! Please login with [login] and [password] as admin first!' );
}

//If other than POST then assume documentation report
if ( strcmp( $_SERVER['REQUEST_METHOD'], "POST")  ){

    $doc = new phpList_RESTAPI_Doc();
    $doc->addClass( 'phpList_RESTAPI_Actions' );
    $doc->addClass( 'phpList_RESTAPI_Lists' );
    $doc->addClass( 'phpList_RESTAPI_Users' );
    $doc->addClass( 'phpList_RESTAPI_Templates' );
    $doc->addClass( 'phpList_RESTAPI_Messages' );
    $doc->output();

}

//Check if command is empty!
$cmd = $_REQUEST['cmd'];
if ( empty($cmd) ){
    phpList_RESTAPI_Response::outputMessage('OK! For action, please provide Post Param Key [cmd] !');
}

//Now bind the commands with static functions
if ( is_callable( array( 'phpList_RESTAPI_Lists',       $cmd ) ) ) phpList_RESTAPI_Lists::$cmd();
if ( is_callable( array( 'phpList_RESTAPI_Actions',     $cmd ) ) ) phpList_RESTAPI_Actions::$cmd();
if ( is_callable( array( 'phpList_RESTAPI_Users',       $cmd ) ) ) phpList_RESTAPI_Users::$cmd();
if ( is_callable( array( 'phpList_RESTAPI_Templates',   $cmd ) ) ) phpList_RESTAPI_Templates::$cmd();
if ( is_callable( array( 'phpList_RESTAPI_Messages',    $cmd ) ) ) phpList_RESTAPI_Messages::$cmd();

//If no command found, return error message!
phpList_RESTAPI_Response::outputErrorMessage( 'No function for provided [cmd] found!' );

?>