<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

include 'includes/common.php';

$plugin = $GLOBALS["plugins"][$_GET["pi"]];

$url = Common::apiUrl( $website );

?>

<h1>RESTAPI</h1>

    <h2>Version 0.2.5</h2>
    <p>The plugin provides a REST API to phpList.<br/>
    Development by Flowcom AB, Andreas Ek (<a href="https://twitter.com/ekandreas">@EkAndreas</a>)</p>

    <p>
        <h2>Commands</h2>
        To discover all commands to this API just make a GET request or click here:<br/>
        <a href="<?php echo $url; ?>">phpList API Command Reference list</a><br/>
        The documentation is generated in realtime.
    </p>
    <p>
        <h2>Access</h2>
        Autentication required as admin in phpList.<br/>
        All requests to the RESTAPI is made by method POST.<br/>
        RESTAPI-Url to this installation:<br/>
        <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
    </p>
    <p>
        First login to phpList with method POST and body parameters "login" and "password".<br/>
    </p>
    <p>
        <h2>Client</h2>
        To try the RESTAPI, please use a client like CocaRestClient or eqvivalent!<br/>
        There is an example class in restapi-test/phplist_restapi_helper.php if you like to try it in PHP.<br/>
        For examples check commands in restapi-test/main.php
    </p>

    <p>
        <h2>More information</h2>
        Please read more at Github!
        <a href="https://github.com/EkAndreas/phplistapi">https://github.com/EkAndreas/phplistapi</a>
    </p>

    <p>
        <h2>Issues</h2>
        All issues regarding the RESTAPI is handled at Github!
        <a href="https://github.com/EkAndreas/phplistapi">https://github.com/EkAndreas/phplistapi</a>
    </p>

