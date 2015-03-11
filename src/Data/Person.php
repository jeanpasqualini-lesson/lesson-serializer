<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/10/15
 * Time: 6:51 AM
 */
namespace Data;

class Person {

    private $firstName;

    private $lastname;

    public function __construct($firstname = "", $lastname = "")
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public static function getDirectory()
    {
        return __DIR__;
    }

    public static function getClass()
    {
        return __CLASS__;
    }

    public function __toString()
    {
        return $this->firstname." ".$this->lastname;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function setFirstName($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

}