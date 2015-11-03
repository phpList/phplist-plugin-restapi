<?php

namespace Rapi;

// Symfony namespaces
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// NOTE: All include classes used to have this init check, now it's only here
defined('PHPLISTINIT') || die;

require_once 'vendor/autoload.php';

$plugin = $GLOBALS['plugins'][$_GET['pi']];

// Create Symfony DI service container object for use by other classes
$container = new ContainerBuilder();
// Create new Symfony file loader to handle the YAML service config file
$loader = new YamlFileLoader( $container, new FileLocator(__DIR__) );
// Load the service config file, which is in YAML format
$loader->load( 'services.yml' );

// Set necessary config class parameter
$container->setParameter( 'config.configfile', dirname(__FILE__) . '/config.php' );
// Set service parameters for the database connection
// These service parameters will be used as constructor arguments for pdoEx{}
$container->setParameter( 'pdoEx.hostname', $GLOBALS['database_host'] );
$container->setParameter( 'pdoEx.username', $GLOBALS['database_user'] );
$container->setParameter( 'pdoEx.pass', $GLOBALS['database_password'] );
$container->setParameter( 'pdoEx.dbname', $GLOBALS['database_name'] );

// Get Common{} object
$common = $container->get( 'Common' );

// TODO: Replace hardcoded admin url with one set centrally
$url = $common->apiUrl( $website, $pageroot, '/admin/' );

// Import CSS from plugin
$css = file_get_contents( dirname(__FILE__) . '/style.css' );
// Inject CSS into page
echo "<style>$css</style>";

?>
<link rel="stylesheet" type="text/css" href="<?php echo dirname(__FILE__); ?>/style.css"/>

<h1>REST API</h1>

<p><strong>License: GPLv3 or later</strong></p>

<p>The plugin provides a REST API to phpList.<br/> Development by <a href='http://samtuke.com'>Sam Tuke</a>, <a href='http://phplist.com'>Michiel Dethmers</a>. Based on work by <a href='https://twitter.com/ekandreas'>Andreas Ek</a> of Flowcom AB.</p>

<h2>Commands</h2>

<h3>Subscriber actions</h3>

<dl>
    <dt>add</dt>
    <dd>Insert a new subscriber with complete subscriber details</dd>

    <dt>addEmailOnly</dt>
    <dd>Insert a new subscriber with only an email address</dd>

    <dt>getById</dt>
    <dd>Get a subscriber by their ID</dd>

    <dt>delete</dt>
    <dd>Delete a subscriber by their ID</dd>
</dl>

<h2>Access</h2>

<p>phpList admin autentication is required. This can be completed via an API call, or via web form login. The session must be authenticated for plugin access.</p>

<h3>Login authentication</h3>

<p>Logging in via an API call is different to other calls - login is handled by the host phpList installation and not by the plugin itself.</p>
<p>Simply send an HTTP POST request with two parameters to login remotely: <code>login</code> and <code>password</code>.

<p>All requests to the RESTAPI are made by method HTTP POST (GET is not allowed). Use of HTTPS is strongly recommended.</p>

<h3>URL</h3>

<p>The URL for making API calls to this server is:<br/><strong><a href='<?php echo $url; ?>'><?php echo $url; ?></a></strong></p>

<h2>Clients and examples</h2>
<p> Make test calls to the RESTAPI using a client such as <a href="https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?hl=en" rel="nofollow" target="blank">Postman</a> for Chrome/Chromium browsers, or <a href="https://mmattozzi.github.io/cocoa-rest-client/" rel="nofollow" target="blank">CocaRestClient</a>.</p>
<p>Implementation examples in PHP can be found in <code>/docs/example.php</code>, and in unit test cases.</p>

<h2>Configuration</h2>

<h3>Access permission</h3>

<p>Allowable API calls are defined in <code>/whitelist.php</code>. Both sets of actions (e.g. subscriber-related calls), and also individual calls (e.g. delete subscriber) may be white or blacklisted.</p>

<h3>phpList 4</h3>

<p>The plugin uses phpList 4 as an external library for handling execution of API calls. phpList4 settings can be added in <code>config-phplist4.php</code>.

<h2>More information</h2>
<p>See the <code>/docs</code> folder for detailed documentation on the structure and usage of this plugin.</p>

<h2>Issues</h2>
<p>
    Report issues to the central phpList <a href='https://mantis.phplist.com/'>bug tracker</a>.
</p>
