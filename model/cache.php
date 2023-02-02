<?php

class Domain_Model_Cache
{

    const lifetime = 2592000;
    const daily = 86400;
    const hourly = 3600;
    const quarter = 600;
    const t5m = 300;
    const t1m = 60;

    public static function forge($identify, $id, $values = null)
    {
        return \Inflector::friendly_title(
                $identify . '-' . (is_array($id) ? implode('-', $id) : $id)
                , '-', true)
            . (empty($values) ? '' : ('.' . md5(
                (is_object($values) and is_subclass_of($values, 'Database_Query')) ?
                $values->compile() :
                \Format::forge((array) $values)->to_json()
            ))
            ) // best performance to encode
        ;
    }

}
