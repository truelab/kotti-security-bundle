<?php

namespace Truelab\KottiSecurityBundle\Tests\Security;
use Truelab\KottiSecurityBundle\Security\KottiSecurityContextInterface;
use Truelab\KottiSecurityBundle\Security\KottiSecurityContext;

/**
 * Class KottiSecurityContextTest
 * @package Truelab\KottiSecurityBundle\Tests\Security
 */
class KottiSecurityContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KottiSecurityContextInterface
     */
    private $kottiSecurityContext;

    public function setUp()
    {
        $this->kottiSecurityContext = new KottiSecurityContext();
    }

    public function testHasRoleReturnFalseWhenUserIsNotSet()
    {
        $actual = $this->kottiSecurityContext->hasRole('ROLE_FUCKER');
        $expected = false;

        $this->assertEquals($expected, $actual);
    }

    public function testHasRoleReturnFalse()
    {
        $user = $this->getMock('Truelab\KottiSecurityBundle\Model\PrincipalInterface');

        $user
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN']);

        $this->kottiSecurityContext->setUser($user);

        $actual = $this->kottiSecurityContext->hasRole('ROLE_USER');
        $expected = false;

        $this->assertEquals($expected, $actual);
    }

    public function testHasRoleAdmin()
    {
        $user = $this->getMock('Truelab\KottiSecurityBundle\Model\PrincipalInterface');

        $user
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn(['ROLE_ADMIN']);

        $this->kottiSecurityContext->setUser($user);

        $actual = $this->kottiSecurityContext->hasRole('ROLE_ADMIN');
        $expected = true;

        $this->assertEquals($expected, $actual);
    }

}
