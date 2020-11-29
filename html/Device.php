<?php


class Device
{
    private $DeviceID;
    private $DeviceName;
    private $Status;
    private $Components;

    public function __construct($DeviceID, $DeviceName, $Status)
    {
        $this->DeviceID = $DeviceID;
        $this->DeviceName = $DeviceName;
        $this->Status = $Status;
    }

    public function getDeviceID()
    {
        return $this->DeviceID;
    }

    public function setDeviceID($DeviceID)
    {
        $this->DeviceID = $DeviceID;
    }

    public function getDeviceName()
    {
        return $this->DeviceName;
    }

    public function setDeviceName($DeviceName)
    {
        $this->DeviceName = $DeviceName;
    }

    public function getStatus()
    {
        return $this->Status;
    }

    public function setStatus($Status)
    {
        $this->Status = $Status;
    }

    public function getComponents()
    {
        return $this->Components;
    }

    public function setComponents($Components)
    {
        $this->Components = $Components;
    }
}