<?php

namespace Rapi\Handler;
use phpList\helper\Logger;
use Rapi\Admin;

/**
 * Class LoginHandler
 * @package Rapi\Handler
 */
class LoginHandler
{

    /**
     * LoginHandler constructor.
     *
     * @param Admin $admin
     * @param Logger $logger
     */
    public function __construct( Admin $admin, Logger $logger)
    {
        $this->admin = $admin;
        $logger->debug('LoginHandler was called');
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     */
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