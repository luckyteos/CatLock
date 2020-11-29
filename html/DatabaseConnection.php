<?php
// Is it really secure? Dont know ¯\_(ツ)_/¯
class dbconnection {
    // Properties
    private $conn;

    function __construct($dbUser) {
        $servername = "localhost";
        $password = "NewUserP@ssw0rd";
        $database = "PussyLocker";

        // Create connection
        $this->conn = new mysqli($servername, $dbUser, $password, $database);
    }

    // Select *
    function getSelect($statement) {
        return $this->conn->query($statement);
    }

    // Return connection
    function getConn() {
        return $this->conn;
    }

    // Check connection
    function checkConn() {
        if ($this->conn->connect_error) {
            return false;
        }else {
            return true;
        }
    }

    function __destruct() {
        $this->conn->close();
    }
}
