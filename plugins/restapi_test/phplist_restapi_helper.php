<?php
/**
 * Class to communicate with phpList via API REST
 *
 **/
class phpList_RESTAPI_Helper {

    private $session = null;
    private $url = '';

    function __construct($url) {
        $this->url = $url;
    }

    private function setSessionID($sid){
        $this->session = $sid;
    }

    public function getSessionID(){
        return $this->session;
    }

    /************************************************************
     * Administrators Operation
     ************************************************************/

    /**
     * @param $loginname
     * @param $password
     * @return mixed
     */
    public function login( $login, $password ) {

        //Post Data
        $post_params = array(
            'login' => $login,
            'password' => $password
        );

        $result = $this->callAPI( 'login', $post_params);

        if ( $result->status == 'success' ) {
            $this->setSessionID( $result->data );
            return $result;
        }

        return $result;
    }

    public function processQueue( $login, $password ) {

        //Post Data
        $post_params = array(
            'login' => $login,
            'password' => $password
        );

        $result = $this->callAPI( 'processQueue', $post_params);

        if ( $result->status == 'success' ) {
            $this->setSessionID( $result->data );
            return $result;
        }

        return $result;
    }


    /************************************************************
     * Lists
     ************************************************************/

    public function listGet( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'listGet', $post_params);

