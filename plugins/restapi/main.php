<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

include_once 'includes/common.php';

$plugin = $GLOBALS['plugins'][$_GET['pi']];

$url = Common::apiUrl($website);

?>

<h1>RESTAPI</h1>

    <h2>Version <?=$plugin->version?></h2>
    <p>This plugin provides a REST API to phpList.<br/>
    Development by Michiel Dethmers, phpList Ltd <br/>
    Based on work from Andreas Ek (<a href="https://twitter.com/ekandreas">@EkAndreas</a>)</p>

    <p>
        <h2>Commands</h2>
        To discover all commands to this API just make a GET request or click here:<br/>
        <a href="<?php echo $url; ?>">phpList API Command Reference list</a><br/>
        The documentation is generated in realtime.
    </p>
    <p>
      <h2>Example code</h2>
      To find example code for using the Rest API go to <a href="https://github.com/michield/phplist-restapi-client">https://github.com/michield/phplist-restapi-client</a>
      </p>
    
    <p>
        <h2>Access</h2>
        Autentication required as admin in phpList.<br/>
        All requests to the RESTAPI are made by method POST.<br/>
        RESTAPI-Url to this installation:<br/>
        <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
    </p>
    <p>
        First login to phpList with method POST and body parameters "login" and "password".<br/>
    </p>
    <p>
        <h2>Security</h2>
        <p>In the phpList <strong>Settings</strong> you can set various options to increase API security:
        <ul>
        <li>Require SSL on Rest API calls<br/>
        This is only useful when you run phpList on an HTTPS URL (recommended).
        </li>
        <li>IP Address that is allowed to access the API<br/>
        If you always access the API from the same IP address, use this option.</li>
        <li>Require the secret code for Rest API calls<br/>
        You will need to include the remote processing secret in all calls. See the example code.</li>
        </ul></p>
    </p>

    <p>
        <h2>More information</h2>
        Please read more at Github!
        <a href="https://github.com/phpList/phplist-plugin-restapi">https://github.com/phpList/phplist-plugin-restapi</a>
    </p>

    <p>
        <h2>Issues</h2>
        All issues regarding the RESTAPI are handled at Github!
        <a href="https://github.com/phpList/phplist-plugin-restapi/issues">https://github.com/phpList/phplist-plugin-restapi/issues</a>
    </p>

