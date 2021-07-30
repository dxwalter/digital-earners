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

    }


    $investorId = htmlspecialchars(strip_tags($userInput -> investor));
    $investmentObject -> userId = $investorId;
    
    // latest investment
    $runningInvestment = $investmentObject -> latestRunningInvestment();

    if ($runningInvestment -> rowCount()) {
        
        while ($row = $runningInvestment->fetch(PDO::FETCH_ASSOC)){
            
            $runningInvestmentCapital = $row["capital"];
            $runningInvestmentRoi = $row["roi"];
            $investementId = $row['id'];
            
        }

        $runningInvestmentArray = [
            "capital" => $runningInvestmentCapital,
            "roi" => $runningInvestmentRoi
        ];

    } else {
        $runningInvestmentArray = [
            "capital" => 0,
            "roi" => 0
        ];
    }




    // total ROI
    $earnedRoi = $investmentObject -> totalRoiEarned();
    if ($earnedRoi -> rowCount()) {
        
        while ($row = $earnedRoi->fetch(PDO::FETCH_ASSOC)){
            $summedRoi = $row["SUM(roi)"];
        }

        if ($summedRoi < 1) {
            $summedRoi = 0;
        }

        $totalRoi = [
            "totalRoi" => $summedRoi
        ];

    } else {
        $totalRoi = [
            "totalRoi" => 0
        ];
    }



    
    $totalInvestmentCapital = $investmentObject -> totalUserInvestmentCapital($investorId);

    if ($totalInvestmentCapital -> rowCount()) {
        
        while ($row = $totalInvestmentCapital->fetch(PDO::FETCH_ASSOC)){
            $summedCapital = $row["SUM(capital)"];
        }

        $totalCapital = [
            "totalCapital" => $summedCapital
        ];

    } else {
        $totalCapital = [
            "totalCapital" => 0
        ];
    }
    
    $customerInvestmentData = [
        "latestInvestment" => $runningInvestmentArray,
        "totalRoi" => $totalRoi,
        "investedCapital" => $totalCapital
    ];

    // set response code - 200 OK
    http_response_code(200);

    // show products data in json format
    echo json_encode($customerInvestmentData);