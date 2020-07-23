<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Doctrine\ORM\EntityRepository;

/**
 * post_tags
 *
 * @ORM\Table(name = "post_tags")
 * @ORM\Entity
 */
class PostTags 
{
    /**
     * @ORM\Column(name = "postTagID",type="integer")
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
     * @ORM\Column(name="imageName", type="string", length=50)
     */
    private $imageName;
/**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=50)
     */
    private $tags;
    /**
     * @var string
     *
     * @ORM\Column(name="x_axis", type="string", length=40)
     */
    private $x_axis;

    /**
     * @var \string
     *
     * @ORM\Column(name="y_axis", type="string")
     */
    private $y_axis;

    /**
     * @var string
     *
     * @ORM\Column(name="tagNote", type="string", length=40)
     */
    private $tagNote;
	
/**
     * @var string
     *
     * @ORM\Column(name="imageType", type="string", length=40)
     */
    private $imageType;
   /**
     * @var string
     *
     * @ORM\Column(name="imageSize", type="string", length=40)
     */
    private $imageSize;
    /**
     * @var string
     *
     * @ORM\Column(name="image_width", type="string", length=40)
     */
    private $image_width;
    
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
     * Set imageName
     *
     * @param string $imageName
     * @return User
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    
        return $this;
    }

    /**
     * Get imageName
     *
     * @return string 
     */
    public function getImageName()
    {
        return $this->imageName;
    }
    /**
     * Set tags
     *
     * @param string $tags
     * @return User
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    
        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set x_axis
     *
     * @param string $x_axis
     * @return User
     */
    public function setX_Axis($x_axis)
    {
        $this->x_axis = $x_axis;
    
        return $this;
    }

    /**
     * Get x_axis
     *
     * @return string 
     */
    public function getX_Axis()
    {
        return $this->x_axis;
    }

    /**
     * Set y_axis
     *
     * @param \string $y_axis
     * @return User
     */
    public function setY_Axis($y_axis)
    {
        $this->y_axis = $y_axis;
    
        return $this;
    }

    /**
     * Get y_axis
     *
     * @return \string 
     */
    public function getY_Axis()
    {
        return $this->y_axis;
    }

    /**
     * Set tagNote
     *
     * @param string $tagNote
     * @return User
     */
    public function setTagNote($tagNote)
    {
        $this->tagNote = $tagNote;
    
        return $this;
    }

    /**
     * Get tagNote
     *
     * @return string 
     */
    public function getTagNote()
    {
        return $this->tagNote;
    }
	

    /**
     * Set imageType
     *
     * @param string $imageType
     * @return User
     */
     public function setImageType($imageType)
    {
        $this->imageType = $imageType;
    
        return $this;
    }

    /**
     * Get imageType
     *
     * @return string 
     */
    public function getImageType()
    {
        return $this->imageType;
    }
 /**
     * Set imageSize
     *
     * @param string $imageSize
     * @return User
     */
     public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;
    
        return $this;
    }

    /**
     * Get imageSize
     *
     * @return string 
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }
     /**
     * Set image_width
     *
     * @param string $image_width
     * @return User
     */
     public function setImageWidth($image_width)
    {
        $this->image_width = $image_width;
    
        return $this;
    }

    /**
     * Get image_width
     *
     * @return string 
     */
    public function getImageWidth()
    {
        return $this->image_width;
    }

    
}
