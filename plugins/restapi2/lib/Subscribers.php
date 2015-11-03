<?php

namespace Rapi;

class Subscribers {

    protected $common;
    protected $pdoEx;
    protected $response;

    public function __construct( Common $common, PdoEx $pdoEx, Response $response )
    {
        $this->common = $common;
        $this->response = $response;
        $this->pdoEx = $pdoEx;
    }

    /**
     * <p>Get all the Subscribers in the system.</p>
		 * <p><strong>Parameters:</strong><br/>
		 * [order_by] {string} name of column to sort, default 'id'.<br/>
		 * [order] {string} sort order asc or desc, default: asc.<br/>
		 * [limit] {integer} limit the result, default 100.<br/>
		 * </p>
     * <p><strong>Returns:</strong><br/>
     * List of Subscribers.
     * </p>
     */
    static function subscribersGet( $order_by='id', $order='asc', $limit=100 ) {

        //getting optional values
        if ( isset( $_REQUEST['order_by'] ) && !empty( $_REQUEST['order_by'] ) ) $order_by = $_REQUEST['order_by'];
        if ( isset( $_REQUEST['order'] ) && !empty( $_REQUEST['order'] ) ) $order = $_REQUEST['order'];
        if ( isset( $_REQUEST['limit'] ) && !empty( $_REQUEST['limit'] ) ) $limit = $_REQUEST['limit'];

        $this->common->select( 'Users', 'SELECT * FROM ' . $GLOBALS['usertable_prefix'] . 'user ORDER BY $order_by $order LIMIT $limit;' );
    }

    /**
     * <p>Gets one given Subscriber.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One Subscriber only.
     * </p>
     */
    static function subscriberGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        $this->common->select( 'User', 'SELECT * FROM ' . $GLOBALS['usertable_prefix'] . 'user WHERE id = $id;', true );
    }

    /**
     * <p>Gets one Subscriber via email address.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One Subscriber only.
     * </p>
     */
    static function subscriberGetByEmail( $email = '') {
        if ( empty( $email ) ) {
            $email = $_REQUEST['email'];
        }

        $this->common->select( 'User', 'SELECT * FROM ' . $GLOBALS['usertable_prefix'] . 'user WHERE email = "$email";', true );
    }

    /**
     * <p>Adds one Subscriber to the system.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the Subscriber.<br/>
     * [*confirmed] {integer} 1=confirmed, 0=unconfirmed.<br/>
     * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
     * [*rssfrequency] {integer}<br/>
     * [*password] {string} The password to this Subscriber.<br/>
     * [*disabled] {integer} 1=disabled, 0=enabled<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The added Subscriber.
     * </p>
     */
    static function subscriberAdd(){

        $sql = 'INSERT INTO ' . $GLOBALS['usertable_prefix'] . 'user (email, confirmed, htmlemail, rssfrequency, password, passwordchanged, disabled, entered, uniqid) VALUES (:email, :confirmed, :htmlemail, :rssfrequency, :password, now(), :disabled, now(), :uniqid);';
        try {
            $stmt = $pdoEx->prepare($sql);
            $stmt->bindParam('email', $_REQUEST['email']);
            $stmt->bindParam('confirmed', $_REQUEST['confirmed']);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail']);
            $stmt->bindParam('rssfrequency', $_REQUEST['rssfrequency']);
            $stmt->bindParam('password', $_REQUEST['password']);
            $stmt->bindParam('disabled', $_REQUEST['disabled']);

            // fails on strict
#            $stmt->bindParam('uniqid', md5(uniqid(mt_rand())));

            $uniq = md5(uniqid(mt_rand()));
            $stmt->bindParam('uniqid', $uniq);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            $this->SubscriberGet( $id );
        } catch(\PDOException $e) {
            $this->response->outputError($e);
        }

    }

    /**
        * <p>Updates one Subscriber to the system.</p>
        * <p><strong>Parameters:</strong><br/>
        * [*id] {integer} the ID of the Subscriber.<br/>
        * [*email] {string} the email address of the Subscriber.<br/>
        * [*confirmed] {integer} 1=confirmed, 0=unconfirmed.<br/>
        * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
        * [*rssfrequency] {integer}<br/>
        * [*password] {string} The password to this Subscriber.<br/>
        * [*disabled] {integer} 1=disabled, 0=enabled<br/>
        * </p>
        * <p><strong>Returns:</strong><br/>
        * The updated Subscriber.
        * </p>
        */
    static function subscriberUpdate(){

        $sql = 'UPDATE ' . $GLOBALS['usertable_prefix'] . 'user SET email=:email, confirmed=:confirmed, htmlemail=:htmlemail, rssfrequency=:rssfrequency, password=:password, passwordchanged=now(), disabled=:disabled WHERE id=:id;';

        try {
            $db = $this->pdoEx->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->bindParam('email', $_REQUEST['email'] );
            $stmt->bindParam('confirmed', $_REQUEST['confirmed'] );
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail'] );
            $stmt->bindParam('rssfrequency', $_REQUEST['rssfrequency'] );
            $stmt->bindParam('password', $_REQUEST['password'] );
            $stmt->bindParam('disabled', $_REQUEST['disabled'] );
            $stmt->execute();
            $db = null;
            $this->SubscriberGet( $_REQUEST['id'] );
        } catch(\PDOException $e) {
            $this->response->outputError($e);
        }

    }

    /**
        * <p>Deletes a Subscriber.</p>
        * <p><strong>Parameters:</strong><br/>
        * [*id] {integer} the ID of the Subscriber.<br/>
        * </p>
        * <p><strong>Returns:</strong><br/>
        * The deleted Subscriber ID.
        * </p>
        */
    static function subscriberDelete(){

        $sql = 'DELETE FROM ' . $GLOBALS['usertable_prefix'] . 'user WHERE id=:id;';
        try {
            $db = $this->pdoEx->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            $this->response->outputDeleted( 'Subscriber', $_REQUEST['id'] );
        } catch(\PDOException $e) {
            $this->response->outputError($e);
        }

    }

}
