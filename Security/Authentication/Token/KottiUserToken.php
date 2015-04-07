<?php

namespace Truelab\KottiSecurityBundle\Security\Authentication\Token;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class KottiUserToken
 * @package Truelab\KottiSecurityBundle\Security\Authentication\Token
 */
class KottiUserToken extends AbstractToken
{

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }
}
