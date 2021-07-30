<?php

require_once 'BaseFunction.php';

class Admin extends BaseFunction {

    private $tableName = 'admin';
    private $dbConnection;

    // table columns
    public $id;
    public $username;
    public $password;
    public $fullname;
    public $dateCreated;
    public $timeCreated;
    
    function __construct ($dbObject) {
        $this -> dbConnection = $dbObject;
    }

    public function createAmin()
    {
        # code...
        $query = $this -> dbConnection -> prepare("
            INSERT into ".$this -> tableName." 
            (id, username, password, fullname, date_created, time_created)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");

        $query -> execute ([
            "",
            $this -> username,
            $this -> password,
            $this -> fullname 
        ]);

        if ($query -> rowCount()) {
            return true;
        } else {
            return false;
        }

    }

    public function checkIfUsernameExists()
    {
        # code...
        $query = $this -> dbConnection -> prepare("
            SELECT id from ".$this -> tableName." WHERE username = :username LIMIT 0, 1;
        ");

        $query -> bindParam(':username', $this -> username);
        $query -> execute();
        $count = $query -> rowCount();
        return $count;

    }

    public function authenticateUsername()
    {
        # code...
        $query = $this -> dbConnection -> prepare("
            SELECT * from ".$this -> tableName." WHERE username = :username LIMIT 0, 1;
        ");

        $query -> bindParam(':username', $this -> username);
        $query -> execute();
        $count = $query -> rowCount();
        
        if ($count) {
            $row = $query -> fetch(PDO::FETCH_ASSOC);
            
            $this -> id = $row["id"];
            $this -> username = $row["username"];
            $this -> password = $row["password"];
            $this -> fullname = $row["fullname"];
            
            return true;
        } else {
            return false;
        }

    }

}