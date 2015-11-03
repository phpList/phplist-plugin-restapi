<?php

// Set necessary phpList4 settings from host app globals and save to session
// NOTE: phpList 4 by default loads its configuration from a config file.
// When shipped as a library, manual config is not possible, and a default
// config file is used. To override default config we must set options here
// using a phpList4 config object so that they're stored in session and override
// the default config options which will be loaded from file in client code.
$pl4Config->setRunningConfig( 'DATABASE_USER', $GLOBALS['database_user'] );
$pl4Config->setRunningConfig( 'DATABASE_PASSWORD', $GLOBALS['database_password'] );
$pl4Config->setRunningConfig( 'DATABASE_DSN', 'mysql:host=' . $GLOBALS['database_host'] . ';port=3306;dbname=' . $GLOBALS['database_name'] );
$pl4Config->setRunningConfig( 'TABLE_PREFIX', $GLOBALS['table_prefix'] );
$pl4Config->setRunningConfig( 'USERTABLE_PREFIX', $GLOBALS['usertable_prefix'] );
// FIXME: Set this hashing algo from phpList3's config instead
$pl4Config->setRunningConfig( 'ENCRYPTION_ALGO', 'md5' );
