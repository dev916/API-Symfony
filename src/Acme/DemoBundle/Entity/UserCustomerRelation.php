<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * user_customer_relation
 *
 * @ORM\Table(name = "user_customer_relation")
 * @ORM\Entity
 */
class UserCustomerRelation {

    /**
     * @ORM\Column(name = "userCustomerRelationID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="userID", type="string", length=50)
     */
    private $userID;
      /**
     * @var string
     *
     * @ORM\Column(name="companyID", type="string", length=50)
     */
    private $companyID;
     /**
     * @var string
     *
     * @ORM\Column(name="userCustomerRelationDate", type="string", length=2)
     */
    private $userCustomerRelationDate;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }
/**
     * Set userID
     *
     * @param string $userID
     * @return User
     */
    public function setUserID($userID) {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get userID
     *
     * @return string 
     */
    public function getUserID() {
        return $this->userID;
    }
/**
     * Set companyID
     *
     * @param string $companyID
     * @return User
     */
    public function setCompanyID($companyID) {
        $this->companyID = $companyID;

        return $this;
    }

    /**
     * Get companyID
     *
     * @return string 
     */
    public function getCompanyID() {
        return $this->companyID;
    }
     /**
     * Get userCustomerRelationDate
     *
     * @return string 
     */
     public function getUserCustomerRelationDate()
    {
        return $this->userCustomerRelationDate;
    }

}

