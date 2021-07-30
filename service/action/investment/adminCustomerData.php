<?php

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';

    /**
     * JWT FILES WILL BE INCLUDED HERE
     */
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();

    $customerObject = new Customer($dbObject);

    // create investment object
    $investmentObject = new Investment($dbObject);

    // retrieve customer data from user input
    $userInput = json_decode(file_get_contents("php://input"));

    $jwt = isset($userInput->jwt) ? $userInput->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        }

    } else {
        $investmentObject -> apiStatusCode(401, "Access denied", "false");
        return;
    }


    // total active investment investment
    $totalActiveInvestment = $investmentObject -> totalActiveInvestment();

    if ($totalActiveInvestment -> rowCount()) {
        
        while ($row = $totalActiveInvestment->fetch(PDO::FETCH_ASSOC)){
            
            $runningInvestmentCapital = $row["SUM(capital)"];
        }

        $totalActiveInvestmentArray = [
            "capital" => $runningInvestmentCapital
        ];

    } else {
        $totalActiveInvestmentArray = [
            "capital" => 0,
        ];
    }




    // total ROI
    $earnedRoi = $investmentObject -> totalRoiToBePaid();
    if ($earnedRoi -> rowCount()) {
        
        while ($row = $earnedRoi->fetch(PDO::FETCH_ASSOC)){
            $summedRoi = $row["SUM(roi)"];
        }

        $totalRoi = [
            "totalRoi" => $summedRoi
        ];

    } else {
        $totalRoi = [
            "totalRoi" => 0
        ];
    }



    // unconfirmed investments
    $totalUnconfirmedInvestments = $investmentObject -> totalUnconfirmedInvestments();
        
    $unconfirmedRow = $totalUnconfirmedInvestments->fetch(PDO::FETCH_ASSOC);
    $summedUnconfirmedInvestment = $unconfirmedRow["count"];

    if ($summedUnconfirmedInvestment) {
        $totalUnconfirmedINV = [
            "unconfirmedInvestments" => $summedUnconfirmedInvestment
        ];
    } else {
        $totalUnconfirmedINV = [
            "unconfirmedInvestments" => 0
        ];
    }



    // total numbers of investors
    $allInvestors = $customerObject -> allCustomersCount();
    $totalInvestorsRow = $allInvestors -> fetch(PDO::FETCH_ASSOC);
    $summedInvestors = $totalInvestorsRow["users"];

    if ($summedInvestors) {
        $totalNumInvestors = [
            "allInvestors" => $summedInvestors
        ];
    } else {
        $totalNumInvestors = [
            "allInvestors" => 0
        ];
    }


    // response array
    $customerInvestmentData = [
        "runningCapital" => $totalActiveInvestmentArray,
        "totalRoi" => $totalRoi,
        "totalInvestors" => $totalNumInvestors,
        "unconfirmedINV" => $totalUnconfirmedINV
    ];

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode($customerInvestmentData);