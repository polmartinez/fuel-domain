<?php

class Domain_Service_HTTP_Session extends \Domain_Service_Entity
{

    static public function deleteAll()
    {
        \Session::destroy();
    }

    static public function deleteKey(string $field)
    {
        \Session::delete($field);
    }

    static public function write()
    {
        \Session::close(true);
    }

    static public function setKey(string $field, $value, $append = false)
    {
        if ($append) {
            $values = [$value];
            $pre = (array) \Session::get($field);
            if (isset($pre)) {
                $values = array_merge($pre, $values);
            }
            \Session::set($field, $values);
        } else {
            \Session::set($field, $value);
        }
    }

    static public function getKey(string $field, $default = null)
    {
        return \Session::get($field, $default);
    }

}
