<?php
try {
    require_once("DatabaseProcessing.php");
} catch(exception $e) {
    echo "Hello";
}

session_start();
if($_SERVER['REQUEST_METHOD'] == "POST" && $_SESSION['token'] == $_POST['token']) {
    $cat = $_POST["cat"];
    $processing = new DatabaseProcessing();

    if("updateDeviceName" == $cat) {
        $deviceID = $_POST["deviceID"];
        $deviceName = $_POST["deviceName"];

        echo $processing->updateDeviceName($deviceID, $deviceName);
    } elseif("updateComponent" == $cat) {
        $deviceID = $_POST["deviceID"];
        $temp = $_POST["temp"];
        $lock = $_POST["lock"];

        echo $processing->updateComponents($deviceID, $temp, $lock);
    } elseif("getUpdatedTimeLog" == $cat) {
        $alertID = $_POST["alertID"];

        $jsonObj = array();
        $jsonObj["id"] = $alertID;

        $timezone = new DateTimeZone("Asia/Singapore");

        $yesterdayStart = new DateTime(date('d.m.Y', strtotime("-1 days")), $timezone);
        $todayStart = new DateTime(date("Y-m-d"), $timezone);
        $compareDate = new DateTime($processing->getAlertTime($alertID), $timezone);

        $now = new DateTime();
        $now->setTimezone($timezone);

        if($todayStart <= $compareDate) {
            // TODAY
            $seconds = $now->getTimestamp() - $compareDate->getTimestamp();
            if($seconds < 60) {
                $jsonObj["data"] = $seconds . " seconds ago";
                $jsonObj["update"] = 1;
            }elseif($seconds < 60 * 60) {
                $jsonObj["data"] = FLOOR($seconds / 60) . " minutes ago";
                $jsonObj["update"] = 2;
            }elseif($seconds < 24 * 60 * 60) {
                $jsonObj["data"] = FLOOR($seconds / (60 * 60)) . " hours ago";
                $jsonObj["update"] = 3;
            }
        }elseif($yesterdayStart <= $compareDate) {
            // YESTERDAY
            $jsonObj["data"] = "Yesterday, " . $compareDate->format("h:i A");
        }else {
            $jsonObj["data"] = $compareDate->format("F d, h:i A");
        }

        echo json_encode($jsonObj);
    } elseif("setUnread" == $cat) {
        $alertID = $_POST["alertID"];
        $readOrNot = $_POST["readOrNot"];

        echo $processing->updateAlerts($readOrNot, $alertID);
    } elseif("getLockStatus" == $cat) {
        $componentID = $_POST["componentID"];

        echo $processing->getLockStatus($componentID);
    } elseif("getComponentLog" == $cat) {
        $componentID = $_POST["componentID"];

        if($logs = $processing->getLogs($componentID, 1)) {
            $log = array_pop($logs);
            echo $log->getValue();
        }else{
            echo false;
        }
    } elseif("getAlerts" == $cat) {
        echo $num = $processing->getNumAlert();
    }
}
