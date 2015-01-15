<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

class UserAttributes {

    /**
     * <p>Get all the Attributes in the system.</p>
                 * <p><strong>Parameters:</strong><br/>
                 * [order_by] {string} name of column to sort, default "listorder,name".<br/>
                 * [order] {string} sort order asc or desc, default: asc.<br/>
                 * [limit] {integer} limit the result, default 100.<br/>
                 * </p>
     * <p><strong>Returns:</strong><br/>
     * List of User Attributes.
     * </p>
     */
    static function attributeListGet( $order_by='listorder,name', $order='asc', $limit=100 ) {

                                //getting optional values
                                if ( isset( $_REQUEST['order_by'] ) && !empty( $_REQUEST['order_by'] ) ) $order_by = $_REQUEST['order_by'];
                                if ( isset( $_REQUEST['order'] ) && !empty( $_REQUEST['order'] ) ) $order = $_REQUEST['order'];
                                if ( isset( $_REQUEST['limit'] ) && !empty( $_REQUEST['limit'] ) ) $limit = $_REQUEST['limit'];

        Common::select( 'Attributes', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "attribute ORDER BY $order_by $order LIMIT $limit;" );

    }

                /**
     * <p>Gets one Attribute via name.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*name] {string} the name of the attribute .<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     *  Attribute Data.
     * </p>
     */
    static function attributeGetByName( $name = "") {
        if ( empty( $name ) ) $name = $_REQUEST['name'];
                                if ( empty($name) ) {
                                        Response::outputErrorMessage( 'Attribute Name empty!' );
                                }

        Common::select( 'Attributes', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "attribute WHERE name = '$name';", true );
    }


