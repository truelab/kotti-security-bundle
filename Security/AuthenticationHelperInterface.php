<?php

namespace Truelab\KottiSecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface AuthenticationHelperInterface
 * @package Truelab\KottiSecurityBundle\Security
 */
interface AuthenticationHelperInterface
{
    /**
     * @param string $ticket
     *
     * @param string $ip
     *
     * @return array
     */
    public function parseTicket($ticket, $ip = '0.0.0.0');

    /**
     * @param Request $request
     *
     * @return AuthenticationIdentityInterface|null
     */
    public function identify(Request $request);

    /**
     * @param string $ip
     * @param string $timestamp
     *
     * @param bool $printable
     *
     * @return string
     */
    public function encodeIpTimestamp($ip, $timestamp, $printable = false);

    /**
     * @param string $ip
     * @param string $timestamp
     * @param string $userid
     * @param string $tokens
     * @param string $userData
     *
     * @return string
     */
    public function calculateDigest($ip, $timestamp, $userid, $tokens, $userData);
}
