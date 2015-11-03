<?php

namespace Rapi;

class Common {

    // Extended pdo object
    protected $pdoEx;

    public function __construct( PdoEx $pdoEx, Response $response )
    {
        $this->pdoEx = $pdoEx;
    }

    /**
     * Generate a URL for executing API calls
     * @param [type] $website [description]
     */
    public function apiUrl( $website, $pageRoot, $adminDir )
    {
        $protocol = '';
        // If server is using SSL rewrite URI accordingly
        if( !empty( $_SERVER['HTTPS'] ) ) {
            if( $_SERVER['HTTPS'] !== 'off' ) {
                $protocol = 'https://'; //https
            } else {
                $protocol = 'http://'; //http
            }
        } else {
            $protocol = 'http://'; //http
        }

        // Generate the path plus get vars
        $path = $pageRoot . $adminDir . '?page=call&pi=restapi2';

        $url = $protocol . $website . $path;
        $trimmedUrl = rtrim( $url, '/' );

        return $trimmedUrl;
    }

}
