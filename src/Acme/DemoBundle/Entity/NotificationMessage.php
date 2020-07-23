<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * notification
 *
 * @ORM\Table(name = "notification_message")
 * @ORM\Entity
 */
class NotificationMessage {

    /**
     * @ORM\Column(name = "notificationMessageID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="notificationTitle", type="string", length=500)
     */
    private $notificationTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="notificationMessage", type="string", length=50)
     */
    private $notificationMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="notificationStatus", type="string", length=50)
     */
    private $notificationStatus;
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
	
	private $notification_datetime;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set notificationTitle
     *
     * @param string $notificationTitle
     * @return User
     */
    public function setNotificationTitle($notificationTitle) {
        $this->notificationTitle = $notificationTitle;

        return $this;
    }

    /**
     * Get notificationTitle
     *
     * @return string 
     */
    public function getNotificationTitle() {
        return $this->notificationTitle;
    }

    /**
     * Set notificationMessage
     *
     * @param string $notificationMessage
     * @return User
     */
    public function setNotificationMessage($notificationMessage) {
        $this->notificationMessage = $notificationMessage;

        return $this;
    }

    /**
     * Get notificationMessage
     *
     * @return string 
     */
    public function getNotificationMessage() {
        return $this->notificationMessage;
    }

    /**
     * Set notificationStatus
     *
     * @param string $notificationStatus
     * @return User
     */
    public function setNotificationStatus($notificationStatus) {
        $this->notificationStatus = $notificationStatus;

        return $this;
    }

    /**
     * Get notificationStatus
     *
     * @return string 
     */
    public function getNotificationStatus() {
        return $this->notificationStatus;
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
	
	
	 public function setnotificationdatetime($notification_datetime) {
        $this->notification_datetime = $notification_datetime;

        return $this;
    }
	
	
	   public function getnotification_datetime() {
        return $this->notification_datetime;
    }


}
