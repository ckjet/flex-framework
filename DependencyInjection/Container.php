<?php

namespace Hypilon\DependencyInjection;

class Container
{
    private static $registry;

    public static function get($var, $defaultValue = null)
    {
        return self::$registry[$var] ?? $defaultValue;
    }

    public static function set($var, $value)
    {
        self::$registry[$var] = $value;
    }

    public static function all()
    {
        return self::$registry;
    }

    public static function setArray($array)
    {
        foreach($array as $var => $value) {
            self::$registry[$var] = $value;
        }
    }
}