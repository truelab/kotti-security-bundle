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
     * @return array
     */
    public function parseTicket($ticket);

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
     * @return string
     */
    public function encodeIpTimestamp($ip, $timestamp);
}
