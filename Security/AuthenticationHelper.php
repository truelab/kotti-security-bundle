<?php

namespace Truelab\KottiSecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookie;
use Truelab\KottiSecurityBundle\Security\Cookie\SignedCookieInterface;

use Truelab\KottiSecurityBundle\Security\Exception\BadTicketException;

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
    private $secret;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var string
     */
    private $hashAlg;

    /**
     * @var PyConverterInterface
     */
    private $pyConverter;

    private $digestSizes = [
        'sha512' => 128,
        'md5' => 32
    ];

    public function __construct($secret, $cookieName = 'auth_tkt', $hashAlg = 'sha512')
    {
        $this->secret = $secret;
        $this->cookieName = $cookieName;
        $this->hashAlg = $hashAlg;
        $this->digestSize = $this->digestSizes[$hashAlg];
    }

    public function setPyConverter(PyConverterInterface $pyConverter)
    {
        $this->pyConverter = $pyConverter;
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
     * TODO: private
     *
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

//    Python pyramid version
//    # this function licensed under the MIT license (stolen from Paste)
//    def encode_ip_timestamp(ip, timestamp):
//        ip_chars = ''.join(map(chr, map(int, ip.split('.'))))
//        t = int(timestamp)
//        ts = ((t & 0xff000000) >> 24,
//            (t & 0xff0000) >> 16,
//            (t & 0xff00) >> 8,
//            t & 0xff)
//        ts_chars = ''.join(map(chr, ts))
//    return bytes_(ip_chars + ts_chars)


// Pdb debugging
//[31/03/15 10:01:51] Davide Moro: da python? sì
//[31/03/15 10:02:00] Davide Moro: metti una riga "import pdb; pdb.set_trace()"
//[31/03/15 10:02:15] Davide Moro: riavvia
//[31/03/15 10:02:22] Davide Moro: e poi devi usare i seguenti comandi:
//[31/03/15 10:02:31] Davide Moro: l -> mostra dove ti trovi più o meno
//[31/03/15 10:02:39] Davide Moro: l 112 -> ti mostra l'intorno della riga 112
//[31/03/15 10:02:59] Davide Moro: c -> continua ed esci dal pdb, a meno che non l'abbia messo in un ciclo o becca un breakpoint
//[31/03/15 10:03:18] Davide Moro: w -> mostra l'intero stack trace, in evidenza dove ti trovi
//[31/03/15 10:03:28] Davide Moro: n -> next (non entra dentro le funzioni)
//[31/03/15 10:03:37] Davide Moro: s -> step into (come next, ma entra nelle funzioni)
//[31/03/15 10:04:05] Davide Moro: u -> sali nello stack per vedere sopra chi ha chiamato la tua func
//[31/03/15 10:04:18] Davide Moro: d -> down, torni giù di una posizione nello stack
//[31/03/15 10:04:33] Davide Moro: b 215 -> imposta un breakpoint alla linea 215
}
