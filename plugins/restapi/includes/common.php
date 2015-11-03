<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

class Common
{
    public static function select($type, $sql, $single = false)
    {
        $response = new Response();
        try {
            $db = PDO::getConnection();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($single && is_array($result) && isset($result[0])) {
                $result = $result[0];
            }
            $response->setData($type, $result);
        } catch (PDOException $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->output();
    }

    public static function apiUrl($website)
    {
        $url = '';
        if (!empty($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] !== 'off') {
                $url = 'https://';
            } //https
            else {
                $url = 'http://';
            } //http
        } else {
            $url = 'http://';
        } //http

        $api_url = str_replace('page=main&pi=restapi_test', 'page=call&pi=restapi', $_SERVER['REQUEST_URI']);
        $api_url = preg_replace('/\&tk\=[^&]*/', '', $api_url);
        $api_url = str_replace('page=main&pi=restapi', 'page=call&pi=restapi', $api_url);

        $url = $url.$website.$api_url;
        $url = rtrim($url, '/');

        return $url;
    }
    
    public static function parms($string,$data) {
        $indexed=$data==array_values($data);
        foreach($data as $k=>$v) {
            if(is_string($v)) $v="'$v'";
            if($indexed) $string=preg_replace('/\?/',$v,$string,1);
            else $string=str_replace(":$k",$v,$string);
        }
        return $string;
    }

}
