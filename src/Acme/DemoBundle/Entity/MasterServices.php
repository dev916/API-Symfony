<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * master_services
 *
 * @ORM\Table(name = "master_services")
 * @ORM\Entity
 */
class MasterServices {

    /**
     * @ORM\Column(name = "serviceID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="serviceName", type="string", length=50)
     */
    private $serviceName;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set serviceName
     *
     * @param string $serviceName
     * @return User
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * Get userFirstName
     *
     * @return string 
     */
    public function getServiceName() {
        return $this->serviceName;
    }

}
