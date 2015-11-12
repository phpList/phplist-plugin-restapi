<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

class Actions
{
    /**
     * Function to call for login.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*login] {string} loginname as an admin to phpList<br/>
     * [*password] {string} the password
     * </p>.
     */
    public static function login()
    {
        Response::outputMessage('Welcome!');
    }

}
