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

    // create investment object
    $investmentObject = new Investment($dbObject);

    // retrieve customer data from user input
    $historyInput = json_decode(file_get_contents("php://input"));

    $jwt = isset($historyInput->jwt) ? $historyInput->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        }

    }


    $investorId = htmlspecialchars(strip_tags($historyInput -> investor));
    $historyType = htmlspecialchars(strip_tags($historyInput -> type));
    $count = htmlspecialchars(strip_tags($historyInput -> count));

    $investmentObject -> userId = $investorId;

    if ($historyType == "uncategorised") {
        // all investment history - whether running or completed
        // take count into account
        $getInvestmentHistory = $investmentObject -> getUncategorisedHistory($count);
    }

    if ($historyType == "categorised") {

        $categoryType = htmlspecialchars(strip_tags($historyInput -> categoryType));
        if ($categoryType == "running") {
            $getInvestmentHistory = $investmentObject -> getRunningHistory();
        } else if ($categoryType == "completed") {
            $getInvestmentHistory = $investmentObject -> getCompletedHistory();
        }

    }
 

    $rowCount = $getInvestmentHistory -> rowCount();
    $historyArray=["recordCount" => $rowCount];

    $historyArray["records"]=array();

    if ($rowCount > 0) {

        $historyArray["status"] = "true";

        while ($row = $getInvestmentHistory->fetch(PDO::FETCH_ASSOC)){

            extract($row); 

            $cashoutDate = date_create(date('d-m-Y', $binary_date));

            $totalAmount = $capital + $roi;

            $historyItem = [
                "id" => $id,
                "capital" => $capital,
                "roi" => $roi,
                "uploadDate" => $upload_date,
                "uploadTime" => $upload_time,
                "cashOutDate" => date_format($cashoutDate, "l j F, Y"),
                "proofOfPayment" => $proof_of_payment,
                "receiptStatus" => $receipt_status,
                "roiReceived" => $roi_payment_status,
                "totalAmount" => $totalAmount
            ];

            array_push($historyArray["records"], $historyItem);

        }

        // set response code - 200 OK
        http_response_code(200);
    
        // show products data in json format
        echo json_encode($historyArray);

    } else {
        $investmentObject -> apiStatusCode(200, "No data was found for this request", "false");
    }

    // variable number of history


    // running investment history

    // completed investment history

    