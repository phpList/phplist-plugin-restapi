# Application structure

## Components

The Rest API (Rapi) plugin uses a combination of call listeners, action handlers, and third party libraries to generate responses to API calls.

A call is processed in the following order:

1. call.php (procedural plugin page)
1. Call{} class (validates and passes call to appropriate handler)
1. Handler{} class (prepares arguments for passing to external library)
1. phpList 4 library class (core class of phpList 4)

### call.php

This script meets the requirements of a plugin page - it is part of the URL accessed by applications making API calls, and is publicly accessible when the plugin is installed and initialised.

### Call{} class

This class is part of the Rapi plugin and houses a number of methods for checking and executing incoming calls. Critically, it contains object properties, one for each of the Handler classes which are available to handle API calls. E.g. Call{} has a property SubscriberHander{}.

#### Validation

When processing an incoming call, Call{} performs at least the following validation:

* Valid syntax
* available className
* available method name
* permitted (on the whitelist of allowed call actions)

If validation passes, then the requested method on the requested Handler class is executed, and supplied with all arguments which accomanied the incoming call.

#### Parameters & arguments

An unlimited number of call parameters can be accepted, and are passed to Handler class methods as function arguments. Currently parameters **must** be in alphabetical order. All Handler methods must specify default values therefore, as required arguments which are not ordered alphabetically in the handler method will upset the order of the arguments in passing through the application.

### Handler{} class

Handler classes are simple wrappers for the phpList 4 class method that they represent.

#### Purpose

Some phpList class methods require, for example, Entity objects as arguments (see phpList 4 documentation for an explanation of Entity data objects). As Entity objects cannot be provided by an HTTP POST call, they must be generated and populated with the correct properties, before a call to the required phpList 4 class method can be made. Other processing may also be necessary, of both the arguments sent to class methods, and the responses received from them.

#### Benefits

Handler classes are an abstraction layer between the plugin and low level actions, providing great flexibility. classNames and method names exposed via the API can be changed, if necessary, without any impact on the underlaying phpList 4 classes which handle the request. However maintaining parity between Handler method names and the methods of the classes which they call is recommended to avoid confusion.

#### Limitations

* Handler methods which support multiple arguments **must** provide those arguments in the correct order. E.g. POST requests should provide arguments in this order:
    - className (e.g. ```subscriberHandler```)
    - method (e.g. ```add```)
    - argument 1 (e.g. ```blacklisted```)
    - argument 2 (e.g. ```bounceCount```)
    - argument 3 (e.g. ```confirmed```)
    - ...

### phpList 4 library class

The Rapi plugin uses phpList 4 to enact requests received via its API, rather than handling those requests itself. Fetching, adding, and deleting subscribers, for example, is delegated to phpList 4.

Composer package manager is used to install and maintain the phpList 4 libraries. This is an unusual way to use composer, as phpList 4 is a standalone, full-stack application, and though it is used by the plugin as a library, it is not designed to be so.

phpList 4 package versions, hosted on packagist, are important. As phpList 4 development continues, changes which will break compatibility with the Rapi plugin are very likely. These can be avoided by using strict package versions in composer's configuration file.

## Authentication

Authentication is handled by the host phpList application. In phpList 3, the first API call must always be to login to the application. This is achieved by simply supplying two POST parameters: 'login', and 'password'. No action, className, or method are required. An HTML response will be returned if login fails. This is not controllable by the plugin at this time due to phpList 3 architecture.

Once authentication has been successful, API calls can be made using HTTP POST. HTTP GET calls are not accepted for security reasons.

#### Limitations

* Upon a successful login over the REST API, an html page will be returned, not a proper API response. This is due to the centralised handling of plugins and authentication in phpList.
* Upon an attempt to login by a client that is already logged in, a API response will be returned with an error message that no action was requested.
