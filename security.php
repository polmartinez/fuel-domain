<?php

class Domain_Security
{

    public static function readable_random_string($length = 6)
    {
        $string = '';
        $vowels = ['a', 'e', 'o', 'u'];
        $consonants = [
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'm',
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
        ];

        $max = $length / 2;
        for ($i = 1; $i <= $max; $i++) {
            shuffle($consonants);
            shuffle($vowels);
            $string .= $consonants[0];
            $string .= $vowels[0];
        }

        return $string;
    }

    public static function clean_field($value = null, $newline = false, $strip_tags = true)
    {
        (!empty($value))
            and $value = ($strip_tags ? str_replace('&nbsp;', ' ', (string) $value) : $value)
            and $value = ($strip_tags ? str_replace(array('<br />', '<br/>', '</br>', '<br>'), "\n ", $value) : $value)
            and $value = str_replace("\t", ' ', $value)
            and $value = str_replace("\r", '', $value)
            and $value = str_replace("\n \n", "\n\n", $value)
            // and $data = (get_magic_quotes_gpc() ? stripcslashes($data) : $data)
            // no usar \Security:: que reemplaza los quotes
            and $value = ($strip_tags ? \Security::xss_clean($value) : $value)
            and $value = ($strip_tags ? strip_tags($value) : $value)
            and $value = preg_replace(
                ($newline ? '/[ ]{3,}/' : '/\s+/') // 3 para markdown /br
                , ' '
                , $value
            )
            and $value = str_replace("\n\n ", "\n\n", $value)
            and $value = trim($value);

        return (string) $value;
    }

    public static function get($field, $newline = false, $strip_tags = true)
    {
        return self::clean_field(\Input::get($field), $newline, $strip_tags);
    }

    public static function post($field, $newline = false, $strip_tags = true)
    {
        return self::clean_field(\Input::post($field), $newline, $strip_tags);
    }

}
