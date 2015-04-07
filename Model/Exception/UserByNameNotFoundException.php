<?php

namespace Truelab\KottiSecurityBundle\Model\Exception;

/**
 * Class UserByNameNotFoundException
 * @package Truelab\KottiSecurityBundle\Model\Exception
 */
class UserByNameNotFoundException extends \Exception
{
    public function __construct($name = "", $code = 0, \Exception $previous = null)
    {
        $message = sprintf('User with name : "%s" was not found', $name);
        parent::__construct($message, $code, $previous);
    }
}