                /**
     * <p>Get all the Attributes of Subscriber .</p>
                 * <p><strong>Parameters:</strong><br/>
                 * [*id] {integer} the ID of the Subscriber.<br/>
                 * [order] {string} sort order asc or desc, default: asc.<br/>
                 * [limit] {integer} limit the result, default 100.<br/>
                 * </p>
     * <p><strong>Returns:</strong><br/>
     * List of User Attributes.
     * </p>
     */
    static function userAttributeListGet( $id=0, $order_by='listorder,name', $order='asc', $limit=100, $raw=false ) {
                                if ( $id==0 ) $id = $_REQUEST['id'];
                                if ( empty($id) ) {
                                        Response::outputErrorMessage( 'UserID empty!' );
                                }

                                //getting optional values
                                if ( isset( $_REQUEST['order_by'] ) && !empty( $_REQUEST['order_by'] ) ) $order_by = $_REQUEST['order_by'];
                                if ( isset( $_REQUEST['order'] ) && !empty( $_REQUEST['order'] ) ) $order = $_REQUEST['order'];
                                if ( isset( $_REQUEST['limit'] ) && !empty( $_REQUEST['limit'] ) ) $limit = $_REQUEST['limit'];

                                if ($raw) {
                                        Common::select( 'UserAttributes', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user_attribute WHERE userid='$id' LIMIT $limit;" );
                                }
                                else {
                                        Common::select( 'UserAttributes', "SELECT attributeid,userid,value,name,type,listorder,default_value,required,tablename FROM ".$GLOBALS['usertable_prefix']."user_attribute AS u JOIN ".$GLOBALS['usertable_prefix']."attribute AS a ON u.attributeid=a.id WHERE u.userid='$id' LIMIT $limit;" );
                                }

    }

                /**
     * <p>Get One Attribute of Subscriber .</p>
                 * <p><strong>Parameters:</strong><br/>
                 * [*id] {integer} the ID of the Subscriber.<br/>
                 * [*aid] {integer} the ID of the Attribute.<br/>
                 * </p>
     * <p><strong>Returns:</strong><br/>
     * Attribute Data.
     * </p>
     */
    static function userAttributeGet( $id=0, $aid=0, $raw=false) {
                                if ( $id==0 ) $id = $_REQUEST['id'];
                                if ( $aid==0 ) $aid = $_REQUEST['aid'];
                                if ( empty($aid) || empty($id)) {
                                        Response::outputErrorMessage( 'UserID or AttributeID empty!' );
                                }

                                if ($raw) {
                                        Common::select( 'UserAttributes', "SELECT * FROM " . $GLOBALS['usertable_prefix'] . "user_attribute WHERE userid='$id' AND attributeid='$aid';", true );
                                } else {
                                        Common::select( 'UserAttributes', "SELECT attributeid,userid,value,name,type,listorder,default_value,required,tablename FROM ".$GLOBALS['usertable_prefix']."user_attribute AS u JOIN ".$GLOBALS['usertable_prefix']."attribute AS a ON u.attributeid=a.id WHERE u.userid='$id' AND u.attributeid='$aid';", true );
                                }

    }

                /**
     * <p>Adds Attribute to Subscriber.</p>
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} the ID of the Subscriber.<br/>
     * [*aid] {integer} the ID of the Attribute.<br/>
     * [*value] {string} the Value of the Attribute.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * The added Attribute.
     * </p>
     */
                static function userAttributeAdd($id=0, $aid=0, $value="") {
                                if ( $id==0 ) $id = $_REQUEST['id'];
                                if ( $aid==0 ) $aid = $_REQUEST['aid'];
                                if ( empty( $value ) ) $value = $_REQUEST['value'];
                                if ( empty($aid) || empty($id) || empty($value)) {
                                        Response::outputErrorMessage( 'UserID or AttributeID or Value empty!' );
                                }


                                $sql = "INSERT INTO " . $GLOBALS['usertable_prefix'] . "user_attribute (attributeid, userid, value) VALUES (:attributeid, :userid, :value);";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("attributeid", $aid);
            $stmt->bindParam("userid", $id);
            $stmt->bindParam("value", $value);

            $stmt->execute();
            $db = null;
            UserAttributes::userAttributeGet($id, $aid );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
                }

                /**
                 * <p>Updates one Subscriber Attribute.</p>
                 * <p><strong>Parameters:</strong><br/>
                 * [*id] {integer} the ID of the Subscriber.<br/>
                 * [*aid] {integer} the ID of the Attribute.<br/>
                 * [*value] {string} the Value of the Attribute.<br/>
                 * </p>
                 * <p><strong>Returns:</strong><br/>
                 * The updated Subscriber Attribute.
                 * </p>
                 */
                static function userAttributeUpdate() {
                                if ( empty($_REQUEST['aid']) || empty($_REQUEST['id']) || empty($_REQUEST['value'])) {
                                        Response::outputErrorMessage( 'UserID or AttributeID or Value empty!' );
                                }

                                $sql = "UPDATE " . $GLOBALS['usertable_prefix'] . "user_attribute SET value=:value WHERE attributeid=:attributeid AND userid=:userid;";

        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("attributeid", $_REQUEST['aid']);
            $stmt->bindParam("userid", $_REQUEST['id'] );
            $stmt->bindParam("value", $_REQUEST['value'] );
            $stmt->execute();
            $db = null;
            UserAttributes::userAttributeGet( $_REQUEST['id'], $_REQUEST['aid'] );
        } catch(\PDOException $e) {
            Response::outputError($e);
        }
                }

                /**
                 * <p>Deletes a Subscriber Attribute.</p>
                 * <p><strong>Parameters:</strong><br/>
                 * [*id] {integer} the ID of the Subscriber.<br/>
                 * [*aid] {integer} the ID of the Attribute.<br/>
                 * </p>
                 * <p><strong>Returns:</strong><br/>
                 * The deleted Subscriber ID.
                 * </p>
                 */
    static function userAttributeDelete(){
                                if ( empty($_REQUEST['aid']) || empty($_REQUEST['id'])) {
                                        Response::outputErrorMessage( 'UserID or AttributeID empty!' );
                                }
                                /*
                                * do not delet row, set it NULL!
                                */
        //$sql = "DELETE FROM " . $GLOBALS['usertable_prefix'] . "user_attribute attributeid=:attributeid AND userid=:userid;";
        $sql = "UPDATE " . $GLOBALS['usertable_prefix'] . "user_attribute SET value=NULL WHERE attributeid=:attributeid AND userid=:userid;";
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("attributeid", $_REQUEST['aid']);
            $stmt->bindParam("userid", $_REQUEST['id'] );
            $stmt->execute();
            $db = null;
                                                Response::outputMessage( 'Attribute ' . $_REQUEST['aid'] . ' for Subscriber ' . $_REQUEST['id'] .' deleted!' );

        } catch(\PDOException $e) {
            Response::outputError($e);
        }

    }
}
