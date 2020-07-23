<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * user_chat
 *
 * @ORM\Table(name = "user_chat")
 * @ORM\Entity
 */
class UserChat {

    /**
     * @ORM\Column(name = "userChatID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fromUserID", type="string", length=500)
     */
    private $fromUserID;

    /**
     * @var string
     *
     * @ORM\Column(name="toUserID", type="string", length=50)
     */
    private $toUserID;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryStatus", type="string", length=50)
     */
    private $deliveryStatus;
      /**
     * @var string
     *
     * @ORM\Column(name="userMessage", type="string", length=50)
     */
    private $userMessage;
     /**
     * @var string
     *
     * @ORM\Column(name="userChatType", type="string", length=50)
     */
    private $userChatType;
     /**
     * @var string
     *
     * @ORM\Column(name="userChatDate", type="string", length=50)
     */
    private $userChatDate;
      /**
     * @var string
     *
     * @ORM\Column(name="date_time", type="string", length=50)
     */
    private $date_time;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fromUserID
     *
     * @param string $fromUserID
     * @return User
     */
    public function setFromUserID($fromUserID) {
        $this->fromUserID = $fromUserID;

        return $this;
    }

    /**
     * Get fromUserID
     *
     * @return string 
     */
    public function getFromUserID() {
        return $this->fromUserID;
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
     * Set deliveryStatus
     *
     * @param string $deliveryStatus
     * @return User
     */
    public function setDeliveryStatus($deliveryStatus) {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    /**
     * Get deliveryStatus
     *
     * @return string 
     */
    public function getDeliveryStatus() {
        return $this->deliveryStatus;
    }
    /**
     * Set userMessage
     *
     * @param string $userMessage
     * @return User
     */
    public function setUserMessage($userMessage) {
        $this->userMessage = $userMessage;

        return $this;
    }

    /**
     * Get userMessage
     *
     * @return string 
     */
    public function getUserMessage() {
        return $this->userMessage;
    }
 /**
     * Set userChatType
     *
     * @param string $userChatType
     * @return User
     */
    public function setUserChatType($userChatType) {
        $this->userChatType = $userChatType;

        return $this;
    }

    /**
     * Get userChatType
     *
     * @return string 
     */
    public function getUserChatType() {
        return $this->userChatType;
    }
    /**
     * Set userChatDate
     *
     * @param string $userChatDate
     * @return User
     */
    public function setUserChatDate($userChatDate) {
        $this->userChatDate = $userChatDate;

        return $this;
    }

    /**
     * Get userChatDate
     *
     * @return string 
     */
    public function getUserChatDate() {
        return $this->userChatDate;
    }
    /**
     * Set date_time
     *
     * @param string $date_time
     * @return User
     */
    public function setDateTime($date_time) {
        $this->date_time = $date_time;

        return $this;
    }

    /**
     * Get date_time
     *
     * @return string 
     */
    public function getDateTime() {
        return $this->date_time;
    }

}
