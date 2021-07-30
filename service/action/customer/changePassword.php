<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';

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
    
    $textData  = json_decode(file_get_contents("php://input"));

    // // test to validate JWT
    $jwt = isset($textData->jwt) ? $textData->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        }

    }

    $password = $textData -> passwordString;
    $customerObject -> phone = $customerdata -> data -> phoneNumber;

    if (strlen($password) < 6) {
        // set response code - 400 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "Password should be greater than 6 characters",
            "status" => "false"
        ));
        return;
    }

    $password = sha1(strtolower($textData -> passwordString));
    $authenticatePhoneNumber = $customerObject -> authenticatePhoneNumber();

    $oldPassword = $customerObject -> getPassword();

    if ($password == $oldPassword) {
        // set response code - 400 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "You are using a password that you have used before.",
            "status" => "false"
        ));
        return;
    }

    $customerObject -> setPassword($password);

    $updatePassword = $customerObject -> updatePassword();

    if ($updatePassword) {
        // set response code - 400 bad request
        http_response_code(201);
        // tell the user
        echo json_encode(array(
            "message" => "Your password was updated successfully",
            "status" => "true"
            ));
        return;
    } else {
        // set response code - 400 bad request
        http_response_code(400);
        // tell the user
        echo json_encode(array("message" => "An error occured updating your password"));
        return;
    }