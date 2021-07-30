<?php

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/investment.php';

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

    // create investment 
    $investmentObject = new Investment($dbObject);


    // retrieve customer data from user input
    $imageData = $_FILES['image']; // this is already in an array

    $investmentData  = json_decode($_POST['investmentData']);

    // // test to validate JWT
    $jwt = isset($investmentData->jwt) ? $investmentData->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        } else {
            
            $customerObject -> id = $customerdata -> data -> userId;
            $customerObject -> fullName = $customerdata -> data -> fullName;
            $customerObject -> email = $customerdata -> data -> email;
            $customerObject -> phone = $customerdata -> data -> phoneNumber;
            $customerObject -> profilePicture = $customerdata -> data -> profilePicture; 
            $customerObject -> activeStatus = $customerdata -> data -> activeStatus; 
            $customerObject -> firstTimeAccess = $customerdata -> data -> firstTimeAccess; 

        }

    } else {
        $investmentObject -> apiStatusCode(401, "Access denied. Unidentified user token", "false");
        return;
    }


            // upload image

    if ($imageData["name"]) {
        // require the image processor class and instantiate it
        require_once '../../lib/imageProcessor/imageProcessor.php';
        $imageProcessorObject = new imageProcessor();

        $uploadPath = "../../../images/proof_of_payment/".$customerObject -> id."/";

        $imageName = $imageData["name"];
        $imageSize = $imageData["size"];
        $imageType = $imageData["type"];
        $imageTmpLoc = $imageData["tmp_name"];
        $imageEXT = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $providedImageName = sha1($imageName);

        $imageDataArray = [
            $imageName,
            $imageSize,
            $imageType,
            $imageTmpLoc,
            $imageEXT,
            $providedImageName
        ];

        echo $imageUpload = $imageProcessorObject -> imageProcessorEntry($imageDataArray, $uploadPath, $investmentObject);

    } else {
        $customerObject -> apiStatusCode(200, "A picture of your proof of payment is required", "false");
    }



    if ($investmentData -> capital) {

        $capital = htmlspecialchars(strip_tags($investmentData -> capital));
        $roi = $capital * .3;

        date_default_timezone_set('Africa/Lagos');

        $createDate = date_create(date('d-m-Y'));
        $dateOfUpload = date_format($createDate, "l j F, Y");

        $createTime = date_create(date('H:i'));
        $timeOfUpload = date_format($createTime, "h:i A");

        // initialize the properties of investment class
        $investmentObject -> userId = $customerObject -> id;
        $investmentObject -> capital = $capital;
        $investmentObject -> roi = $roi;
        $investmentObject -> dateOfUpload = $dateOfUpload;
        $investmentObject -> timeOfUpload = $timeOfUpload;
        $investmentObject -> receiptStatus = 0;
        $investmentObject -> roiPaymentStatus = 0;



        $createInvestment = $investmentObject -> createInvestment();

        $link = "https://boss.digitalearners.cc";

        $emailAddress = "harrynova4business@gmail.com";
        $subject = "New investment";

        $messageUiToSend = [
            "messageBody" => "<b>".$customerdata -> data -> fullName."</b> just invested the sum of ₦".$customerObject -> numberFormat($capital).". Clink the link below to confirm payment",
            "link" => $link,
            "subject" => $subject,
            "buttonText" => "Confirm Payment"
        ];

        $customerObject -> sendEmail ($emailAddress, $messageUiToSend);

        if ($createInvestment) {
            $customerObject -> apiStatusCode(201, "Your investment has been created successfully. Wait for confirmation", "true");
        } else {
            $customerObject -> apiStatusCode(200, "An error occured creating your investment", "false");
        }   

        
    } else {
        $customerObject -> apiStatusCode(200, "Enter the amount of capital you invested", "false");
    }