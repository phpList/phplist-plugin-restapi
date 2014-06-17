<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

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
        Response::outputMessage( 'Not implemented' );
    }

}
