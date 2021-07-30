<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';

    $database = new Database();
    $dbObject = $database -> getConnection();
    $customerInstance = new Customer($dbObject);

    $customerStatusData = json_decode(file_get_contents("php://input"));  

    $statusAction = htmlspecialchars(strip_tags($customerStatusData -> statusAction));
    $customerId = htmlspecialchars(strip_tags($customerStatusData -> userid));

    if (empty($statusAction) || empty($customerId)) {
        // set response code - 400 bad request
        http_response_code(400);
        // tell the user
        echo json_encode(array("message" => "Bad request. Incomplete paramenter"));
        return;
    }


    if ($statusAction == 'activate') {

        if ($customerInstance -> activateInvestor($customerId)) {
            // set response code - 400 bad request
            http_response_code(200);
            // tell the user
            echo json_encode(array("message" => "Account activated successfully"));
            return;
        } else {
            // set response code - 400 bad request
            http_response_code(400);
            // tell the user
            echo json_encode(array("message" => "An error occured activating account"));
            return;
        }

    }

    if ($statusAction == "deactivate") {
        
        if ($customerInstance -> deactivateInvestor($customerId)) {
            // set response code - 400 bad request
            http_response_code(200);
            // tell the user
            echo json_encode(array("message" => "Account deactivated successfully"));
            return;
        } else {
            // set response code - 400 bad request
            http_response_code(400);
            // tell the user
            echo json_encode(array("message" => "An error occured deaactivating account"));
            return;
        }
    }