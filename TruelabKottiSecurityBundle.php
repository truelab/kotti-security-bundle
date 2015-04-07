<?php

namespace Truelab\KottiSecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Truelab\KottiSecurityBundle\DependencyInjection\Security\Factory\KottiFactory;

class TruelabKottiSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new KottiFactory());
    }
}
