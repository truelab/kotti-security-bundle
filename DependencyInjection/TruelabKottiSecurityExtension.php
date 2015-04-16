<?php

namespace Truelab\KottiSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TruelabKottiSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('truelab_kotti_security.auth.secret', $config['auth']['secret']);
        $container->setParameter('truelab_kotti_security.auth.cookie_name', $config['auth']['cookie_name']);
        $container->setParameter('truelab_kotti_security.auth.hash_alg', $config['auth']['hash_alg']);
        $container->setParameter('truelab_kotti_security.auth.include_ip', $config['auth']['include_ip']);
        $container->setParameter('truelab_kotti_security.auth.see_as_anonymous', $config['see_as_anonymous']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
