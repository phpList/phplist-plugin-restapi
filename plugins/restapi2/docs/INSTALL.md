# How to install the REST API plugin

## Pre-packaged

Pre-packaged versions of the plugins require no setup or configuration. Simply
put the application folder inside your phpList plugin directory and access
the plugin's main page though the phpList admin web UI.

## Manual installation

To install the plugin from development files, e.g. from the cloned Git
repository, complete these steps:

* Check that you are on the correct branch (your default branch may be outdated)
* [Install composer](https://getcomposer.org/download/) PHP package manager on your computer
* Move to inside the restapi folder and install dependencies with this command: ```composer install```
* Access the plugin's main page via the phpList web UI.
