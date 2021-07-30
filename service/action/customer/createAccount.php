<?php 
    /**
     * This file manages creating a customer account
    */
    require_once '../../config/corsConfig.php';

    /**
     * Require database
     */
    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';

       // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';

    $database = new Database();
    $dbObject = $database -> getConnection();


    $customerInstance = new Customer($dbObject);
    $investmentObject = new Investment($dbObject);

    $newUserData = json_decode(file_get_contents("php://input"));    

        // // test to validate JWT
    $jwt = isset($newUserData->jwt) ? $newUserData->jwt : "";

    require_once '../../config/authenticateJWT.php';

    $userEmail = htmlspecialchars(strip_tags($newUserData -> email));
    $userPhoneNumber = htmlspecialchars(strip_tags($newUserData -> phone));
    $userPassword = $newUserData -> password;
    $emailPassword = strtolower($userPassword);


    /**
     *Email address validation
    */

    if (!empty($userEmail) && !ctype_space($userEmail)) {

        $checkEmailExistence = $customerInstance -> CheckEmailExistence($userEmail);
        if ($checkEmailExistence > 0) {
            // set response code - 200 bad request
            http_response_code(200);
            // tell the user
            echo json_encode(array(
                "message" => "The email address ".$userEmail." already exists.",
                "status" => "false"
            ));
            return;
        }
    } else {

        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "An email address is required",
            "status" => "false"
        ));
        return;
    }

    /**
     *password validation
    */

    if (strlen($userPassword) < 6) {
        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "Password should be greater than 6 characters",
            "status" => "false"
        ));
        return;
    } else {
        $userPassword = sha1(strtolower($newUserData -> password));
    }


    /**
     *Phone number validation
    */

    $customerInstance -> phone = $userPhoneNumber;

    if ( empty($userPhoneNumber) || ctype_space($userPhoneNumber) ) {
        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "An phone number is required",
            "status" => "false"
        ));
        return;

    } else if (strlen($userPhoneNumber) < 11) {
        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "Phone numbers should be greater than 11 characters",
            "status" => "false"
        ));
        return;
    } else if ($customerInstance -> authenticatePhoneNumber()) {
        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "The phone number already exists",
            "status" => "false"
        ));
        return;
    }

    /**
     * Assign users data to class(customer) properties
    */
    $customerInstance -> email = $userEmail;
    $customerInstance -> setPassword($userPassword);
    $customerInstance -> phone = $userPhoneNumber;


    /**
    *  Create a new customer
    */

    

    if ($customerInstance -> createNewInvestor()) {
        // send email 
        $link = "https://digitalearners.cc";

        $emailAddress = $userEmail;
        $subject = "Account Created Successfully";
        $messageBody = "
            Hello ".$userEmail.", congratulations your account has been created successfully. <br>
            Your login credential comprises of <br><br>
            PHONE NUMBER: <b>".$userPhoneNumber."</b><br>
            PASSWORD: <b>".$emailPassword."</b><br><br>
            When you first sign in, make sure you edit your profile and change your password.
            <br>
            Click the sign in link below to continue
        ";

        $messageUiToSend = [
            "messageBody" => $messageBody,
            "link" => $link,
            "subject" => $subject,
            "buttonText" => "Sign in"
        ];

        $investmentObject -> sendEmail ($userEmail, $messageUiToSend);
        
        // set response code - 200 bad request
        http_response_code(201);
        // tell the user
        echo json_encode(array(
            "message" => "Account created successfully",
            "status" => "true"
        ));
        return;
    } else {
        // set response code - 200 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "An error occured while creating an account", 
            "status" => "false"
         ));
        return;
    }