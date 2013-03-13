<?php

/**
 * Class phpList_RESTAPI_Lists
 * Getting lists, adding and removing its users and messages
 */
class phpList_RESTAPI_Lists{

    /**
     * <p>Gets all lists in phpList as an array.</p>
     * <p><strong>Parameters:</strong><br/>
     * (none)
     * <p><strong>Returns:</strong><br/>
     * Array of lists.
     * </p>
     */
    static function listsGet() {
        phpList_RESTAPI_Common::select( 'Lists', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list ORDER BY listorder;" );
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
        phpList_RESTAPI_Common::select( 'List', "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id = $id;", true );
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
            $db = phpList_RESTAPI_PDO::getConnection();
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
            phpList_RESTAPI_Lists::listGet( $id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
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
            $db = phpList_RESTAPI_PDO::getConnection();
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
            phpList_RESTAPI_Lists::listGet( $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
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
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Response::outputDeleted( 'List', $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Lists assigned to User.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*user_id] {integer} the User-ID.
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the user is assigned to.
     * </p>
     */
    static function listsUser( $user_id=0 ) {
        $response = new phpList_RESTAPI_Response();
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id IN (SELECT listid FROM " . $GLOBALS['table_prefix'] . "listuser WHERE userid=" . $user_id . ") ORDER BY listorder;";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('Lists', $result);
            $response->output();
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>Adds a user to a list.</p>
     * <p>The user then subscribes to the list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*user_id] {integer} the ID of the user.<br/>
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the user is assigned to.
     * </p>
     */
    static function listUserAdd( $list_id=0, $user_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listuser (userid, listid, entered) VALUES (:user_id, :list_id, now());";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Lists::listsUser( $user_id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }
        die(0);
    }

    /**
     * <p>UnassignsAdds a user to a list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*user_id] {integer} the ID of the user.
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    static function listUserDelete( $list_id=0, $user_id=0 ){
        if ( $list_id==0 ) $list_id = $_REQUEST['list_id'];
        if ( $user_id==0 ) $user_id = $_REQUEST['user_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listuser WHERE listid=:list_id AND userid=:user_id;";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Response::outputMessage( 'User ' . $user_id . ' is unassigned from list ' . $list_id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
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
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Lists::listGet( $list_id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
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
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Response::outputMessage( 'Message ' . $message_id . ' is unassigned from list ' . $list_id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }
        die(0);
    }


}



?>