<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * user_services
 *
 * @ORM\Table(name = "user_services")
 * @ORM\Entity
 */
class UserServices {

    /**
     * @ORM\Column(name = "userServiceID",type="integer")
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
     * @ORM\Column(name="serviceID", type="string", length=50)
     */
    private $serviceID;

    /**
     * @var string
     *
     * @ORM\Column(name="servicePrice", type="string", length=50)
     */
    private $servicePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="topService", type="string", length=2)
     */
    private $topService;

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
     * Set serviceID
     *
     * @param string $serviceID
     * @return User
     */
    public function setServiceID($serviceID) {
        $this->serviceID = $serviceID;

        return $this;
    }

    /**
     * Get serviceID
     *
     * @return string 
     */
    public function getServiceID() {
        return $this->serviceID;
    }

    /**
     * Set servicePrice
     *
     * @param string $servicePrice
     * @return User
     */
    public function setServicePrice($servicePrice) {
        $this->servicePrice = $servicePrice;

        return $this;
    }

    /**
     * Get servicePrice
     *
     * @return string 
     */
    public function getServicePrice() {
        return $this->servicePrice;
    }

    /**
     * Set topService
     *
     * @param string $topService
     * @return User
     */
    public function setTopService($topService) {
        $this->topService = $topService;

        return $this;
    }

    /**
     * Get topService
     *
     * @return string 
     */
    public function getTopService(){
        return $this->topService;
    }

}
