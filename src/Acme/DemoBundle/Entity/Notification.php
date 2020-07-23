<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * notification
 *
 * @ORM\Table(name = "notification")
 * @ORM\Entity
 */
class Notification {

    /**
     * @ORM\Column(name = "notificationID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceID", type="string", length=500)
     */
    private $deviceID;

    /**
     * @var string
     *
     * @ORM\Column(name="imei", type="string", length=50)
     */
    private $imei;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceType", type="string", length=50)
     */
    private $deviceType;
      /**
     * @var string
     *
     * @ORM\Column(name="userID", type="string", length=50)
     */
    private $userID;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set deviceID
     *
     * @param string $deviceID
     * @return User
     */
    public function setDeviceID($deviceID) {
        $this->deviceID = $deviceID;

        return $this;
    }

    /**
     * Get deviceID
     *
     * @return string 
     */
    public function getDeviceID() {
        return $this->deviceID;
    }

    /**
     * Set imei
     *
     * @param string $imei
     * @return User
     */
    public function setImei($imei) {
        $this->imei = $imei;

        return $this;
    }

    /**
     * Get imei
     *
     * @return string 
     */
    public function getImei() {
        return $this->imei;
    }

    /**
     * Set deviceType
     *
     * @param string $deviceType
     * @return User
     */
    public function setDeviceType($deviceType) {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType
     *
     * @return string 
     */
    public function getDeviceType() {
        return $this->deviceType;
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

}
