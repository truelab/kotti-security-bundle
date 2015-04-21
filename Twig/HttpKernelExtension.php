<?php

namespace Truelab\KottiSecurityBundle\Twig;

/**
 * Class HttpKernelExtension
 * @package Truelab\KottiSecurityBundle\Twig
 */
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Truelab\KottiSecurityBundle\Security\KottiSecurityContextInterface;

/**
 * Class HttpKernelExtension
 * @package MIP\CoreBundle\Twig
 */
class HttpKernelExtension extends \Twig_Extension
{

    private $handler;

    private $authorizationChecker;

    private $tokenStorage;

    private $kottiSecurityContext;

    public function __construct(FragmentHandler $handler,
                                TokenStorageInterface $tokenStorage,
                                AuthorizationCheckerInterface $authorizationChecker,
                                KottiSecurityContextInterface $kottiSecurityContext)
    {
        $this->handler = $handler;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->kottiSecurityContext = $kottiSecurityContext;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_esi', array($this, 'renderEsi'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders a fragment with ESI strategy bypassing it if user is an authenticated admin and is not acting as anonymous.
     *
     * @param string|ControllerReference $uri     A URI as a string or a ControllerReference instance
     * @param array                      $options An array of options
     *
     * @return string The fragment content
     *
     * @see FragmentHandler::render()
     */
    public function renderEsi($uri, $options = [])
    {
        // we don't use esi fragment cache when user is an admin and is not acting as anonymous (admin always see fresh data)
        if($this->isConnected() &&
            $this->authorizationChecker->isGranted('ROLE_ADMIN') &&
            $this->kottiSecurityContext->actAsAnonymous() !== true) {
            return $this->handler->render($uri, 'inline', $options);
        }else{
            return $this->handler->render($uri, 'esi', $options);
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'truelab_security_http_extension';
    }

    /**
     * Check if a connected user exists
     *
     * @return bool
     */
    protected function isConnected()
    {
        return $this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser();
    }
}
