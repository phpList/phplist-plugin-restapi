<?php

class phpList_API_Common{

    static function select( $type, $sql, $single=false ){
        $response = new phpList_API_Response();
        try {
            $db = phpList_API_PDO::getConnection();
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

        $api_url = str_replace( 'page=main&pi=api_test', 'page=call&pi=api', $_SERVER['REQUEST_URI'] );
        $api_url = str_replace( 'page=main&pi=api', 'page=call&pi=api', $api_url );

        $url = $url . $website . $api_url;
        $url = rtrim($url,'/');

        return $url;

    }

}
