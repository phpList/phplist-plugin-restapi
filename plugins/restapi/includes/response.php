<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Common response as success and error
 * Andreas Ek, 2012-12-26.
 */
class Response
{
    private $result;

    public function __construct()
    {
        $this->result = array();
    }

    public function setError($code, $message)
    {
        $this->result['status'] = 'error';
        $this->result['type'] = 'Error';
        $this->result['data'] = array(
            'code'    => $code,
            'message' => $message,
        );
    }

    public function setData($type, $data)
    {
        $this->result['status'] = 'success';
        $this->result['type'] = $type;
        $this->result['data'] = $data;
    }

    public function output()
    {
        header('Content-Type: application/json; charset=utf-8;');
        echo $this->json_encode2($this->result);
        die(0);
    }

    /**
     * Convert an object into an associative array.
     *
     * This function converts an object into an associative array by iterating
     * over its public properties. Because this function uses the foreach
     * construct, Iterators are respected. It also works on arrays of objects.
     *
     * @return array
     */
    public function object_to_array($var)
    {
        $result = array();
        $references = array();

        // loop over elements/properties
        foreach ($var as $key => $value) {
            // recursively convert objects
            if (is_object($value) || is_array($value)) {
                // but prevent cycles
                if (!in_array($value, $references)) {
                    $result[$key] = $this->object_to_array($value);
                    $references[] = $value;
                }
            } else {
                // simple values are untouched
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Convert a value to JSON.
     *
     * This function returns a JSON representation of $param. It uses json_encode
     * to accomplish this, but converts objects and arrays containing objects to
     * associative arrays first. This way, objects that do not expose (all) their
     * properties directly but only through an Iterator interface are also encoded
     * correctly.
     */
    public function json_encode2($param)
    {
        if (is_object($param) || is_array($param)) {
            $param = $this->object_to_array($param);
        }

        return json_encode($param);
    }

    public static function outputError($e)
    {
        $response = new self();
        $response->setError($e->getCode(), $e->getMessage());
        $response->output();
    }

    public static function outputErrorMessage($message)
    {
        $response = new self();
        $response->setError(0, $message);
        $response->output();
    }

    public static function outputDeleted($type, $id)
    {
        $response = new self();
        $response->setData($type, 'Item with '.$id.' is successfully deleted!');
        $response->output();
    }

    public static function outputMessage($message)
    {
        $response = new self();
        $response->setData('SystemMessage', $message);
        $response->output();
    }
}
