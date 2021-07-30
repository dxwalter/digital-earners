<?php 

    require_once '../../config/corsConfig.php';

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/customer.php';
    require_once '../../objects/recoverPassword.php';

    $database = new Database();
    $dbObject = $database -> getConnection();
    $customerInstance = new Customer($dbObject);
    $recoverPasswordInstance = new PasswordRecovery($dbObject);

    $customerData = json_decode(file_get_contents("php://input")); 
    $customerUserId = htmlspecialchars(strip_tags($customerData -> userId));
    $customerPassword = $customerData -> password;


	// check if the link is still valid
	$recoveryDetails = $recoverPasswordInstance -> getRecoveryDetials($customerUserId);

	$currentTime = time();
	$elapseTime = $recoveryDetails["timer"];

	$deleteRecovery = $recoverPasswordInstance -> deleteRecovery($customerUserId);

	if ($currentTime > $elapseTime) {
		$customerInstance -> apiStatusCode(200, "Password reset link has expired", "false");
	} else {

	   $newPassword = sha1(strtolower($customerPassword));
	   // update the new password in 
	   $updatePassword = $customerInstance -> updateNewPassword($newPassword, $customerUserId);

	   if ($updatePassword) {
	   	$customerInstance -> apiStatusCode(200, "Your new password has been saved", "true");
	   } else {
	   	$customerInstance -> apiStatusCode(200, "An error occured saving your new password", "false");
	   }
	}

 
