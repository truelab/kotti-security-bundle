<?php

namespace Truelab\KottiSecurityBundle\Tests\DependencyInjection;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Truelab\KottiSecurityBundle\DependencyInjection\Configuration;

/**
 * Class ConfigurationTest
 * @package Truelab\KottiSecurityBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "auth" at path "truelab_kotti_security" must be configured
     */
    public function testAuthKeyMustBeConfiguredException()
    {
        $this->assertProcessedConfigurationEquals([
            []
        ],[]);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "secret" at path "truelab_kotti_security.auth" must be configured
     */
    public function testSecretKeyMustBeConfiguredException()
    {
        $this->assertProcessedConfigurationEquals([
            ['auth' => []]
        ], []);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The path "truelab_kotti_security.auth.secret" cannot contain an empty value, but got
     *
     * @dataProvider emptyValueProvider
     *
     * @param $secret - a value not valid (empty) that raise exception
     */
    public function testSecretKeyCannotBeEmptyException($secret)
    {
        $this->assertProcessedConfigurationEquals([
            ['auth' => ['secret'=> $secret ]]
        ], []);
    }

    public function emptyValueProvider()
    {
        return [
            [''],
            [null],
            [false]
        ];
    }

    public function testDefault()
    {
        $expected = [
            'auth' => [
                'secret' => 'qwerty',
                'hash_alg' => 'sha512',
                'cookie_name' => 'auth_tkt',
                'include_ip' => false
            ]
        ];

        $this->assertProcessedConfigurationEquals([
            ['auth' => ['secret' => 'qwerty' ]]
        ], $expected);

        $this->assertProcessedConfigurationEquals([
            ['auth' => [
                'secret' => 'qwerty',
                'hash_alg' => null,
                'cookie_name' => null
            ]]
        ], $expected);

        $this->assertProcessedConfigurationEquals([
            ['auth' => [
                'secret' => 'qwerty',
                'hash_alg' => false,
                'cookie_name' => false
            ]]
        ], $expected);

        $this->assertProcessedConfigurationEquals([
            ['auth' => [
                'secret' => 'qwerty',
                'hash_alg' => true,
                'cookie_name' => true
            ]]
        ], $expected);
    }
}
