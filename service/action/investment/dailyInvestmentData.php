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

        $createDbInstance = new Database();
        $dbObject = $createDbInstance -> getConnection();
        
        // retrieve customer data from user input
        $inputData = json_decode(file_get_contents("php://input"));
        
        // create investment instance
        $investmentObject = new Investment($dbObject);

        // create investor's instance
        $customerObject = new Customer($dbObject);

            // // test to validate JWT
        $jwt = isset($investmentDetails->jwt) ? $inputData ->jwt : "";
        $dataDate = htmlspecialchars(strip_tags($inputData -> dataDate));
        $operationType = htmlspecialchars(strip_tags($inputData -> type));

        if ($jwt) {

            $validate = validateJWT ($jwt, $key);
            $customerdata = json_decode($validate);
            
            if ($customerdata -> message == "false") {
                echo $validate;
                return;
            }

        }

        // format date to retreive investment
        $formattedDate = rtrim(trim(str_replace(" ", "/", $dataDate)));
        $createDate = new DateTime($formattedDate);
        $dateForInvestment = date_format($createDate, "l j F, Y");

        // epoch unix time stamp
        $dateForInvestmentPayment = date_format($createDate, 'U');

        // condition to check type
        if ($operationType == "investment") {
            
            $getInvestment = $investmentObject -> getInvestmentListing($dateForInvestment);
            
            $rowCount = $getInvestment -> rowCount();
            $investmentDetails = [];

            if ($rowCount > 0) {
                $investmentDetailsRecord = array();

                list($totalCapital, $totalRoi) = 0;

                while ($row = $getInvestment->fetch(PDO::FETCH_ASSOC)){
                    extract($row); 
                    
                    $cashoutDate = date_create(date('d-m-Y', $binary_date));
                    $totalAmount = $capital + $roi;
                    $totalCapital += $capital;
                    $totalRoi += $roi;

                    $investments = [
                        "id" => $id,
                        "capital" => $capital,
                        "roi" => $roi,
                        "uploadDate" => $upload_date,
                        "uploadTime" => $upload_time,
                        "cashOutDate" => date_format($cashoutDate, "l j F, Y"),
                        "proofOfPayment" => $proof_of_payment,
                        "receiptStatus" => $receipt_status,
                        "roiReceived" => $roi_payment_status,
                        "totalAmount" => $totalAmount,
                        "userid" => $userid,
                        "fullname" => $fullname,
                        "profilePicture" => $profile_picture
                    ];

                    array_push($investmentDetailsRecord, $investments);

                }


                $investmentDetails = [
                    "totalCapital" => $totalCapital,
                    "totalRoi" => $totalRoi,
                    "cashTotal" => $totalCapital + $totalRoi,
                    "status" => "true",
                    "humanDate" => $dateForInvestment,
                    "recordCount" => $rowCount,
                    "records" => $investmentDetailsRecord
                ];
                
                // set response code - 200 OK
                http_response_code(200);
                // show products data in json format
                echo json_encode($investmentDetails);

            } else {
                $investmentObject -> apiStatusCode(200, "No investment was made on this date \"".$dateForInvestment."\" ", "false");
            }

        }

        if ($operationType == "payout") {

            $getInvestment = $investmentObject -> getPayoutInvestmentListing($dateForInvestmentPayment);
            
            $rowCount = $getInvestment -> rowCount();

            $investmentDetailsRecord = [];

            if ($rowCount > 0) {

                $investmentDetails["status"] = "true";

                list($totalCapital, $totalRoi) = 0;

                while ($row = $getInvestment->fetch(PDO::FETCH_ASSOC)){

                    extract($row); 

                    $cashoutDate = date_create(date('d-m-Y', $binary_date));

                    $totalAmount = $capital + $roi;
                    $totalCapital += $capital;
                    $totalRoi += $roi;

                    $investments = [
                        "id" => $id,
                        "capital" => $capital,
                        "roi" => $roi,
                        "uploadDate" => $upload_date,
                        "uploadTime" => $upload_time,
                        "cashOutDate" => date_format($cashoutDate, "l j F, Y"),
                        "proofOfPayment" => $proof_of_payment,
                        "receiptStatus" => $receipt_status,
                        "roiReceived" => $roi_payment_status,
                        "totalAmount" => $totalAmount,
                        "userid" => $userid,
                        "fullname" => $fullname,
                        "profilePicture" => $profile_picture
                    ];

                    $investmentDetails["totalCapital"] = $totalCapital;
                    $investmentDetails["totalRoi"] = $totalRoi;
                    $investmentDetails["cashTotal"] = $totalCapital + $totalRoi;
                    array_push($investmentDetailsRecord, $investments);

                }

                $investmentDetails = [
                    "totalCapital" => $totalCapital,
                    "totalRoi" => $totalRoi,
                    "cashTotal" => $totalCapital + $totalRoi,
                    "status" => "true",
                    "humanDate" => $dateForInvestment,
                    "recordCount" => $rowCount,
                    "records" => $investmentDetailsRecord
                ];


                // set response code - 200 OK
                http_response_code(200);
                // show products data in json format
                echo json_encode($investmentDetails);

            } else {
                $investmentObject -> apiStatusCode(200, "No cashout is expected on this date  \"".$dateForInvestment."\" ", "false");
            }

        }