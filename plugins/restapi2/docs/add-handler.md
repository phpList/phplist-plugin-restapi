# How to add calls to the REST API

The REST API (RAPI) uses "handler" classes for providing access to underlaying phpList functionality. See ```strucutre.md``` for more explanation.

Each handler class corresponds to a lower level class responsible for a group of actions. To add support for more calls to RAPI, either extend an existing handler class, or add a new one if the calls relate to a new category of action. See existing handlers for examples of actions currently supported, and how they're grouped.

## Adding a call to an existing handler

This can be achieved by simply adding a wrapper method to the existing handler class. Access to the underlaying class which will fulfill the request should already be available - the classes you need should be properties of the handler class in question. E.g. the SubscriberHandler has object properties housing SubscriberManager{} and SubscriberEntity{} -- calls to either of those phplist 4 classes is therefore easily available.

You must also whitelist your new call by adding it to the array for the handler class in question, in ```/whitelist.php```. Add an array key with the name of your call, with a value of ```(bool) true```.

## Adding a new handler

The Call{} class received incoming API requests, and validates them before searching for a corresponding handler class and method.

To add a new handler class:

* Create a new class following the existing naming convention, inside the ```/lib/Handler``` folder
* Add a service definition in ```/services.yml```, in the handler classes section
* Add your new handler object as a required argument of ```/lib/Call.php```
* Add a new child array with the name of your handler class as the parent array key in ```/whitelist.php```, and add each new call as a key value pair to it, with the name of the call as the key, and the value as ```(bool) true```.
