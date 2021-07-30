<?php

    include_once 'BaseFunction.php';

    class Notification extends BaseFunction {

        public $id;
        public $userId;
        public $message;
        public $readStatus;
        public $type;
        public $typeid;
        private $dbConnection;
        private $tableName = 'notification';

        public function __construct($dbObject)
        {
            $this -> dbConnection = $dbObject;
        }

        public function createNotification()
        {
            $query = $this -> dbConnection -> prepare(
                "INSERT INTO ".$this -> tableName." (id, userid, message, type, typeid, read_status, date_created, time_created) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            $query -> execute([
                "",
                $this -> userId,
                $this -> message,
                $this -> type,
                $this -> typeid,
                $this -> readStatus,
            ]);

            return $query -> rowCount();
        }

        public function markNotification()
        {
            $query = $this -> dbConnection -> prepare("
                UPDATE ".$this -> tableName."
                    SET
                        read_status = :statusNum
                    WHERE
                        userid = :userId
            ");

            $statusNum = 1;
            $query -> bindParam(":statusNum", $statusNum);
            $query -> bindParam(":userId", $this -> userId);

            $query -> execute();

            return $query -> rowCount();
        }

        public function countUnreadNotification()
        {
            $zero = 0;

            $query = $this -> dbConnection -> prepare("
                SELECT COUNT(id) as count FROM ".$this -> tableName."
                WHERE 
                    read_status = :receiptStatus AND userid = :userId 
            ");

            $query -> bindParam(":receiptStatus", $zero);
            $query -> bindParam(":userId", $this -> userId);

            $query -> execute();

            return $query;
        }

        public function readAllNotifications()
        {
            $query = $this -> dbConnection -> prepare("
                SELECT * FROM ".$this -> tableName."
                WHERE
                    userid = :userId
                ORDER BY 
                    id DESC
            ");

            $query -> bindParam(":userId", $this -> userId);
            $query -> execute();

            return $query;
        }

    }