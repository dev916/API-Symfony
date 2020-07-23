<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * post_category
 *
 * @ORM\Table(name = "post_category")
 * @ORM\Entity
 */
class PostCategory
{
    /**
     * @ORM\Column(name = "postCategoryID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="postID", type="string", length=50)
     */
    private $postID;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryID", type="string", length=50)
     */
    private $categoryID;

   
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set postID
     *
     * @param string $postID
     * @return User
     */
    public function setPostID($postID)
    {
        $this->postID = $postID;
    
        return $this;
    }

    /**
     * Get postID
     *
     * @return string 
     */
    public function getPostID()
    {
        return $this->postID;
    }

    /**
     * Set categoryID
     *
     * @param string $categoryID
     * @return User
     */
    public function setCategoryID($categoryID)
    {
        $this->categoryID = $categoryID;
    
        return $this;
    }

    /**
     * Get categoryID
     *
     * @return string 
     */
    public function getCategoryID()
    {
        return $this->categoryID;
    }
    

    

    
}
