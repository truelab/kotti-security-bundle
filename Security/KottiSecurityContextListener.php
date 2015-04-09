<?php

namespace Truelab\KottiSecurityBundle\Security;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Model\UserManagerInterface;
use Truelab\KottiSecurityBundle\Model\PrincipalInterface;

/**
 * Class KottiSecurityContextListener
 * @package Truelab\KottiSecurityBundle\Security
 */
class KottiSecurityContextListener
{
    private $authenticationHelper;

    private $userManager;

    private $kottiSecurityContext;

    public function __construct(AuthenticationHelperInterface $authenticationHelper,
                                UserManagerInterface $userManager,
                                KottiSecurityContextInterface $kottiSecurityContext,
                                LoggerInterface $logger)
    {
        $this->authenticationHelper = $authenticationHelper;
        $this->userManager = $userManager;
        $this->kottiSecurityContext = $kottiSecurityContext;
        $this->logger = $logger;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if(!$this->authenticationHelper->canIdentify($request)) {
            $this->logger->debug('KottiSecurityContextListener can\'t identify current request', [
                'request' => $request->__toString()
            ]);
            return;
        }

        try{
            $identity = $this->authenticationHelper->identify($event->getRequest());
            $user = $this->userManager->loadByName($identity->getUserId());

            if($user instanceof PrincipalInterface) {
                $this->kottiSecurityContext->setUser($user);
            }
            return;
        }catch (IdentifyException $e) {
            $this->logger->error($e, [
                'request' => $request->__toString()
            ]);
            return;
        }
    }
}
