#phpList API
REST API as a plugin to [phpList](https://www.phplist.com)

Two plugins inside this repo: "restapi" and "restapi-test"

Development by Flowcom AB, Andreas Ek ([@EkAndreas](https://twitter.com/ekandreas))

License: GPLv2

##History
```
v.			Date				Description
0.2.7		2013-03-13	Added order and limit to listsGet
0.2.6		2013-03-13	Test script fixed with login and password.
0.2.5		2013-02-24	New name: RESTAPI and some documentation updates. No new functionality.
0.2.4		2012-12-29	API-location "coderoot" dynamically routed, recommended by Bramley
0.2.3		2012-12-29	Corrections to static calls, recommended by Bramley
0.2.2		2012-12-29	Some more documentation added
0.2.1		2012-12-29	Documentation generator started
0.2			2012-12-28	Slim Framework removed
0.1			2012-12-26	Created
```

##Installation
###1. Activate plugins in phpList
Change the config-parameter for plugin folder in /config/config.php.

Example of definition in config-file:
```
define("PLUGIN_ROOTDIR","/var/www/yoursite.com/plugins");
```

###2. Copy plugin files
Copy the files under "plugins" to the plugins folder!

Example of destination:
```
/var/www/yoursite.com/plugins
```

###3. Log in
Log in to /admin and there should be two menues added; "restapi" and "restapi test".

Click on left admin menu "restapi" for more information!

###4. Test
Click on "restapi test" to test your installation of the API plugin!

##Access
Autentication required as admin in phpList.

All requests to the RESTAPI is made by method POST.

Example of RESTAPI-Url:
```
http://www.yoursite.com/lists/admin/?page=call&pi=restapi
```

First login to phpList with *POST* method and body parameters: "login" and "password".


##Client
To try the RESTAPI, please use a client like CocaRestClient or eqvivalent!

There is an example class in restapi-test/phplist_restapi_helper.php if you like to try it in PHP.

For examples check commands in restapi-test/main.php

##Standard reponse
All responses is returned in json and encoded to UTF-8.

Success response from the API
```json
{
  "status":"success",
  "type":"List",
  "data":[{
    "id":"12",
    "name":"A new list in phpList"
  }]
}
```
Error response from the API
```json
{
  "status":"error",
  "type":"Error",
  "data":[{
    "code":"123",
    "message":"Error when creating list"
  }]
}
```


##Example in PHP
The following code log

```php
<?php

$url = 'http://www.yoursite.com/lists/admin/?page=call&pi=restapi';

//initialize cUrl for remote content
$c = curl_init();
curl_setopt( $c, CURLOPT_COOKIEFILE, 'phpList_RESTAPI_Helper' );
curl_setopt( $c, CURLOPT_COOKIEJAR, 'phpList_RESTAPI_Helper' );
curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $c, CURLOPT_POST, 1 );

//Call for the session-id via /login 
curl_setopt( $c, CURLOPT_URL, $url );
curl_setopt( $c, CURLOPT_POSTFIELDS, 'cmd=login&login=admin&password=phplist' );
$result = curl_exec( $c );
$result = json_decode( $result );
var_dump( $result->data ); 

//Get all lists in phpList via /listsGet
curl_setopt( $c, CURLOPT_URL, $url );
curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $c, CURLOPT_POST, 1 );
curl_setopt( $c, CURLOPT_POSTFIELDS, 'cmd=listsGet' );
$result = curl_exec( $c );
$result = json_decode( $result );

//Now close the cUrl when finished 
curl_close( $c );

//Dump all lists in phpList via /listsGet
var_dump( $result->data );

?>
```
