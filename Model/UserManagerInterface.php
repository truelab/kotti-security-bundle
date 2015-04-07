<?php

namespace Truelab\KottiSecurityBundle\Model;

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
     */
    public function loadByName($name);
}
