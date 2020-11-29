<?php


class Alert {
    private $AlertID;
    private $Time;
    private $ReadOrNot;
    private $AlertMessage;
    private $DeviceID;
    private $DeviceName;

    public function __construct($AlertID, $Time, $ReadOrNot, $AlertMessage, $DeviceID) {
        $this->AlertID = $AlertID;
        $this->Time = $Time;
        $this->ReadOrNot = $ReadOrNot;
        $this->AlertMessage = $AlertMessage;
        $this->DeviceID = $DeviceID;
    }

    public function getAlertID() {
        return $this->AlertID;
    }

    public function setAlertID($AlertID) {
        $this->AlertID = $AlertID;
    }

    public function getTime() {
        return $this->Time;
    }

    public function setTime($Time) {
        $this->Time = $Time;
    }

    public function getReadOrNot() {
        return $this->ReadOrNot;
    }

    public function setReadOrNot($ReadOrNot) {
        $this->ReadOrNot = $ReadOrNot;
    }

    public function getAlertMessage() {
        return $this->AlertMessage;
    }

    public function setAlertMessage($AlertMessage) {
        $this->AlertMessage = $AlertMessage;
    }

    public function getDeviceID() {
        return $this->DeviceID;
    }

    public function setDeviceID($DeviceID) {
        $this->DeviceID = $DeviceID;
    }

    public function getDeviceName() {
        return $this->DeviceName;
    }

    public function setDeviceName($DeviceName) {
        $this->DeviceName = $DeviceName;
    }
}