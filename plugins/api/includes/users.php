<?php

class phpList_API_Users{

    static function usersGet() {
        phpList_API_Common::select( 'Users', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user ORDER BY id;" );
    }

    static function userGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        phpList_API_Common::select( 'User', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user WHERE id = $id;", true );
    }

    static function userGetByEmail( $email ) {
        if ( empty( $email ) ) $email = $_REQUEST['email'];
        phpList_API_Common::select( 'User', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user WHERE email = '$email';", true );
    }

    static function userAdd(){

        $sql = "INSERT INTO " . $GLOBALS['usertable_prefix'] . "user (email, confirmed, htmlemail, rssfrequency, password, passwordchanged, disabled, entered, uniqid) VALUES (:email, :confirmed, :htmlemail, :rssfrequency, :password, now(), :disabled, now(), :uniqid);";
        try {
            $db = phpList_API_PDO::getConnection();
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
            phpList_API_Users::userGet( $id );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }

    static function userUpdate(){

        $sql = "UPDATE " . $GLOBALS['usertable_prefix'] . "user SET email=:email, confirmed=:confirmed, htmlemail=:htmlemail, rssfrequency=:rssfrequency, password=:password, passwordchanged=now(), disabled=:disabled WHERE id=:id;";

        try {
            $db = phpList_API_PDO::getConnection();
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
            phpList_API_Users::userGet( $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }

    static function userDelete(){

        $sql = "DELETE FROM " . $GLOBALS['usertable_prefix'] . "user WHERE id=:id;";
        try {
            $db = phpList_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            phpList_API_Response::outputDeleted( 'User', $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }

}



?>