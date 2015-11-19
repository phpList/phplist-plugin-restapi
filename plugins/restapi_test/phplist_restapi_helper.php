<?php

namespace phpListRestapi;

/**
 * Class to communicate with phpList via API REST.
 **/
class Helper
{
    private $session = null;
    private $url = '';

    public function __construct($url)
    {
        $this->url = $url;
    }

    private function setSessionID($sid)
    {
        $this->session = $sid;
    }

    public function getSessionID()
    {
        return $this->session;
    }

    /************************************************************
     * Administrators Operation
     ************************************************************/

    /**
     * @param $loginname
     * @param $password
     *
     * @return mixed
     */
    public function login($login, $password)
    {

        //Post Data
        $post_params = array(
            'login'    => $login,
            'password' => $password,
        );

        $result = $this->callAPI('login', $post_params);

        if ($result->status == 'success') {
            $this->setSessionID($result->data);

            return $result;
        }

        return $result;
    }

    public function processQueue($login, $password)
    {

        //Post Data
        $post_params = array(
            'login'    => $login,
            'password' => $password,
        );

        $result = $this->callAPI('processQueue', $post_params);

        if ($result->status == 'success') {
            $this->setSessionID($result->data);

            return $result;
        }

        return $result;
    }

    /************************************************************
     * Lists
     ************************************************************/

    public function listGet($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('listGet', $post_params);

        return $result;
    }

    public function listsGet()
    {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI('listsGet', $post_params);

        return $result;
    }

    public function listAdd($name, $description = '', $listorder = '0', $prefix = '', $rssfeed = '', $active = '1')
    {

        //Post Data
        $post_params = array(
            'name'        => $name,
            'description' => $description,
            'listorder'   => $listorder,
            'prefix'      => $prefix,
            'rssfeed'     => $rssfeed,
            'active'      => $active,
        );

        $result = $this->callAPI('listAdd', $post_params);

        return $result;
    }

    public function listUpdate($id, $name, $description = '', $listorder = '0', $prefix = '', $rssfeed = '', $active = '1')
    {

        //Post Data
        $post_params = array(
            'id'          => $id,
            'name'        => $name,
            'description' => $description,
            'listorder'   => $listorder,
            'prefix'      => $prefix,
            'rssfeed'     => $rssfeed,
            'active'      => $active,
        );

        $result = $this->callAPI('listUpdate', $post_params);

        return $result;
    }

    public function listDelete($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('listDelete', $post_params);

        return $result;
    }

    public function listsSubscriber($subscriber_id)
    {

        //Post Data
        $post_params = array(
            'subscriber_id' => $subscriber_id,
        );

        $result = $this->callAPI('listsSubscriber', $post_params);

        return $result;
    }

    public function listSubscriberAdd($list_id, $subscriber_id)
    {

        //Post Data
        $post_params = array(
            'list_id'       => $list_id,
            'subscriber_id' => $subscriber_id,
        );

        $result = $this->callAPI('listSubscriberAdd', $post_params);

        return $result;
    }

    public function listSubscriberDelete($list_id, $subscriber_id)
    {

        //Post Data
        $post_params = array(
            'list_id'       => $list_id,
            'subscriber_id' => $subscriber_id,
        );

        $result = $this->callAPI('listSubscriberDelete', $post_params);

        return $result;
    }

    public function listCampaignAdd($list_id, $message_id)
    {

        //Post Data
        $post_params = array(
            'list_id'    => $list_id,
            'campaign_id' => $message_id,
        );

        $result = $this->callAPI('listCampaignAdd', $post_params);

        return $result;
    }

    public function listMessageDelete($list_id, $message_id)
    {

        //Post Data
        $post_params = array(
            'list_id'    => $list_id,
            'campaign_id' => $message_id,
        );

        $result = $this->callAPI('listCampaignDelete', $post_params);

        return $result;
    }

    /************************************************************
     * subscribers
     ************************************************************/

    public function subscriberGet($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('subscriberGet', $post_params);

        return $result;
    }

    public function subscriberGetByEmail($email)
    {

        //Post Data
        $post_params = array(
            'email' => $email,
        );

        $result = $this->callAPI('subscriberGetByEmail', $post_params);

        return $result;
    }

    public function subscribersGet()
    {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI('subscribersGet', $post_params);

        return $result;
    }

    public function subscriberAdd($email, $confirmed, $htmlemail, $password = '', $disabled = 0, $rssfrequency = '')
    {

        //Post Data
        $post_params = array(
            'email'        => $email,
            'confirmed'    => $confirmed,
            'htmlemail'    => $htmlemail,
            'password'     => $password,
            'disabled'     => $disabled,
            'rssfrequency' => $rssfrequency,
        );

        $result = $this->callAPI('subscriberAdd', $post_params);

        return $result;
    }

