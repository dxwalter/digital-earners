<?php

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';
    require_once '../../objects/notification.php';

    /**
     * JWT FILES WILL BE INCLUDED HERE
     */
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();

    // create customer object from customer class
    $customerObject = new Customer($dbObject);

    // create investment object
    $investmentObject = new Investment($dbObject);

    $today = time();
 
    $getListsOfPayableInvestors = $investmentObject -> getListOfPayableInvestors($today);

    $recordCount = $getListsOfPayableInvestors -> rowCount();
    $receiversArray = ["recordCount" => $recordCount];

    $receiversArray["records"]=array();

    if ($recordCount > 0) {

        while ($row = $getListsOfPayableInvestors->fetch(PDO::FETCH_ASSOC)){

            extract($row); 

            $cashoutDate = date_create(date('d-m-Y', $binary_date));
            $totalPay = $capital + $roi;

            $payDayData = [
                "investmentId" => $id,
                "capital" => $capital,
                "roi" => $roi,
                "uploadDate" => $upload_date,
                "uploadTime" => $upload_time,
                "cashOutDate" => date_format($cashoutDate, "l j F, Y"),
                "proofOfPayment" => $proof_of_payment,
                "receiptStatus" => $receipt_status,
                "roiReceived" => $roi_payment_status,
                "userId" => $userid,
                "profilePicture" => $profile_picture,
                "fullname" => $fullname,
                "totalPay" => $totalPay
            ];

            array_push($receiversArray["records"], $payDayData);

        }

        // set response code - 200 OK
        http_response_code(200);
    
        // show products data in json format
        echo json_encode($receiversArray);

    } else {
        $customerObject -> apiStatusCode(200, "No investor is to be paid today", "false");
    }
