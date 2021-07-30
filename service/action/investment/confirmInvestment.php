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
    $investmentDetails = json_decode(file_get_contents("php://input"));
    
    $investor = htmlspecialchars(strip_tags($investmentDetails -> investor));
    $investmentId = htmlspecialchars(strip_tags($investmentDetails -> investmentId));

    // initialize investment object
    $investmentObject -> id = $investmentId;
    $investmentObject -> userId = $investor;


    // // test to validate JWT
    $jwt = isset($investmentDetails->jwt) ? $investmentDetails->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        }

    }

    $investorDetails = $customerObject -> getCustomerData($investor);
    $investorEmail = $investorDetails["email"];


    // SET payday time, update db, send email and set customers notification

    // Get the due date to be paid after investing
    $getPaymentDate = $investmentObject -> getPaymentDate();
    $investmentObject -> binaryDate = $getPaymentDate;

    // confirm investment
    $confirmInvestment = $investmentObject -> confirmInvestment();

    if ($confirmInvestment) {

        $capital = $investmentObject -> getInvestmentDetails($investmentId);
        $notificationObject -> message = "Your investment of ₦".$investmentObject -> numberFormat($capital["capital"])." has been confirmed"; 
        //send email
        $link = "https://digitalearners.cc";

        $emailAddress = $investorEmail;
        $subject = "Confirmed investment";

        $messageUiToSend = [
            "messageBody" => $notificationObject -> message,
            "link" => $link,
            "subject" => $subject,
            "buttonText" => "View Details"
        ];

        $customerObject -> sendEmail ($emailAddress, $messageUiToSend);
        
        // set notification
        $notificationObject -> userId = $investor;
        $notificationObject -> readStatus = 0;

        $notificationObject -> type = "investment"; 
        $notificationObject -> typeid = $investmentId; 

        // create notification
        $createNotification = $notificationObject -> createNotification();

        if ($createNotification) {
            $customerObject -> apiStatusCode(201, "Investment has been confirmed successfully.", "true");
        } else {
            $customerObject -> apiStatusCode(201, "Investment has been confirmed successfully but we could not a send a notification to the investor.", "true");
        }

    } else {
        $customerObject -> apiStatusCode(201, "An error occured during confirmation. Contact the technical team", "false");
    }
