<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class PDO.
 */
class PDO extends \PDO
{
    public static function getConnection()
    {
        $dbhost = $GLOBALS['database_host'];
        $dbuser = $GLOBALS['database_user'];
        $dbpass = $GLOBALS['database_password'];
        $dbname = $GLOBALS['database_name'];
        $dbport = $GLOBALS['database_port'];
        $dbsock = $GLOBALS['database_socket'];

        // There is no error checking here
        if (isset($dbsock) ) {
            $dbh = new \PDO("mysql:dbname=$dbname;unix_socket=$dbsock;charset=UTF8;", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        } else {
            $dbh = new \PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbport;charset=UTF8;", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        }
        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $dbh;
    }
}
