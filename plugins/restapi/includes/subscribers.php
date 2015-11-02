<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

class Subscribers
{
    /**
     * Get all the Subscribers in the system.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [order_by] {string} name of column to sort, default "id".<br/>
     * [order] {string} sort order asc or desc, default: asc.<br/>
     * [limit] {integer} limit the result, default 100.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * List of Subscribers.
     * </p>
     */
    public static function subscribersGet($order_by = 'id', $order = 'asc', $limit = 100)
    {

        //getting optional values
        if (isset($_REQUEST['order_by']) && !empty($_REQUEST['order_by'])) {
            $order_by = $_REQUEST['order_by'];
        }
        if (isset($_REQUEST['order']) && !empty($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }
        if (isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])) {
            $limit = $_REQUEST['limit'];
        }

        Common::select('Users', 'SELECT * FROM '.$GLOBALS['usertable_prefix']."user ORDER BY $order_by $order LIMIT $limit;");
    }

    /**
     * Get the total of Subscribers in the system.
     * 
     * <p><strong>Parameters:</strong><br/>
     * none
     * </p>
     * <p><strong>Returns:</strong><br/>
     * Number of subscribers.
     * </p>
     */
    public static function subscribersCount()
    {
        Common::select('Users', 'SELECT count(id) as total FROM '.$GLOBALS['usertable_prefix']."user",true);
    }

    /**
     * Get one Subscriber by ID.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One Subscriber only.
     * </p>
     */
    public static function subscriberGet($id = 0)
    {
        if ($id == 0) {
            $id = $_REQUEST['id'];
        }
        Common::select('User', 'SELECT * FROM '.$GLOBALS['usertable_prefix']."user WHERE id = $id;", true);
    }

    /**
     * Get one Subscriber by email address.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One Subscriber only.
     * </p>
     */
    public static function subscriberGetByEmail($email = '')
    {
        if (empty($email)) {
            $email = $_REQUEST['email'];
        }
        Common::select('User', 'SELECT * FROM '.$GLOBALS['usertable_prefix']."user WHERE email = '$email';", true);
    }

    /**
     * Add one Subscriber.
     * 
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
    public static function subscriberAdd()
    {
        $sql = 'INSERT INTO '.$GLOBALS['usertable_prefix'].'user (email, confirmed, htmlemail, rssfrequency, password, passwordchanged, disabled, entered, uniqid) VALUES (:email, :confirmed, :htmlemail, :rssfrequency, :password, now(), :disabled, now(), :uniqid);';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('email', $_REQUEST['email']);
            $stmt->bindParam('confirmed', $_REQUEST['confirmed']);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail']);
            $stmt->bindParam('rssfrequency', $_REQUEST['rssfrequency']);
            $stmt->bindParam('password', $_REQUEST['password']);
            $stmt->bindParam('disabled', $_REQUEST['disabled']);

            // fails on strict
#            $stmt->bindParam("uniqid", md5(uniqid(mt_rand())));

            $uniq = md5(uniqid(mt_rand()));
            $stmt->bindParam('uniqid', $uniq);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            self::SubscriberGet($id);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    /**
     * Update one Subscriber.
     * 
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
    public static function subscriberUpdate()
    {
        $sql = 'UPDATE '.$GLOBALS['usertable_prefix'].'user SET email=:email, confirmed=:confirmed, htmlemail=:htmlemail, rssfrequency=:rssfrequency, password=:password, passwordchanged=now(), disabled=:disabled WHERE id=:id;';

        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->bindParam('email', $_REQUEST['email']);
            $stmt->bindParam('confirmed', $_REQUEST['confirmed']);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail']);
            $stmt->bindParam('rssfrequency', $_REQUEST['rssfrequency']);
            $stmt->bindParam('password', $_REQUEST['password']);
            $stmt->bindParam('disabled', $_REQUEST['disabled']);
            $stmt->execute();
            $db = null;
            self::SubscriberGet($_REQUEST['id']);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    /**
     * Delete a Subscriber.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The deleted Subscriber ID.
     * </p>
     */
    public static function subscriberDelete()
    {
        $sql = 'DELETE FROM '.$GLOBALS['usertable_prefix'].'user WHERE id=:id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            Response::outputDeleted('Subscriber', $_REQUEST['id']);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }
}
