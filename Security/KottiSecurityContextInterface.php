<?php

namespace Truelab\KottiSecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Model\UserManagerInterface;
use Truelab\KottiSecurityBundle\Model\PrincipalInterface;

/**
 * Class KottiSecurityContextInterface
 * @package Truelab\KottiSecurityBundle\Security
 */
interface KottiSecurityContextInterface
{
    public function setUser(PrincipalInterface $user);

    public function hasRole($role);
}
