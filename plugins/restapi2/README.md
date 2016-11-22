REST API
========

**License: GPLv3 or later**

The plugin provides a REST API to phpList.
Development by [Sam Tuke](http://samtuke.com), [Michiel
Dethmers](http://phplist.com). Based on work by [Andreas
Ek](https://twitter.com/ekandreas) of Flowcom AB.

Commands
--------

**See also the API blueprint in the /docs folder**

### Login actions

login
:   Login via the API

### Subscriber actions

add
:   Insert a new subscriber with complete subscriber details

addEmailOnly
:   Insert a new subscriber with only an email address

getById
:   Get a subscriber by their ID

delete
:   Delete a subscriber by their ID

### List actions

addSubscriber
:   Add subscriber to a list

Access
------

phpList admin autentication is required. This can be completed via an
API call, or via web form login. The session must be authenticated for
plugin access.

### Authentication

All requests to the RESTAPI are made by method HTTP POST (GET is not
allowed). Use of HTTPS is strongly recommended.

### URL

The URL for making API calls to this server is relative to the host server: 
`[phplist server address]/lists/admin/?page=call&pi=restapi2`

For convenience, the automatically generated URL can be found in the phpList web interface once the REST API v2 Plugin is installed and enabled, via the System menu -> 'restapi2 - main' page.

Clients and examples
--------------------

Make test calls to the RESTAPI using a client such as
[Postman](https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?hl=en)
for Chrome/Chromium browsers, or
[CocaRestClient](https://mmattozzi.github.io/cocoa-rest-client/).

Implementation examples in PHP can be found in `/docs/example.php`, and
in unit test cases.

Configuration
-------------

### Access permission

Allowable API calls are defined in `/whitelist.php`. Both sets of
actions (e.g. subscriber-related calls), and also individual calls (e.g.
delete subscriber) may be white or blacklisted.

### phpList 4

The plugin uses phpList 4 as an external library for handling execution
of API calls. phpList4 settings can be added in `config-phplist4.php`.

More information
----------------

See the `/docs` folder for detailed documentation on the structure and
usage of this plugin.

Issues
------

Report issues to the central phpList [bug
tracker](https://mantis.phplist.com/).
