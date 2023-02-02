<?php

class Domain_Math
{

    static function round($value, $decimals = 2)
    {
        $factor = pow(10, $decimals + 1);
        return round(intval($value * $factor) / $factor, $decimals, PHP_ROUND_HALF_ODD);
    }

    static function round_down($value, $decimals = 2)
    {
//        $factor = pow(10, $decimals);
//        return round(floor($value * $factor) / $factor, $decimals);
//        $factor = pow(10, $decimals + 1);
//        return round(intval($value * $factor) / $factor, $decimals, PHP_ROUND_HALF_UP);
        return round($value, $decimals, PHP_ROUND_HALF_ODD);
    }

    static function num_format($number, int $decimals = 0)
    {
//        if ($decimals === null) {
//            $decimals = strlen(end(explode('.', (string) $number, 2)));
//            ($decimals > 4)
//                and $decimals = 2;
//        }
        $formatter = (new \NumberFormatter(\Service_I18n::locale(), \NumberFormatter::DECIMAL));
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        return $formatter->format((float) $number);
    }

    static function round_smart($value, $decimals = 2)
    {
        $second_decimal = ($value - floor($value)) * 100;

        if ($second_decimal == 0) {

        } elseif ($second_decimal < 20) {
            $second_decimal = 99;
            $value--;
        } elseif ($second_decimal < 60) {
            $second_decimal = 00;
        } else {
            $second_decimal = 99;
        }

        $value = (intval($value) * 100 + $second_decimal) / 100;

        return round($value, $decimals);
    }

}
