<?php

namespace Truelab\KottiSecurityBundle\Twig;
use Truelab\KottiSecurityBundle\Util\ContextAdapter;

/**
 * Class KottiSecurityExtension
 * @package Truelab\KottiSecurityBundle\Twig
 */
class KottiSecurityExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('kotti_security_context_adapter', function ($input) {
                return ContextAdapter::wrap($input);
            })
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
       return 'kotti_security';
    }
}
