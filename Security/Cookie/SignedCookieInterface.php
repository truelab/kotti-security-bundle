<?php

namespace Truelab\KottiSecurityBundle\Security\Cookie;

use \Symfony\Component\HttpFoundation\Cookie;

/**
 * Interface SignedCookieInterface
 * @package Truelab\KottiSecurityBundle\Security\Cookie
 */
interface SignedCookieInterface extends CookieInterface
{
    public function decode();

    public function encode();

    public static function createFromCookie($secret, Cookie $cookie);
}
