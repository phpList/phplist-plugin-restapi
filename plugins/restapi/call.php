<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;


//Getting phpList globals for this plugin
$plugin = $GLOBALS['plugins'][$_GET['pi']];

include_once 'includes/response.php';
include_once 'includes/pdo.php';
include_once 'includes/common.php';
include_once 'includes/actions.php';
include_once 'includes/lists.php';
include_once 'includes/subscribers.php';
include_once 'includes/templates.php';
include_once 'includes/campaigns.php';
//If other than POST then assume documentation report
if (strcmp($_SERVER['REQUEST_METHOD'], 'POST')) {
    include_once 'doc/doc.php';
    $doc = new phpListRestapiDoc();
    $doc->addClass('Actions');
    $doc->addClass('Lists');
    $doc->addClass('Subscribers');
    $doc->addClass('Templates');
    $doc->addClass('Campaigns');
    print $doc->output();
    return;
}
ob_end_clean();

$cmd = $_REQUEST['cmd'];
$cmd = preg_replace('/\W/', '', $cmd);
if (empty($cmd)) {
    Response::outputMessage('OK! For action, please provide Post Param Key [cmd] !');
}

if (function_exists('api_request_log')) {
    api_request_log();
}

if (empty($plugin->coderoot)) {
    Response::outputErrorMessage('Not authorized! Please login with [login] and [password] as admin first!');
}

if ($cmd != 'login') {
  Common::logRequest($cmd);
  Common::enforceRequestLimit(getConfig('restapi_limit'));
}
$ipAddress = getConfig('restapi_ipaddress');
if (!empty($ipAddress) && ($GLOBALS['remoteAddr'] != $ipAddress)) {
    $response->outputErrorMessage('Incorrect ip address for request. Check your settings.');
    die(0);
} 
$requireSecret = getConfig('restapi_usesecret');
if ($requireSecret) {
  $secret = getConfig('remote_processing_secret');
  if (empty($_REQUEST['secret']) || $_REQUEST['secret'] != $secret) {
    $response->outputErrorMessage('Incorrect processing secret. Check your settings.');
    die(0);
  }
} 
$enforceSSL = getConfig('restapi_enforcessl');
if ($enforceSSL && empty($_SERVER['HTTPS'])) {
    $response->outputErrorMessage('Invalid API request. Request is not using SSL, which is enforced by the plugin settings.');
    die(0);
}

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
if (is_callable(array('phpListRestapi\Campaigns',    $cmd))) {
    Campaigns::$cmd();
}

//If no command found, return error message!
Response::outputErrorMessage('No function for provided [cmd] found!');
