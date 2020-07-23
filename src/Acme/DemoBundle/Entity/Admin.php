<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * admin
 *
 * @ORM\Table(name = "admin")
 * @ORM\Entity
 */
class Admin {

    /**
     * @ORM\Column(name = "adminID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="adminEmail", type="string", length=50)
     */
    private $adminEmail;
    /**
     * @var string
     *
     * @ORM\Column(name="adminPassword", type="string", length=50)
     */
    private $adminPassword;
    
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="followStatus", type="string", length=50)
//     */
//    private $followStatus;
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set adminEmail
     *
     * @param string $adminEmail
     * @return User
     */
    public function setAdminEmail($adminEmail) {
        $this->adminEmail = $adminEmail;

        return $this;
    }

    /**
     * Get adminEmail
     *
     * @return string 
     */
    public function getAdminEmail() {
        return $this->userID;
    }
    /**
     * Set adminPassword
     *
     * @param string $adminPassword
     * @return User
     */
    public function setAdminPassword($adminPassword) {
        $this->adminPassword = $adminPassword;

        return $this;
    }

    /**
     * Get adminPassword
     *
     * @return string 
     */
    public function geAdminPassword() {
        return $this->adminPassword;
    }

}
