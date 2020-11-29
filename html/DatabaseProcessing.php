<?php

require_once("Device.php");
require_once("Component.php");
require_once("Log.php");
require_once("Alert.php");
require_once('DatabaseConnection.php');

class DatabaseProcessing {
    private $db;

    function __construct() {
        $this->db = new dbconnection('NewUser');
    }

    public function getEverything() {
        if($devices = $this->getAllDevice()) {
            if($devices = $this->getAllComponents($devices)) {
                foreach ($devices as $device) {
                    $components = $device->getComponents();
                    foreach ($components as $component) {
                        $componentID = $component->getComponentID();
                        $logs = $this->getLogs($componentID, 24);
                        if($logs) {
                            $component->setLogs($logs);
                        }
                    }
                }

                return $devices;
            }
        }

        return false;
    }

    public function getEverythingDashboard() {
        if($devices = $this->getDeviceStatus("On")) {
            if($devices = $this->getAllComponents($devices)) {
                foreach ($devices as $device) {
                    $components = $device->getComponents();
                    foreach ($components as $component) {
                        $componentID = $component->getComponentID();
                        $logs = $this->getLogs($componentID, 1);
                        if($logs) {
                            $log = array_pop($logs);
                            $component->setLogs($log);
                        }
                    }
                }

                return $devices;
            }
        }

        return false;
    }

    public function getEverythingStatistics() {
        if($devices = $this->getAllDevice()) {
            if($devices = $this->getAllComponents($devices)) {
                foreach ($devices as $device) {
                    $components = $device->getComponents();
                    foreach ($components as $component) {
                        $componentID = $component->getComponentID();
                        $logs = $this->getLogsStatistics($componentID, 30, 15);
                        if($logs) {
                            $component->setLogs($logs);
                        }
                    }
                }

                return $devices;
            }
        }

        return false;
    }

    public function insertNewDevice($ip, $json_line) {
        if($this->db->checkConn()) {
            // Create New Device
            $bytes = random_bytes(5);
            $deviceID = bin2hex($bytes);
            $status = "On";

            $stmt = $this->db->getConn()->prepare("INSERT INTO device(DeviceID, DeviceName, Status, Address) VALUES (?,?,?,?);");
            $stmt->bind_param("ssss", $deviceID, $deviceID, $status, $ip);

            if(!$stmt->execute()){
                return false;
            }

            // Create New Components
            $bytes = random_bytes(5);
            $componentID = bin2hex($bytes);
            $sensorCat = "Sensor";

            $stmt = $this->db->getConn()->prepare("INSERT INTO device_component(ComponentID, ComponentName, Category, Status, StoredValue, DeviceID) VALUES (?,?,?,?,?,?);");
            $stmt->bind_param("ssssss", $componentID, $componentID, $sensorCat, $status, number_format($json_line["Threshold"], 1), $deviceID);

            if(!$stmt->execute()){
                return false;
            }

            $bytes = random_bytes(5);
            $componentID = bin2hex($bytes);
            $lockCat = "Lock";

            $stmt = $this->db->getConn()->prepare("INSERT INTO device_component(ComponentID, ComponentName, Category, Status, StoredValue, DeviceID) VALUES (?,?,?,?,?,?);");
            $stmt->bind_param("ssssss", $componentID, $componentID, $lockCat, $status, $json_line["Lock Status"], $deviceID);

            if(!$stmt->execute()){
                return false;
            }

            return true;
        }

        return false;
    }

