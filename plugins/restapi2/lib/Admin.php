<?php

namespace Rapi;

/**
 * Class for account related commands
 */
class Admin {
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var \phpListAdminAuthentication
     */
    private $admin;

    /**
     * Admin constructor.
     * @param Response $response
     * @param \phpListAdminAuthentication $admin
     */
    public function __construct(Response $response, \phpListAdminAuthentication $admin)
    {
        $this->response = $response;
        $this->admin = $admin;
    }

    /**
     * Function to call for login.
     *
     * @todo: Finish implementing this method
     *
     * @param string $password login name as an admin to phpList
     * @param string $username the password
     *
     * @return bool
     */
    public function login( $password, $username )
    {
        $data = $this->admin->validateLogin($password, $username);

        if($data['result']){
            $this->admin->setLoginToken($data['admin']->id);
            return $this->admin->getLoginToken($data['admin']->id);
        }

        return false;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function isLoggedIn( $token ){
        return $this->admin->checkIfTheTokenIsValid($token);
    }

    /**
     * Processes the Message Queue in phpList.
     * @todo: Finish implementing this method
     * @note: Perhaps this is done via CRON or manually through the admin interface?
     */
    public function processQueue()
    {
        $this->response->outputMessage( 'Not implemented' );
    }
}
