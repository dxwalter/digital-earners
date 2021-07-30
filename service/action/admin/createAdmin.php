<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/admin.php';

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();

    // create admin object from Admin class
    $createAdminObject = new Admin($dbObject);

    // retrieve admin data from user input
    $newAdminData = json_decode(file_get_contents("php://input"));

    $username = htmlspecialchars(strip_tags($newAdminData -> username));
    $userFullname = htmlspecialchars(strip_tags($newAdminData -> fullname));

    /**
     * Assign values to the properties of the Admin class
     */
    $createAdminObject -> username = $username;
    $createAdminObject -> fullname = $userFullname;
     


    // validate username
    if (!empty($username)) {    

        $checkIfUserNameExists = $createAdminObject -> checkIfUsernameExists();

        if ($checkIfUserNameExists) {
            /**
             * The username already exists
             * Set response code - 400 bad request
             */
            $createAdminObject -> apiStatusCode(201, "The username ".$username." already exists.", "false");
            return;
        }

    } else {
        $createAdminObject -> apiStatusCode(201, "Your username is required", "false");
        return;
    }

    // Validate password
    if (strlen($newAdminData -> password) < 6) {

        $createAdminObject -> apiStatusCode(201, "Your password must be not be less than 6 characters", "false");
        return;

    } else {

        $userPassword = sha1(strtolower($newAdminData -> password));
        $createAdminObject -> password = $userPassword;

    }

    // validate fullname
    if (empty($userFullname)) {

        $createAdminObject -> apiStatusCode(201, "Your name is required", "false");
        return;

    }

    /**
     * register new admin
     */

    if ($createAdminObject -> createAmin()) {
        /**
         * Account created successfully
         */
        $createAdminObject -> apiStatusCode(200, "Your account has been created successfully. You can now login", "true");
        return;
    } else {
        /**
         * Account registration failed
         */
        $createAdminObject -> apiStatusCode(400, "An error occured while creating your account", "false");
        return;
    }

