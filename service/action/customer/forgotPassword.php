<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/recoverPassword.php';

    $database = new Database();
    $dbObject = $database -> getConnection();
    $customerInstance = new Customer($dbObject);
    $recoverPasswordInstance = new PasswordRecovery($dbObject);

    $customerData = json_decode(file_get_contents("php://input")); 
    $custmerEmailAddress = htmlspecialchars(strip_tags($customerData -> email));

    $checkEmailAddress = $customerInstance -> CheckEmailExistence($custmerEmailAddress);

    if (!$checkEmailAddress) {
        // set response code - 400 bad request
        http_response_code(200);
        // tell the user
        echo json_encode(array(
            "message" => "Enter a valid email address",
            "status" => "0"
        ));
        return;
    }

    

    // create a link
    $getUserData = $customerInstance -> getCustomerDataWithEmail($custmerEmailAddress);

    $userId = $getUserData['userid'];
    $currentTime = time();
    $elapseTime = $currentTime + 3600;

    if ($userId) {

        $recoverPasswordInstance -> userId = $userId;
        $recoverPasswordInstance -> timer = $elapseTime;
        // populate the recovery password db

        $createRecovery = $recoverPasswordInstance -> createRecovery();

        $link = "https://digitalearners.cc/NewPassword/".$userId."";

        $emailAddress = $custmerEmailAddress;
        $subject = "Hello ".$custmerEmailAddress.", recover your password";

        $messageUiToSend = [
            "messageBody" => " We got a request to reset your password on Digital Earners. Click the link below to continue. <br> The link expires after one hour.<br>
            If you did not initiate password recovery, contact the admin.",
            "link" => $link,
            "subject" => $subject,
            "buttonText" => "Reset your password"
        ];

        if ($customerInstance -> sendEmail($emailAddress, $messageUiToSend)) {
            // set response code - 400 bad request
            http_response_code(200);
            // tell the user
            echo json_encode(array(
                "message" => "A message has been sent to ".$custmerEmailAddress."",
                "status" => "true"
            ));
        } else {
            http_response_code(200);
            // tell the user
            echo json_encode(array(
                "message" => "A error occured sending a message to ".$custmerEmailAddress."",
                "status" => "false"
            ));
        }
    }