<?php

namespace Truelab\KottiSecurityBundle\Security\Firewall;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelperInterface;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Security\Authentication\Token\KottiUserToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

/**
 * Class KottiSecurityListener
 * @package Truelab\KottiSecurityBundle\Security\Firewall
 */
class KottiSecurityListener implements ListenerInterface
{
    private $authenticationHelper;

    private $tokenStorage;

    private $authenticationManager;

    public function __construct(AuthenticationHelperInterface $authenticationHelper, TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->authenticationHelper = $authenticationHelper;
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if(!$this->authenticationHelper->canIdentify($request)) {
            return;
        }

        try{
            $identity = $this->authenticationHelper->identify($request);
        }catch(IdentifyException $e) {
            // FIXME LOGGER
            return;
        }

        $token = new KottiUserToken();
        $token->setUser($identity->getUserId());

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);
            return;
        } catch (AuthenticationException $failed) {

            // FIXME LOGGER
            // ... you might log something here

            // To deny the authentication clear the token. This will redirect to the login page.
            // Make sure to only clear your token, not those of other authentication listeners.
            // $token = $this->tokenStorage->getToken();
            // if ($token instanceof WsseUserToken && $this->providerKey === $token->getProviderKey()) {
            //     $this->tokenStorage->setToken(null);
            // }
            return;
        }
    }
}
