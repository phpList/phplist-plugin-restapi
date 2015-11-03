<?php

namespace Rapi;

/**
 * Class for handling lists
 */
class Lists {

    protected $common;
    protected $response;
    protected $request; // HTTP request variables

    public function __construct( Common $common, PdoEx $pdoEx, Response $response )
    {
        // FIXME: Common{} probably isn't required any more
        $this->common = $common;
        $this->pdoEx = $pdoEx;
        $this->response = $response;
        // TODO: Check how this is used and add method to set it's values
        $this->request = array();
    }

    /**
     * Gets all lists in phpList as an array.
     * Parameters:
     * (none)
     * Returns:
     * Array of lists.
     *
     */
    public function multiListGet()
    {
        return $this->pdoEx->select(
            $this->response
            , "SELECT * FROM " . $GLOBALS['table_prefix'] . "list ORDER BY listorder;"
            , 'Lists'
        );
    }

    /**
     * Gets one (1) list.
     * Parameters:
     * [*id] {integer} the ID of the list.
     * Returns:
     * One list.
     *
     */
    public function listGet( array $params )
    {
        // Map array members to variables
        // Expected params: [id]
        extract( $params );

        // FIXME: What purpose does this conditional serve? Under what
        // circumstances would _RESPONSE['id'] be set? Leaving for now
        if ( $id==0 ) {
            $id = $this->request['id'];
        }

        // Fetch list
        $response = $this->pdoEx->doQueryResponse(
            $this->response
            , "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id = $id;"
            , 'List'
            , true
        );

        return $response;
    }

