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
        $this->kottiSecurityContext = new KottiSecurityContext(
            $this->getSessionMock()
        );
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

    public function testActAsAnonymousDefault()
    {
        $session = $this->getSessionMock();

        $session
            ->expects($this->once())
            ->method('get')
            ->with(KottiSecurityContext::ACT_AS_ANON_SESSION_KEY, false)
            ->willReturn(false);

        $securityContext = new KottiSecurityContext($session);

        $this->assertEquals(false, $securityContext->actAsAnonymous());
    }

    public function testActAsAnonymousSetTrue()
    {
        $session = $this->getSessionMock();

        $session
            ->expects($this->any())
            ->method('set')
            ->with(KottiSecurityContext::ACT_AS_ANON_SESSION_KEY, true);

        $session
            ->expects($this->any())
            ->method('get')
            ->with(KottiSecurityContext::ACT_AS_ANON_SESSION_KEY, false)
            ->willReturn(true);

        $securityContext = new KottiSecurityContext($session);

        $this->assertEquals(true, $securityContext->actAsAnonymous(true));
    }

    protected function getSessionMock()
    {
        return $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Session\SessionInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

}
