<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

//No HTML-output, please!
ob_end_clean();

//Getting phpList globals for this plugin
$plugin = $GLOBALS['plugins'][$_GET['pi']];

include_once 'includes/response.php';
include_once 'includes/pdo.php';
include_once 'includes/common.php';
include_once 'includes/actions.php';
include_once 'includes/lists.php';
include_once 'includes/subscribers.php';
include_once 'includes/templates.php';
include_once 'includes/messages.php';
//If other than POST then assume documentation report
if (strcmp($_SERVER['REQUEST_METHOD'], 'POST')) {
    include_once 'doc/doc.php';
    $doc = new \phpListRestapiDoc();
    $doc->addClass('Actions');
    $doc->addClass('Lists');
    $doc->addClass('Subscribers');
    $doc->addClass('Templates');
    $doc->addClass('Messages');
    $doc->output();
}

$cmd = $_REQUEST['cmd'];
$cmd = preg_replace('/\W/', '', $cmd);
if (empty($cmd)) {
    Response::outputMessage('OK! For action, please provide Post Param Key [cmd] !');
}

$plugin->logRequest($cmd);

if (function_exists('api_request_log')) {
    api_request_log();
}


//Check if this is called outside phpList auth, this should never occur!
if (empty($plugin->coderoot)) {
    Response::outputErrorMessage('Not authorized! Please login with [login] and [password] as admin first!');
}

Common::LogRequest($cmd);
Common::enforceRequestLimit(getConfig('restapi_limit'));

//Now bind the commands with static functions
if (is_callable(array('phpListRestapi\Lists',       $cmd))) {
    Lists::$cmd();
}
if (is_callable(array('phpListRestapi\Actions',     $cmd))) {
    Actions::$cmd();
}
if (is_callable(array('phpListRestapi\Subscribers', $cmd))) {
    Subscribers::$cmd();
}
if (is_callable(array('phpListRestapi\Templates',   $cmd))) {
    Templates::$cmd();
}
if (is_callable(array('phpListRestapi\Messages',    $cmd))) {
    Messages::$cmd();
}

//If no command found, return error message!
Response::outputErrorMessage('No function for provided [cmd] found!');