    public function subscriberUpdate($id, $email, $confirmed, $htmlemail, $password = '', $disabled = 0, $rssfrequency = '')
    {

        //Post Data
        $post_params = array(
            'id'           => $id,
            'email'        => $email,
            'confirmed'    => $confirmed,
            'htmlemail'    => $htmlemail,
            'password'     => $password,
            'disabled'     => $disabled,
            'rssfrequency' => $rssfrequency,
        );

        $result = $this->callAPI('subscriberUpdate', $post_params);

        return $result;
    }

    public function subscriberDelete($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('subscriberDelete', $post_params);

        return $result;
    }

    /************************************************************
     * Templates
     ************************************************************/

    public function templateGet($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('templateGet', $post_params);

        return $result;
    }

    public function templateGetByTitle($title)
    {

        //Post Data
        $post_params = array(
            'title' => $title,
        );

        $result = $this->callAPI('templateGetByTitle', $post_params);

        return $result;
    }

    public function templatesGet()
    {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI('templatesGet', $post_params);

        return $result;
    }

    public function templateAdd($title, $template)
    {

        //Post Data
        $post_params = array(
            'title'    => $title,
            'template' => $template,
        );

        $result = $this->callAPI('templateAdd', $post_params);

        return $result;
    }

    public function templateUpdate($id, $title, $template)
    {

        //Post Data
        $post_params = array(
            'id'       => $id,
            'title'    => $title,
            'template' => $template,
        );

        $result = $this->callAPI('templateUpdate', $post_params);

        return $result;
    }

    public function templateDelete($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('templateDelete', $post_params);

        return $result;
    }

    /************************************************************
     * Messages
     ************************************************************/

    public function campaignGet($id)
    {

        //Post Data
        $post_params = array(
            'id' => $id,
        );

        $result = $this->callAPI('campaignGet', $post_params);

        return $result;
    }

    public function campaignsGet()
    {

        //Post Data
        $post_params = array(
        );

        $result = $this->callAPI('campaignsGet', $post_params);

        return $result;
    }

    public function campaignAdd($subject, $fromfield, $replyto, $message, $textmessage, $template, $embargo, $status = 'draft', $owner = 0, $sendformat = 'HTML', $footer = '', $rsstemplate = '')
    {

        //Post Data
        $post_params = array(
            'subject'     => $subject,
            'fromfield'   => $fromfield,
            'replyto'     => $replyto,
            'message'     => $message,
            'textmessage' => $textmessage,
            'template'    => $template,
            'embargo'     => $embargo,
            'footer'      => $footer,
            'status'      => $status,
            'sendformat'  => $sendformat,
            'rsstemplate' => $rsstemplate,
            'owner'       => $owner,
        );

        $result = $this->callAPI('campaignAdd', $post_params);

        return $result;
    }

    public function campaignUpdate($id, $subject, $fromfield, $replyto, $message, $textmessage, $template, $embargo, $status = 'draft', $owner = 0, $sendformat = 'HTML', $footer = '', $rsstemplate = '')
    {

        //Post Data
        $post_params = array(
            'id'          => $id,
            'subject'     => $subject,
            'fromfield'   => $fromfield,
            'replyto'     => $replyto,
            'message'     => $message,
            'textmessage' => $textmessage,
            'template'    => $template,
            'embargo'     => $embargo,
            'footer'      => $footer,
            'status'      => $status,
            'sendformat'  => $sendformat,
            'rsstemplate' => $rsstemplate,
            'owner'       => $owner,
        );

        $result = $this->callAPI('campaignUpdate', $post_params);

        return $result;
    }

    /************************************************************
     * Remote Requests via cUrl or other
     ************************************************************/

    private function callAPI($command, $post_params, $no_decode = false)
    {
        $post_params['cmd'] = $command;

        $post_params = http_build_query($post_params);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL,            $this->url);
        curl_setopt($c, CURLOPT_HEADER,         0);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_POST,           1);
        curl_setopt($c, CURLOPT_POSTFIELDS,     $post_params);
        curl_setopt($c, CURLOPT_COOKIEFILE,     $GLOBALS['tmpdir'].'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt($c, CURLOPT_COOKIEJAR,      $GLOBALS['tmpdir'].'/phpList_RESTAPI_Helper_cookiejar.txt');
        curl_setopt($c, CURLOPT_HTTPHEADER,     array('Connection: Keep-Alive', 'Keep-Alive: 60'));

        $result = curl_exec($c);

        if (!$no_decode) {
            $result = json_decode($result);
        }

        return $result;
    }
}
