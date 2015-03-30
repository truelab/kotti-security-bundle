<?php

namespace Truelab\KottiSecurityBundle\Security;

/**
 * Class AuthenticationIdentity
 * @package Truelab\KottiSecurityBundle\Security
 */
class AuthenticationIdentity implements AuthenticationIdentityInterface
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var array
     */
    private $userData = [];

    /**
     * @var array
     */
    private $tokens = [];

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var \DateTime
     */
    private $dateTimestamp;

    public function __construct($userId, $userData, $tokens = [], $timestamp)
    {
        $this->userId        = $userId;
        $this->userData      = $userData;
        $this->tokens        = $tokens;
        $this->timestamp     = $timestamp;
        $this->dateTimestamp = (new \DateTime())->setTimestamp($timestamp);
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimestamp()
    {
        return $this->dateTimestamp;
    }
}
