<?php

namespace Travian\Utils;

class Form
{
    public $value_array = [];
    private $error_array = [];
    private $error_count;

    public function __construct()
    {
        if (isset($_SESSION['error_array']) && isset($_SESSION['value_array'])) {
            $this->error_array = $_SESSION['error_array'];
            $this->value_array = $_SESSION['value_array'];
            $this->error_count = count($this->error_array);

            unset($_SESSION['error_array']);
            unset($_SESSION['value_array']);
        } else {
            $this->error_count = 0;
        }
    }

    public function addError($field, $error)
    {
        $this->error_array[$field] = $error;
        $this->error_count = count($this->error_array);
    }

    public function getError($field)
    {
        if (array_key_exists($field, $this->error_array)) {
            return $this->error_array[$field];
        } else {
            return "";
        }
    }

    public function getValue($field)
    {
        if (array_key_exists($field, $this->value_array)) {
            return $this->value_array[$field];
        } else {
            return "";
        }
    }

    public function getDiff($field, $cookie)
    {
        if (array_key_exists($field, $this->value_array) && $this->value_array[$field] != $cookie) {
            return $this->value_array[$field];
        } else {
            return $cookie;
        }
    }

    public function getRadio($field, $value)
    {
        if (array_key_exists($field, $this->value_array) && $this->value_array[$field] == $value) {
            return "checked";
        } else {
            return "";
        }
    }

    public function returnErrors()
    {
        return $this->error_count;
    }

    public function getErrors()
    {
        return $this->error_array;
    }
}