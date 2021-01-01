<?php

namespace Travian\Libs;

class Validation
{
    public $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function isEmpty($data)
    {
        if (is_array($data)) {
            return empty($data);
        } elseif ($data == "") {
            return true;
        } else {
            return false;
        }
    }

    public function validEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        return true;
    }

    public function userExist($username)
    {
        return $this->exist("users", "username", $username);
    }

    public function emailExist($email)
    {
        return $this->exist("users", "email", $email);
    }

    public function userAlfNum($username)
    {
        return ctype_alnum($username) ? true : false;
    }

    public function validDate($date, $format = "Y-m-d")
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function validKey($key)
    {
        if (strlen($key) != 40) {
            return false;
        }

        $result = $this->db->select1("SELECT * FROM `users` WHERE `codeactivation` = :code", ["code" => $key]);

        if (count($result) !== 1) {
            return false;
        }

        if ($result->codeactivation == null) {
            return false;
        }

        return true;
    }

    /**
     * Private area
     */
    private function exist($table, $column, $value)
    {
        $db = Database::getInstance();
        $result = $db->select("SELECT * FROM `$table` WHERE `$column` = :value", ["value" => $value]);
        return count($result) > 0 ? true : false;
    }
}