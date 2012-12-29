<?php

include 'phplist_api_helper.php';
include 'includes/common.php';

$plugin = $GLOBALS["plugins"][$_GET["pi"]];

$url = phpList_API_Common::apiUrl( $website );

?>

<h1>API</h1>

    <h2>Version 0.2.4</h2>
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
        All requests to the API is made by method POST.<br/>
        API-Url to this installation:<br/>
        <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
    </p>
    <p>
        First login to phpList with method POST and body parameters "login" and "password".<br/>
    </p>
    <p>
        <h2>Client</h2>
        To try the API, please use a client like CocaRestClient or eqvivalent!<br/>
        There is an example class in api-test/phplist_api_helper.php if you like to try it in PHP.<br/>
        For examples check commands in api-test/main.php
    </p>

    <p>
        <h2>More information</h2>
        Please read more at Github!
        <a href="https://github.com/EkAndreas/phplistapi">https://github.com/EkAndreas/phplistapi</a>
    </p>

    <p>
        <h2>Issues</h2>
        All issues regarding the API is handled at Github!
        <a href="https://github.com/EkAndreas/phplistapi">https://github.com/EkAndreas/phplistapi</a>
    </p>

<?php

?>

