<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * report_problem
 *
 * @ORM\Table(name = "report_problem")
 * @ORM\Entity
 */
class ReportProblem {

    /**
     * @ORM\Column(name = "reportProblemID",type="integer")
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
     * @ORM\Column(name="reportDescription", type="string", length=50)
     */
    private $reportDescription;
    
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
     * Set reportDescription
     *
     * @param string $reportDescription
     * @return User
     */
    public function setReportDescription($reportDescription) {
        $this->reportDescription = $reportDescription;

        return $this;
    }

    /**
     * Get reportDescription
     *
     * @return string 
     */
    public function geReportDescription() {
        return $this->reportDescription;
    }
 
}
