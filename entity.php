<?php

class Domain_Entity
{

    private $_params = [];

    function __construct(array $params = null)
    {
        if (!empty($params)) {
            $this->_params = $params;
        }
    }

    public function param($field, $default = null)
    {
        return \Arr::get($this->_params, $field, $default);
    }

    public function params()
    {
        return $this->_params;
    }

}
