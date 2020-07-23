<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tbl_domain
 *
 * @ORM\Table(name = "tbl_domain")
 * @ORM\Entity
 */
class Domain {

    /**
     * @ORM\Column(name = "domainID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="domainName", type="string", length=50)
     */
    private $domainName;
      /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=50)
     */
    private $status;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set domainName
     *
     * @param string $domainName
     * @return User
     */
    public function setDomainName($domainName) {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * Get domainName
     *
     * @return string 
     */
    public function getDomainName() {
        return $this->domainName;
    }
     /**
     * Set status
     *
     * @param string $status
     * @return User
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

}
