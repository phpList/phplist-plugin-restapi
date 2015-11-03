<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class Templates
 * Handling templates at phplist.
 */
class Templates
{
    public static function templatesGet()
    {
        Common::select('Templates', 'SELECT * FROM '.$GLOBALS['table_prefix'].'template ORDER BY listorder;');
    }

    public static function templateGet($id = 0)
    {
        if ($id == 0) {
            $id = $_REQUEST['id'];
        }
        Common::select('Template', 'SELECT * FROM '.$GLOBALS['table_prefix'].'template WHERE id='.$id.';', true);
    }

    public static function templateGetByTitle($title = '')
    {
        if (empty($title)) {
            $title = $_REQUEST['title'];
        }
        Common::select('Template', 'SELECT * FROM '.$GLOBALS['table_prefix']."template WHERE title='".$title."';", true);
    }

    /**
     * Add a new template.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*title] {string} the name of the list.<br/>
     * [template] {string} adds a description to the list.<br/>
     * <p><strong>Returns:</strong><br/>
     * The template added.
     * </p>
     */
    public static function templateAdd()
    {
        $sql = 'INSERT INTO '.$GLOBALS['table_prefix'].'template (title, template) VALUES (:title, :template);';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('title', $_REQUEST['title']);
            $stmt->bindParam('template', $_REQUEST['template']);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            self::templateGet($id);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    public static function templateUpdate()
    {
        $sql = 'UPDATE '.$GLOBALS['table_prefix'].'template SET title=:title, template=:template WHERE id=:id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->bindParam('title', $_REQUEST['title']);
            $stmt->bindParam('template', $_REQUEST['template']);
            $stmt->execute();
            $db = null;
            self::templateGet($_REQUEST['id']);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    public static function templateDelete()
    {
        $sql = 'DELETE FROM '.$GLOBALS['table_prefix'].'template WHERE id=:id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id']);
            $stmt->execute();
            $db = null;
            Response::outputDeleted('Template', $_REQUEST['id']);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }
}
