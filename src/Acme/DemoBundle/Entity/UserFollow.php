<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * user_follow
 *
 * @ORM\Table(name = "user_follow")
 * @ORM\Entity
 */
class UserFollow {

    /**
     * @ORM\Column(name = "userFollowID",type="integer")
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
     * @ORM\Column(name="toUserID", type="string", length=50)
     */
    private $toUserID;
    
    /**
     * @var string
     *
     * @ORM\Column(name="followStatus", type="string", length=50)
     */
    private $followStatus;
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
     * Set toUserID
     *
     * @param string $toUserID
     * @return User
     */
    public function setToUserID($toUserID) {
        $this->toUserID = $toUserID;

        return $this;
    }

    /**
     * Get toUserID
     *
     * @return string 
     */
    public function getToUserID() {
        return $this->toUserID;
    }
 /**
     * Set followStatus
     *
     * @param string $followStatus
     * @return User
     */
    public function setFollowStatus($followStatus) {
        $this->followStatus = $followStatus;

        return $this;
    }

    /**
     * Get followStatus
     *
     * @return string 
     */
    public function geFollowStatus() {
        return $this->followStatus;
    }
}
