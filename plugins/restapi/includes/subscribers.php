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
     * [limit] {integer} limit the result, default 100 (max 100)<br/>
     * [offset] {integer} offset of the result, default 0.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * List of Subscribers.
     * </p>
     */
    public static function subscribersGet($order_by = 'id', $order = 'asc', $limit = 100, $offset = 0)
    {

        if (isset($_REQUEST['order_by']) && !empty($_REQUEST['order_by'])) {
            $order_by = $_REQUEST['order_by'];
        }
        if (isset($_REQUEST['order']) && !empty($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }
        if (isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])) {
            $limit = intval($_REQUEST['limit']);
        }
        if (isset($_REQUEST['offset']) && !empty($_REQUEST['offset'])) {
            $offset = intval($_REQUEST['offset']);
        }
        if ($limit > 100) {
            $limit = 100;
        }
       
        $params = array (
            'order_by' => array($order_by,PDO::PARAM_STR),
            'order' => array($order,PDO::PARAM_STR),
            'limit' => array($limit,PDO::PARAM_INT),
            'offset' => array($offset,PDO::PARAM_INT),
        );

        Common::select('Subscribers', 'SELECT * FROM '.$GLOBALS['tables']['user']." ORDER BY :order_by :order LIMIT :limit OFFSET :offset;",$params);
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
        Common::select('Subscribers', 'SELECT count(id) as total FROM '.$GLOBALS['tables']['user'],array(),true);
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
            $id = sprintf('%d',$_REQUEST['id']);
        }
        if (!is_numeric($id) || empty($id)) {
            Response::outputErrorMessage('invalid call');
        }
        
        $params = array(
            'id' => array($id,PDO::PARAM_INT),
        );
        Common::select('Subscriber', 'SELECT * FROM '.$GLOBALS['tables']['user']." WHERE id = :id;",$params, true);
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
        $params = array(
            'email' => array($email,PDO::PARAM_STR)
        );
        Common::select('Subscriber', 'SELECT * FROM '.$GLOBALS['tables']['user']." WHERE email = :email;",$params, true);
    }

    /**
     * Get one Subscriber by foreign key.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*foreignkey] {string} the foreign key of the Subscriber.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One Subscriber only.
     * </p>
     */
    public static function subscriberGetByForeignkey($foreignkey = '')
    {
        if (empty($foreignkey)) {
            $foreignkey = $_REQUEST['foreignkey'];
        }
        $params = array(
            'foreignkey' => array($foreignkey,PDO::PARAM_STR)
        );
        Common::select('Subscriber', 'SELECT * FROM '.$GLOBALS['tables']['user']." WHERE foreignkey = :foreignkey;",$params, true);
    }

    /**
     * Add one Subscriber.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the Subscriber.<br/>
     * [*confirmed] {integer} 1=confirmed, 0=unconfirmed.<br/>
     * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
     * [*foreignkey] {string} Foreign key.<br/>
     * [*subscribepage] {integer} subscribe page to sign up to.<br/>
     * [*password] {string} The password for this Subscriber.<br/>
     * [*disabled] {integer} 1=disabled, 0=enabled<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The added Subscriber.
     * </p>
     */
    public static function subscriberAdd()
    {
        $sql = 'INSERT INTO '.$GLOBALS['tables']['user'].'
          (email, confirmed, foreignkey, htmlemail, password, passwordchanged, subscribepage, disabled, entered, uniqid) 
          VALUES (:email, :confirmed, :foreignkey, :htmlemail, :password, now(), :subscribepage, :disabled, now(), :uniqid);';

        $encPwd = Common::encryptPassword($_REQUEST['password']);
        $uniqueID = Common::createUniqId();
        if (!validateEmail($_REQUEST['email'])) {
            Response::outputErrorMessage('invalid email address');
        }
        
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('email', $_REQUEST['email'], PDO::PARAM_STR);
            $stmt->bindParam('confirmed', $_REQUEST['confirmed'], PDO::PARAM_BOOL);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail'], PDO::PARAM_BOOL);
            /* @@todo ensure uniqueness of FK */
            $stmt->bindParam('foreignkey', $_REQUEST['foreignkey'], PDO::PARAM_STR);
            $stmt->bindParam('password', $encPwd, PDO::PARAM_STR);
            $stmt->bindParam('subscribepage', $_REQUEST['subscribepage'], PDO::PARAM_INT);
            $stmt->bindParam('disabled', $_REQUEST['disabled'], PDO::PARAM_BOOL);
            $stmt->bindParam('uniqid', $uniqueID, PDO::PARAM_STR);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            self::SubscriberGet($id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
    }


    /**
     * Add / Updates multiples Subscribers.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*subscribers] {json}  array of object with this properties (  email, confirmed, htmlemail, foreignkey, subscribepage, password, disabled )
     * </p>
     * <p><strong>Returns:</strong><br/>
     * Array of Subscriber Objects
     * </p>
     */
    public static function subscriberBulkAdd()
    {
        $subscribers =  json_decode( stripslashes($_REQUEST['subscribers'] ), true );
        if(!isset($_REQUEST['subscribers'])){
            return Response::outputError(new \Exception("subscribers variable not set"));
        }

        if( !is_array($subscribers ) ){
            return Response::outputError(new \Exception("subscribers variable is invalid"));
        }

        $sqlCount = 'SELECT id FROM '.$GLOBALS['tables']['user'].'
                        WHERE email = :email';

        $sqlInsert = 'INSERT INTO '.$GLOBALS['tables']['user'].'
          (email, confirmed, foreignkey, htmlemail, password, passwordchanged, subscribepage, disabled, entered, uniqid)
          VALUES (:email, :confirmed, :foreignkey, :htmlemail, :password, now(), :subscribepage, :disabled, now(), :uniqid);';

        $sqlUpdate = 'UPDATE '.$GLOBALS['tables']['user'].'
                        SET email = :email,
                            confirmed=:confirmed,
                            foreignkey=:foreignkey,
                            htmlemail=:htmlemail,
                            password= :password,
                            passwordchanged:=now(),
                            subscribepage=:subscribepage,
                            disabled=:disabled
                        WHERE id = :id ';
        $db = PDO::getConnection();
        try {
            $db->beginTransaction();
            $stmtCount  = $db->prepare($sqlCount);
            $stmtInsert = $db->prepare($sqlInsert);
            $stmtUpdate = $db->prepare($sqlUpdate);
            $objs = array(); # <5.3 compatibility
            foreach ($subscribers as $subscriber) {
                if (!validateEmail($subscriber['email'])) {
                    Response::outputErrorMessage('invalid email address');
                }
            }
            foreach ($subscribers as $subscriber) {
                $stmtCount->bindParam('email', $subscriber['email'], PDO::PARAM_STR);

                $stmtCount->execute();

                $result = $stmtCount->fetchAll();
                if ( isset($result[0])) {
                    $id = $result[0]['id'];
                }else{
                    $id = null;
                }


                if ($id){
                    // update
                    $uniqueID = Common::createUniqId();
                    $encPwd = Common::encryptPassword($subscriber['password']);
                    $stmtUpdate->bindParam('email', $subscriber['email'], PDO::PARAM_STR);
                    $stmtUpdate->bindParam('confirmed', $subscriber['confirmed'], PDO::PARAM_BOOL);
                    $stmtUpdate->bindParam('htmlemail', $subscriber['htmlemail'], PDO::PARAM_BOOL);
                    $stmtUpdate->bindParam('foreignkey', $subscriber['foreignkey'], PDO::PARAM_STR);
                    $stmtUpdate->bindParam('password', $encPwd, PDO::PARAM_STR);
                    $stmtUpdate->bindParam('subscribepage', $subscriber['subscribepage'], PDO::PARAM_INT);
                    $stmtUpdate->bindParam('disabled', $subscriber['disabled'], PDO::PARAM_BOOL);
                    $stmtUpdate->bindParam('id', $id, PDO::PARAM_INT);
                    $stmtUpdate->execute();
                    $obj = new \StdClass();
                    $obj->id            = $id;
                    $obj->email         = $subscriber['email'];
                    $obj->confirmed     = $subscriber['confirmed'];
                    $obj->htmlemail     = $subscriber['htmlemail'];
                    $obj->foreignkey    = $subscriber['foreignkey'];
                    $obj->subscribepage = $subscriber['subscribepage'];
                    $obj->disabled      = $subscriber['disabled'];
                    $obj->password      = $encPwd;
                    $objs[]=$obj;
                }else{
                    // insert
                    $encPwd = Common::encryptPassword($subscriber['password']);
                    $stmtInsert->bindParam('email', $subscriber['email'], PDO::PARAM_STR);
                    $stmtInsert->bindParam('confirmed', $subscriber['confirmed'], PDO::PARAM_BOOL);
                    $stmtInsert->bindParam('htmlemail', $subscriber['htmlemail'], PDO::PARAM_BOOL);
                    $stmtInsert->bindParam('foreignkey', $subscriber['foreignkey'], PDO::PARAM_STR);
                    $stmtInsert->bindParam('password', $encPwd, PDO::PARAM_STR);
                    $stmtInsert->bindParam('subscribepage', $subscriber['subscribepage'], PDO::PARAM_INT);
                    $stmtInsert->bindParam('disabled', $subscriber['disabled'], PDO::PARAM_BOOL);
                    $stmtInsert->bindParam('uniqid', $uniqueID, PDO::PARAM_STR);
                    $stmtInsert->execute();
                    $id = $db->lastInsertId();
                    $obj = new \StdClass();
                    $obj->id            = $id;
                    $obj->email         = $subscriber['email'];
                    $obj->confirmed     = $subscriber['confirmed'];
                    $obj->htmlemail     = $subscriber['htmlemail'];
                    $obj->foreignkey    = $subscriber['foreignkey'];
                    $obj->subscribepage = $subscriber['subscribepage'];
                    $obj->disabled      = $subscriber['disabled'];
                    $obj->password      = $encPwd;
                    $objs[]=$obj;
                }
            }
            $db->commit();
            $response = new Response();
            $response->setData("Subscribers", $objs);
            $response->output();

            $db = null;
        } catch (\Exception $e) {
            $db->rollBack();
            Response::outputError($e);
        }
    }
    
    /**
     * Add a Subscriber with lists.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the Subscriber.<br/>
     * [*foreignkey] {string} Foreign key.<br/>
     * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
     * [*subscribepage] {integer} subscribepage to sign up to.<br/>
     * [*lists] {string} comma-separated list IDs.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The added Subscriber.
     * </p>
     */
    public static function subscribe()
    {
        $sql = 'INSERT INTO '.$GLOBALS['tables']['user'].' 
          (email, htmlemail, foreignkey, subscribepage, entered, uniqid) 
          VALUES (:email, :htmlemail, :foreignkey, :subscribepage, now(), :uniqid);';

        $uniqueID = Common::createUniqId();
        $subscribePage = sprintf('%d',$_REQUEST['subscribepage']);
        if (!validateEmail($_REQUEST['email'])) {
            Response::outputErrorMessage('invalid email address');
        }
        
        $listNames = '';
        $lists = explode(',',$_REQUEST['lists']);
        
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('email', $_REQUEST['email'], PDO::PARAM_STR);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail'], PDO::PARAM_BOOL);
            /* @@todo ensure uniqueness of FK */
            $stmt->bindParam('foreignkey', $_REQUEST['foreignkey'], PDO::PARAM_STR);
            $stmt->bindParam('subscribepage', $subscribePage, PDO::PARAM_INT);
            $stmt->bindParam('uniqid', $uniqueID, PDO::PARAM_STR);
            $stmt->execute();
            $subscriberId = $db->lastInsertId();
            foreach ($lists as $listId) {
                $stmt = $db->prepare('replace into '.$GLOBALS['tables']['listuser'].' (userid,listid,entered) values(:userid,:listid,now())');
                $stmt->bindParam('userid', $subscriberId, PDO::PARAM_INT);
                $stmt->bindParam('listid', $listId, PDO::PARAM_INT);
                $stmt->execute();
                $listNames .= "\n  * ".listname($listId);
            }
            $subscribeMessage = getUserConfig("subscribemessage:$subscribePage", $subscriberId);
            $subscribeMessage = str_replace('[LISTS]',$listNames,$subscribeMessage);
            
            $subscribePage = sprintf('%d',$_REQUEST['subscribepage']);
            sendMail($_REQUEST['email'], getConfig("subscribesubject:$subscribePage"), $subscribeMessage );
            addUserHistory($_REQUEST['email'], 'Subscription', 'Subscription via the Rest-API plugin');
            $db = null;
            self::SubscriberGet($subscriberId);
        } catch (\Exception $e) {
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
        $sql = 'UPDATE '.$GLOBALS['tables']['user'].' SET email=:email, confirmed=:confirmed, htmlemail=:htmlemail WHERE id=:id;';
        
        $id = sprintf('%d',$_REQUEST['id']);
        if (empty($id)) {
            Response::outputErrorMessage('invalid call');
        }
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->bindParam('email', $_REQUEST['email'], PDO::PARAM_STR);
            $stmt->bindParam('confirmed', $_REQUEST['confirmed'], PDO::PARAM_BOOL);
            $stmt->bindParam('htmlemail', $_REQUEST['htmlemail'], PDO::PARAM_BOOL);
            $stmt->execute();
            $db = null;
            self::SubscriberGet($id);
        } catch (\Exception $e) {
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
        $sql = 'DELETE FROM '.$GLOBALS['tables']['user'].' WHERE id=:id;';
        try {
            if (!is_numeric($_REQUEST['id'])) {
                Response::outputErrorMessage('invalid call');
            }
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id'], PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            Response::outputDeleted('Subscriber', sprintf('%d',$_REQUEST['id']));
        } catch (\Exception $e) {
            Response::outputError($e);
        }
    }

    /**
     * Get messages (campaigns) sent to a user or an email address (userid param is preferred)
     * <p><strong>Parameters (only one required):</strong><br/>
     * [*userid] {integer} the ID of the Subscriber that received the messages<br/>
     * [*email] {string} email that received the messages
     * </p>
     * <p><strong>Returns:</strong><br/>
     * List of received campaigns.
     * </p>
     */
    public static function subscriberMessages($userid=0, $email=''){
        $email = ($email == '') ? trim($_REQUEST['email']) : trim($email);

        if((int)$userid == 0){
            $userid = (int)$_REQUEST['userid'];
        }


        if($email == '' && $userid == 0){
            Response::outputErrorMessage( 'Invalid params' );
        }

        $sql = "SELECT
                messageid,
                `subject`,
                userid,".
            $GLOBALS['tables']['usermessage'].".entered as entered,".
            $GLOBALS['tables']['usermessage'].".viewed as viewed,".
            $GLOBALS['tables']['usermessage'].".`status` as `status`,
                email
                FROM (".$GLOBALS['tables']['message']."
                INNER JOIN ".$GLOBALS['tables']['usermessage']." ON ".$GLOBALS['tables']['message'].".id=".$GLOBALS['tables']['usermessage'].".messageid)
                INNER JOIN ".$GLOBALS['tables']['user']." ON ".$GLOBALS['tables']['usermessage'].".userid = ".$GLOBALS['tables']['user'].".id
        ";

        if($userid) {
            $sql .= " WHERE userid = :userid";
            $bind_param = array(
                'key' => 'userid',
                'value', $userid,
                'type' => PDO::PARAM_INT
            );

        } else { // At this points there is a user or email
            $sql .= " WHERE email = :email";
            $bind_param = array(
                'key' => 'email',
                'value' => $email,
                'type' => PDO::PARAM_STR
            );
        }
        $sql .= " ORDER BY entered DESC";
        try {
            $response = new Response();
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam($bind_param['key'], $bind_param['value'], $bind_param['type']);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $response->setData('messages', $result);
            $db = null;
            $response->output();
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
        die(0);
    }
}
