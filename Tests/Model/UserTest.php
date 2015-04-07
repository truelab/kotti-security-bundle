<?php

namespace Truelab\KottiSecurityBundle\Tests\Model;
use Truelab\KottiSecurityBundle\Model\User;

/**
 * Class UserTest
 * @package Truelab\KottiSecurityBundle\Tests\Model
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoles()
    {
        $adminUser = $this->createUser();
        $adminUser->setGroups(['role:admin']);
        $this->assertEquals(['ROLE_ADMIN'], $adminUser->getRoles());

        $anonymUser = $this->createUser();
        $anonymUser->setGroups([]);
        $this->assertEquals([], $anonymUser->getRoles());
    }

    /**
     * @return User
     */
    protected function createUser()
    {
        return new User();
    }
}
