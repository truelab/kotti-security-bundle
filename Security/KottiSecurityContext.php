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

    /**
     * Check if $role (string) is present in the roles array
     * WARN!! it's stupid, we actually don't use the role hierarchy nor ACL nor Symfony voters
     * e.g. it's not like using: ```is_granted('ROLE_ADMIN')```
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->user ? in_array($role, $this->user->getRoles()) : false;
    }
}
