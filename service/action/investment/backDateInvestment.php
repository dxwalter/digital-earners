<?php 

    require_once '../../config/corsConfig.php';
    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();
    
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';
    
    // create customer object from Admin class
    $customerObject = new Customer($dbObject);
    $investmentObject = new Investment($dbObject);

    // retrieve admin data from user input
    $inputData = json_decode(file_get_contents("php://input"));

    // // test to validate JWT
    $jwt = isset($inputData->jwt) ? $inputData->jwt : "";

    require_once '../../config/authenticateJWT.php';  

    $customerId = htmlspecialchars(strip_tags($inputData -> customer));
    $capital = htmlspecialchars(strip_tags($inputData -> capital));
    $dateInvested = htmlspecialchars(strip_tags($inputData -> dateInvested));
    $paymentStatus = htmlspecialchars(strip_tags($inputData -> paymentStatus));

    if (strlen($customerId) < 0) { 
        return $customerObject -> apiStatusCode(201, "This investor is not recognised", "false");
    }

    if (strlen($capital) < 5) {
        return $customerObject -> apiStatusCode(201, "Enter a valid investment capital", "false");
    } else {
        $roi = $capital * 0.3;
    }

    if (strlen($dateInvested) < 1) {
        return $customerObject -> apiStatusCode(201, "Enter the date the investment was made", "false");
    } else {
        $newDate = explode(" ", $dateInvested);
        
        $createdDate = $newDate[0]."/".$newDate[1]."/".$newDate[2];

        $mainDate = new DateTime($createdDate); // format: MM/DD/YYYY
        $dbDate = $mainDate->format('U');
    }

    // binary date complete
    $binaryDate = (16 * 86400) + $dbDate;


    $createTime = date_create(date('H:i'));

    // time of upload complete
    $timeOfUpload = date_format($createTime, "h:i A");

    date_default_timezone_set('Africa/Lagos');

    // upload date complete
    $dateOfUpload = gmdate("l j F, Y", $dbDate);

    $investmentObject -> userId = $customerId;
    $investmentObject -> capital = $capital;
    $investmentObject -> roi = $roi;
    $investmentObject -> dateOfUpload = $dateOfUpload;
    $investmentObject -> timeOfUpload = $timeOfUpload;
    $investmentObject -> binaryDate = $binaryDate;
    $investmentObject -> receiptStatus = 1;
    $investmentObject -> profilePicture = "";
    $investmentObject -> roiPaymentStatus = $paymentStatus;
    
    $createInvestment = $investmentObject -> createInvestment();

    if ($createInvestment) {
        $customerObject -> apiStatusCode(200, "Investment has been created successfully.", "true");
    } else {
        $customerObject -> apiStatusCode(400, "An error occured creating investment", "false");
    }  
