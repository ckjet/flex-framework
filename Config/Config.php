<?php

namespace Hypilon\Config;

class Config
{
    private static $config;
    public static function get($var, $defaultValue = null)
    {
        return self::$config[$var] ?? $defaultValue;
    }

    public static function set($var, $value)
    {
        self::$config[$var] = $value;
    }

    public static function all()
    {
        return self::$config;
    }

    public static function setArray($array)
    {
        foreach($array as $var => $value) {
            self::$config[$var] = $value;
        }
    }
}