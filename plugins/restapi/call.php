<?php

namespace phpListRestapi;

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

if (function_exists('api_request_log')) {
  api_request_log();
}

//Check if this is called outside phpList auth, this should never occur!
if ( empty( $plugin->coderoot ) ){
    Response::outputErrorMessage( 'Not authorized! Please login with [login] and [password] as admin first!' );
}

//If other than POST then assume documentation report
if ( strcmp( $_SERVER['REQUEST_METHOD'], "POST")  ) {
    $doc = new \phpListRestapiDoc();
    $doc->addClass( 'Actions' );
    $doc->addClass( 'Lists' );
    $doc->addClass( 'Users' );
    $doc->addClass( 'Templates' );
    $doc->addClass( 'Messages' );
    $doc->output();
}

//Check if command is empty!
$cmd = $_REQUEST['cmd'];
$cmd = preg_replace('/\W/','',$cmd);
if ( empty($cmd) ){
  Response::outputMessage('OK! For action, please provide Post Param Key [cmd] !');
}

//Now bind the commands with static functions
if ( is_callable( array( 'phpListRestapi\Lists',       $cmd ) ) ) Lists::$cmd();
if ( is_callable( array( 'phpListRestapi\Actions',     $cmd ) ) ) Actions::$cmd();
if ( is_callable( array( 'phpListRestapi\Subscribers', $cmd ) ) ) Subscribers::$cmd();
if ( is_callable( array( 'phpListRestapi\Templates',   $cmd ) ) ) Templates::$cmd();
if ( is_callable( array( 'phpListRestapi\Messages',    $cmd ) ) ) Messages::$cmd();

//If no command found, return error message!
Response::outputErrorMessage( 'No function for provided [cmd] found!' );
