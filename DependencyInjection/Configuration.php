<?php

namespace Truelab\KottiSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    public function getDefaultConfiguration()
    {
        return [
            'auth' => [
                'hash_alg' => 'sha512',
                'cookie_name' => 'auth_tkt',
                'include_ip' => false
            ],
            'act_as_anonymous' => false,
            'twig' => [
                'http_kernel_extension_override' => false
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder   = new TreeBuilder();
        $defaultConfig = $this->getDefaultConfiguration();

        $rootNode = $treeBuilder->root('truelab_kotti_security');


        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('auth')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->scalarNode('secret')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->treatTrueLike(null)
                            ->treatFalseLike(null)
                        ->end()
                        ->scalarNode('cookie_name')
                            ->cannotBeEmpty()
                            ->treatNullLike(false)
                            ->treatTrueLike(false)
                            ->treatFalseLike($defaultConfig['auth']['cookie_name'])
                            ->defaultValue($defaultConfig['auth']['cookie_name'])
                        ->end()
                        ->scalarNode('hash_alg')
                            ->cannotBeEmpty()
                            ->treatNullLike(false)
                            ->treatTrueLike(false)
                            ->treatFalseLike($defaultConfig['auth']['hash_alg'])
                            ->defaultValue($defaultConfig['auth']['hash_alg'])
                        ->end()
                        ->scalarNode('include_ip')
                            ->cannotBeEmpty()
                            ->treatNullLike(false)
                            ->treatFalseLike($defaultConfig['auth']['include_ip'])
                            ->defaultValue($defaultConfig['auth']['include_ip'])
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('act_as_anonymous')
                    ->cannotBeEmpty()
                    ->defaultValue($defaultConfig['act_as_anonymous'])
                ->end()
                ->arrayNode('twig')
                    ->cannotBeEmpty()
                     ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('http_kernel_extension_override')
                            ->treatNullLike(false)
                            ->defaultValue($defaultConfig['twig']['http_kernel_extension_override'])
                        ->end()
                    ->end()
            ->end();


        return $treeBuilder;
    }
}
