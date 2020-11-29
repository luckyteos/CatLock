<?php
require_once("DatabaseProcessing.php");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $processing = new DatabaseProcessing();
    $originIP = getIPAddress();

    // Request
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    $components = $processing->getAllComponentsStoredValue($originIP);
    if(!$components) {
        // Doesn't existing... Creating
        $processing->insertNewDevice($originIP, $data);
        $components = $processing->getAllComponentsStoredValue($originIP);
    }

    // Saving Log to DB
    if($processing->insertComponentLogFromIP($originIP, $data)) {
        //echo "SUCCESS";
    }else {
        //echo "FAILED";
    }

    // Response (Fetch from DB)
    $fp = fopen('php://output', 'w');

    foreach($components as $component) {
        $category = $component->getCategory();
        $storedValue = $component->getStoredValue();
        
        if("Sensor" == $category) {
            // Checking for alerts here cause I lazy :/
            if($data["Temperature"] >= $storedValue) {
                $processing->insertAlert("Temperature has reached threshold!", $originIP);
                // Update
                if($processing->updateComponent($originIP, $data)) {
                    $component->setStoredValue($data["Lock Status"]);
                }
                
            }

            if(substr(strval($storedValue), 0, 1) == "-") {
                // Negative
                $storedValue = "-" . str_pad(substr($storedValue, 1), 4,"x",STR_PAD_LEFT);
                fwrite($fp, "Threshold:" . $storedValue);
            }else {
                // Positive
                $storedValue = "+" . str_pad($storedValue, 4,"x",STR_PAD_LEFT);
                fwrite($fp, "Threshold:" . $storedValue);
            }
        }elseif("Lock" == $category) {
            // Checking for alerts here cause I lazy :/
            if($data["Lock Status"] != $storedValue) {
                $processing->insertAlert("Lock has been " . $data["Lock Status"] . "!", $originIP);
            }

            fwrite($fp, "\nLock:" . $storedValue);
        }
    }

    fclose($fp);
}elseif($_SERVER['REQUEST_METHOD'] == "GET") {
    echo "<html><head></head><body style=\"padding:0px;margin:0px\"><video width=\"100%\" height=\"100%\" controls autoplay muted loop><source src=\"/image/Cute_Dog_Sleeping.mp4\" type=\"video/mp4\"></body></html>";
}

function getIPAddress() {
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