    public function getAllDevice() {
        if($this->db->checkConn()) {
            $devices = array();
            $result = $this->db->getSelect("SELECT DeviceID, DeviceName, Status FROM device;");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    array_push($devices, new Device($row["DeviceID"], $row["DeviceName"], $row["Status"]));
                }

                return $devices;
            }
        }

        return false;
    }

    public function getDevice($deviceID) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT DeviceID, DeviceName, Status FROM device WHERE DeviceID=?");
            $stmt->bind_param("s", $deviceID);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return new Device($row["DeviceID"], $row["DeviceName"], $row["Status"]);
            }
        }

        return false;
    }

    public function getDeviceStatus($status) {
        if($this->db->checkConn()) {
            $devices = array();

            $stmt = $this->db->getConn()->prepare("SELECT DeviceID, DeviceName, Status FROM device WHERE Status=?");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    array_push($devices, new Device($row["DeviceID"], $row["DeviceName"], $row["Status"]));
                }

                return $devices;
            }
        }

        return false;
    }

    public function updateDeviceName($deviceID, $deviceName) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("UPDATE device SET DeviceName=? WHERE DeviceID=?;");
            $stmt->bind_param("ss", $deviceName, $deviceID);

            if($stmt->execute()) {
                return true;
            }
        }

        return false;
    }

    public function getAllComponents($devices) {
        if($this->db->checkConn()) {
            // Get All Components
            foreach ($devices as $device) {
                $stmt = $this->db->getConn()->prepare("SELECT dc.ComponentID, dc.ComponentName, dc.Category, dc.Status, dc.StoredValue FROM device_component AS dc INNER JOIN device AS d ON dc.DeviceID = d.DeviceID WHERE dc.DeviceID=?");
                $deviceID = $device->getDeviceID();
                $stmt->bind_param("s", $deviceID);
                $stmt->execute();
                $result = $stmt->get_result();

                // If not empty row
                if ($result->num_rows > 0) {
                    $components = array();
                    while ($row = $result->fetch_assoc()) {
                        $component = new Component($row["ComponentID"], $row["ComponentName"], $row["Category"], $row["Status"]);
                        $component->setStoredValue($row["StoredValue"]);
                        array_push($components, $component);
                    }
                    $device->setComponents($components);
                }
            }

            return $devices;
        }

        return false;
    }
    
    public function updateComponent($ip, $json_line) {
        $success = true;

        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT dc.ComponentID, dc.Category FROM device AS d INNER JOIN device_component AS dc ON d.DeviceID = dc.DeviceID WHERE Address=?;");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $componentID = $row["ComponentID"];
                    $lock = "Unlocked";
                    
                    $stmt = $this->db->getConn()->prepare("UPDATE device_component SET StoredValue=? WHERE ComponentID=? AND Category='Lock';");
                    $stmt->bind_param("ss", $lock, $componentID);

                    if(!$stmt->execute()){
                        $success = false;
                    }
                }
            }
        }else {
            $success = false;
        }

        return $success;
    }

    public function getComponent($componentID) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT ComponentID, ComponentName, Category, Status, StoredValue FROM device_component WHERE ComponentID=?");
            $stmt->bind_param("s", $componentID);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $component = new Component($row["ComponentID"], $row["ComponentName"], $row["Category"], $row["Status"]);
                $component->setStoredValue($row["StoredValue"]);

                return $component;
            }
        }

        return false;
    }

    public function getAllComponentsStoredValue($ip) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT dc.ComponentID, dc.ComponentName, dc.Category, dc.Status, dc.StoredValue FROM device_component AS dc INNER JOIN device AS d ON dc.DeviceID = d.DeviceID WHERE d.Address=? ORDER BY dc.Category DESC;");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $components = array();
                while ($row = $result->fetch_assoc()) {
                    $component = new Component($row["ComponentID"], $row["ComponentName"], $row["Category"], $row["Status"]);
                    $component->setStoredValue($row["StoredValue"]);
                    array_push($components, $component);
                }

                return $components;
            }
        }

        return false;
    }

    public function updateComponents($deviceID, $temp, $lock) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("UPDATE device_component SET StoredValue=? WHERE DeviceID=? AND Category='Sensor';");
            $stmt->bind_param("ss", $temp, $deviceID);

            if(!$stmt->execute()) {
                return false;
            }

            $stmt = $this->db->getConn()->prepare("UPDATE device_component SET StoredValue=? WHERE DeviceID=? AND Category='Lock';");
            $stmt->bind_param("ss", $lock, $deviceID);

            if(!$stmt->execute()) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function getLogs($componentID, $limit) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT LogID, Value, Time FROM device_log WHERE ComponentID=? ORDER BY Time DESC LIMIT ?;");
            $stmt->bind_param("si", $componentID, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $logs = array();

                while ($row = $result->fetch_assoc()) {
                    $log = new Log($row["LogID"], $row["Value"], $row["Time"]);
                    array_push($logs, $log);
                }

                return $logs;
            }
        }

        return false;
    }

    public function getLogsStatistics($componentID, $limit, $interval) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT LogID, FLOOR(UNIX_TIMESTAMP(Time)/(? * 60)) AS Timekey,from_unixtime(FLOOR(UNIX_TIMESTAMP(Time)/(? * 60))* ? * 60) AS Timestamp, AVG(Value) AS Average FROM device_log WHERE ComponentID=? GROUP BY Timekey LIMIT ?;");
            $stmt->bind_param("iiisi", $interval, $interval, $interval, $componentID, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $logs = array();

                while ($row = $result->fetch_assoc()) {
                    $log = new Log($row["LogID"], $row["Average"], $row["Timestamp"]);
                    array_push($logs, $log);
                }

                return $logs;
            }
        }

        return false;
    }

    public function insertLogs($log) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT LogID, Value, Time FROM device_log WHERE ComponentID=? ORDER BY Time DESC LIMIT ?;");
            $stmt->bind_param("si", $componentID, $limit);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $logs = array();

                while ($row = $result->fetch_assoc()) {
                    $log = new Log($row["LogID"], $row["Value"], $row["Time"]);
                    array_push($logs, $log);
                }

                return $logs;
            }
        }

        return false;
    }

    public function insertComponentLogFromIP($ip, $json_line) {
        $success = true;

        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT dc.ComponentID, dc.Category FROM device AS d INNER JOIN device_component AS dc ON d.DeviceID = dc.DeviceID WHERE Address=?;");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $componentID = $row["ComponentID"];

                    $stmt = $this->db->getConn()->prepare("INSERT INTO device_log(Value, ComponentID) VALUES (?, ?);");

                    if("Sensor" == $row["Category"]) {
                        $stmt->bind_param("ss", $json_line["Temperature"], $componentID);
                    }elseif("Lock" == $row["Category"]) {
                        $stmt->bind_param("ss", $json_line["Lock Status"], $componentID);
                    }

                    if(!$stmt->execute()){
                        $success = false;
                    }
                }
            }
        }else {
            $success = false;
        }

        return $success;
    }

    public function getAlerts() {
        if($this->db->checkConn()) {
            $result = $this->db->getSelect("SELECT da.AlertID, da.Time, da.ReadOrNot, da.AlertMessage, d.DeviceID, d.DeviceName FROM device_alert AS da INNER JOIN device AS d ON da.DeviceID=d.DeviceID ORDER BY Time DESC;");

            // If not empty row
            if ($result->num_rows > 0) {
                $alerts = array();

                while ($row = $result->fetch_assoc()) {
                    $alert = new Alert($row["AlertID"], $row["Time"], $row["ReadOrNot"], $row["AlertMessage"], $row["DeviceID"]);
                    $alert->setDeviceName($row["DeviceName"]);
                    array_push($alerts, $alert);
                }

                return $alerts;
            }
        }

        return false;
    }

    public function updateAlerts($readOrNot, $alertID) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("UPDATE device_alert SET ReadOrNot=? WHERE AlertID=?;");
            $stmt->bind_param("si", $readOrNot, $alertID);

            if($stmt->execute()) {
                return true;
            }
        }

        return false;
    }

    public function updateAllAlerts() {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("UPDATE device_alert SET ReadOrNot='Read' WHERE ReadOrNot='Unread';");

            if($stmt->execute()) {
                return true;
            }
        }

        return false;
    }

    public function insertAlert($value, $ip) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT d.DeviceID FROM device AS d INNER JOIN device_component AS dc ON d.DeviceID = dc.DeviceID WHERE Address=?;");
            $stmt->bind_param("s", $ip);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $deviceID = $row["DeviceID"];

                $stmt2 = $this->db->getConn()->prepare("INSERT INTO device_alert(AlertMessage, DeviceID) VALUES (?,?);");
                $stmt2->bind_param("ss", $value, $deviceID);

                if($stmt2->execute()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getAlertTime($alertID) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT Time FROM device_alert WHERE AlertID=?;");
            $stmt->bind_param("i", $alertID);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $alerts = array();

                $row = $result->fetch_assoc();

                return $row["Time"];
            }
        }

        return false;
    }

    public function getNumAlert() {
        if($this->db->checkConn()) {
            $result = $this->db->getSelect("SELECT count(AlertID) AS num FROM device_alert WHERE ReadOrNot='Unread';");

            // If not empty row
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                return $row["num"];
            }
        }

        return false;
    }
    
    public function getLockStatus($componentID) {
        if($this->db->checkConn()) {
            $stmt = $this->db->getConn()->prepare("SELECT StoredValue FROM device_component WHERE ComponentID=?;");
            $stmt->bind_param("s", $componentID);
            $stmt->execute();
            $result = $stmt->get_result();

            // If not empty row
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                return $row["StoredValue"];
            }
        }
        
        return false;
    }
}
