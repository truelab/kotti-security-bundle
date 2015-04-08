<?php

namespace Truelab\KottiSecurityBundle\Tests\DependencyInjection;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Truelab\KottiSecurityBundle\DependencyInjection\TruelabKottiSecurityExtension;

/**
 * Class TruelabKottiSecurityExtensionTest
 * @package Truelab\KottiSecurityBundle\Tests\DependencyInjection
 */
class TruelabKottiSecurityExtensionTest extends AbstractExtensionTestCase
{

    /**
     * Return an array of container extensions you need to be registered for each test (usually just the container
     * extension you are testing.
     *
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return array(
            new TruelabKottiSecurityExtension()
        );
    }

    public function testAfterLoadingAllCorrectRequiredAndDefaultParametersHadBeenSet()
    {
        $expected = [
            'auth' => [
                'secret' => 'qwerty',
                'hash_alg' => 'sha512',
                'cookie_name' => 'auth_tkt',
                'include_ip' => false
            ]
        ];

        $this->load(array('auth' => [
            'secret' => 'qwerty'
        ]));

        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.secret', $expected['auth']['secret']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.cookie_name', $expected['auth']['cookie_name']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.hash_alg', $expected['auth']['hash_alg']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.include_ip', $expected['auth']['include_ip']);
    }

    public function testAfterLoadingAllParametersHadBeenSet()
    {
        $expected = [
            'auth' => [
                'secret' => 'cusbsgwh373d987gv',
                'hash_alg' => 'md5',
                'cookie_name' => 'custom_tookie_name',
                'include_ip' => true
            ]
        ];

        $this->load($expected);

        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.secret', $expected['auth']['secret']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.cookie_name', $expected['auth']['cookie_name']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.hash_alg', $expected['auth']['hash_alg']);
        $this->assertContainerBuilderHasParameter('truelab_kotti_security.auth.include_ip', $expected['auth']['include_ip']);

    }
}
