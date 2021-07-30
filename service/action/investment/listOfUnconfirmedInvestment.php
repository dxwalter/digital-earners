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

    $countType = htmlspecialchars(strip_tags($requestInput -> count));
    
    // test to validate JWT
    $jwt = isset($requestInput->jwt) ? $requestInput->jwt : "";

    require_once '../../config/authenticateJWT.php';

    if ($countType == "all") {
        // retrieve all uncofirmed investments
        $retrieveUnconfirmedInvestments = $investmentObject -> getUnconfirmedInvestments($countType);
    } else {
        // retrieve variable amount of investments
        $retrieveUnconfirmedInvestments = $investmentObject -> getUnconfirmedInvestments($countType);
    }
    

    $rowCount = $retrieveUnconfirmedInvestments -> rowCount();
    $unconfirmedInvestmentArray = ["recordCount" => $rowCount];
    
    $unconfirmedInvestmentArray["records"] = array();

    if ($rowCount > 0) {
        while ($row = $retrieveUnconfirmedInvestments->fetch(PDO::FETCH_ASSOC)){

            extract($row); 

            // $cashoutDate = date_create(date('d-m-Y', $binary_date));
            $totalAmount = $capital + $roi;

            $data = [
                "id" => $id,
                "fullname" => $fullname,
                "userId" => $userid,
                "profilePicture" => $profile_picture,
                "capital" => $capital,
                "roi" => $roi,
                "totalAmount" => $totalAmount,
                "uploadDate" => $upload_date,
                "uploadTime" => $upload_time,
                // "cashOutDate" => date_format($cashoutDate, "l j F, Y"),
                "proofOfPayment" => $proof_of_payment,
                "receiptStatus" => $receipt_status,
                "roiReceived" => $roi_payment_status
            ];

            array_push($unconfirmedInvestmentArray["records"], $data);

        }

        // set response code - 200 OK
        http_response_code(200);
    
        // show products data in json format
        echo json_encode($unconfirmedInvestmentArray);

    } else {
        $customerObject -> apiStatusCode(200, "No new investment", "false");
    }

