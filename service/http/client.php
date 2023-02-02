<?php

// gestiona peticiones http

class Domain_Service_HTTP_Client extends \Domain_Service_Entity
{

    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_GET = 'get';
    const METHOD_DELETE = 'delete';

    static public $errors = [];
    //
    private $_client = null;

    public function client($client = null)
    {
        if ($client === null) {
            return $this->_client;
        }
        $this->_client = $client;
    }

    public static function forge(array $params = null)
    {
        return new static($params);
    }

}
