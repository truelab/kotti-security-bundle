<?php

namespace Truelab\KottiSecurityBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

        return [
            'admin' => $admin,
            'backend_base_url' => $backendBaseUrl
        ];
    }
}
