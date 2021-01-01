<?php

namespace Travian\Libs;

class Token
{
    private static $token_name = 'csrf-token';

    public function __construct()
    {
    }

    public static function generate()
    {
        $token = self::$token_name;

        return Session::set($token, sha1_gen());
    }

    public static function check($token)
    {
        $token_name = self::$token_name;

        if (Session::get($token_name) && $token === Session::get($token_name)) {
            Session::destroy($token_name);
            return true;
        }
        return false;
    }
}