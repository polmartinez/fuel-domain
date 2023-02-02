<?php

define('CRUD_INT', 'integer');
define('CRUD_DECIMAL', 'decimal');
define('CRUD_BIT', 'bit');
define('CRUD_BIT_0', 0);
define('CRUD_BIT_1', 1);

class Domain_Model_Crud extends Fuel\Core\Model_Crud
{

    protected static $_cache_data = [];
    protected static $_connection = null;
    protected $cached = false;

    const bit_active = 1;
    const bit_unactive = 0;

    public static $query_params = [];

    public function cached($cache = null)
    {
        if (is_null($cache)) {
            return $this->cached;
        }

        $this->cached = $cache ? ($cache === true ? 600 : (int) $cache) : false;
        return $this;
    }

    public static function to_objects_array($result)
    {
        if (is_object($result)) {
            return (Object) $result->to_array();
        } else {
            foreach ($result as $key => $item) {
                $result[$key] = (Object) $item->to_array();
            }
            return $result;
        }
    }

    protected static function post_find($result)
    {

        if (!empty($result)) {

            if (!empty(static::$_integer_properties)) {

                foreach ($result as $item) {
                    foreach (static::$_integer_properties as $field) {
                        (isset($item->$field))
                            and $item->$field = (int) $item->$field;
                    }
                }
            }

            if (!empty(static::$_properties_type)) {

                foreach ($result as $item) {
                    foreach (static::$_properties_type as $field => $type) {
                        if (isset($item->$field)) {
                            switch ($type) {
                                case CRUD_INT:
                                    $item->$field = (int) $item->$field;
                                    break;
                                case CRUD_DECIMAL:
                                    $item->$field = (float) $item->$field;
                                    break;
                                case CRUD_BIT:
                                    $item->$field = $item->$field ? self::bit_active : self::bit_unactive;
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Called right after values retrieval, before save,
     * update, setting defaults and validation.
     *
     * @param   array  $values  input array
     * @return  array
     */
    protected function prep_values($values)
    {

        foreach (static::$_properties as $_property) {
            (empty($values[$_property]) && (!isset($values[$_property]) || ($values[$_property] !== 0 && $values[$_property] !== '0')))
                and $values[$_property] = null;
        }

        empty($values['created_microtime'])
            and $values['created_microtime'] = (int) (microtime(true) * 1000);

        $values['updated_datetime'] = (string) \DateTime_Time::forge();

        empty($values['created_datetime'])
            and $values['created_datetime'] = $values['updated_datetime'];

        in_array('created_date', static::$_properties) && empty($values['created_date'])
            and $values['created_date'] = (string) \DateTime_Date::forge();

        in_array('hash', static::$_properties) && empty($values['hash'])
            and $values['hash'] = self::create_hash();

        (!empty($values['active']))
            and $values['active'] = self::bit_active
            or $values['active'] = self::bit_unactive;

        if (!empty(static::$_defaults)) {
            foreach (static::$_defaults as $field => $default) {
                (!isset($values[$field]) || $values[$field] == '')
                    and $values[$field] = $default;
            }
        }
        return $values;
    }

    public static function set_table_name_data_suffix($prefix, $create = false)
    {

        if (is_array($prefix)) {
            @list($prefix, $connection) = $prefix;
        }

        (empty($connection) || $connection == 'default')
            and $connection = 'default_data';

        static::$_connection = $connection;
        static::$_table_name = $prefix . '_' . static::$_table_name_base;
    }

    public static function get_table_name_base()
    {
        if (empty(static::$_table_name_base)) {
            return static::$_table_name;
        }
        return static::$_table_name_base;
    }

    public static function get_connection_name()
    {
        return static::$_connection;
    }

    public static function get_class_name()
    {
        return get_called_class();
    }

    public static function get_primary_key()
    {
        return static::$_primary_key;
    }

    public static function get_table_name()
    {
        return static::$_table_name;
    }

    public static function get_field($field = null, $alias = null)
    {
        if ($alias) {
            // si usa un alias, no se puede devolver igual
            // ya que se quotea como si fuera un campo y tabla
            $field = $alias . '.' . \Db::identifier($field);
            $field = \DB::Expr($field);
            return $field;
        } else {
            return self::get_table_name() . (empty($field) ? '' : '.' . $field);
        }
    }

    protected function array_model_to_objects($array)
    {
        foreach ($array as $key => $item) {
            $array[$key] = (Object) $item->to_array();
        }
        return $array;
    }

    public static function create_hash($data = null, $max_size = 25)
    {

        if (empty($data)) {
            $data = uniqid('', true);
        }

        if (is_scalar($data)) {
            $code = md5((string) $data);
        } else {
            $code = md5(json_encode($data));
        }

        $hash = base_convert($code, 16, 36);

        // $max_size en base 36 son 25 chars en vez de 32 de md5
        // $max_size en base 36 son 32 chars en vez de 40 de sha1
        // si es distinta completamos con 0
        if (strlen($hash) != $max_size) {
            $hash = str_repeat('0', 25) . $hash;
            if (strlen($hash) > $max_size) {
                $hash = substr($hash, $max_size * -1);
            }
        }
        return $hash;
    }

}
