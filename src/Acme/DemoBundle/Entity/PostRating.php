<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * post_rating
 *
 * @ORM\Table(name = "post_rating")
 * @ORM\Entity
 */
class PostRating
{
    /**
     * @ORM\Column(name = "postRatingID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fromUserID", type="string", length=50)
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
     * @ORM\Column(name="postID", type="string", length=50)
     */
    private $postID;
    /**
     * @var string
     *
     * @ORM\Column(name="postRating", type="string", length=40)
     */
    private $postRating;

    
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
     * Set fromUserID
     *
     * @param string $fromUserID
     * @return User
     */
    public function setFromUserID($fromUserID)
    {
        $this->fromUserID = $fromUserID;
    
        return $this;
    }

    /**
     * Get fromUserID
     *
     * @return string 
     */
    public function getFromUserID()
    {
        return $this->fromUserID;
    }

    /**
     * Set toUserID
     *
     * @param string $toUserID
     * @return User
     */
    public function setToUserID($toUserID)
    {
        $this->toUserID = $toUserID;
    
        return $this;
    }

    /**
     * Get toUserID
     *
     * @return string 
     */
    public function getToUserID()
    {
        return $this->toUserID;
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
     * Set postRating
     *
     * @param string $postRating
     * @return User
     */
    public function setPostRating($postRating)
    {
        $this->postRating = $postRating;
    
        return $this;
    }

    /**
     * Get postRating
     *
     * @return string 
     */
    public function getPostRating()
    {
        return $this->postRating;
    }

   
    
}
