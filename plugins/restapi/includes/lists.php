<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class phpList_RESTAPI_Lists
 * Getting lists, adding and removing its subscribers and campaigns.
 */
class Lists
{
    /**
     * Gets all lists in phpList as an array.
     *
     * <p><strong>Parameters:</strong><br/>
     * (none)
     * <p><strong>Returns:</strong><br/>
     * Array of lists. Limited to 50.
     * </p>
     */
    public static function listsGet()
    {
        Common::select('Lists', 'SELECT * FROM '.$GLOBALS['tables']['list'].' ORDER BY listorder limit 50;',array());
    }

    /**
     * Gets one (1) list.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.
     * <p><strong>Returns:</strong><br/>
     * One list.
     * </p>
     */
    public static function listGet($id = 0)
    {
        if ($id == 0) {
            $id = $_REQUEST['id'];
        }

        $params = array(
            'id'=> array($id,PDO::PARAM_INT),
            );


        Common::select('List', 'SELECT * FROM '.$GLOBALS['tables']['list']." WHERE id = :id;",$params,true);
    }

    /**
     * Add a new list.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*name] {string} the name of the list.<br/>
     * [description] {string} adds a description to the list.<br/>
     * [listorder] {integer} an expression to sortorder, eg 100.<br/>
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.<br/>
     * <p><strong>Returns:</strong><br/>
     * The list added.
     * </p>
     */
    public static function listAdd()
    {
        $sql = 'INSERT INTO '.$GLOBALS['tables']['list'].'
          (name, description, listorder, category, active)
          VALUES (:name, :description, :listorder, :category, :active);';

        // allow for an empty category, which didn't exist before
        if (!isset($_REQUEST['category'])) {
            $_REQUEST['category'] = '';
        }
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $_REQUEST['name'],PDO::PARAM_STR);
            $stmt->bindParam('description', $_REQUEST['description'],PDO::PARAM_STR);
            $stmt->bindParam('listorder', $_REQUEST['listorder'],PDO::PARAM_INT);
            $stmt->bindParam('category', $_REQUEST['category'],PDO::PARAM_STR);
            $stmt->bindParam('active', $_REQUEST['active'],PDO::PARAM_BOOL);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            self::listGet($id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Update existing List.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.<br/>
     * [*name] {string} the name of the list.<br/>
     * [description] {string} adds a description to the list.<br/>
     * [listorder] {integer} an expression to sortorder, eg 100.<br/>
     * [active] {integer} if list should be active set this one to 1, otherwise it will be disabled.<br/>
     * <p><strong>Returns:</strong><br/>
     * The list updated.
     * </p>
     */
    public static function listUpdate()
    {
        $sql = 'UPDATE '.$GLOBALS['tables']['list'].'
          SET name=:name, description=:description, listorder=:listorder, category=:category, active=:active
          WHERE id=:id;';

        // allow for an empty category, which didn't exist before
        if (!isset($_REQUEST['category'])) {
            $_REQUEST['category'] = '';
        }

        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id'],PDO::PARAM_INT);
            $stmt->bindParam('name', $_REQUEST['name'],PDO::PARAM_STR);
            $stmt->bindParam('description', $_REQUEST['description'],PDO::PARAM_STR);
            $stmt->bindParam('listorder', $_REQUEST['listorder'],PDO::PARAM_INT);
            $stmt->bindParam('category', $_REQUEST['category'],PDO::PARAM_STR);
            $stmt->bindParam('active', $_REQUEST['active'],PDO::PARAM_BOOL);
            $stmt->execute();
            $db = null;
            self::listGet($_REQUEST['id']);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Delete a List.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the list.
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    public static function listDelete()
    {
        $sql = 'DELETE FROM '.$GLOBALS['tables']['list'].' WHERE id=:id;';
        try {
            if (!is_numeric($_REQUEST['id']) || empty($_REQUEST['id'])) {
                Response::outputErrorMessage('invalid call');
            }
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $_REQUEST['id'],PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            Response::outputDeleted('List', sprintf('%d',$_REQUEST['id']));
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Get Lists a Subscriber is Member of.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*subscriber_id] {integer} the Subscriber-ID.
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the subscriber is assigned to.
     * </p>
     */
    public static function listsSubscriber($subscriber_id = 0)
    {
        $response = new Response();
        if ($subscriber_id == 0) {
            $subscriber_id = sprintf('%d',$_REQUEST['subscriber_id']);
        }
        $sql = 'SELECT * FROM '.$GLOBALS['tables']['list'].' WHERE id IN
          (SELECT listid FROM '.$GLOBALS['tables']['listuser'].' WHERE userid=:subscriber_id) ORDER BY listorder;';
        if (!is_numeric($subscriber_id) || empty($subscriber_id)) {
            Response::outputErrorMessage('invalid call');
        }
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('subscriber_id', $subscriber_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('Lists', $result);
            $response->output();
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Add a subscriber to a list.
     *
     * <p>The subscriber then subscribes to the list.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*subscriber_id] {integer} the ID of the subscriber.<br/>
     * <p><strong>Returns:</strong><br/>
     * Array of lists where the subscriber is assigned to.
     * </p>
     */
    public static function listSubscriberAdd($list_id = 0, $subscriber_id = 0)
    {
        if ($list_id == 0) {
            $list_id = sprintf('%d',$_REQUEST['list_id']);
        }
        if ($subscriber_id == 0) {
            $subscriber_id = sprintf('%d',$_REQUEST['subscriber_id']);
        }
         if (empty($subscriber_id) || empty($list_id)) {
            Response::outputErrorMessage('invalid call');
        }
        $sql = 'INSERT INTO '.$GLOBALS['tables']['listuser'].' (userid, listid, entered) VALUES (:subscriber_id, :list_id, now());';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('subscriber_id', $subscriber_id,PDO::PARAM_INT);
            $stmt->bindParam('list_id', $list_id,PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            self::listsSubscriber($subscriber_id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Remove a subscriber from a list.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*subscriber_id] {integer} the ID of the subscriber.
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    public static function listSubscriberDelete($list_id = 0, $subscriber_id = 0)
    {
        if ($list_id == 0) {
            $list_id = $_REQUEST['list_id'];
        }
        if ($subscriber_id == 0) {
            $subscriber_id = $_REQUEST['subscriber_id'];
        }
        $sql = 'DELETE FROM '.$GLOBALS['tables']['listuser'].' WHERE listid=:list_id AND userid=:subscriber_id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('subscriber_id', $subscriber_id,PDO::PARAM_INT);
            $stmt->bindParam('list_id', $list_id,PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            self::listsSubscriber($subscriber_id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Assigns a list to a campaign.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*campaign_id] {integer} the ID of the campaign.
     * <p><strong>Returns:</strong><br/>
     * The list assigned.
     * </p>
     */
    public static function listCampaignAdd($list_id = 0, $campaign_id = 0)
    {
        if ($list_id == 0) {
            $list_id = $_REQUEST['list_id'];
        }
        if ($campaign_id == 0) {
            $campaign_id = $_REQUEST['campaign_id'];
        }
        $sql = 'INSERT INTO '.$GLOBALS['tables']['listmessage'].' (messageid, listid, entered) VALUES (:campaign_id, :list_id, now());';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('campaign_id', $campaign_id,PDO::PARAM_INT);
            $stmt->bindParam('list_id', $list_id,PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            self::listGet($list_id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Unassigns a list from a campaign.
     *
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the ID of the list.<br/>
     * [*campaign_id] {integer} the ID of the campaign.
     * </p>
     * <p><strong>Returns:</strong><br/>
     * System message of action.
     * </p>
     */
    public static function listCampaignDelete($list_id = 0, $campaign_id = 0)
    {
        if ($list_id == 0) {
            $list_id = $_REQUEST['list_id'];
        }
        if ($campaign_id == 0) {
            $campaign_id = $_REQUEST['campaign_id'];
        }
        $sql = 'DELETE FROM '.$GLOBALS['tables']['listmessage'].' WHERE listid=:list_id AND messageid=:campaign_id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('campaign_id', $campaign_id,PDO::PARAM_INT);
            $stmt->bindParam('list_id', $list_id,PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            Response::outputMessage('Campaign '.$campaign_id.' wsa removed from list '.$list_id);
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }
   /**
     * Get all subscribers from a list
     *
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the List-ID.
     * <p><strong>Returns:</strong><br/>
     * Array of subscribers assigned to the list.
     * </p>
     */
    public static function listSubscribers($list_id = 0)
    {
        $response = new Response();
        if ($list_id == 0) {
            $list_id = sprintf('%d',$_REQUEST['list_id']);
        }
        $sql = 'SELECT * FROM '.$GLOBALS['tables']['user'].' WHERE id IN
          (SELECT userid FROM '.$GLOBALS['tables']['listuser'].' WHERE listid=:list_id) ORDER BY id;';
        if (!is_numeric($list_id) || empty($list_id)) {
            Response::outputErrorMessage('invalid call');
        }
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('list_id', $list_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('Subscribers', $result);
            $response->output();
        } catch (\Exception $e) {
            Response::outputError($e);
        }
        die(0);
    }

    /**
     * Get num of subscribers from a list
     *
     * <p><strong>Parameters:</strong><br/>
     * [*list_id] {integer} the List-ID.
     * <p><strong>Returns:</strong><br/>
     * List_id and count of subscriber.
     * </p>
     */
    public static function listSubscribersCount($list_id = 0)
    {
        $response = new Response();
        if($list_id == 0) {
            $list_id = sprintf('%d', $_REQUEST['list_id']);
        }
        $sql = 'SELECT listid, COUNT(*) AS count  FROM '.$GLOBALS['tables']['listuser'].' WHERE listid=:list_id;';
        if(!is_numeric($list_id) || empty($list_id)){
            Response::outputErrorMessage('invalid call');
        }
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('list_id', $list_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $db = null;
            $response->setData('SubscribersCount', $result);
            $response->output();
        } catch(\Exception $e) {
            Response::outputError($e);
        }
    }

   /**
     * Get all lists within a category
     *
     * <p><strong>Parameters:</strong><br/>
     * [*category] {string} the category.
     * <p><strong>Returns:</strong><br/>
     * Array of lists that are in the category.
     * </p>
     */
    public static function listsByCategory($category = '')
    {
        if ($category == '') {
            $category = sprintf('%s', $_REQUEST['category']);
        }
        $sql = 'SELECT * FROM '.$GLOBALS['tables']['list'].' WHERE category = :category';
        $params = [
            'category' => [$category, PDO::PARAM_STR]
        ];
        Common::select('Lists', $sql, $params);
    }
}
