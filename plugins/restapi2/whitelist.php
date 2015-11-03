<?php

/**
 * Configuration file for allowed API calls.
 * Top level arrays are allowed Handlers. Set 'enabled' to false to blacklist
 * all methods of a given handler. E.g. set
 * $whitelist['subscriberHandler']['enabled'] to false to disable all
 * subscriber-related API calls.
 *
 * Individual handler methods can be disabled by setting the key with the method
 * name in the array to false.
 */

// Whitelist configuration variable
$whitelist = array(
    'subscriberHandler' => array(
        'enabled' => true
        , 'methods' => array(
            'add' => true
            , 'addEmailOnly' => true
            , 'getById' => true
            , 'delete' => true
        )
    )
    , 'listHandler' => array(
        'enabled' => true
        , 'methods' => array(
            'addSubscriber' => true
        )
    )
);
