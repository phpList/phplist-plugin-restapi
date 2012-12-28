<?php

/**
 * Common functions to manage lists in PHPlist
 */
class PHPlist_API_Lists{

    /**
     * All lists returned
     */
    static function listsGet() {
        PHPlist_API_Common::select( 'Lists', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list ORDER BY listorder;" );
    }

    /**
     * Get one list
     * @param int $id
     */
    static function listGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        PHPlist_API_Common::select( 'List', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id = $id;", true );
    }

    /**
     * Add a new list
     */
    static function listAdd(){

        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "list (name, description, listorder, prefix, rssfeed, active) VALUES (:name, :description, :listorder, :prefix, :rssfeed, :active);";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("name", $_REQUEST['name']);
            $stmt->bindParam("description", $_REQUEST['description']);
            $stmt->bindParam("listorder", $_REQUEST['listorder']);
            $stmt->bindParam("prefix", $_REQUEST['prefix']);
            $stmt->bindParam("rssfeed", $_REQUEST['rssfeed']);
            $stmt->bindParam("active", $_REQUEST['active']);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            PHPlist_API_Lists::listGet( $id );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listUpdate(){

        $sql = "UPDATE " . $GLOBALS['table_prefix'] . "list SET name=:name, description=:description, listorder=:listorder, prefix=:prefix, rssfeed=:rssfeed, active=:active WHERE id=:id;";

        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->bindParam("name", $_REQUEST['name'] );
            $stmt->bindParam("description", $_REQUEST['description'] );
            $stmt->bindParam("listorder", $_REQUEST['listorder'] );
            $stmt->bindParam("prefix", $_REQUEST['prefix'] );
            $stmt->bindParam("rssfeed", $_REQUEST['rssfeed'] );
            $stmt->bindParam("active", $_REQUEST['active'] );
            $stmt->execute();
            $db = null;
            PHPlist_API_Lists::listGet( $_REQUEST['id'] );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listDelete(){

        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "list WHERE id=:id;";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            PHPlist_API_Response::outputDeleted( 'List', $_REQUEST['id'] );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listsUser( $user_id=0 ) {
        $response = new PHPlist_API_Response();
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id IN (SELECT listid FROM " . $GLOBALS['table_prefix'] . "listuser WHERE userid=" . $user_id . ") ORDER BY listorder;";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('Lists', $result);
            $response->output();
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listUserAdd( $list_id=0, $user_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listuser (userid, listid, entered) VALUES (:user_id, :list_id, now());";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            PHPlist_API_Lists::listGet( $list_id );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listUserDelete( $list_id=0, $user_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listuser WHERE listid=:list_id AND userid=:user_id;";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            PHPlist_API_Response::outputMessage( 'User ' . $user_id . ' is unassigned from list ' . $list_id );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listMessageAdd( $list_id=0, $message_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $message_id==0 ) $message_id = $_REQUEST['message_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listmessage (messageid, listid, entered) VALUES (:message_id, :list_id, now());";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            PHPlist_API_Lists::listGet( $list_id );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }

    static function listMessageDelete( $list_id=0, $message_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $message_id==0 ) $message_id = $_REQUEST['message_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listmessage WHERE listid=:list_id AND messageid=:message_id;";
        try {
            $db = PHPlist_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            PHPlist_API_Response::outputMessage( 'Message ' . $message_id . ' is unassigned from list ' . $list_id );
        } catch(PDOException $e) {
            PHPlist_API_Response::outputError($e);
        }
        die(0);
    }


}



?>