    /**
     * Adds a new list.
     * Parameters:
     * [*name] {string} the name of the list.
     * [description] {string} adds a description to the list.
     * [listorder] {integer} an expression to sortorder, eg 100.
     * [prefix] {string} adds a prefix to the list (?).
     * [rssfeed] {string} the url to the feed for this list (?).
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.
     * Returns:
     * The list added.
     *
     */
    public function listAdd()
    {

        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "list (name, description, listorder, prefix, rssfeed, active) VALUES (:name, :description, :listorder, :prefix, :rssfeed, :active);";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "name", $this->request['name'] );
            $stmt->bindParam( "description", $this->request['description'] );
            $stmt->bindParam( "listorder", $this->request['listorder'] );
            $stmt->bindParam( "prefix", $this->request['prefix'] );
            $stmt->bindParam( "rssfeed", $this->request['rssfeed'] );
            $stmt->bindParam( "active", $this->request['active'] );
            $stmt->execute();
            $id = $this->pdoEx->lastInsertId();
            return $this->listGet( $id );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
            return $this->response;
        }
        die( 0 );
    }

    /**
     * Updates existing list.
     * Parameters:
     * [*id] {integer} the ID of the list.
     * [*name] {string} the name of the list.
     * [description] {string} adds a description to the list.
     * [listorder] {integer} an expression to sortorder, eg 100.
     * [prefix] {string} adds a prefix to the list (?).
     * [rssfeed] {string} the url to the feed for this list (?).
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.
     * Returns:
     * The list updated.
     *
     */
    public function listUpdate()
    {

        $sql = "UPDATE " . $GLOBALS['table_prefix'] . "list SET name=:name, description=:description, listorder=:listorder, prefix=:prefix, rssfeed=:rssfeed, active=:active WHERE id=:id;";

        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "id", $this->request['id']);
            $stmt->bindParam( "name", $this->request['name'] );
            $stmt->bindParam( "description", $this->request['description'] );
            $stmt->bindParam( "listorder", $this->request['listorder'] );
            $stmt->bindParam( "prefix", $this->request['prefix'] );
            $stmt->bindParam( "rssfeed", $this->request['rssfeed'] );
            $stmt->bindParam( "active", $this->request['active'] );
            $stmt->execute();
            return $this->listGet( $this->request['id'] );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
            return $this->response;
        }
        die(0);
    }

    /**
     * Deletes a list.
     * Parameters:
     * [*id] {integer} the ID of the list.
     * Returns:
     * System message of action.
     *
     */
    public function listDelete()
    {

        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "list WHERE id=:id;";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "id", $this->request['id'] );
            $stmt->execute();
            $this->response->outputDeleted( 'List', $this->request['id'] );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        return $this->response;
        die(0);
    }

    /**
     * Lists assigned to Subscriber.
     * Parameters:
     * [*user_id] {integer} the Subscriber-ID.
     * Returns:
     * Array of lists where the subscriber is assigned to.
     *
     */
    public function listsSubscriber ( $subscriber_id=0 )
    {
        if ( $subscriber_id==0 ) $subscriber_id = $this->request['subscriber_id'];
        $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "list WHERE id IN (SELECT listid FROM " . $GLOBALS['table_prefix'] . "listuser WHERE userid=" . $subscriber_id . ") ORDER BY listorder;";
        try {
            $stmt = $this->pdoEx->query( $sql );
            $result = $stmt->fetchAll( PDO::FETCH_OBJ );
            $this->response->setData( 'Lists', $result );
            $this->response->output();
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        return $this->response;
        die(0);
    }

    /**
     * Adds a subscriber to a list.
     * The subscriber then subscribes to the list.
     * Parameters:
     * [*list_id] {integer} the ID of the list.
     * [*subscriber_id] {integer} the ID of the subscriber.
     * Returns:
     * Array of lists where the subscriber is assigned to.
     *
     */
    public function listSubscriberAdd( $list_id=0, $subscriber_id=0 )
    {
        if ( $list_id==0 ) $list_id = $this->request['list_id'];
        if ( $subscriber_id==0 ) $subscriber_id = $this->request['subscriber_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listuser (userid, listid, entered) VALUES (:subscriber_id, :list_id, now());";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "subscriber_id", $subscriber_id );
            $stmt->bindParam( "list_id", $list_id );
            $stmt->execute();
            $this->listsSubscriber( $subscriber_id );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        return $this->response;
        die(0);
    }

    /**
     * Unassigns a subscriber from a list.
     * Parameters:
     * [*list_id] {integer} the ID of the list.
     * [*subscriber_id] {integer} the ID of the subscriber.
     * Returns:
     * System message of action.
     *
     */
    public function listSubscriberDelete( $list_id=0, $subscriber_id=0 )
    {
        if ( $list_id==0 ) $list_id = $this->request['list_id'];
        if ( $subscriber_id==0 ) $subscriber_id = $this->request['subscriber_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listuser WHERE listid=:list_id AND userid=:subscriber_id;";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "subscriber_id", $subscriber_id );
            $stmt->bindParam( "list_id", $list_id );
            $stmt->execute();
            $this->response->outputMessage( 'Subscriber ' . $subscriber_id . ' is unassigned from list ' . $list_id );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        return $this->response;
        die(0);
    }

    /**
     * Assigns a list to a message.
     * Parameters:
     * [*list_id] {integer} the ID of the list.
     * [*message_id] {integer} the ID of the message.
     * Returns:
     * The list assigned.
     *
     */
    public function listMessageAdd( $list_id=0, $message_id=0 )
    {
        if ( $list_id==0 ) $list_id = $this->request['list_id'];
        if ( $message_id==0 ) $message_id = $this->request['message_id'];
        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "listmessage (messageid, listid, entered) VALUES (:message_id, :list_id, now());";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam("message_id", $message_id );
            $stmt->bindParam("list_id", $list_id );
            $stmt->execute();
            $this->listGet( $list_id );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        // FIXME: Check this works; quick workaround
        return $this->response;
        die( 0 );
    }

    /**
     * Unassigns a list from a message.
     * Parameters:
     * [*list_id] {integer} the ID of the list.
     * [*message_id] {integer} the ID of the message.
     *
     * Returns:
     * System message of action.
     *
     */
    public function listMessageDelete( $list_id=0, $message_id=0 )
    {
        if ( $list_id==0 ) $list_id = $this->request['list_id'];
        if ( $message_id==0 ) $message_id = $this->request['message_id'];
        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "listmessage WHERE listid=:list_id AND messageid=:message_id;";
        try {
            $stmt = $this->pdoEx->prepare( $sql );
            $stmt->bindParam( "message_id", $message_id );
            $stmt->bindParam( "list_id", $list_id );
            $stmt->execute();
            $this->response->outputMessage( 'Message ' . $message_id . ' is unassigned from list ' . $list_id );
        } catch( \PDOException $e ) {
            $this->response->outputError( $e );
        }
        return $this->response;
        die(0);
    }


}
