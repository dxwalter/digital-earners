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

    // create customer object from Admin class
    $customerObject = new Customer($dbObject);

    // retrieve customer data from user input
    $customerLoginData = json_decode(file_get_contents("php://input"));

    // // test to validate JWT
    // $jwt = isset($adminLoginData->jwt) ? $adminLoginData->jwt : "";

    // if ($jwt) {
    //    echo validateJWT ($jwt, $key);
    // }
    

    $customerPhoneNumber = htmlspecialchars(strip_tags($customerLoginData -> phoneNumber));
    $customerPassword = sha1(strtolower($customerLoginData -> password));

    // initialize admin class
    $customerObject -> phone = $customerPhoneNumber;

    // Check if phone number exists
    $checkPhoneExistence = $customerObject -> authenticatePhoneNumber();

    if ($checkPhoneExistence ==  false) {
        $customerObject -> apiStatusCode(200, "Incorrect login credentials", "false");
        return;
    }

    if ($customerPassword != $customerObject -> getPassword()) {

        $customerObject -> apiStatusCode(200, "Incorrect login credentials", "false");
        return;
    } else {

        /**
         * Check if the customer's account was
         * suspended before creating JWT
         */

         if ($customerObject -> activeStatus == 0) {
            // Account has been suspended
            $customerObject -> apiStatusCode(400, "Your account has been suspended. Contact the admin", "false");
            return;

         } else {

            $createLastSeen = strtotime('now');
            $customerObject -> lastSeen = $createLastSeen;

            if ($customerObject -> updateLastSeen() == true) {

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

                $callJWTEncode = createCustomerJWT (
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

                echo $callJWTEncode;
                return;

            } else {
                $customerObject -> apiStatusCode(200, "An error occured with last seen status", "false");
                return;
            }

         }


    }
