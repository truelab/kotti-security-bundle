<?php

namespace Truelab\KottiSecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Model\UserManagerInterface;
use Truelab\KottiSecurityBundle\Model\PrincipalInterface;

/**
 * Class KottiSecurityContext
 * @package Truelab\KottiSecurityBundle\Security
 */
class KottiSecurityContext implements KottiSecurityContextInterface
{
    /**
     * @var PrincipalInterface
     */
    private $user;

    public function setUser(PrincipalInterface $user)
    {
        $this->user = $user;
    }

    public function hasRole($role)
    {
        if(!$this->user) {
            return false;
        }

        $userRoles = $this->user->getRoles();

        if(in_array($role, $userRoles)) {
            return true;
        }else{
            return false;
        }
    }
}
