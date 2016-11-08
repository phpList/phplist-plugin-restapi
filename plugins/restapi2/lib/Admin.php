<?php

namespace Rapi;

/**
 * Class for account related commands
 */
class Admin {

    // Response object
    protected $response;
    /**
     * @var AdminModel
     */
    private $admin;

    /**
     * Admin constructor.
     * @param Response $response
     * @param AdminModel $adminModel
     */
    public function __construct(Response $response, \phpList\Admin $admin )
    {
        $this->response = $response;
        $this->admin = $admin;
    }

    /**
     * Function to call for login.
     * @todo: Finish implementing this method
     * [*login] {string} loginname as an admin to phpList
     * [*password] {string} the password
     * @return boolean
     * @throws \Exception
     */
    public function login( $password, $username )
    {
        $data = $this->admin->validateLogin($password, $username);

        if($data['result']){
            $this->admin->setLoginToken($data['admin']->id);
            return $this->admin->getLoginToken($data['admin']->id);
        }
    }

    /**
     * @param $token string
     * @return bool
     */
    public function isLoggedIn( $token ){
        return $this->admin->checkIfTheTokenIsValid($token);
    }

    /**
     * Processes the Message Queue in phpList.
     * @todo: Finish implementing this method
     * @note: Perhaps this is done via CRON or manually through the admin interface?
     * [*login] {string} loginname as an admin to phpList
     */
    public function processQueue()
    {
        $this->response->outputMessage( 'Not implemented' );
    }
}
