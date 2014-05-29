<?php

/**
 * Class phpList_RESTAPI_Messages
 * Manage phplist Messages
 */
class phpList_RESTAPI_Messages{

    static function messageGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        phpList_RESTAPI_Common::select( 'Message', "SELECT * FROM " . $GLOBALS['table_prefix'] . "message WHERE id=" . $id . ";", true );
    }

    static function messagesGet() {
        phpList_RESTAPI_Common::select( 'Messages', "SELECT * FROM " . $GLOBALS['table_prefix'] . "message ORDER BY Modified DESC;" );
    }

    /**
     * <p>Adds a new message/campaing.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*subject] {string} <br/>
     * [*fromfield] {string} <br/>
     * [*replyto] {string} <br/>
     * [*message] {string} <br/>
     * [*textmessage] {string} <br/>
     * [*footer] {string} <br/>
     * [*status] {string} <br/>
     * [*sendformat] {string} <br/>
     * [*template] {string} <br/>
     * [*embargo] {string} <br/>
     * [*rsstemplate] {string} <br/>
     * [*owner] {string} <br/>
     * <p><strong>Returns:</strong><br/>
     * The message added.
     * </p>
     */
    static function messageAdd(){

        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "message (subject, fromfield, replyto, message, textmessage, footer, entered, status, sendformat, template, embargo, rsstemplate, owner ) VALUES ( :subject, :fromfield, :replyto, :message, :textmessage, :footer, now(), :status, :sendformat, :template, :embargo, :rsstemplate, :owner );";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("subject", $_REQUEST['subject'] );
            $stmt->bindParam("fromfield", $_REQUEST['fromfield'] );
            $stmt->bindParam("replyto", $_REQUEST['replyto'] );
            $stmt->bindParam("message", $_REQUEST['message'] );
            $stmt->bindParam("textmessage", $_REQUEST['textmessage'] );
            $stmt->bindParam("footer", $_REQUEST['footer'] );
            $stmt->bindParam("status", $_REQUEST['status'] );
            $stmt->bindParam("sendformat", $_REQUEST['sendformat'] );
            $stmt->bindParam("template", $_REQUEST['template'] );
            $stmt->bindParam("embargo", $_REQUEST['embargo'] );
            $stmt->bindParam("rsstemplate", $_REQUEST['rsstemplate'] );
            $stmt->bindParam("owner", $_REQUEST['owner'] );
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            phpList_RESTAPI_Messages::messageGet( $id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }

    }

    /**
     * <p>Updates existing message/campaing.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} <br/>
     * [*subject] {string} <br/>
     * [*fromfield] {string} <br/>
     * [*replyto] {string} <br/>
     * [*message] {string} <br/>
     * [*textmessage] {string} <br/>
     * [*footer] {string} <br/>
     * [*status] {string} <br/>
     * [*sendformat] {string} <br/>
     * [*template] {string} <br/>
     * [*embargo] {string} <br/>
     * [*rsstemplate] {string} <br/>
     * [owner] {string} <br/>
     * <p><strong>Returns:</strong><br/>
     * The message added.
     * </p>
     */
    static function messageUpdate( $id = 0 ){

        if ( $id == 0 ) $id = $_REQUEST['id'];
        $sql = "UPDATE " . $GLOBALS['table_prefix'] . "message SET subject=:subject, fromfield=:fromfield, replyto=:replyto, message=:message, textmessage=:textmessage, footer=:footer, status=:status, sendformat=:sendformat, template=:template, sendstart=:sendstart, rsstemplate=:rsstemplate, owner=:owner WHERE id=:id;";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id );
            $stmt->bindParam("subject", $_REQUEST['subject'] );
            $stmt->bindParam("fromfield", $_REQUEST['fromfield'] );
            $stmt->bindParam("replyto", $_REQUEST['replyto'] );
            $stmt->bindParam("message", $_REQUEST['message'] );
            $stmt->bindParam("textmessage", $_REQUEST['textmessage'] );
            $stmt->bindParam("footer", $_REQUEST['footer'] );
            $stmt->bindParam("status", $_REQUEST['status'] );
            $stmt->bindParam("sendformat", $_REQUEST['sendformat'] );
            $stmt->bindParam("template", $_REQUEST['template'] );
            $stmt->bindParam("sendstart", $_REQUEST['sendstart'] );
            $stmt->bindParam("rsstemplate", $_REQUEST['rsstemplate'] );
            $stmt->bindParam("owner", $_REQUEST['owner'] );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Messages::messageGet( $id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }

    }

	static function imageAdd(){
		if($_POST['image'] and $_POST['name']){
			$imageDirectory = "/var/www/phplist/content/c/";
			$dest = tempnam($imageDirectory,date("ymd_his_"));
			$pi = pathinfo($_POST['name']);
			$dest.= '.'.$pi['extension'];
			file_put_contents($dest,base64_decode($_POST['image']));
			$response = new phpList_RESTAPI_Response();
			$response->setData('Filename', basename($dest));
			$response->output();
		}
	}
	
	static function formtokenGet(){
		$key = md5(time().mt_rand(0,10000));
		Sql_Query(sprintf('insert into %s (adminid,value,entered,expires) values(%d,"%s",%d,date_add(now(),interval 1 hour))',
		$GLOBALS['tables']['admintoken'],$_SESSION['logindetails']['id'],$key,time()),1);
		$response = new phpList_RESTAPI_Response();
		$response->setData('formtoken', $key);
		$response->output();
	}
}



?>
