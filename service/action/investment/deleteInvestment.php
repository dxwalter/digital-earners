<?php

    // cancel an investment

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

    // get investor detail from db
    $getInvestorDbData = $customerObject -> getInvestorData($investor);
    $investorData = $getInvestorDbData -> fetch(PDO::FETCH_OBJ);
    $investorEmail = $investorData -> email;


    // get investment detail from db
    $getInvestmentDetailDb = $investmentObject -> getInvestmentDetails($investmentId);


    // // test to validate JWT
    $jwt = isset($investmentDetails->jwt) ? $investmentDetails->jwt : "";

    require_once '../../config/authenticateJWT.php';


    $capital = $investmentObject -> getInvestmentDetails($investmentId);

    $unconfirmInvestment = $investmentObject -> deleteInvestment();

    if ($unconfirmInvestment) {
        // send mail
        $capital = $getInvestmentDetailDb["capital"];

        $notificationObject -> message = "Your investment of ₦".$notificationObject -> numberFormat($capital)." that you invested on ".$getInvestmentDetailDb["upload_date"]." has been declined. Speak to the administrator for more info.";
        //send email
        $link = "https://digitalearners.cc";

        $emailAddress = $investorEmail;
        $subject = "Declined investment";

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
            $customerObject -> apiStatusCode(200, "Investment has been deleted successfully.",  "true");
        } else {
            $customerObject -> apiStatusCode(200, "Investment has been deleted successfully but we could not a send a notification to the investor.", "true");
        }

    } else {
        $customerObject -> apiStatusCode(401, "An error occured. We could not delete the investment .Contact the technical team",  "false");
    }
