<?php

namespace Truelab\KottiSecurityBundle\Model;
use Doctrine\Common\Persistence\ObjectRepository;
use Truelab\KottiSecurityBundle\Model\Exception\UserByNameNotFoundException;

/**
 * Class UserManager
 * @package Truelab\KottiSecurityBundle\Model
 */
class UserManager implements UserManagerInterface
{
    private $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $name
     *
     * @return PrincipalInterface
     *
     * @throws UserByNameNotFoundException
     */
    public function loadByName($name)
    {
        $user = $this->repository->findOneBy([
            'name' => $name
        ]);

        if(!$user instanceof PrincipalInterface) {
            throw new UserByNameNotFoundException($name);
        }

        return $user;
    }
}
