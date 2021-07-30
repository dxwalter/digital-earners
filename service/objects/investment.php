<?php

    include_once 'BaseFunction.php';

    class Investment extends BaseFunction{

        public $id;
        public $userId;
        public $capital;
        public $roi;
        public $binaryDate = 0;
        public $dateOfUpload;
        public $timeOfUpload;

        /**
         * The profile picture here stands for proof of payment.
         * Because of the imageProcessor library that collects 
         * an object of a class a paramenter, I had to set the proof of 
         * payment to profile picture which is the same in the customer class.
         */
        public $profilePicture;
        public $receiptStatus;
        public $roiPaymentStatus;

        private $dbConnection;
        private $tableName = 'investment';

        public function __construct($dbObject)
        {
            $this -> dbConnection = $dbObject;
        }

        public function createInvestment()
        {
            $queryString = "INSERT INTO ".$this -> tableName." (id, userid, capital, roi, upload_date, upload_time, binary_date, proof_of_payment, receipt_status, roi_payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $query = $this -> dbConnection -> prepare($queryString);


            $query -> execute([
                "",
                $this -> userId,
                $this -> capital,
                $this -> roi,
                $this -> dateOfUpload,
                $this -> timeOfUpload,
                $this -> binaryDate,
                $this -> profilePicture,
                $this -> receiptStatus,
                $this -> roiPaymentStatus
            ]);

            return $query -> rowCount();

        }

        public function getPaymentDate()
        {

            $sixteenDays = 17 * 86400;

            $currentTime = [
                "hour" => date('H'),
                "min" => date("i")
            ];

            $currenthour = $currentTime["hour"];
            $currentMinute = $currentTime["min"];          

            if ($currentMinute > 0) {
                $currentMinute = $currentMinute + (60 - $currentMinute);
            } else {
                $currentMinute = 0;
            }

            if ($currentMinute == 60) {
                $currenthour = $currenthour + 1;
            }

            if ($currenthour > 0) {
                $currenthour = (23 - $currenthour) + 1;
            } else {
                $currenthour = 0;
            }

            // convert current hour to seconds
            $currenthourToSecond = (60 * $currenthour) * 60;

            // Payday
            $payday = strtotime('now') + $sixteenDays + $currenthourToSecond;

            // return $payday;
            $dateToPay = explode(" ", date("l j m Y H i s A", $payday));

            $yearTopay = $dateToPay[3];
            $monthTopay = $dateToPay[1];
            $dayToPay = $dateToPay[2];

            $mainPayDay = mktime(0, 0, 0, $dayToPay, $monthTopay, $yearTopay);
            
            return $mainPayDay;


            
            // Take note of this script, it's what you'll use in knowing whren to pay
            // $dateTopay = date_create(date('d-m-Y', $payday));
            // echo date_format($dateTopay, "l j F, Y H:i:s");
            // $dateToPay = print_r(explode(" ", date("l j F Y H i s A", $payday)));
            // print_r($dateToPay);
            // die();
        
        }

        public function calculatepaydayremainin()
        {
            # code...
                        // 
            // $now = strtotime('now');

            // $countdayUnix = $payday - $now;

            // echo floor($countdayUnix / 86400);
        }

        public function confirmInvestment()
        {
            $query = $this -> dbConnection -> prepare("
                UPDATE ".$this -> tableName."
                SET binary_date = :binaryDate, receipt_status = :confirmReceipt WHERE id = :id AND userid = :userId LIMIT 1
            ");

            $confirmReceipt = 1;

            $query -> bindParam(":binaryDate", $this -> binaryDate);
            $query -> bindParam(":confirmReceipt", $confirmReceipt);
            $query -> bindParam(":id", $this -> id);
            $query -> bindParam(":userId", $this -> userId);

            $query -> execute();

            return $query -> rowCount();

        }

        public function getInvestmentDetails($investmentId)
        {
            $query = $this -> dbConnection -> prepare(
                "SELECT * FROM  ".$this -> tableName." WHERE id = :id LIMIT 1"
            );
            $query -> bindParam(":id", $investmentId);

            $query -> execute();

            if ($query -> rowCount()) {
                $row = $query -> fetch(PDO::FETCH_ASSOC);

                return $row;
            } else {
                return false;
            }
            
        }

        public function paymentCashout()
        {

            $paymentStatus = 1;
            $query = $this -> dbConnection -> prepare(
                "UPDATE ".$this -> tableName." 
                    SET 
                        roi_payment_status = :paymentStatus
                    WHERE
                        userid = :userId AND id = :id
                    LIMIT 1
                "
            );

            $query -> bindParam(":paymentStatus", $paymentStatus);
            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":id", $this -> id);

            $query -> execute();

            return $query -> rowCount();

        }

        public function getUncategorisedHistory($count) 
        {
            if ($count == "all") {
                $limit = "";
            } else {
                $limit = "LIMIT ".$count."";
            }

            $one = 1;

            $query = $this -> dbConnection -> prepare("
                SELECT * FROM ".$this -> tableName."
                    WHERE userid = :userId AND receipt_status = :receiptStatus ORDER BY id DESC ".$limit."
            ");

            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":receiptStatus", $one);

            $query -> execute();

            return $query;
        }


        public function getRunningHistory()
        {
            // This method is to get all the 
            // investment that is still running
            // the criteria will be when roi_payment_status field
            // is equal to zero

            $zero = 0;
            $one = 1;

            $query = $this -> dbConnection -> prepare("
                SELECT * FROM ".$this -> tableName."
                    WHERE
                        userid = :userId AND roi_payment_status = :zero AND receipt_status = :receiptStatus
                    ORDER BY id DESC
            ");


            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":zero", $zero);
            $query -> bindParam(":receiptStatus", $one);

            $query -> execute();

            return $query;
        }

        public function getCompletedHistory()
        {
            // This method is to get all the 
            // investment that is still running
            // the criteria will be when roi_payment_status field
            // is equal to zero

            $one = 1;

            $query = $this -> dbConnection -> prepare("
                SELECT * FROM ".$this -> tableName."
                    WHERE
                        userid = :userId AND roi_payment_status = :one
                    ORDER BY id DESC
            ");


            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":one", $one);

            $query -> execute();

            return $query;
        }

        public function latestRunningInvestment()
        {   

            $zero = 0;

            $query = $this -> dbConnection -> prepare(
                "
                    SELECT capital, roi, id FROM ".$this -> tableName."
                    WHERE
                        userid = :userId AND roi_payment_status = :zero
                    ORDER BY id DESC LIMIT 1
                "
            );

            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":zero", $zero);

            $query -> execute();

            return $query;
        }

        public function totalRoiToBePaid()
        {
            $receiptStatus = 1;
            $roiPaymentStatus = 0;

            $query = $this -> dbConnection -> prepare(
                "
                SELECT SUM(roi) FROM ".$this -> tableName."
                WHERE receipt_status = :receiptStatus AND roi_payment_status = :roiPaymentStatus"
                
            );

            $query -> bindParam(":receiptStatus", $receiptStatus);
            $query -> bindParam(":roiPaymentStatus", $roiPaymentStatus);

            $query -> execute();

            return $query;
            
        }
        
        public function totalRoiEarned()
        {
            $receiptStatus = 1;
            $roiPaymentStatus = 1;

            $query = $this -> dbConnection -> prepare(
                "
                SELECT SUM(roi) FROM ".$this -> tableName."
                    WHERE  
                            userid = :userId 
                        AND 
                            receipt_status = :receiptStatus 
                        AND 
                            roi_payment_status = :roiPaymentStatus
                "
            );

            $query -> bindParam(":userId", $this -> userId);
            $query -> bindParam(":receiptStatus", $receiptStatus);
            $query -> bindParam(":roiPaymentStatus", $roiPaymentStatus);

            $query -> execute();

            return $query;         
        }

        public function totalInvestmentCapital()
        {
            $query = $this -> dbConnection -> prepare(
                "SELECT SUM(capital) FROM ".$this -> tableName.""
            );

            $query -> execute();

            return $query;
            
        }


        public function totalUserInvestmentCapital($investorId)
        {
            $receiptStatus = 1;

            $query = $this -> dbConnection -> prepare(
                "
                    SELECT SUM(capital) FROM ".$this -> tableName."
                    WHERE userid = :userId AND receipt_status = :receiptStatus
                "
            );


            $query -> bindParam(":receiptStatus", $receiptStatus);
            $query -> bindParam(":userId", $investorId);
            $query -> execute();

            return $query;
            
        }


        public function totalActiveInvestment()
        {
            $receiptStatus = 1;
            $roiPaymentStatus = 0;

            $query = $this -> dbConnection -> prepare(
                "SELECT SUM(capital) FROM ".$this -> tableName." WHERE receipt_status = :receiptStatus AND roi_payment_status = :roiPaymentStatus"
            );

            $query -> bindParam(":receiptStatus", $receiptStatus);
            $query -> bindParam(":roiPaymentStatus", $roiPaymentStatus);

            $query -> execute();

            return $query;
            
        }

        public function totalUnconfirmedInvestments()
        {
            $zero = 0;

            $query = $this -> dbConnection -> prepare("
                SELECT COUNT(id) as count FROM ".$this -> tableName."
                WHERE 
                    receipt_status = :receiptStatus
            ");

            $query -> bindParam(":receiptStatus", $zero);
            $query -> execute();

            return $query;
        }

        public function deleteInvestment()
        {
            $query = $this -> dbConnection -> prepare("
                DELETE FROM ".$this -> tableName."
                    WHERE 
                        id = :investmentId AND userid = :investorId
                    LIMIT 1
            ");

            $query -> bindParam(":investmentId", $this -> id);
            $query -> bindParam(":investorId", $this -> userId);

            $query -> execute();

            return $query -> rowCount();

        }

        public function getListOfPayableInvestors($today)
        {

            $binaryDate = $today;
            $roiStatus = 0;
            $invReceiptStat = 1;

            $query = $this -> dbConnection -> prepare(
                "
                    SELECT 
                        customer.fullname, customer.profile_picture, investment.*
                    FROM
                        investment
                    INNER JOIN
                        customer 
                    ON 
                        investment.userid = customer.userid
                    WHERE
                        investment.binary_date <= :binaryDate AND `roi_payment_status` = :roiStatus AND investment.receipt_status = :invReceiptStat
                    ORDER BY
                        investment.id ASC
                "
            );

            $query -> bindParam(":binaryDate", $binaryDate);
            $query -> bindParam(":roiStatus", $roiStatus);
            $query -> bindParam(":invReceiptStat", $invReceiptStat);

            $query -> execute();

            return $query;

        }

        public function getUnconfirmedInvestments($countType)
        {
            $zero = 0;

            if ($countType == "all") {
                $queryString = "
                    SELECT 
                        customer.fullname, customer.profile_picture, investment.*
                    FROM
                        investment
                    INNER JOIN
                        customer
                    ON 
                        investment.userid = customer.userid
                    WHERE
                        investment.receipt_status = :zero
                    ORDER BY 
                        investment.id ASC
                ";
            } else{
                $queryString = "
                    SELECT 
                        customer.fullname, customer.profile_picture, investment.*
                    FROM
                        investment
                    INNER JOIN
                        customer
                    ON 
                        investment.userid = customer.userid
                    WHERE
                        investment.receipt_status = :zero
                    ORDER BY 
                        investment.id ASC
                    LIMIT
                        $countType
                ";
            }

            $query = $this -> dbConnection -> prepare($queryString);

            $query -> bindParam(':zero', $zero);

            $query -> execute();

            return $query;

        }

        public function getTotalNumberOfNewInvestments()    
        {
            $totalUnconfirmedInvestments = $this -> totalUnconfirmedInvestments();
            $unconfirmedRow = $totalUnconfirmedInvestments->fetch(PDO::FETCH_ASSOC);
            $summedUnconfirmedInvestment = $unconfirmedRow["count"];
            return $summedUnconfirmedInvestment;
        }

        public function getInvestmentListing($investmentDate)
        {
            $receiptStatus = 1;

            $query = $this -> dbConnection -> prepare("
               SELECT 
                    customer.fullname, customer.profile_picture, investment.*
                FROM
                    ".$this -> tableName."
                INNER JOIN
                    customer
                ON
                    investment.userid = customer.userid
                WHERE 
                    investment.upload_date = :dateOfUpload AND receipt_status = :receiptStatus
                ORDER by investment.id DESC
            ");

            $query -> bindParam(":dateOfUpload", $investmentDate);
            $query -> bindParam(":receiptStatus", $receiptStatus);
            $query -> execute();

            return $query;
        }

        public function getPayoutInvestmentListing($payoutDate)
        {
            $query = $this -> dbConnection -> prepare("
                SELECT 
                    customer.fullname, customer.profile_picture, investment.*
                FROM 
                    ".$this -> tableName."
                INNER JOIN
                    customer
                ON
                    investment.userid = customer.userid
                    WHERE 
                        investment.binary_date = :dateOfpayment
                    ORDER by investment.id DESC
            ");

            $query -> bindParam(":dateOfpayment", $payoutDate);
            $query -> execute();

            return $query;
        }
    }
