<?php

class phpList_API_Templates{

    static function templatesGet() {
        phpList_API_Common::select( 'Templates', "SELECT * FROM " . $GLOBALS['table_prefix'] . "template ORDER BY listorder;" );
    }

    static function templateGet( $id=0 ) {
        if ( $id==0 ) $id = $_REQUEST['id'];
        phpList_API_Common::select( 'Template', "SELECT * FROM " . $GLOBALS['table_prefix'] . "template WHERE id=" . $id . ";", true );
    }

    static function templateGetByTitle( $title='' ) {
        if ( empty($title) ) $title = $_REQUEST['title'];
        phpList_API_Common::select( 'Template', "SELECT * FROM " . $GLOBALS['table_prefix'] . "template WHERE title='" . $title . "';", true );
    }

    static function templateAdd(){

        $sql = "INSERT INTO " . $GLOBALS['table_prefix'] . "template (title, template) VALUES (:title, :template);";
        try {
            $db = phpList_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("title", $_REQUEST['title']);
            $stmt->bindParam("template", $_REQUEST['template']);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            phpList_API_Templates::templateGet( $id );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }

    static function templateUpdate(){

        $sql = "UPDATE " . $GLOBALS['table_prefix'] . "template SET title=:title, template=:template WHERE id=:id;";
        try {
            $db = phpList_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->bindParam("title", $_REQUEST['title']);
            $stmt->bindParam("template", $_REQUEST['template']);
            $stmt->execute();
            $db = null;
            phpList_API_Templates::templateGet( $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }

    static function templateDelete(){

        $sql = "DELETE FROM " . $GLOBALS['table_prefix'] . "template WHERE id=:id;";
        try {
            $db = phpList_API_PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            phpList_API_Response::outputDeleted( 'Template', $_REQUEST['id'] );
        } catch(PDOException $e) {
            phpList_API_Response::outputError($e);
        }

    }


}



?>