<?php

namespace phpListRestapi;

class Actions {

    /**
     * <p>Function to call for login.<p>
     * <p><strong>Parameters:</strong><br/>
     * [*login] {string} loginname as an admin to phpList<br/>
     * [*password] {string} the password
     * </p>
     */
    static function login() {
        Response::outputMessage( 'Welcome!' );
    }

    /**
     * <p>Processes the Message Queue in phpList.<br/>
     * Perhaps this is done via CRON or manually through the admin interface?</p>
     * <p><strong>Parameters:</strong><br/>
     * [*login] {string} loginname as an admin to phpList<br/>
     * [*password] {string} the password
     *
     */
    static function processQueue() {

        $admin_id = $_SESSION["logindetails"]["id"];
        $url = Common::apiUrl( $_SERVER['HTTP_HOST'] );
        $url = str_replace( 'page=call&pi=restapi', 'page=processqueue&login=' . $login . '&password=' . $password . '&ajax=1', $url );

        //open connection
        //ob_start();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);

        //clean up
        curl_close($ch);

        //ob_end_clean();

        Response::outputMessage( 'Queue is processed!' );

    }

}
