<?php 
    /**
     * This file handles the action of blocking a customer
     * Only an admin can access this route
     */
    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();
    
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';
    
    // create customer object from Admin class
    $createCustomerObject = new Customer($dbObject);

    // retrieve admin data from user input
    $retreiveUserData = json_decode(file_get_contents("php://input"));

    // test to validate JWT
    $jwt = isset($retreiveUserData -> jwt) ? $retreiveUserData -> jwt : "";

    // validate JWT
    if ($jwt) {
       $validatedAdminResult = json_decode(validateJWT ($jwt, $key));

       if ($validatedAdminResult -> message == "false") {
            // tell the user access denied
            return $createCustomerObject -> apiStatusCode(401, "Access denied. Login again to continue");
       }


    }

    $customerId = $retreiveUserData -> customerId;

    if (empty($customerId)) {

        // tell the user access denied
        return $createCustomerObject -> apiStatusCode(200, "Customer cannot be identified", "false");

    } else {

        $createCustomerObject -> userId = htmlspecialchars(strip_tags($customerId));

        // Block customer account
        $blockCustomer = $createCustomerObject -> deactivateInvestor();
        
        if ($blockCustomer) {
            return $createCustomerObject -> apiStatusCode(200, "Account deactivated successfully", "true");
        } else {
            return $createCustomerObject -> apiStatusCode(400, "An error occured deactivating customer's account", "false");
        }
    }
