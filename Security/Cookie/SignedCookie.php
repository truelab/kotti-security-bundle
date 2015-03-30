<?php

namespace Truelab\KottiSecurityBundle\Security\Cookie;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class SignedCookie
 * @package Truelab\KottiSecurityBundle\Security\Cookie
 */
class SignedCookie extends Cookie implements SignedCookieInterface
{
    private $secret;

    public function __construct($secret, $name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        parent::__construct($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        $this->secret = $secret;
    }

    public function decode()
    {
        // TODO implement this method
        return '';
    }

    public function encode()
    {
        // TODO implement this method
        return '';
    }

    public static function createFromCookie($secret, Cookie $cookie)
    {
        return new SignedCookie($secret, $cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
    }
}
