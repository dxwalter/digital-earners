<?php

    if ($jwt) {

        $validate = validateJWT ($jwt, $key);
        $customerdata = json_decode($validate);
        
        if ($customerdata -> message == "false") {
            echo $validate;
            return;
        }

    } else {
        $investmentObject -> apiStatusCode(401, "Access denied", "false");
        // return;
    }

?>