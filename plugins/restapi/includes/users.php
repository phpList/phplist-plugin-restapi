<?php

class phpList_RESTAPI_Users{

    /**
     * <p>Get all the users in the system.</p>
		 * <p><strong>Parameters:</strong><br/>
		 * [order_by] {string} name of column to sort, default "id".<br/>
		 * [order] {string} sort order asc or desc, default: asc.<br/>
		 * [limit] {integer} limit the result, default 100.<br/>
		 * </p>
     * <p><strong>Returns:</strong><br/>
     * List of users.
     * </p>
     */
    static function usersGet( $order_by='id', $order='asc', $limit=100 ) {

				//getting optional values
				if ( isset( $_REQUEST['order_by'] ) && !empty( $_REQUEST['order_by'] ) ) $order_by = $_REQUEST['order_by'];
				if ( isset( $_REQUEST['order'] ) && !empty( $_REQUEST['order'] ) ) $order = $_REQUEST['order'];
				if ( isset( $_REQUEST['limit'] ) && !empty( $_REQUEST['limit'] ) ) $limit = $_REQUEST['limit'];

        phpList_RESTAPI_Common::select( 'Users', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user ORDER BY $order_by $order LIMIT $limit;" );
    }

    /**
     * <p>Gets one given user.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the user.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One user only.
     * </p>
     */
    static function userGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        phpList_RESTAPI_Common::select( 'User', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user WHERE id = $id;", true );
    }

    /**
     * <p>Gets one user via email address.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the user.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * One user only.
     * </p>
     */
    static function userGetByEmail( $email ) {
        if ( empty( $email ) ) $email = $_REQUEST['email'];
        phpList_RESTAPI_Common::select( 'User', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user WHERE email = '$email';", true );
    }

    /**
     * <p>Adds one user to the system.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*email] {string} the email address of the user.<br/>
     * [*confirmed] {integer} 1=confirmed, 0=unconfirmed.<br/>
     * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
     * [*rssfrequency] {integer}<br/>
     * [*password] {string} The password to this user.<br/>
     * [*disabled] {integer} 1=disabled, 0=enabled<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The added user.
     * </p>
     */
    static function userAdd(){

        $sql = "INSERT INTO " . $GLOBALS['usertable_prefix'] . "user (email, confirmed, htmlemail, rssfrequency, password, passwordchanged, disabled, entered, uniqid) VALUES (:email, :confirmed, :htmlemail, :rssfrequency, :password, now(), :disabled, now(), :uniqid);";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("email", $_REQUEST['email']);
            $stmt->bindParam("confirmed", $_REQUEST['confirmed']);
            $stmt->bindParam("htmlemail", $_REQUEST['htmlemail']);
            $stmt->bindParam("rssfrequency", $_REQUEST['rssfrequency']);
            $stmt->bindParam("password", $_REQUEST['password']);
            $stmt->bindParam("disabled", $_REQUEST['disabled']);
            $stmt->bindParam("uniqid", md5(uniqid(mt_rand())));
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            phpList_RESTAPI_Users::userGet( $id );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }

    }

		/**
		 * <p>Updates one user to the system.</p>
		 * <p><strong>Parameters:</strong><br/>
		 * [*id] {integer} the ID of the user.<br/>
		 * [*email] {string} the email address of the user.<br/>
		 * [*confirmed] {integer} 1=confirmed, 0=unconfirmed.<br/>
		 * [*htmlemail] {integer} 1=html emails, 0=no html emails.<br/>
		 * [*rssfrequency] {integer}<br/>
		 * [*password] {string} The password to this user.<br/>
		 * [*disabled] {integer} 1=disabled, 0=enabled<br/>
		 * </p>
		 * <p><strong>Returns:</strong><br/>
		 * The updated user.
		 * </p>
		 */
    static function userUpdate(){

        $sql = "UPDATE " . $GLOBALS['usertable_prefix'] . "user SET email=:email, confirmed=:confirmed, htmlemail=:htmlemail, rssfrequency=:rssfrequency, password=:password, passwordchanged=now(), disabled=:disabled WHERE id=:id;";

        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->bindParam("email", $_REQUEST['email'] );
            $stmt->bindParam("confirmed", $_REQUEST['confirmed'] );
            $stmt->bindParam("htmlemail", $_REQUEST['htmlemail'] );
            $stmt->bindParam("rssfrequency", $_REQUEST['rssfrequency'] );
            $stmt->bindParam("password", $_REQUEST['password'] );
            $stmt->bindParam("disabled", $_REQUEST['disabled'] );
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Users::userGet( $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }

    }

		/**
		 * <p>Deletes a user.</p>
		 * <p><strong>Parameters:</strong><br/>
		 * [*id] {integer} the ID of the user.<br/>
		 * </p>
		 * <p><strong>Returns:</strong><br/>
		 * The deleted user ID.
		 * </p>
		 */
    static function userDelete(){

        $sql = "DELETE FROM " . $GLOBALS['usertable_prefix'] . "user WHERE id=:id;";
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            phpList_RESTAPI_Response::outputDeleted( 'User', $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_RESTAPI_Response::outputError($e);
        }

    }

}



?>