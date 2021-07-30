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
    $customerObject = new Customer($dbObject);

    // create investment object
    $investmentObject = new Investment($dbObject);

    // create notification object
    $notificationObject = new Notification($dbObject);

    
    // retrieve customer data from user input
    $userInvestmentDetails = json_decode(file_get_contents("php://input"));
    
    $investmentId = htmlspecialchars(strip_tags($userInvestmentDetails -> investmentId));

    // initialize investment object
    $investmentObject -> id = $investmentId;

        // // test to validate JWT
    $jwt = isset($userInvestmentDetails->jwt) ? $userInvestmentDetails->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        } 
    } 

    // get invesment details from database
    $investmentDetails = $investmentObject -> getInvestmentDetails($investmentId);
    
    $investmentObject -> userId = $investmentDetails["userid"];
    $amountReceived = $investmentDetails["capital"] + $investmentDetails["roi"];

    // make cashout payment status
    $makePaymentStatus = $investmentObject -> paymentCashout();

    if ($makePaymentStatus) {

        $notificationObject -> userId = $investmentDetails["userid"];
        $notificationObject -> readStatus = 0;
        $notificationObject -> typeid = $investmentId;
        $notificationObject -> type = "investment";

        $investorDetails = $customerObject -> getCustomerData($investmentDetails["userid"]);
        $investorEmail = $investorDetails["email"];
        
        $notificationObject -> message = "A sum of ₦".$notificationObject -> numberFormat($amountReceived)." has been paid into your bank account for investing ₦".$notificationObject -> numberFormat($investmentDetails["capital"])." on ".$investmentDetails["upload_date"]."";

        //send email
        $link = "https://digitalearners.cc";

        $emailAddress = $investorEmail;
        $subject = "Credited Account";

        $messageUiToSend = [
            "messageBody" => $notificationObject -> message,
            "link" => $link,
            "subject" => $subject,
            "buttonText" => "View Details"
        ];

        $customerObject -> sendEmail ($investorEmail, $messageUiToSend);

        // make notification
        $createNotification = $notificationObject -> createNotification();

        if ($createNotification) {
            $customerObject -> apiStatusCode(201, "Payment notice was successfully sent.", "true");
        } else {
            $customerObject -> apiStatusCode(200, "Payment notice was successfully sent but a notification was not sent to the investor.", "true");
        }

    } else {
        $customerObject -> apiStatusCode(400, "An error sending payment status to investor", "false");
    }