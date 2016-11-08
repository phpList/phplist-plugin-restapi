<?php

namespace Rapi\Handler;
use phpList\helper\Logger;

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
    public function __construct( \Rapi\Admin $admin, Logger $logger)
    {
        $this->admin = $admin;
        $logger->debug('LoginHandler was called');
    }

    public function login($username, $password){
        $data = $this->admin->login($username, $password);
        if($data){
            return [
                "message" => "Success",
                "token" => $data
            ];
        }else{
            return [
                "message" => "Failure"
            ];
        }
    }

}