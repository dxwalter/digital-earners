<?php
	
	require_once 'BaseFunction.php';
	class PasswordRecovery extends BaseFunction{

		private $dbConnection;
        private $tableName = 'password_recovery';

        // customer table columns
        public $id;
        public $userId;
        public $timer;
        public $dateadded;


        public function __construct ($dbObject) {
            $this -> dbConnection = $dbObject;
        }

        public function createRecovery()
        {
        	$query = $this -> dbConnection -> prepare("
                INSERT into ".$this -> tableName." 
                (id, userid, timer, dateadded)
                VALUES (?, ?, ?, NOW())
            ");

            $query -> execute([
            	"",
            	$this -> userId,
            	$this -> timer,
            ]);

            if ($query) {
            	return true;
            } else {
            	return false;
            }
        }

        public function getRecoveryDetials($customerUserId) {
        	 $query = $this -> dbConnection -> prepare("
                SELECT * from ".$this -> tableName." WHERE userid = :userId ORDER BY id DESC LIMIT 0, 1;
            ");
            $query -> bindParam(':userId', $customerUserId);
            $query -> execute();
            
            $row = $query -> fetch(PDO::FETCH_ASSOC);
            return $row;
        }

        public function deleteRecovery($customerUserId) {
        	$query = $this -> dbConnection -> prepare ("
        		DELETE FROM  ".$this -> tableName." WHERE userid = :userId
        	");

        	$query -> bindParam(':userId', $customerUserId);
            $query -> execute();
        }

	}