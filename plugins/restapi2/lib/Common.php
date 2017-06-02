<?php

namespace Rapi;

class Common {
    /**
     * @var PdoEx
     */
    protected $pdoEx;

    /**
     * Common constructor.
     *
     * @param PdoEx $pdoEx
     * @param Response $response
     */
    public function __construct( PdoEx $pdoEx, Response $response )
    {
        $this->pdoEx = $pdoEx;
    }

    /**
     * Generate a URL for executing API calls
     *
     * @param string $website
     * @param string $pageRoot
     * @param string $adminDir
     *
     * @return string
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
