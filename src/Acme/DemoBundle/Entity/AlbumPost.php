<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * post
 *
 * @ORM\Table(name = "post")
 * @ORM\Entity
 */
class AlbumPost
{
    /**
     * @ORM\Column(name = "postID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_Front", type="string", length=50)
     */
    private $post_Image_Front;

    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_FrontLeft", type="string", length=50)
     */
    private $post_Image_FrontLeft;
/**
     * @var string
     *
     * @ORM\Column(name="post_Image_Left", type="string", length=50)
     */
    private $post_Image_Left;
    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_BackLeft", type="string", length=40)
     */
    private $post_Image_BackLeft;

    /**
     * @var \string
     *
     * @ORM\Column(name="post_Image_Back", type="string")
     */
    private $post_Image_Back;

    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_BackRight", type="string", length=40)
     */
    private $post_Image_BackRight;

    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_Right", type="string", length=60)
     */
    private $post_Image_Right;

    /**
     * @var string
     *
     * @ORM\Column(name="post_Image_FrontRight", type="string")
     */
    private $post_Image_FrontRight;

    /**
     * @var string
     *
     * @ORM\Column(name="postCaption", type="string", length=60)
     */
    private $postCaption;

    /**
     * @var string
     *
     * @ORM\Column(name="userID", type="string", length=60)
     */
    private $userID;

    /**
     * @var string
     *
     * @ORM\Column(name="userTagID", type="string", length=40)
     */
    private $userTagID;
     /**
     * @var string
     *
     * @ORM\Column(name="postStatus", type="string", length=40)
     */
    private $postStatus;
     /**
     * @var string
     *
     * @ORM\Column(name="spPostStatus", type="string", length=40)
     */
    private $spPostStatus;
     /**
     * @var string
     *
     * @ORM\Column(name="postNote", type="string", length=40)
     */
    private $postNote;
     /**
     * @var string
     *
     * @ORM\Column(name="postDate", type="string", length=40)
     */
    private $postDate;
    

    
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
     * Set post_Image_Front
     *
     * @param string $post_Image_Front
     * @return User
     */
    public function setPostImageFront($post_Image_Front)
    {
        $this->post_Image_Front = $post_Image_Front;
    
        return $this;
    }

    /**
     * Get post_Image_Front
     *
     * @return string 
     */
    public function getPostImageFront()
    {
        return $this->post_Image_Front;
    }

    /**
     * Set post_Image_FrontLeft
     *
     * @param string $post_Image_FrontLeft
     * @return User
     */
    public function setPostImageFrontLeft($post_Image_FrontLeft)
    {
        $this->post_Image_FrontLeft = $post_Image_FrontLeft;
    
        return $this;
    }

    /**
     * Get post_Image_FrontLeft
     *
     * @return string 
     */
    public function getPostImageFrontLeft()
    {
        return $this->post_Image_FrontLeft;
    }
    /**
     * Set post_Image_Left
     *
     * @param string $post_Image_Left
     * @return User
     */
    public function setPostImageLeft($post_Image_Left)
    {
        $this->post_Image_Left = $post_Image_Left;
    
        return $this;
    }

    /**
     * Get post_Image_Left
     *
     * @return string 
     */
    public function getPostImageLeft()
    {
        return $this->post_Image_Left;
    }

    /**
     * Set post_Image_BackLeft
     *
     * @param string $post_Image_BackLeft
     * @return User
     */
    public function setPostImageBackLeft($post_Image_BackLeft)
    {
        $this->post_Image_BackLeft = $post_Image_BackLeft;
    
        return $this;
    }

    /**
     * Get post_Image_BackLeft
     *
     * @return string 
     */
    public function getPostImageBackLeft()
    {
        return $this->post_Image_BackLeft;
    }

    /**
     * Set post_Image_Back
     *
     * @param \string $post_Image_Back
     * @return User
     */
    public function setPostImageBack($post_Image_Back)
    {
        $this->post_Image_Back = $post_Image_Back;
    
        return $this;
    }

    /**
     * Get post_Image_Back
     *
     * @return \string 
     */
    public function getPostImageBack()
    {
        return $this->post_Image_Back;
    }

    /**
     * Set post_Image_BackRight
     *
     * @param string $post_Image_BackRight
     * @return User
     */
    public function setPostImageBackRight($post_Image_BackRight)
    {
        $this->post_Image_BackRight = $post_Image_BackRight;
    
        return $this;
    }

    /**
     * Get post_Image_BackRight
     *
     * @return string 
     */
    public function getPostImageBackRight()
    {
        return $this->post_Image_BackRight;
    }

    /**
     * Set post_Image_Right
     *
     * @param string $post_Image_Right
     * @return User
     */
    public function setPostImageRight($post_Image_Right)
    {
        $this->post_Image_Right = $post_Image_Right;
    
        return $this;
    }

    /**
     * Get post_Image_Right
     *
     * @return string 
     */
    public function getPostImageRight()
    {
        return $this->post_Image_Right;
    }

    /**
     * Set post_Image_FrontRight
     *
     * @param string $post_Image_FrontRight
     * @return User
     */
    public function setPostImageFrontRight($post_Image_FrontRight)
    {
        $this->post_Image_FrontRight = $post_Image_FrontRight;
    
        return $this;
    }

    /**
     * Get post_Image_FrontRight
     *
     * @return string 
     */
    public function getPostImageFrontRight()
    {
        return $this->post_Image_FrontRight;
    }

    /**
     * Set postCaption
     *
     * @param string $postCaption
     * @return User
     */
    public function setPostCaption($postCaption)
    {
        $this->postCaption = $postCaption;
    
        return $this;
    }

    /**
     * Get postCaption
     *
     * @return string 
     */
    public function getPostCaption()
    {
        return $this->postCaption;
    }

    /**
     * Set userID
     *
     * @param string $userID
     * @return User
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    
        return $this;
    }

    /**
     * Get userID
     *
     * @return string 
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set userTagID
     *
     * @param string $userTagID
     * @return User
     */
    public function setUserTagID($userTagID)
    {
        $this->userTagID = $userTagID;
    
        return $this;
    }

    /**
     * Get userTagID
     *
     * @return string 
     */
    public function getUserTagID()
    {
        return $this->userTagID;
    }
     /**
     * Set userTagID
     *
     * @param string $userTagID
     * @return User
     */
    public function setPostStatus($postStatus)
    {
        $this->postStatus = $postStatus;
    
        return $this;
    }

    /**
     * Get postStatus
     *
     * @return string 
     */
    public function getPostStatus()
    {
        return $this->postStatus;
    }
    
      /**
     * Set spPostStatus
     *
     * @param string $spPostStatus
     * @return User
     */
    public function setSpPostStatus($spPostStatus)
    {
        $this->spPostStatus = $spPostStatus;
    
        return $this;
    }

    /**
     * Get spPostStatus
     *
     * @return string 
     */
    public function getSpPostStatus()
    {
        return $this->spPostStatus;
    }
    /**
     * Set postNote
     *
     * @param string $postNote
     * @return User
     */
     public function setPostNote($postNote)
    {
        $this->postNote = $postNote;
    
        return $this;
    }
    /**
     * Get postNote
     *
     * @return string 
     */
    public function getPostNote()
    {
        return $this->postNote;
    }
     /**
     * Set postDate
     *
     * @param string $postNote
     * @return User
     */
     public function setPostDate($postDate)
    {
        $this->postDate = $postDate;
    
        return $this;
    }

    /**
     * Get postDate
     *
     * @return string 
     */
    public function getPostDate()
    {
        return $this->postDate;
    }
     

    
}
