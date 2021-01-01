<?php

namespace Travian\Libs;

use PDO;
use PDOException;

class Database extends PDO
{
    /**
     * @var Database of Database class itself
     */
    private static $instance;

    public function __construct($DB_TYPE, $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS)
    {
        try {
            parent::__construct($DB_TYPE . ':host=' . $DB_HOST . ';port=' . $DB_PORT . ';dbname=' . $DB_NAME . ';charset=UTF8', $DB_USER, $DB_PASS);
            // comente esta linha se você não quiser relatórios de erros
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->exec("SET CHARACTER SET utf8");
        } catch (PDOException $exc) {
            echo "<b>Connection error:</b> " . $exc->getMessage() . "<br />";
            echo "<br /> PHP Version : " . phpversion() . "<br />";
            exit();
        }
    }

    private function __clone() { }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self(DB_TYPE, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS);
        }
        return self::$instance;
    }

    public function select($sql, $array = [], $fetchMode = PDO::FETCH_OBJ)
    {
        $db = self::getInstance();

        $sth = $db->prepare($sql);

        foreach ($array as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }

    public function select1($sql, $array = array())
    {
        $db = self::getInstance();

        $sth = $db->prepare($sql);

        foreach ($array as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
        return $sth->fetchObject();
    }

    public function insert($table, $data = [])
    {
        $db = self::getInstance();

        ksort($data);

        $fileNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sth = $db->prepare("INSERT INTO $table (`$fileNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    public function update($table, $data, $where, $whereBindArray = array())
    {
        $db = self::getInstance();

        ksort($data);

        $fieldDetails = null;

        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key` = :$key,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $db->prepare("UPDATE $table SET $fieldDetails WHERE $where");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        foreach ($whereBindArray as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    public function delete($table, $where, $bind = [], $limit = 1)
    {
        $db = self::getInstance();

        $sth = $db->prepare("DELETE FROM $table WHERE $where LIMIT $limit");

        foreach ($bind as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

}