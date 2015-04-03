<?php

namespace Truelab\KottiSecurityBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelper;
use Truelab\KottiSecurityBundle\Security\AuthenticationHelperInterface;
use Truelab\KottiSecurityBundle\Security\Exception\BadTicketException;
use Truelab\KottiSecurityBundle\Util\PyConverter\PyConverter;

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
        // sha512 digest
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
        $this->helper->setPyConverter(new PyConverter());
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
        $result = $this->helper->encodeIpTimestamp('0.0.0.0', '1427710482', true);
        $expected = '\x00\x00\x00\x00U\x19"\x12';
        $this->assertEquals($expected, $result);

        $result = $this->helper->encodeIpTimestamp('198.45.240.10','1427710482', true);
        $expected = '\xc6-\xf0\nU\x19"\x12';
        $this->assertEquals($expected, $result);

        $result = $this->helper->encodeIpTimestamp('198.85.255.10','1437710482', true);
        $expected = '\xc6U\xff\nU\xb1\xb8\x92';
        $this->assertEquals($expected, $result);
    }

    public function testCalculateDigest()
    {
        $result = $this->helper->calculateDigest('0.0.0.0', '1427710482', 'YWRtaW4=', '', 'userid_type:b64unicode');
        $expected = '1ad057373be91d55b710382bc3122cf5106a9bf2ee6078f09f341d7d586e2ac08b859f5945a0784099ac8f5871c0637440fdc706079782d86531a9278fb9df8e';

        $this->assertEquals($expected, $result);
    }
}
