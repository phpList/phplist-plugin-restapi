<?php

class phpList_RESTAPI_Common{

    static function select( $type, $sql, $single=false ){
        $response = new phpList_RESTAPI_Response();
        try {
            $db = phpList_RESTAPI_PDO::getConnection();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            if ($single && is_array($result)) $result = $result[0];
            $response->setData($type, $result);
        } catch( PDOException $e ) {
            $response->setError( $e->getCode(), $e->getMessage() );
        }
        $response->output();
    }

    static function apiUrl( $website ){

        $url = '';
        if( !empty( $_SERVER["HTTPS"] ) ){
            if($_SERVER["HTTPS"]!=="off")
                $url = 'https://'; //https
            else
                $url = 'http://'; //http
        }
        else
            $url = 'http://'; //http

        $api_url = str_replace( 'page=main&pi=restapi_test', 'page=call&pi=restapi', $_SERVER['REQUEST_URI'] );
        $api_url = str_replace( 'page=main&pi=restapi', 'page=call&pi=restapi', $api_url );

        $url = $url . $website . $api_url;
        $url = rtrim($url,'/');

        return $url;

    }

}
