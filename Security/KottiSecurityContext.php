<?php

namespace Truelab\KottiSecurityBundle\Security;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Truelab\KottiSecurityBundle\Model\PrincipalInterface;

/**
 * Class KottiSecurityContext
 * @package Truelab\KottiSecurityBundle\Security
 */
class KottiSecurityContext implements KottiSecurityContextInterface
{
    private $session;

    private $actAsAnonymousIsActive;

    const ACT_AS_ANON_SESSION_KEY = 'kotti_security_act_as_anonymous';

    public function __construct(SessionInterface $session, $actAsAnonymousIsActive = false)
    {
        $this->session = $session;
        $this->actAsAnonymousIsActive = $actAsAnonymousIsActive;
    }

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

    /**
     * Return if current user act as anonymous
     * With $flag arg you can change the state (true|false)
     *
     * @param bool $flag
     *
     * @return bool
     */
    public function actAsAnonymous($flag = null)
    {
        if(!$this->actAsAnonymousIsActive) {
            return false;
        }

        if($this->actAsAnonymousIsActive && !$flag !== null && is_bool($flag)) {
            $this->session->set(self::ACT_AS_ANON_SESSION_KEY, $flag);
        }

        return $this->session->get(self::ACT_AS_ANON_SESSION_KEY, false);
    }
}
