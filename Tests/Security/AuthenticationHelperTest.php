<?php

namespace Truelab\KottiSecurityBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelper;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelperInterface;
use Truelab\KottiSecurityBundle\Security\Exception\BadTicketException;

/**
 * Class AuthenticationHelperTest
 * @package Truelab\KottiSecurityBundle\Tests\Security
 */
class AuthenticationHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthenticationHelperInterface
     */
    private $helper;

    private $secret = 'qwerty';

    private $cookieName = 'auth_tkt';

    private $ticketParts = [
        // md5 digest
        'digest' => 'f5e4b03f95637b075f28ad874347781643b0b7f2aea4f7ed4fcd1e8c19479a40667f717476c86246a46fcafb95af1a2214fa3423fe96dec856a3c78b28356fbf',

        // hexadecimal timestamp
        'timestamp' => '55192448',

        // user id base 64 and url encoded
        'user_id'   => 'YWRtaW4%3D',

        'extra'     => 'userid_type:b64unicode'
    ];

    private $ticket;

    private $expectedTicketTimestamp = '1427711048';

    private $expectedTicketUserId = 'YWRtaW4=';

    public function setUp()
    {
        $this->ticket = $this->createTicket();
        $this->helper = new AuthenticationHelper($this->secret, $this->cookieName);
    }

    protected function createTicket( $digest = null, $timestamp = null, $userId = null, $delimiter = null, $extra = null )
    {
        $ticket = '';
        $ticket .= $digest !== null ? $digest : $this->ticketParts['digest'];
        $ticket .= $timestamp !== null ? $timestamp : $this->ticketParts['timestamp'];
        $ticket .= $userId !== null ? $userId : $this->ticketParts['user_id'];
        $ticket .= $delimiter !== null ? $delimiter : '!';
        $ticket .= $extra !== null ? $extra : $this->ticketParts['extra'];

        return '"' . $ticket . '"';
    }

    protected function getRequestMock()
    {
        $requestMock = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->cookies = $this->getMockBuilder('\Symfony\Component\HttpFoundation\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->cookies->expects($this->once())
            ->method('has')
            ->with($this->cookieName)
            ->willReturn(true);

        $requestMock->cookies->expects($this->once())
            ->method('get')
            ->with($this->cookieName)
            ->willReturn(new Cookie($this->cookieName, $this->ticket));

        return $requestMock;
    }

    /**
     * @expectedException \Truelab\KottiSecurityBundle\Security\Exception\BadTicketException
     */
    public function testParseTicketThrowException1()
    {
        $ticket = $this->createTicket(null, null, null, '', null);
        $this->helper->parseTicket($ticket);
    }

    public function testParseTicketResultContainsRequiredKeys()
    {
        $result = $this->helper->parseTicket($this->ticket);

        $this->assertArrayHasKey('digest', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('user_data', $result);
        $this->assertArrayHasKey('tokens', $result);
    }

    public function testParseTicketDigest()
    {
        $result = $this->helper->parseTicket($this->ticket);
        $this->assertEquals($this->ticketParts['digest'], $result['digest']);
    }

    public function testParseTicketTimestamp()
    {
        $result = $this->helper->parseTicket($this->ticket);
        $this->assertEquals($this->expectedTicketTimestamp, $result['timestamp']);
    }

    public function testParseTicketUserIdResult()
    {
        $result = $this->helper->parseTicket($this->ticket);
        $this->assertEquals($this->expectedTicketUserId, $result['user_id']);
    }

    public function testIdentifyIdentityResult()
    {
        $identity = $this->helper->identify($this->getRequestMock());
        $this->assertInstanceOf('Truelab\KottiSecurityBundle\Security\AuthenticationIdentityInterface', $identity);
    }

    public function testIdentifyIdentityResultUserId()
    {
        $identity = $this->helper->identify($this->getRequestMock());
        $this->assertEquals('admin', $identity->getUserId());
    }

    public function testIdentifyIdentityResultTimestamp()
    {
        $identity = $this->helper->identify($this->getRequestMock());
        $this->assertEquals($this->expectedTicketTimestamp, $identity->getTimestamp());
    }

    public function testIdentifyIdentityResultDateTimestamp()
    {
        $identity = $this->helper->identify($this->getRequestMock());
        $this->assertInstanceOf('\DateTime', $identity->getDateTimestamp());
        $this->assertEquals($this->expectedTicketTimestamp, $identity->getDateTimestamp()->getTimestamp());
    }

    public function testEncodeIpTimestamp()
    {
        $this->helper->encodeIpTimestamp('127.0.0.1', '1427720510');

    }

}
