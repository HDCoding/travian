<?php

namespace Travian\Libs;

class Cookie
{
    public function __construct() { }

    private function __clone() { }

    public static function exists($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    public static function get($name)
    {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        } else {
            return $name;
        }
    }

    public static function set($name, $value, $expiry)
    {
        if (setcookie($name, $value, time() + $expiry, "/")) {
            return true;
        }
        return false;
    }

    public static function delete($name)
    {
        self::set($name, "", time() - 1);
    }
}