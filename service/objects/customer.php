<?php

    require_once 'BaseFunction.php';

    class Customer extends BaseFunction{
        
        private $dbConnection;
        private $tableName = 'customer';

        // customer table columns
        public $id;
        public $userId;
        public $fullName;
        public $email;
        private $password;
        public $profilePicture;
        public $phone;
        public $lastSeen;
        public $activeStatus;
        public $firstTimeAccess;


        public function __construct ($dbObject) {
            $this -> dbConnection = $dbObject;
        }

        public function createNewInvestor () {

            $query = $this -> dbConnection -> prepare("
                INSERT into ".$this -> tableName." 
                (id, userid, fullname, email, phone_number, password, last_seen, active_status, first_time_access, date_added)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $query -> execute ([
                "",
                "",
                "",
                $this -> email,
                $this -> phone,
                $this -> password,
                "",
                "1",
                "0"
            ]);

            if ($query) {
                
                $lastInsertedId = $this -> dbConnection -> lastInsertId();
                $hashedPassword = sha1($lastInsertedId);

                $query = $this -> dbConnection -> prepare (
                    "UPDATE ".$this -> tableName." SET userid = ? WHERE id = ? LIMIT 1"
                );

                $query -> execute ([$hashedPassword, $lastInsertedId]);

                if ($query) {
                    return true;
                } else {
                    return false;
                }
            
            } else {
                return false;
            }
        }

        public function getAllInvestors ()
        {
            $query = $this -> dbConnection -> prepare (
                "SELECT * FROM ".$this -> tableName." ORDER BY id DESC"
            );

            $query -> execute();

            return $query;
        }

        public function updatePassword()
        {
            $query = $this -> dbConnection -> prepare("
                UPDATE ".$this -> tableName."
                    SET 
                        password = :password
                    WHERE
                        userid = :userId
                    LIMIT 1;
            ");

            $query -> bindParam(":password", $this -> password);
            $query -> bindParam(":userId", $this -> id);

            $query -> execute();

            return $query -> rowCount();
        }

        public function updateProfile()
        {
            # code...
            $firstTimeAccess = 1;

            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." SET fullname = :fullname, email = :email, phone_number = :phone_number, profile_picture = :profile_picture, first_time_access = :first_time_access WHERE userid = :userid LIMIT 1"
            );

            $query -> bindParam(':fullname', $this -> fullName);
            $query -> bindParam(':email', $this -> email);
            $query -> bindParam(':phone_number', $this -> phone);
            $query -> bindParam(':profile_picture', $this -> profilePicture);
            $query -> bindParam(':first_time_access', $firstTimeAccess);
            $query -> bindParam(':userid', $this -> id);

            $query -> execute();
            return $query -> rowCount();

        }

        public function CheckEmailExistence($email) {
            $query = $this -> dbConnection -> prepare("SELECT id FROM ".$this -> tableName." WHERE email = ? LIMIT 1");
            $query -> execute([$email]);
            $count = $query -> rowCount();

            return $count;

        }

        public function checkPhoneNumberExistence($phoneNumber) {
            $query = $this -> dbConnection -> prepare("SELECT id FROM ".$this -> tableName." WHERE phone_number = ? AND userid != ? LIMIT 1");
            $query -> execute([$phoneNumber, $this -> id]);
            $count = $query -> rowCount();

            return $count;

        }

        public function authenticatePhoneNumber()
        {
            # code...
            $query = $this -> dbConnection -> prepare("
                SELECT * from ".$this -> tableName." WHERE phone_number = :phoneNumber LIMIT 1;
            ");

            $query -> bindParam(':phoneNumber', $this -> phone);
            $query -> execute();
                
            if ($query -> rowCount()) {
                $row = $query -> fetch(PDO::FETCH_ASSOC);
                
                $this -> id = $row["userid"];
                $this -> fullName = $row["fullname"];
                $this -> email = $row["email"];
                $this -> password = $row["password"];
                $this -> phone = $row["phone_number"];
                $this -> profilePicture = $row["profile_picture"];
                $this -> lastSeen = $row["last_seen"];
                $this -> activeStatus = $row["active_status"];
                $this -> firstTimeAccess = $row["first_time_access"];
                
                return true;
            } else {
                return false;
            }
        }

        public function getCustomerData ($userId) { 

            # code...
            $query = $this -> dbConnection -> prepare("
                SELECT * from ".$this -> tableName." WHERE userid = :userId LIMIT 0, 1;
            ");
            $query -> bindParam(':userId', $userId);
            $query -> execute();
            
            $row = $query -> fetch(PDO::FETCH_ASSOC);
            return $row;
        }

        public function getCustomerDataWithEmail ($email) { 

            # code...
            $query = $this -> dbConnection -> prepare("
                SELECT * from ".$this -> tableName." WHERE email = :email LIMIT 0, 1;
            ");
            $query -> bindParam(':email', $email);
            $query -> execute();
            
            $row = $query -> fetch(PDO::FETCH_ASSOC);
            return $row;
        }

        public function getPassword()
        {
            # code...
            return $this -> password;
        }

        public function setPassword($password)
        {
            # code...
            return $this -> password = $password;
        }

        public function updateNewPassword($newPassword, $customerUserId) {
            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." SET password = :password WHERE userid = :userID LIMIT 1"
            );
            $query -> bindParam(':password', $newPassword);
            $query -> bindParam(':userID', $customerUserId);

            $query -> execute();
            return $query -> rowCount();
        }

        public function activateInvestor() 
        {
                        
            $statusNumber = 1;

            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." SET active_status = :statusNumber WHERE userid = :userID LIMIT 1"
            );
            $query -> bindParam(':statusNumber', $statusNumber);
            $query -> bindParam(':userID', $this -> userId);

            $query -> execute();
            return $query -> rowCount();
            
            
           
        }

        public function deactivateInvestor() 
        {
           
            $statusNumber = 0;

            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." SET active_status = :statusNumber WHERE userid = :userID LIMIT 1"
            );
            $query -> bindParam(':statusNumber', $statusNumber);
            $query -> bindParam(':userID', $this -> userId);

            $query -> execute();
            return $query -> rowCount();
            
            
           
        }
    
        public function updateLastSeen()
        {
            # code...
            $lastSeen = $this -> lastSeen;

            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." SET last_seen = :lastSeen WHERE userid = :userID LIMIT 1"
            );
            $query -> bindParam(':lastSeen', $lastSeen);
            $query -> bindParam(':userID', $this -> id);

            $query -> execute();
            return $query -> rowCount();

        }


        public function allCustomersCount()
        {
            $query = $this -> dbConnection -> prepare("
                SELECT COUNT(id) as users FROM ".$this -> tableName."
            ");

            $query -> execute();

            return $query;
        }

        public function getInvestorData($userId)
        {
            $query = $this -> dbConnection -> prepare(
                "
                    SELECT * FROM ".$this -> tableName."
                        WHERE
                            userid = :userId
                        LIMIT 1
                "
            );

            $query -> bindParam(":userId", $userId);
            $query -> execute();

            return $query;
        }

        public function customerSearch ($searchString) 
        {

            $searchString = '%'.$searchString.'%';

            $query = $this -> dbConnection -> prepare(
                "
                    SELECT id, fullname, userid, profile_picture
                        FROM ".$this -> tableName."
                            WHERE
                                fullname LIKE :searchString
                                    ORDER BY
                                        id
                                            DESC LIMIT 5;
                "
            );

            $query -> bindParam(":searchString", $searchString);
            $query -> execute();

            return $query;
        }

    }