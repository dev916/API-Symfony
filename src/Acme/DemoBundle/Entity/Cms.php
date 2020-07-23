<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * cms
 *
 * @ORM\Table(name = "cms")
 * @ORM\Entity
 */
class Cms {

    /**
     * @ORM\Column(name = "cmsID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cmsTitle", type="string", length=50)
     */
    private $cmsTitle;
    /**
     * @var string
     *
     * @ORM\Column(name="cmsDescription", type="string", length=50)
     */
    private $cmsDescription;
    
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
     * Set cmsTitle
     *
     * @param string $cmsTitle
     * @return User
     */
    public function setCmsTitle($cmsTitle) {
        $this->cmsTitle = $cmsTitle;

        return $this;
    }

    /**
     * Get cmsTitle
     *
     * @return string 
     */
    public function getCmsTitle() {
        return $this->cmsTitle;
    }
    /**
     * Set cmsDescription
     *
     * @param string $cmsDescription
     * @return User
     */
    public function setCmsDescription($cmsDescription) {
        $this->cmsDescription = $cmsDescription;

        return $this;
    }

    /**
     * Get cmsDescription
     *
     * @return string 
     */
    public function geCmsDescription() {
        return $this->cmsDescription;
    }
 
}
