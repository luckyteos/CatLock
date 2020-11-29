<?php

class Component
{
    private $ComponentID;
    private $ComponentName;
    private $Category;
    private $Status;
    private $StoredValue;
    private $Logs;

    public function __construct($ComponentID, $ComponentName, $Category, $Status)
    {
        $this->ComponentID = $ComponentID;
        $this->ComponentName = $ComponentName;
        $this->Category = $Category;
        $this->Status = $Status;
    }

    public function getComponentID()
    {
        return $this->ComponentID;
    }

    public function setComponentID($ComponentID)
    {
        $this->ComponentID = $ComponentID;
    }

    public function getComponentName()
    {
        return $this->ComponentName;
    }

    public function setComponentName($ComponentName)
    {
        $this->ComponentName = $ComponentName;
    }

    public function getCategory()
    {
        return $this->Category;
    }

    public function setCategory($Category)
    {
        $this->Category = $Category;
    }

    public function getStatus()
    {
        return $this->Status;
    }

    public function setStatus($Status)
    {
        $this->Status = $Status;
    }

    public function getStoredValue()
    {
        return $this->StoredValue;
    }

    public function setStoredValue($StoredValue)
    {
        $this->StoredValue = $StoredValue;
    }

    public function getLogs()
    {
        return $this->Logs;
    }

    public function setLogs($Logs)
    {
        $this->Logs = $Logs;
    }


}