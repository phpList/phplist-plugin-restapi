<?php

include 'phplist_api_helper.php';
include 'includes/common.php';

$plugin = $GLOBALS["plugins"][$_GET["pi"]];

$url = PHPlist_API_Common::apiUrl( $website );

?>

<h1>API</h1>

    <h2>Version 0.2.1</h2>
    <p>The plugin provides a REST API to PHPlist.<br/>
    Development by Flowcom AB, Andreas Ek (<a href="">@EkAndreas</a>)</p>

    <p>
        <h2>Commands</h2>
        To discover all commands to this API just make a GET request or click here:<br/>
        <a href="<?php echo $url; ?>">PHPlist API Command Reference list</a><br/>
        The documentation is generated in realtime.
    </p>
    <p>
        <h2>Access</h2>
        Autentication required as admin in PHPlist.<br/>
        All requests to the API is made by method POST.<br/>
        API-Url to this installation:<br/>
        <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
    </p>
    <p>
        First login to PHPlist with method /post and body parameters "login" and "password". Your session is then cleared!<br/>
    </p>
    <p>
        <h2>Client</h2>
        To try the API, please use a client like CocaRestClient or eqvivalent!<br/>
        There is an example class in api-test/phplist_api_helper.php if you like to try it in PHP.<br/>
        For examples check commands in api-test/main.php
    </p>

    <p>
        <h2>Standard reponse</h2>
        All responses is returned in json and encoded to UTF-8.<br/>
        <br/>
        Success response from the API<br/>
        {
        <blockquote>
            "status":"success",<br/>
            "type":"List",<br/>
            "data":[{
            <blockquote>
                "id":"12",<br/>
                "name":"A new list in PHPlist"
            </blockquote>
            }]
        </blockquote>
        }<br/>
        <br/>
        Error response from the API<br/>
        {
        <blockquote>
            "status":"error",<br/>
            "type":"Error",<br/>
            "data":[{
            <blockquote>
                "code":"123",<br/>
                "message":"Error when creating list"
            </blockquote>
            }]
        </blockquote>
        }<br/>
        <br/>
    </p>

    <p>
        <h2>Example in PHP:</h2>
        <span style="font-weight:bold;color:green">// Copy and paste this code-example!</span><br/><br/>
        <span style="font-weight:bold;color:green">//Get the session via login</span><br/><br/>
        <span style="font-weight:bold;color:green">//initialize cUrl for remote content</span><br/>
        $c = curl_init();<br/>
        curl_setopt($c, CURLOPT_COOKIEFILE,     'PHPlist_API_Helper');
        curl_setopt($c, CURLOPT_COOKIEJAR,      'PHPlist_API_Helper');
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);<br/>
        curl_setopt($c, CURLOPT_POST,           1);<br/>
        <br/>
        <span style="font-weight:bold;color:green">//Call for the session-id via /login</span>
        <br/>
        curl_setopt($c, CURLOPT_URL,            '<?php echo $url; ?>' );<br/>
        curl_setopt($c, CURLOPT_POSTFIELDS,     'cmd=login&login=admin&password=admin');<br/>
        $result = curl_exec($c);<br/>
        $result = json_decode($result);<br/>
        var_dump( $result->data );
        <br/>
        <br/>
        <span style="font-weight:bold;color:green">//Get all lists in PHPlist via /listsGet</span>
        <br/>
        curl_setopt($c, CURLOPT_URL,            '<?php echo $url; ?>' );<br/>
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);<br/>
        curl_setopt($c, CURLOPT_POST,           1);<br/>
        curl_setopt($c, CURLOPT_POSTFIELDS,     'cmd=listsGet' );<br/>
        $result = curl_exec($c);<br/>
        $result = json_decode($result);<br/>
        <br/>
        <span style="font-weight:bold;color:green">//Now close the cUrl when finished</span>
        <br/>
        curl_close($c);<br/>
        <br/>
        <span style="font-weight:bold;color:green">//Dump all lists in PHPlist via /listsGet</span>
        <br/>
        var_dump($result->data);<br/>

    </p>

    <p>
        &nbsp;
    </p>

<?php

?>

