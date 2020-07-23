<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * master_category
 *
 * @ORM\Table(name = "master_category")
 * @ORM\Entity
 */
class MasterCategory {

    /**
     * @ORM\Column(name = "categoryID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryName", type="string", length=50)
     */
    private $categoryName;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set categoryName
     *
     * @param string $categoryName
     * @return User
     */
    public function setCategoryName($categoryName) {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get categoryName
     *
     * @return string 
     */
    public function getCategoryName() {
        return $this->categoryName;
    }

}
