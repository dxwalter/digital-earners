<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();
    
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';

    // create investment object
    $investmentObject = new Investment($dbObject);

    // retrieve admin data from user input
    $inputData = json_decode(file_get_contents("php://input"));

    // // test to validate JWT
    $jwt = isset($inputData->jwt) ? $inputData->jwt : "";

    require_once '../../config/authenticateJWT.php';   

    $investmentId = htmlspecialchars(strip_tags($inputData -> investmentId));

    if ($investmentId) {
        $getInvestmentData = $investmentObject -> getInvestmentDetails($investmentId);    

        if ($getInvestmentData["userid"]) {

            $binaryDate = date_create(date('d-m-Y', $getInvestmentData["binary_date"]));
            $total = $getInvestmentData["capital"] + $getInvestmentData["roi"];

            $investmentArray = [
                "id" => $getInvestmentData["id"],
                "userId" => $getInvestmentData["userid"],
                "capital" => $getInvestmentData["capital"],
                "roi" => $getInvestmentData["roi"],
                "investmentDate" => $getInvestmentData["upload_date"],
                "binaryDate" => date_format($binaryDate, "l j F, Y"),
                "proofOfPayment" => $getInvestmentData["proof_of_payment"],
                "receiptStatus" => $getInvestmentData["receipt_status"],
                "roiPaymentStatus" => $getInvestmentData["roi_payment_status"],
                "totalAmountToPay" => $total,
                "status" => "true",
            ];

            // set response code - 200 OK
            http_response_code(200);
        
            // show products data in json format
            echo json_encode($investmentArray);

        } else {
            $investmentObject -> apiStatusCode(200, "This investment details does not exist", "false");
        }

    } else {
        $investmentObject -> apiStatusCode(200, "An error occured while detecting investment parameter. Contact the technical team", "false");
    }
    
