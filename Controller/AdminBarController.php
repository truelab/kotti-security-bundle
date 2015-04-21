<?php

namespace Truelab\KottiSecurityBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


/**
 * Class AdminBarController
 * @package Truelab\KottiSecurityBundle\Controller
 */
class AdminBarController extends Controller
{
    /**
     * Render the admin bar
     *
     * @Template()
     *
     * @return array
     */
    public function viewAction()
    {
        $admin = $this->getUser();
        $admin = $admin && $this->isGranted('ROLE_ADMIN') ? $admin : null;
        $backendBaseUrl = $this->container->hasParameter('truelab_kotti_frontend.backend_base_url') ?
            $this->container->getParameter('truelab_kotti_frontend.backend_base_url') : 'http://localhost:5000';

        $kottiSecurityContext = $this->get('truelab_kotti_security.kotti_security_context');
        $request = $this->container->get('request_stack')->getMasterRequest();

        return [
            'admin' => $admin,
            'backend_base_url' => $backendBaseUrl,
            'act_as_anonymous' => [
                'active' => $this->actAsAnonymousIsActive(),
                'flag' => $kottiSecurityContext->actAsAnonymous(),
                'referrer' => $request->getUri()
            ]
        ];
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actAsAnonymousAction(Request $request)
    {
        $active = $this->actAsAnonymousIsActive();

        if(!$this->isGranted('ROLE_ADMIN') && $active !== false) {
            throw new AccessDeniedHttpException();
        }

        $flag = (bool) $request->get('flag', false);
        $referrer = $request->get('referrer', '/');
        $kottiSecurityContext = $this->get('truelab_kotti_security.kotti_security_context');
        $kottiSecurityContext->actAsAnonymous($flag);

        return $this->redirect($referrer);
    }

    protected function actAsAnonymousIsActive()
    {
        return   $active = $this->container->hasParameter('truelab_kotti_security.act_as_anonymous') ? $this->container->getParameter('truelab_kotti_security.act_as_anonymous') : false;
    }
}
