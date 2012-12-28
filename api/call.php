<?php

//No HTML-output, please!
ob_end_clean();

//Getting PHPlist globals for this plugin
$plugin = $GLOBALS["plugins"][$_GET["pi"]];

include 'includes/response.php';
include 'includes/pdo.php';

include 'includes/common.php';

include 'includes/actions.php';
include 'includes/lists.php';
include 'includes/users.php';
include 'includes/templates.php';
include 'includes/messages.php';

//Check if this is called outside PHPlist auth, this should never occur!
if ( empty( $plugin->coderoot ) ){
    PHPlist_API_Response::outputMessage('Not authorized! Please login with [login] and [password] as admin first!');
}

//Only POST request methods allowed
if ( strcmp( $_SERVER['REQUEST_METHOD'], "POST")  ){
    PHPlist_API_Response::outputMessage('Only requests method POST is allowed here!');
}

//Check if command is empty!
$cmd = $_REQUEST['cmd'];
if ( empty($cmd) ){
    PHPlist_API_Response::outputMessage('OK! For action, please provide Post Param Key [cmd] !');
}

//Now bind the commands with static functions
call_user_func( array( 'PHPlist_API_Actions',     $cmd ) );
call_user_func( array( 'PHPlist_API_Lists',       $cmd ) );
call_user_func( array( 'PHPlist_API_Users',       $cmd ) );
call_user_func( array( 'PHPlist_API_Templates',   $cmd ) );
call_user_func( array( 'PHPlist_API_Messages',    $cmd ) );

//If no command found, return error message!
PHPlist_API_Response::outputErrorMessage( 'No function for provided [cmd] found!' );

?>