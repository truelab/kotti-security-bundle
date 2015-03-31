<?php

namespace Truelab\KottiSecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookie;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookieInterface;

use Truelab\KottiSecurityBundle\Security\Exception\BadTicketException;

use Truelab\KottiSecurityBundle\Security\Exception\IdentifyException;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyCookieNotFoundException;
use Truelab\KottiSecurityBundle\Security\Exception\IdentifyParseTicketException;

/**
 * Class AuthenticationHelper
 * @package Truelab\KottiSecurityBundle\Security
 */
class AuthenticationHelper implements AuthenticationHelperInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var string
     */
    private $hashAlg;

    public function __construct($secret, $cookieName = 'auth_tkt')
    {
        $this->secret = $secret;
        $this->cookieName = $cookieName;
        $this->hashAlg = 'md5';
        $this->digestSize = 128;
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

        if($request->cookies->has($this->cookieName)) {

            $cookie = $this->createSignedCookie($request->cookies->get($this->cookieName));

            if($cookie->decode() === null) {
                throw new IdentifyException('Decode signed cookie fails, cookie should be not signed');
            }

        }else{
            throw new IdentifyCookieNotFoundException(sprintf('Cookie with key = "%s" was not found in the current request', $this->cookieName));
        }

        try{
           $result = $this->parseTicket($cookie->getValue());
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
     * Create a SignedCookieInterface object from a simple cookie
     *
     * @param $cookie
     * @return SignedCookieInterface
     */
    protected function createSignedCookie($cookie)
    {
        return SignedCookie::createFromCookie($this->secret, $cookie);
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
     * @return array - identity []
     *
     * @throws Exception\BadTicketException
     */
    public function parseTicket($ticket, $ip = null)
    {
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
//
//        $actual = $this->calculateDigest($ip, $timestamp, $userId, $tokens, $userData);
//
//        // Avoid timing attacks (see
//        // http://seb.dbzteam.org/crypto/python-oauth-timing-hmac.pdf)
//        if($actual !== $digest) {
//            throw new BadTicketException(sprintf('Digest signature is not correct. actual: %s, expected: %s', $actual, $digest));
//        }

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
     * @throws \Exception
     */
    private function calculateDigest()
    {
        throw new \Exception('Not implemented method! This ticket is not secure!');
    }

}
