<?php

namespace Truelab\KottiSecurityBundle\Security\Authentication\Provider;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Truelab\KottiSecurityBundle\Security\Authentication\Token\KottiUserToken;

/**
 * Class KottiProvider
 * @package Truelab\KottiSecurityBundle\Security\Authentication\Provider
 */
class KottiProvider implements AuthenticationProviderInterface
{

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }


    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user /*&& $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())*/) {
            $authenticatedToken = new KottiUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The Kotti authentication failed.');
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return bool true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof KottiUserToken;
    }
}
