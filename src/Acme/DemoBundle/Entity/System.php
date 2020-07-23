<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tbl_sytem
 *
 * @ORM\Table(name = "tbl_sytem")
 * @ORM\Entity
 */
class System {

    /**
     * @ORM\Column(name = "systemID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="machineName", type="string", length=50)
     */
    private $machineName;
    /**
     * @var string
     *
     * @ORM\Column(name="machineAddress", type="string", length=50)
     */
    private $machineAddress;
    
    /**
     * @var string
     *
     * @ORM\Column(name="IPAddress", type="string", length=50)
     */
    private $IPAddress;
    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=50)
     */
    private $userName;
    /**
     * @var string
     *
     * @ORM\Column(name="timeZone", type="string", length=50)
     */
    private $timeZone;
     /**
     * @var string
     *
     * @ORM\Column(name="Country", type="string", length=50)
     */
    private $Country;
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
     * Set machineName
     *
     * @param string $machineName
     * @return User
     */
    public function setMachineName($machineName) {
        $this->machineName = $machineName;

        return $this;
    }

    /**
     * Get machineName
     *
     * @return string 
     */
    public function getMachineName() {
        return $this->machineName;
    }
    /**
     * Set machineAddress
     *
     * @param string $machineAddress
     * @return User
     */
    public function setMachineAddress($machineAddress) {
        $this->machineAddress = $machineAddress;

        return $this;
    }

    /**
     * Get machineAddress
     *
     * @return string 
     */
    public function getMachineAddress() {
        return $this->machineAddress;
    }
    /**
     * Set IPAddress
     *
     * @param string $IPAddress
     * @return User
     */
    public function setIPAddress($IPAddress) {
        $this->IPAddress = $IPAddress;

        return $this;
    }

    /**
     * Get IPAddress
     *
     * @return string 
     */
    public function getIPAddress() {
        return $this->IPAddress;
    }

     /**
     * Set userName
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName) {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName() {
        return $this->userName;
    }

     /**
     * Set timeZone
     *
     * @param string $timeZone
     * @return User
     */
    public function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone
     *
     * @return string 
     */
    public function getTimeZone() {
        return $this->timeZone;
    }
     /**
     * Set Country
     *
     * @param string $Country
     * @return User
     */
    public function setCountry($Country) {
        $this->Country = $Country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return string 
     */
    public function getCountry() {
        return $this->Country;
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
