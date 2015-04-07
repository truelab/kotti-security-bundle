<?php

namespace Truelab\KottiSecurityBundle\Tests\Security;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelperInterface;

/**
 * Class AuthenticationHelperFunctionalTest
 * @package Truelab\KottiSecurityBundle\Tests\Security
 *
 * @group functional
 */
class AuthenticationHelperFunctionalTest extends WebTestCase
{
    /**
     * @var AuthenticationHelperInterface
     */
    private $authenticationHelper;

    public function setUp()
    {
        $client = static::createClient();
        $this->authenticationHelper = $client->getContainer()->get('truelab_kotti_security.authentication_helper');
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Truelab\KottiSecurityBundle\Security\AuthenticationHelperInterface', $this->authenticationHelper);
    }
}
