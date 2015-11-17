<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

class Common
{
    public static function select($type, $sql, $params = array(), $single = false)
    {
       $response = new Response();
       try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            foreach ($params as $param => $paramValue) {
                $stmt->bindParam($param, $paramValue[0],$paramValue[1]);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($single && is_array($result) && isset($result[0])) {
                $result = $result[0];
            }
            $response->setData($type, $result);
        } catch (\Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->output();
    }
    
    public static function logRequest($cmd) 
    {
       $response = new Response();
       $requestData = serialize($_REQUEST);
       try {
            $db = PDO::getConnection();
            $stmt = $db->prepare('insert into '.$GLOBALS['table_prefix'].'restapi_request_log (url, cmd, ip, request, date) values(:url, :cmd, :ip, :request, now())');
            $stmt->bindParam('url', $_SERVER['REQUEST_URI'],PDO::PARAM_STR); 
            $stmt->bindParam('cmd', $cmd, PDO::PARAM_STR);
            $stmt->bindParam('ip', $GLOBALS['remoteAddr'],PDO::PARAM_STR);
            $stmt->bindParam('request', $requestData, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
    }

    public static function enforceRequestLimit($limit) 
    {
       $response = new Response();
       try {
            $db = PDO::getConnection();
            $stmt = $db->prepare('select count(cmd) as num from '.$GLOBALS['table_prefix'].'restapi_request_log where date > date_sub(now(),interval 1 minute)');
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            if ($result->num > $limit) {
              $response->outputErrorMessage('Too many requests. Requests are limited to '.$limit.' per minute');
              die(0);
            }
        } catch (\Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
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
    
    public static function encryptPassword($pass)
    {
        if (empty($pass)) {
            return '';
        }

        if (function_exists('hash')) {
            if (!in_array(ENCRYPTION_ALGO, hash_algos(), true)) {
                ## fallback, not that secure, but better than none at all
                $algo = 'md5';
            } else {
                $algo = ENCRYPTION_ALGO;
            }

            return hash($algo, $pass);
        } else {
            return md5($pass);
        }
    }
    
    public static function createUniqId() {
       return md5(uniqid(mt_rand()));
    }
    
    public static function method_allowed($class,$method) {
        if (empty($GLOBALS['restapi_whitelist'])) return true;
        if (in_array(strtolower($method),$GLOBALS['restapi_whitelist'][strtolower($class)])) return true;
        return false;
    }



}
