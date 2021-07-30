<?php

    // cancel an investment

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

    // retrieve customer data from user input
    $requestInput = json_decode(file_get_contents("php://input"));
    
    // test to validate JWT
    $jwt = isset($requestInput->jwt) ? $requestInput->jwt : "";

    require_once '../../config/authenticateJWT.php';

    $dataArray = [];

    // get the number of investors to pay
    $today = time();

    $getListsOfPayableInvestors = $investmentObject -> getListOfPayableInvestors($today);
    $recordCount = $getListsOfPayableInvestors -> rowCount();
    $dataArray = ["investorsToPay" => $recordCount];

    // get total number of new investments
    $unconfirmedInvestments = $investmentObject -> getTotalNumberOfNewInvestments();
    $dataArray["newInvestments"] = $unconfirmedInvestments;

    echo json_encode($dataArray);

