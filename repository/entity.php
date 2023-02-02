<?php

class Domain_Repository_Entity extends Domain_Entity
{

    protected $properties = [];
    protected $cached = false;

    public function cached($cache = null)
    {
        if (is_null($cache)) {
            return $this->cached;
        }

        $this->cached = $cache ? ( $cache === true ? 600 : (int) $cache) : false;
        return $this;
    }

    public function property($field, $value = null)
    {
        if (is_null($value)) {
            return \Arr::get($this->properties, $field);
        }
        \Arr::set($this->properties, $field, $value);
        return true;
    }

    protected static function get_cache_key()
    {
        return \Domain_Model_Cache::forge('repository', __CLASS__);
    }

}
