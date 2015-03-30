<?php

namespace Truelab\KottiSecurityBundle\Security;

/**
 * Interface AuthenticationIdentityInterface
 * @package Truelab\KottiSecurityBundle\Security
 */
interface AuthenticationIdentityInterface
{
    /**
     * @return string
     */
    public function getUserId();

    /**
     * @return array
     */
    public function getUserData();

    /**
     * @return array
     */
    public function getTokens();

    /**
     * @return string
     */
    public function getTimestamp();

    /**
     * @return \DateTime
     */
    public function getDateTimestamp();
}
