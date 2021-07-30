<?php

    header("Access-Control-Allow-Origin: *"); // change this * to admin url
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods; POST");
    header("Access-Control-Max-Age; 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once '../../config/databaseConnection.php';
    require_once '../../objects/notification.php';

    /**
     * JWT FILES WILL BE INCLUDED HERE
     */
    // The file core.php manages the json web token
    require_once '../../config/jwtCore.php';

    // main program body

    $createDbInstance = new Database();
    $dbObject = $createDbInstance -> getConnection();

    // create investment object
    $notificationObject = new Notification($dbObject);

    // retrieve customer data from user input
    $notificationDataInput = json_decode(file_get_contents("php://input"));

    $jwt = isset($notificationDataInput->jwt) ? $notificationDataInput->jwt : "";

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        } else {
            $notificationObject -> userId = $customerdata -> data -> userId;
        }
    }

    // this is to count the total number of unread notification
    if ($notificationDataInput -> type == "count") 
    {
        $countNotification = $notificationObject -> countUnreadNotification();
        while ($row = $countNotification->fetch(PDO::FETCH_ASSOC)){
            $unreadNotificationCount = $row["count"];
        }

        $count = [
            "unreadNotificationCount" => $unreadNotificationCount
        ];

        // set response code - 200 OK
        http_response_code(200);

        // show products data in json format
        echo json_encode($count);
    }

    // this is to mark a notification as read
    if ($notificationDataInput -> type == "mark") 
    {
        $markId = htmlentities(strip_tags($notificationDataInput -> notificationId));
        $notificationObject -> id = $markId;
        $markNotification = $notificationObject -> markNotification();
    }

    // all notification listing
    if ($notificationDataInput -> type == "listing") {
        $readNotifications = $notificationObject -> readAllNotifications();

        $resultCount = $readNotifications -> rowCount();
        $notificationRecord["records"] = [];

        if ($resultCount > 0) {

            $notificationRecord["status"] = "true";

            while ($row = $readNotifications->fetch(PDO::FETCH_ASSOC)){
                extract($row); 

                $notificationData = [
                    "id" => $id,
                    "userId" => $userid,
                    "message" => $message,
                    "readStatus" => $read_status,
                    "type" => $type,
                    "typeId" => $typeid
                ];

                array_push($notificationRecord["records"], $notificationData);
            }

            http_response_code(200);
            echo json_encode($notificationRecord);

        } else {
            $notificationObject -> apiStatusCode(200, "You do not have any notification", "false");
        }
    }