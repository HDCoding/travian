<?php

namespace Travian\Models;

use Travian\Libs\Database;

class Users
{
    public $db;

    public function __construct()
    {
        $this->db = Database::getInstance();

    }

    public function getUserField($ref, $field, $mode)
    {
        if (!$mode) {
            $query = "SELECT $field FROM users where id = '$ref' LIMIT 1";
        } else {
            $query = "SELECT $field FROM users where username = '$ref' LIMIT 1";
        }

        $db_array = $this->db->select($query);
        return $db_array[$field];
    }
}