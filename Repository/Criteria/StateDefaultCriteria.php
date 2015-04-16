<?php

namespace Truelab\KottiSecurityBundle\Repository\Criteria;

use Truelab\KottiModelBundle\Repository\Criteria\DefaultCriteriaInterface;
use Truelab\KottiSecurityBundle\Security\KottiSecurityContextInterface;

/**
 * Class StateDefaultCriteria
 * @package Truelab\KottiSecurityBundle\Repository\Criteria
 */
class StateDefaultCriteria implements DefaultCriteriaInterface
{
    /**
     * @var KottiSecurityContextInterface
     */
    private $kottiSecurityContext;

    /**
     * @param KottiSecurityContextInterface $kottiSecurityContext
     */
    public function setKottiSecurityContext(KottiSecurityContextInterface $kottiSecurityContext)
    {
        $this->kottiSecurityContext = $kottiSecurityContext;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'state';
    }

    /**
     * @return string
     */
    public function getCriteria()
    {
        if($this->isAdmin() && $this->actAsAnonymous() === false) {
            return ['(contents.state = ? OR contents.state = ?)' => ['public', 'private']] ;
        }else{
            return ['contents.state = ?' => 'public'];
        }
    }

    /**
     * Check if current user is a admin user (he can see also privates)
     *
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->kottiSecurityContext->hasRole('ROLE_ADMIN');
    }

    protected function actAsAnonymous()
    {
        return $this->kottiSecurityContext->actAsAnonymous();
    }
}
