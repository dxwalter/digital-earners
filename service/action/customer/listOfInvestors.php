<?php 
    /**
     * This file handles the action of blocking a customer
     * Only an admin can access this route
     */
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

    $getAllInvestors = $customerObject -> getAllInvestors();

    $rowCount = $getAllInvestors -> rowCount();

    $customerArray["recordCount"] = $rowCount;

    $customerArray["records"] = [];

    if ($rowCount > 0) {

    	$customerArray["status"] = "true";

    	while ($row = $getAllInvestors->fetch(PDO::FETCH_ASSOC)){

            extract($row); 

            if ($last_seen != "") {
            	$lastSeen = date_create(date('d-m-Y', $last_seen));
            	$lastSeen = date_format($lastSeen, "l j F, Y");
            } else {
            	$lastSeen = "Not signed in";
            }

            $data = [
                "id" => $id,
                "userId" => $userid,
                "fullname" => $fullname,
                "profilePicture" => $profile_picture,
                "email" => $email,
                "phoneNumber" => $phone_number,
                "lastSeen" => $lastSeen
            ];

            array_push($customerArray["records"], $data);

        }

        // set response code - 200 OK
        http_response_code(200);
    
        // show products data in json format
        echo json_encode($customerArray);

    } else {

    	$customerObject -> apiStatusCode(200, "No investor has been created", "false");

    }