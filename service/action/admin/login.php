<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/admin.php';

    /**
     * JWT FILES WILL BE INCLUDED HERE
     */
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';


    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();

    // create admin object from Admin class
    $createAdminObject = new Admin($dbObject);

    // retrieve admin data from user input
    $adminLoginData = json_decode(file_get_contents("php://input"));

    $adminUsername = htmlspecialchars(strip_tags($adminLoginData -> username));
    $adminPassword = sha1(strtolower($adminLoginData -> password));

    // initialize admin class
    $createAdminObject -> username = $adminUsername;

    // Check if username exists
    $checkUsernameExistence = $createAdminObject -> authenticateUsername();

    if ($checkUsernameExistence ==  false) {
        $createAdminObject -> apiStatusCode(201, "Incorrect login credentials", "false");
        return;
    }

    if ($adminPassword != $createAdminObject -> password) {
        $createAdminObject -> apiStatusCode(201, "Incorrect login credentials", "false");
        return;
    } else {

        $adminId = $createAdminObject -> id;
        $adminFullname = $createAdminObject -> fullname;

        // This is not a class method. It is a function in core.php file.
        // The file core.php manages the json web token and the createJWT resides
        // there

        $callJWTEncode = createJWT($key, $iss, $aud, $adminId, $adminFullname);

        echo $callJWTEncode;
        return;


    }
