<?php

namespace Rapi\Handler;

/**
 * Class LoginHandler
 * @package Rapi\Handler
 */
class LoginHandler
{

    /**
     * LoginHandler constructor.
     * @param \Rapi\Admin $admin
     */
    public function __construct( \Rapi\Admin $admin)
    {
        $this->admin = $admin;
    }

    public function login($username, $password){
        $response = $this->admin->login($username, $password);
        return ["message" => "Success"];
    }

}