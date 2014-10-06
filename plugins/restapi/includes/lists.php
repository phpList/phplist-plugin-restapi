<?php
namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class phpList_RESTAPI_Lists
 * Getting lists, adding and removing its users and messages
 */
class Lists {

    /**
     * <p>Gets all lists in phpList as an array.</p>
     * <p><strong>Parameters:</strong><br/>
     * (none)
     * <p><strong>Returns:</strong><br/>
     * Array of lists.
     * </p>
     */
    static function listsGet() {
        Common::select( 'Lists', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list ORDER BY listorder;" );
    }

    /**
     * <p>Gets one (1) list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.
     * <p><strong>Returns:</strong><br/>
     * One list.
     * </p>
     */
    static function listGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        Common::select( 'List', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id = $id;", true );
    }

    /**
     * <p>Adds a new list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*name] {string} the name of the list.<br/>
     * [description] {string} adds a description to the list.<br/>
     * [listorder] {integer} an expression to sortorder, eg 100.<br/>
     * [prefix] {string} adds a prefix to the list (?).<br/>
     * [rssfeed] {string} the url to the feed for this list (?).<br/>
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.<br/>
     * <p><strong>Returns:</strong><br/>
     * The list added.
     * </p>
     */
    static function listAdd(){

        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "list (name, description, listorder, prefix, rssfeed, active) VALUES (:name, :description, :listorder, :prefix, :rssfeed, :active);";
        try {
            $db = PDO::getConnection();
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
            Lists::listGet( $id );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Updates existing list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.<br/>
     * [*name] {string} the name of the list.<br/>
     * [description] {string} adds a description to the list.<br/>
     * [listorder] {integer} an expression to sortorder, eg 100.<br/>
     * [prefix] {string} adds a prefix to the list (?).<br/>
     * [rssfeed] {string} the url to the feed for this list (?).<br/>
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.<br/>
     * <p><strong>Returns:</strong><br/>
     * The list updated.
     * </p>
     */
    static function listUpdate(){

        $sql = "UPDATE " . $GLOBALS['table_prefix'] . "list SET name=:name, description=:description, listorder=:listorder, prefix=:prefix, rssfeed=:rssfeed, active=:active WHERE id=:id;";

        try {
            $db = PDO::getConnection();
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
            Lists::listGet( $_REQUEST['id'] );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Deletes a list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    static function listDelete(){

        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "list WHERE id=:id;";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            Response::outputDeleted( 'List', $_REQUEST['id'] );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Lists assigned to Subscriber.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*user_id] {integer} the Subscriber-ID.
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the subscriber is assigned to.
     * </p>
     */
    static function listsSubscriber ( $subscriber_id=0 ) {
        $response = new Response();
        if ( $subscriber_id==0 ) $subscriber_id = $_REQUEST['subscriber_id'];
        $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id IN (SELECT listid FROM " . $GLOBALS['table_prefix'] . "listuser WHERE userid=" . $subscriber_id . ") ORDER BY listorder;";
        try {
            $db = PDO::getConnection();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('Lists', $result);
            $response->output();
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Adds a subscriber to a list.</p>
     * <p>The subscriber then subscribes to the list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*subscriber_id] {integer} the ID of the subscriber.<br/>
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the subscriber is assigned to.
     * </p>
     */
    static function listSubscriberAdd( $list_id=0, $subscriber_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $subscriber_id==0 ) $subscriber_id = $_REQUEST['subscriber_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listuser (userid, listid, entered) VALUES (:subscriber_id, :list_id, now());";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("subscriber_id", $subscriber_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            Lists::listsSubscriber( $subscriber_id );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Unassigns a subscriber from a list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*subscriber_id] {integer} the ID of the subscriber.
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    static function listSubscriberDelete( $list_id=0, $subscriber_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $subscriber_id==0 ) $subscriber_id = $_REQUEST['subscriber_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listuser WHERE listid=:list_id AND userid=:subscriber_id;";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("subscriber_id", $subscriber_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            Response::outputMessage( 'Subscriber ' . $subscriber_id . ' is unassigned from list ' . $list_id );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Assigns a list to a message.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*message_id] {integer} the ID of the message.
     * <p><strong>Returns:</strong><br/>
     * The list assigned.
     * </p>
     */
    static function listMessageAdd( $list_id=0, $message_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $message_id==0 ) $message_id = $_REQUEST['message_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listmessage (messageid, listid, entered) VALUES (:message_id, :list_id, now());";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            Lists::listGet( $list_id );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Unassigns a list from a message.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*message_id] {integer} the ID of the message.
     * </p>
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    static function listMessageDelete( $list_id=0, $message_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $message_id==0 ) $message_id = $_REQUEST['message_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listmessage WHERE listid=:list_id AND messageid=:message_id;";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            Response::outputMessage( 'Message ' . $message_id . ' is unassigned from list ' . $list_id );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }


}
