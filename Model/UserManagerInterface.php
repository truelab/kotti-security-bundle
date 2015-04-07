<?php

namespace Truelab\KottiSecurityBundle\Model;
use Truelab\KottiSecurityBundle\Model\Exception\UserByNameNotFoundException;

/**
 * Interface UserManagerInterface
 * @package Truelab\KottiSecurityBundle\Model
 */
interface UserManagerInterface
{
    /**
     * @param string $name
     *
     * @return PrincipalInterface
     *
     * @throws UserByNameNotFoundException
     */
    public function loadByName($name);
}
