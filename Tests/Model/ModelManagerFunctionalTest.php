<?php

namespace Truelab\KottiSecurityBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Truelab\KottiSecurityBundle\Model\UserManagerInterface;

/**
 * Class ModelManagerFunctionalTest
 * @package Truelab\KottiSecurityBundle\Tests\Model
 *
 * @group functional
 */
class ModelManagerFunctionalTest extends WebTestCase
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->userManager = $this->client->getContainer()->get('truelab_kotti_security.user_manager');
    }

    public function testLoadByName()
    {
        $admin = $this->userManager->loadByName('admin');
        $this->assertInstanceOf('Truelab\KottiSecurityBundle\Model\PrincipalInterface', $admin);
    }

    /**
     * @expectedException \Truelab\KottiSecurityBundle\Model\Exception\UserByNameNotFoundException
     */
    public function testLoadByNameThrowNotFoundException()
    {
        $this->userManager->loadByName('notexistent');
    }
}