        return $result;
    }

    public function listsGet( ) {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI( 'listsGet', $post_params);

        return $result;
    }

    public function listAdd( $name, $description='', $listorder='0', $prefix='', $rssfeed='', $active='1' ) {

        //Post Data
        $post_params = array(
            'name' => $name,
            'description' => $description,
            'listorder' => $listorder,
            'prefix' => $prefix,
            'rssfeed' => $rssfeed,
            'active' => $active
        );

        $result = $this->callAPI( 'listAdd', $post_params);

        return $result;
    }

    public function listUpdate( $id, $name, $description='', $listorder='0', $prefix='', $rssfeed='', $active='1' ) {

        //Post Data
        $post_params = array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'listorder' => $listorder,
            'prefix' => $prefix,
            'rssfeed' => $rssfeed,
            'active' => $active
        );

        $result = $this->callAPI( 'listUpdate', $post_params);

        return $result;
    }

    public function listDelete( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'listDelete', $post_params);

        return $result;
    }

    public function listsUser( $user_id ) {

        //Post Data
        $post_params = array(
            'user_id' => $user_id
        );

        $result = $this->callAPI( 'listsUser', $post_params);

        return $result;
    }

    public function listUserAdd( $list_id, $user_id ) {

        //Post Data
        $post_params = array(
            'list_id' => $list_id,
            'user_id' => $user_id
        );

        $result = $this->callAPI( 'listUserAdd', $post_params);

        return $result;
    }

    public function listUserDelete( $list_id, $user_id ) {

        //Post Data
        $post_params = array(
            'list_id' => $list_id,
            'user_id' => $user_id
        );

        $result = $this->callAPI( 'listUserDelete', $post_params);

        return $result;
    }

    public function listMessageAdd( $list_id, $message_id ) {

        //Post Data
        $post_params = array(
            'list_id' => $list_id,
            'message_id' => $message_id
        );

        $result = $this->callAPI( 'listMessageAdd', $post_params);

        return $result;
    }

    public function listMessageDelete( $list_id, $message_id ) {

        //Post Data
        $post_params = array(
            'list_id' => $list_id,
            'message_id' => $message_id
        );

        $result = $this->callAPI( 'listMessageDelete', $post_params);

        return $result;
    }


    /************************************************************
     * Users
     ************************************************************/

    public function userGet( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'userGet', $post_params);

        return $result;
    }

    public function userGetByEmail( $email ) {

        //Post Data
        $post_params = array(
            'email' => $email
        );

        $result = $this->callAPI( 'userGetByEmail', $post_params);

        return $result;
    }

    public function usersGet( ) {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI( 'usersGet', $post_params);

        return $result;
    }

    public function userAdd( $email, $confirmed, $htmlemail, $password='', $disabled=0, $rssfrequency=''  ) {

        //Post Data
        $post_params = array(
            'email' => $email,
            'confirmed' => $confirmed,
            'htmlemail' => $htmlemail,
            'password' => $password,
            'disabled' => $disabled,
            'rssfrequency' => $rssfrequency
        );

        $result = $this->callAPI( 'userAdd', $post_params);

        return $result;
    }

    public function userUpdate( $id, $email, $confirmed, $htmlemail, $password='', $disabled=0, $rssfrequency='' ) {

        //Post Data
        $post_params = array(
            'id' => $id,
            'email' => $email,
            'confirmed' => $confirmed,
            'htmlemail' => $htmlemail,
            'password' => $password,
            'disabled' => $disabled,
            'rssfrequency' => $rssfrequency
        );

        $result = $this->callAPI( 'userUpdate', $post_params);

        return $result;
    }

    public function userDelete( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'userDelete', $post_params);

        return $result;
    }

    /************************************************************
     * Templates
     ************************************************************/

    public function templateGet( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'templateGet', $post_params);

        return $result;
    }

    public function templateGetByTitle( $title ) {

        //Post Data
        $post_params = array(
            'title' => $title
        );

        $result = $this->callAPI( 'templateGetByTitle', $post_params);

        return $result;
    }

    public function templatesGet() {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI( 'templatesGet', $post_params);

        return $result;
    }

    public function templateAdd( $title, $template  ) {

        //Post Data
        $post_params = array(
            'title' => $title,
            'template' => $template
        );

        $result = $this->callAPI( 'templateAdd', $post_params);

        return $result;
    }

    public function templateUpdate( $id, $title, $template  ) {

        //Post Data
        $post_params = array(
            'id' => $id,
            'title' => $title,
            'template' => $template
        );

        $result = $this->callAPI( 'templateUpdate', $post_params);

        return $result;
    }

    public function templateDelete( $id  ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'templateDelete', $post_params);

        return $result;
    }

    /************************************************************
     * Messages
     ************************************************************/

    public function messageGet( $id ) {

        //Post Data
        $post_params = array(
            'id' => $id
        );

        $result = $this->callAPI( 'messageGet', $post_params);

        return $result;
    }

    public function messagesGet() {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI( 'messagesGet', $post_params);

        return $result;
    }

    public function messageAdd( $subject, $fromfield, $replyto, $message, $textmessage, $template, $embargo, $status='draft', $owner=0, $sendformat='HTML', $footer='', $rsstemplate=''  ) {

        //Post Data
        $post_params = array(
            'subject' => $subject,
            'fromfield' => $fromfield,
            'replyto' => $replyto,
            'message' => $message,
            'textmessage' => $textmessage,
            'template' => $template,
            'embargo' => $embargo,
            'footer' => $footer,
            'status' => $status,
            'sendformat' => $sendformat,
            'rsstemplate' => $rsstemplate,
            'owner' => $owner
        );

        $result = $this->callAPI( 'messageAdd', $post_params);

        return $result;
    }

    public function messageUpdate( $id, $subject, $fromfield, $replyto, $message, $textmessage, $template, $embargo, $status='draft', $owner=0, $sendformat='HTML', $footer='', $rsstemplate='' ) {

        //Post Data
        $post_params = array(
            'id' => $id,
            'subject' => $subject,
            'fromfield' => $fromfield,
            'replyto' => $replyto,
            'message' => $message,
            'textmessage' => $textmessage,
            'template' => $template,
            'embargo' => $embargo,
            'footer' => $footer,
            'status' => $status,
            'sendformat' => $sendformat,
            'rsstemplate' => $rsstemplate,
            'owner' => $owner
        );

        $result = $this->callAPI( 'messageUpdate', $post_params);

        return $result;
    }



    /************************************************************
     * Remote Requests via cUrl or other
     ************************************************************/

    private function callAPI($command, $post_params, $no_decode=false ) {

        $post_params['cmd'] = $command;

        $post_params = http_build_query($post_params);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL,            $this->url );
        curl_setopt($c, CURLOPT_HEADER,         0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_POST,           1);
        curl_setopt($c, CURLOPT_POSTFIELDS,     $post_params);
        curl_setopt($c, CURLOPT_COOKIEFILE,     $GLOBALS['tmpdir'].'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt($c, CURLOPT_COOKIEJAR,      $GLOBALS['tmpdir'].'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt($c, CURLOPT_HTTPHEADER,     array( 'Connection: Keep-Alive', 'Keep-Alive: 60' ));

        $result = curl_exec($c);

        if ( !$no_decode ) $result = json_decode($result);

        return $result;

    }

}

