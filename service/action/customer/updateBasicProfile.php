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

    // retrieve customer data from user input
    if (isset($_FILES["image"])) {
        $imageData = $_FILES['image']; // this is already in an array
    } else {
        $imageData = "";
    }

    
    $textData  = json_decode($_POST['data']);

    // // test to validate JWT
    $jwt = isset($textData->jwt) ? $textData->jwt : "";

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

    }

    /**
    ***  1 means something change
    **/
    $imageChange = 0;
    $emailChange = 0;
    $nameChange = 0;
    $phoneChange = 0;

    // upload image

    if (isset($imageData["name"])) {
        // require the image processor class and instantiate it
        require_once '../../lib/imageProcessor/imageProcessor.php';
        $imageProcessorObject = new imageProcessor();

        $uploadPath = "../../../images/profile_pictures/".$customerObject -> id."/";

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
        $imageChange = 1;
        echo $imageUpload = $imageProcessorObject -> imageProcessorEntry($imageDataArray, $uploadPath, $customerObject);

    } else {
        $customerObject -> profilePicture = $customerdata -> data -> profilePicture; 
    }


    // update other info

    // This is for fullname
    if ($textData -> fullName != $customerObject -> fullName) {

        if (strlen($textData -> fullName) < 4) {
            $customerObject -> apiStatusCode(200, "Your fullname cannot be less than 4 characters", "false");
        } else if ($textData -> fullName == "No name yet") {
            $customerObject -> apiStatusCode(200, "The fullname you entered is not valid", "false");
        }else {
            $nameChange = 1;
            $customerObject -> fullName = $textData -> fullName;
        }
    }


    // this is for phone number
    if ($textData -> phone != $customerObject -> phone) {

        if (strlen($textData -> phone) < 10) {
            $customerObject -> apiStatusCode(200, "Enter a valid phone number", "false");
        } else {
            // Check if the phone number already exists
            if ($customerObject -> checkPhoneNumberExistence($textData -> phone)) {
                $customerObject -> apiStatusCode(200, "The phone number you provided is been used by someone on this service", "false");
            } else {
                $phoneChange = 1;
                $customerObject -> phone = $textData -> phone;
            }
        }
    }

    // this is for email address
    if ($textData -> email != $customerObject -> email) {
        
        if (strpos($textData -> email, "@") == false) {
            $customerObject -> apiStatusCode(200, "Enter a valid email address", "false");
        } else if ($customerObject -> CheckEmailExistence($textData -> email)) {
            $customerObject -> apiStatusCode(200, "The email address you provided is been used by someone on this service", "false");
        } else {
            $emailChange = 1;
            $customerObject -> email = $textData -> email;
        }
    }



    $updateData = $customerObject -> updateProfile();


    if ($updateData) {
        
    // generate new jwt;

        $customerId = $customerObject -> id;
        $customerFullname = $customerObject -> fullName;
        $customerEmail = $customerObject -> email;
        $CustomerLastSeen = $customerObject -> lastSeen;
        $customerPhoneNumber = $customerObject -> phone;
        $customerProfilepicture = $customerObject -> profilePicture;
        $customerActiveStatus = $customerObject -> activeStatus;
        $customerFirstTimeAccess = $customerObject -> firstTimeAccess;

        // This is not a class method. It is a function in jwtCore.php file.
        // The file jwtCore.php manages the json web token and the createJWT resides
        // there

        $callJWTEncode = generateToken (
            $key, 
            $iss, 
            $aud,
            $customerId, 
            $customerFullname, 
            $customerEmail, 
            $customerPhoneNumber, 
            $customerActiveStatus, 
            $customerFirstTimeAccess, 
            $customerProfilepicture
        );

        if ($callJWTEncode) {

            http_response_code(200);

            echo json_encode (
                array (
                    "message" => "Your account was updated successfully",
                    "jwt" => $callJWTEncode,
                    "userData" => [
                        "userId" => $customerId,
                        "fullName" => $customerFullname,
                        "email" => $customerEmail,
                        "phoneNumber" => $customerPhoneNumber,
                        "profilePicture" => $customerProfilepicture,
                        "activeStatus" => $customerActiveStatus,
                        "firstTimeAccess" => "1"
                    ],
                    "status" => "true"
                )
            );
            return;
        }

    } else {
        $customerObject -> apiStatusCode(200, "Change your basic details to update your account", "false");
        return;
    }
