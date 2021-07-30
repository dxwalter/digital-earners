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
    list($customerObject, $investmentObject) = [new Customer($dbObject), new Customer($dbObject)];

    
    // retrieve customer data from user input
    $searchData = json_decode(file_get_contents("php://input"));
    
    $searchString = htmlspecialchars(strip_tags($searchData -> searchString));
    // // test to validate JWT
    $jwt = isset($searchData->jwt) ? $searchData->jwt : "";


    require_once '../../config/authenticateJWT.php';


    if (strlen($searchString) > 0 && !ctype_space($searchString)) {

        $searchResult = $customerObject -> customerSearch($searchString);


        $recordCount = $searchResult -> rowCount();

        $searchArray = ["status" => "true", "recordCount" => $recordCount];

        $searchArray["records"]=array();

        if ($recordCount > 0) {

            while ($row = $searchResult->fetch(PDO::FETCH_ASSOC)){

                extract($row); 

                $searchDataArray = [
                    "id" => $id,
                    "userId" => $userid,
                    "profilePicture" => $profile_picture,
                    "fullname" => $fullname
                ];

                array_push($searchArray["records"], $searchDataArray);

            }

            // set response code - 200 OK
            http_response_code(200);
        
            // show products data in json format
            echo json_encode($searchArray);

        } else {
            $customerObject -> apiStatusCode(200, "No record was found for \"$searchString\" ", "true");
        }


    } else {
        return $customerObject -> apiStatusCode(201, "A search text is required to complete this action", "false");
    }