<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();
    
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';
    
    // create customer object from Admin class
    $customerObject = new Customer($dbObject);

    // retrieve admin data from user input
    $inputData = json_decode(file_get_contents("php://input"));

    // // test to validate JWT
    $jwt = isset($inputData->jwt) ? $inputData->jwt : "";

    require_once '../../config/authenticateJWT.php';   

    $customerId = htmlspecialchars(strip_tags($inputData -> customer));

    if ($customerId) {

        $customerDetails = $customerObject -> getCustomerData($customerId);

        if ($customerDetails) {

            $customerDetails["status"] = true;

            $timeStamp = $customerDetails["last_seen"];
            if ($timeStamp) {
                $convertedTime = new DateTime("@$timeStamp");
                $customerDetails["last_seen"] = $convertedTime->format("l j F, Y  H:i A");
            }


            // // set response code - 200 OK
            http_response_code(200);

            echo json_encode($customerDetails);

        } else {
            $customerObject -> apiStatusCode(200, "A breach has occured. This customer does not exists", "false");
        }

    } else {
        $customerObject -> apiStatusCode(200, "A breach has occured", "false");
    }