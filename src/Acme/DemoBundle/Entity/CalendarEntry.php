<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * calendar_entry
 *
 * @ORM\Table(name = "calendar_entry")
 * @ORM\Entity
 */
class CalendarEntry {

    /**
     * @ORM\Column(name = "calendarID",type="integer", length=22)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;


	/**
	 * @var int
	 *
	 * @ORM\Column(name="userID", type="integer", length=22)
	 */
	private $userID;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="start_time", type="string", length=50)
     */
    private $startTime;

    /**
     * @var string
     *
     * @ORM\Column(name="end_time", type="string", length=40)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="start_date", type="string")
     */
    private $startDate;

    /**
     * @var string
     *
     * @ORM\Column(name="end_date", type="string")
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="reminder", type="string")
     */
    private $reminder;

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
     * Set title
     *
     * @param string $title
     * @return CalendarEntry
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CalendarEntry
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set startTime
     *
     * @param string $startTime
     * @return CalendarEntry
     */
    public function setStartTime($startTime) {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return string 
     */
    public function getStartTime() {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param string $endTime
     * @return CalendarEntry
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return string
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * Set startDate
     *
     * @param string $startDate
     * @return CalendarEntry
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return string
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param string $endDate
     * @return CalendarEntry
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return string
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Set Reminder
     *
     * @param string $reminder
     * @return CalendarEntry
     */
    public function setReminder($reminder) {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * Get Reminder
     *
     * @return string
     */
    public function getReminder() {
        return $this->reminder;
    }


}

 