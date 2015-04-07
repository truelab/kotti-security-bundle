<?php

namespace Truelab\KottiSecurityBundle\Model;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface PrincipalInterface
 * @package Truelab\KottiSecurityBundle\Model
 */
interface PrincipalInterface extends UserInterface
{
    public function getGroups();

    public function getName();

    public function getPassword();

    public function isActive();

    public function getTitle();

    public function getCreationDate();

    public function getLastLoginDate();
}
