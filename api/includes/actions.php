<?php

class PHPlist_API_Actions{

    /**
     * Function to call for login.<br/>
     * <p>Parameters<br/>
     * [*login] loginname as an admin to PHPlist<br/>
     * [*password] the password
     * </p>
     */
    static function login(){
        PHPlist_API_Response::outputMessage( 'Welcome!' );
    }

    /**
     * Processes the Message Queue in PHPlist.<br/>
     * Perhaps this is done via CRON or manually through the admin interface?
     * <p>Parameters<br/>
     *
     */
    static function processQueue( ){

        $admin_id = $_SESSION["logindetails"]["id"];

        //Get the password from db!
        $db = PHPlist_API_PDO::getConnection();

        $sql = "SELECT * FROM " . $GLOBALS['table_prefix'] . "admin WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->execute( array( ':id' => $admin_id ) );
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $login = $result[0]->loginname;
        $password = $result[0]->password;

        $url = PHPlist_API_Common::apiUrl( $_SERVER['HTTP_HOST'] );
        $url = str_replace( 'page=call&pi=api', 'page=processqueue&login=' . $login . '&password=' . $password . '&ajax=1', $url );

        //open connection
        //ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);

        //clean up
        curl_close($ch);

        //ob_end_clean();

        PHPlist_API_Response::outputMessage( 'Queue is processed!' );

    }

}


?>