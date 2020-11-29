<?php


class Log
{
    private $LogID;
    private $Value;
    private $Time;

    public function __construct($LogID, $Value, $Time)
    {
        $this->LogID = $LogID;
        $this->Value = $Value;
        $this->Time = $Time;
    }

    public function getLogID()
    {
        return $this->LogID;
    }

    public function setLogID($LogID)
    {
        $this->LogID = $LogID;
    }

    public function getValue()
    {
        return $this->Value;
    }

    public function setValue($Value)
    {
        $this->Value = $Value;
    }

    public function getTime()
    {
        return $this->Time;
    }

    public function setTime($Time)
    {
        $this->Time = $Time;
    }
}