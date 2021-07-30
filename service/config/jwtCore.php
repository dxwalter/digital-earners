<?php

    // show error reporting
    error_reporting(E_ALL);
    
    // set your default time-zone
    date_default_timezone_set('Africa/Lagos');

    include_once '../../lib/jwt/BeforeValidException.php';
    include_once '../../lib/jwt/ExpiredException.php';
    include_once '../../lib/jwt/SignatureInvalidException.php';
    include_once '../../lib/jwt/JWT.php';
    use \Firebase\JWT\JWT;
    
    // variables used for jwt
    $key = "bd3404f882780fb6f1d4233ce0c3d9cbe1ad5b86"; // remeber my crush that yeDSHA;
    $iss = "http://example.org";
    $aud = "http://example.com";


    function createJWT ($key, $iss, $aud, $adminId, $adminFullname) {

        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "data" => array(
                "id" => $adminId,
                "firstname" => $adminFullname,
            )
        );

        http_response_code(200);

        // generate jwt
        $jwt = JWT::encode($token, $key);

        return json_encode (
            array (
                "message" => "Successfully logged in",
                "jwt" => $jwt,
                "status" => "true"
            )
        );

    }

    function generateToken($key, $iss, $aud, $userId, $fullname, $email, $phoneNumber, $activeStatus, $firstTimeAccess, $profilePicture)
    {
        # code...
        $token = array(
            "iss" => $iss,
            "aud" => $aud,
            "data" => array(
                "userId" => $userId,
                "fullName" => $fullname,
                "email" => $email,
                "phoneNumber" => $phoneNumber,
                "profilePicture" => $profilePicture,
                "activeStatus" => $activeStatus,
                "firstTimeAccess" => $firstTimeAccess
            )
        );

        http_response_code(200);

        // generate jwt
        $jwt = JWT::encode($token, $key);

        return $jwt;
    }

    function createCustomerJWT ($key, $iss, $aud, $userId, $fullname, $email, $phoneNumber, $activeStatus, $firstTimeAccess, $profilePicture) {

        $jwt = generateToken($key, $iss, $aud, $userId, $fullname, $email, $phoneNumber, $activeStatus, $firstTimeAccess, $profilePicture);

        return json_encode (
            array (
                "message" => "Welcome ".$fullname.". Your login was successful",
                "status" => true,
                "jwt" => $jwt,
                "userData" => [
                    "userId" => $userId,
                    "fullName" => $fullname,
                    "email" => $email,
                    "phoneNumber" => $phoneNumber,
                    "profilePicture" => $profilePicture,
                    "activeStatus" => $activeStatus,
                    "firstTimeAccess" => $firstTimeAccess
                ]
            )
        );

    }

    function validateJWT ($jwt, $key) {

        if($jwt){
        
            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode($jwt, $key, array('HS256'));
        
                // set response code
                http_response_code(200);
        
                // show user details
                return json_encode(array(
                    "message" => "true",
                    "data" => $decoded->data
                ));
        
            }
        
            // if decode fails, it means jwt is invalid
            catch (Exception $e){
            
                // set response code
                http_response_code(401);
                // tell the user access denied  & show error message
                return json_encode(array(
                    "message" => "false",
                    "error" => $e->getMessage()
                ));
            }

        } else {

            // set response code
            http_response_code(401);
            // tell the user access denied
            return json_encode(array("message" => "Access denied."));

        }

    }

?>