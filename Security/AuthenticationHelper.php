<?php

namespace Truelab\KottiSecurityBundle\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookie;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookieInterface;

use Truelab\KottiSecurityBundle\Security\Exception\BadTicketException;

use Truelab\KottiSecurityBundle\Security\Exception\BadTicketUnexpectedDigestException;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyCookieNotFoundException;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyParseTicketException;
use Truelab\KottiSecurityBundle\Util\PyConverter\PyConverterInterface;

/**
 * Class AuthenticationHelper
 * @package Truelab\KottiSecurityBundle\Security
 */
class AuthenticationHelper implements AuthenticationHelperInterface
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $cookieName;

    /**
     * @var string
     */
    protected $hashAlg;

    /**
     * @var PyConverterInterface
     */
    protected $pyConverter;

    /**
     * @var bool $includeIp
     */
    protected $includeIp;

    /**
     * Hash Alg/Digest size map, supports only md5 and sha512
     * @var array
     */
    protected $digestSizes = [
        'sha512' => 128,
        'md5' => 32
    ];

    /**
     * @param string $secret     - the secret (a string) used for auth_tkt cookie signing. Required.
     * @param string $cookieName - default: 'auth_tkt'. The cookie name used  (string).  Optional.
     * @param string $hashAlg    - default: 'sha512'. tha hash algorithm used to encrypt digest. it can be 'sha512' or 'md5' only. Optional.
     * @param bool   $includeIp  - default: false.  Make the requesting IP address part of the authentication data in the cookie.  Optional.
     */
    public function __construct($secret, $cookieName = 'auth_tkt', $hashAlg = 'sha512', $includeIp = false )
    {
        $this->secret     = $secret;
        $this->cookieName = $cookieName;
        $this->hashAlg    = $hashAlg;
        $this->digestSize = $this->digestSizes[$hashAlg];
        $this->includeIp  = $includeIp;
    }

    public function setPyConverter(PyConverterInterface $pyConverter)
    {
        $this->pyConverter = $pyConverter;
    }

    public function canIdentify(Request $request)
    {
        return $request->cookies->has($this->cookieName);
    }

    /**
     * Return an @see AuthenticationIdentityInterface with authentication information, or throws
     * exceptions if no valid auth_tkt is attached to Request
     *
     * @param Request $request
     *
     * @return AuthenticationIdentityInterface|null
     * @throws IdentifyException
     */
    public function identify(Request $request)
    {
        if($this->canIdentify($request)) {
            $cookie = $request->cookies->get($this->cookieName);
        }else{
            throw new IdentifyCookieNotFoundException(sprintf('Cookie with key = "%s" was not found in the current request', $this->cookieName));
        }

        try{

            if($cookie instanceof Cookie) {
                $cookieValue = $cookie->getValue();
            }elseif(is_string($cookie)) {
                $cookieValue = $cookie;
            }else{
                throw new IdentifyException('Unsupported cookie format');
            }


            $result = $this->parseTicket($cookieValue, !$this->iHaveToIncludeIp() ? '0.0.0.0' : $request->getClientIp());

        }catch(BadTicketException $e) {
            throw new IdentifyParseTicketException('Something went wrong while parsing ticket.', 0, $e);
        }

        $userId    = base64_decode($result['user_id']);
        $userData  = $result['user_data'];
        $tokens    = $result['tokens'];
        $timestamp = $result['timestamp'];

        $identity = new AuthenticationIdentity(
            $userId,
            $userData,
            $tokens,
            $timestamp
        );

        return $identity;
    }


    /**
     *
     * Parse the ticket, returning an associative array:
     * [
     *      'timestamp' => '...',
     *      'digest'    => '...',
     *      'user_id'   => '...',
     *      tokens      => '...',
     *      'user_data' => '...'
     * ]
     *
     * If the ticket cannot be parsed, a @see BadTicketException exception will be raised
     * with an explanation.
     *
     *
     * @param string $ticket
     *
     * @param string $ip
     *
     * @return array - identity []
     *
     * @throws BadTicketException
     */
    public function parseTicket($ticket, $ip = '0.0.0.0')
    {
        $ip = !$this->iHaveToIncludeIp() || $ip === NULL ? '0.0.0.0' : $ip;

        $ticket = trim($ticket, "\"");

        // extract $digest
        $digest = substr($ticket, 0, $this->digestSize);

        // extract hex timestamp
        $timestamp = base_convert(substr($ticket, $this->digestSize, 8), 16, 10);

        // check timestamp
        try{
            $date = new \DateTime();
            $date->setTimestamp($timestamp);
        }catch(\Exception $e) {
            throw new BadTicketException(sprintf('Timestamp is not a hex integer: %s, could not convert to \DateTime', $timestamp));
        }

        // extract user id and data
        list($userId, $data) = array_pad(
            explode("!", substr($ticket, $this->digestSize + 8), 2)
        , -2, null);

        // check if user id exists
        if(!$userId) {
            throw new BadTicketException('userid is not followed by !');
        }

        $userId = urldecode($userId);

        if (strpos($data,'!') !== false) {
            list($userData, $tokens) = explode('!', $data, 1);
        }else{
            $tokens = '';
            $userData = $data;
        }

        $expected = $this->calculateDigest($ip, $timestamp, $userId, $tokens, $userData);

        // Avoid timing attacks (see
        // http://seb.dbzteam.org/crypto/python-oauth-timing-hmac.pdf)
        if($expected !== $digest) {
            throw new BadTicketUnexpectedDigestException(sprintf(
                    'Digest signature is not correct. expected digest: "%s", cookie digest: "%s", ticket: "%s", ip: "%s", timestamp: "%s", userId: "%s", tokens: "%s", userData: "%s"',
                    $expected,
                    $digest,
                    $ticket,
                    $ip,
                    $timestamp,
                    $userId,
                    $tokens,
                    $userData
                )
            );
        }

        $tokens = explode(',', $tokens);

        return [
            'timestamp' => $timestamp,
            'digest' => $digest,
            'user_id' => $userId,
            'tokens' => $tokens,
            'user_data' => $userData
        ];
    }

    /**
     * Not implemented!!!
     * We could not make porting from python to php
     *
     * @param string $ip
     * @param string $timestamp
     * @param string $userid
     * @param string $tokens
     * @param string $userData
     *
     * @return string|void
     */
    public function calculateDigest($ip, $timestamp, $userid, $tokens, $userData)
    {
        $ip = !$this->iHaveToIncludeIp() ? '0.0.0.0' : $ip;

        # Check to see if this is an IPv6 address
        if (strpos($ip,':') !== false) {
            $ipTimestamp = $ip . (string)((int)($timestamp));
        }else{
            $ipTimestamp = $this->encodeIpTimestamp($ip, $timestamp);
        }

        $data = $ipTimestamp . $this->secret . $userid . pack('H*','00') . $tokens .  pack('H*','00') . $userData;
        $digest = hash($this->hashAlg, $data);

        return hash($this->hashAlg, $digest . $this->secret);
    }

    /**
     * @param string $ip
     * @param string $timestamp
     *
     * @param bool $printable
     *
     * @return string
     */
    public function encodeIpTimestamp($ip, $timestamp, $printable = false)
    {
        $ipChars = join("", array_map(function ($ipPart) use ($printable) {
            $ipPart = (int) $ipPart;
            return $this->pyConverter->chr($ipPart, $printable);
        }, explode(".", $ip)));

        $t = (int) $timestamp;
        $ts = [
            ($t & 0xff000000) >> 24,
            ($t & 0xff0000) >> 16,
            ($t & 0xff00) >> 8,
            ($t & 0xff)
        ];

        $tsChars = join("",array_map(function ($tss) use ($printable) {
          return $this->pyConverter->chr($tss, $printable);
        }, $ts));

        return $ipChars . $tsChars;
    }

    /**
     * @return bool
     */
    protected function iHaveToIncludeIp()
    {
        return $this->includeIp !== false;
    }
}
