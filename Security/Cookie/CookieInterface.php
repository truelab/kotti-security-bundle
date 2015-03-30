<?php

namespace Truelab\KottiSecurityBundle\Security\Cookie;

/**
 * Interface CookieInterface
 * @package Truelab\KottiSecurityBundle\Security\Cookie
 */
interface CookieInterface
{

    /**
     * Gets the name of the cookie.
     *
     * @return string
     *
     * @api
     */
    public function getName();

    /**
     * Gets the value of the cookie.
     *
     * @return string
     *
     * @api
     */
    public function getValue();

    /**
     * Gets the domain that the cookie is available to.
     *
     * @return string
     *
     * @api
     */
    public function getDomain();

    /**
     * Gets the time the cookie expires.
     *
     * @return int
     *
     * @api
     */
    public function getExpiresTime();

    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     *
     * @api
     */
    public function getPath();

    /**
     * Checks whether the cookie should only be transmitted over a secure HTTPS connection from the client.
     *
     * @return bool
     *
     * @api
     */
    public function isSecure();

    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @return bool
     *
     * @api
     */
    public function isHttpOnly();

    /**
     * Whether this cookie is about to be cleared.
     *
     * @return bool
     *
     * @api
     */
    public function isCleared();
}
