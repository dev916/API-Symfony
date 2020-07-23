<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * awn_user
 *
 * @ORM\Table(name = "awn_user")
 * @ORM\Entity(repositoryClass="Acme\DemoBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface {

    /**
     * @ORM\Column(name = "userID",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="userFirstName", type="string", length=50)
     */
    private $userFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="userLastName", type="string", length=50)
     */
    private $userLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=50)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="userPassword", type="string", length=40)
     */
    private $userPassword;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	public function __construct()
	{
		$this->isActive = true;

	}

    /**
     * @var \string
     *
     * @ORM\Column(name="userDOB", type="string")
     */
    private $userDOB;

    /**
     * @var string
     *
     * @ORM\Column(name="userEmail", type="string", length=40)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="userNote", type="string", length=60)
     */
    private $userNote;

    /**
     * @var string
     *
     * @ORM\Column(name="userGender", type="string", length=2)
     */
    private $userGender;

    /**
     * @var string
     *
     * @ORM\Column(name="userProfileImage", type="string", length=60)
     */
    private $userProfileImage;

    /**
     * @var string
     *
     * @ORM\Column(name="userSignature", type="string", length=60)
     */
    private $userSignature;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=40)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="userWebsite", type="string", length=40)
     */
    private $userWebsite;

    /**
     * @var string
     *
     * @ORM\Column(name="userBIO", type="string", length=255)
     */
    private $userBIO;

    /**
     * @var string
     *
     * @ORM\Column(name="userAddress", type="string", length=255)
     */
    private $userAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="userMobileNo", type="string", length=30)
     */
    private $userMobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="userType", type="string", length=2)
     */
    private $userType;

    /**
     * @var string
     *
     * @ORM\Column(name="loginType", type="string", length=4)
     */
    private $loginType;

    /**
     * @var string
     *
     * @ORM\Column(name="isNotification", type="string", length=2)
     */
    private $isNotification;

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string", length=121)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="longitute", type="string", length=212)
     */
    private $longitute;

	/**
	 * @ORM\Column(name="signupDate",type="datetime")
	 */
	private $signupDate;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="memberStatus", type="integer", length=2)
	 */
	private $memberStatus;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set userFirstName
     *
     * @param string $userFirstName
     * @return User
     */
    public function setUserFirstName($userFirstName) {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    /**
     * Get userFirstName
     *
     * @return string 
     */
    public function getUserFirstName() {
        return $this->userFirstName;
    }

    /**
     * Set userLastName
     *
     * @param string $userLastName
     * @return User
     */
    public function setUserLastName($userLastName) {
        $this->userLastName = $userLastName;

        return $this;
    }

    /**
     * Get userLastName
     *
     * @return string 
     */
    public function getUserLastName() {
        return $this->userLastName;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName) {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     * @return User
     */
    public function setUserPassword($userPassword) {
        $this->userPassword = $userPassword;

        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string 
     */
    public function getUserPassword() {
        return $this->userPassword;
    }

    /**
     * Set userDOB
     *
     * @param \string $userDOB
     * @return User
     */
    public function setUserDOB($userDOB) {
        $this->userDOB = $userDOB;

        return $this;
    }

    /**
     * Get userDOB
     *
     * @return \string 
     */
    public function getUserDOB() {
        return $this->userDOB;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     * @return User
     */
    public function setUserEmail($userEmail) {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string 
     */
    public function getUserEmail() {
        return $this->userEmail;
    }

    /**
     * Set userNote
     *
     * @param string $userNote
     * @return User
     */
    public function setUserNote($userNote) {
        $this->userNote = $userNote;

        return $this;
    }

    /**
     * Get userNote
     *
     * @return string 
     */
    public function getUserNote() {
        return $this->userNote;
    }

    /**
     * Set userGender
     *
     * @param string $userGender
     * @return User
     */
    public function setUserGender($userGender) {
        $this->userGender = $userGender;

        return $this;
    }

    /**
     * Get userGender
     *
     * @return string 
     */
    public function getUserGender() {
        return $this->userGender;
    }

    /**
     * Set userProfileImage
     *
     * @param string $userProfileImage
     * @return User
     */
    public function setUserProfileImage($userProfileImage) {
        $this->userProfileImage = $userProfileImage;

        return $this;
    }

    /**
     * Get userProfileImage
     *
     * @return string 
     */
    public function getUserProfileImage() {
        return $this->userProfileImage;
    }

    /**
     * Set userSignature
     *
     * @param string $userSignature
     * @return User
     */
    public function setUserSignature($userSignature) {
        $this->userSignature = $userSignature;

        return $this;
    }

    /**
     * Get userSignature
     *
     * @return string 
     */
    public function getUserSignature() {
        return $this->userSignature;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return User
     */
    public function setCompanyName($companyName) {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string 
     */
    public function getCompanyName() {
        return $this->companyName;
    }

    /**
     * Set userWebsite
     *
     * @param string $userWebsite
     * @return User
     */
    public function setUserWebsite($userWebsite) {
        $this->userWebsite = $userWebsite;

        return $this;
    }

    /**
     * Get userWebsite
     *
     * @return string 
     */
    public function getUserWebsite() {
        return $this->userWebsite;
    }

    /**
     * Set userBIO
     *
     * @param string $userBIO
     * @return User
     */
    public function setUserBIO($userBIO) {
        $this->userBIO = $userBIO;

        return $this;
    }

    /**
     * Get userBIO
     *
     * @return string 
     */
    public function getUserBIO() {
        return $this->userBIO;
    }

    /**
     * Set userMobileNo
     *
     * @param string $userMobileNo
     * @return User
     */
    public function setUserMobileNo($userMobileNo) {
        $this->userMobileNo = $userMobileNo;

        return $this;
    }

    /**
     * Get userMobileNo
     *
     * @return string 
     */
    public function getUserMobileNo() {
        return $this->userMobileNo;
    }

    /**
     * Set userAddress
     *
     * @param string $userAddress
     * @return User
     */
    public function setUserAddress($userAddress) {
        $this->userAddress = $userAddress;

        return $this;
    }

    /**
     * Get userAddress
     *
     * @return string 
     */
    public function getUserAddress() {
        return $this->userAddress;
    }

    /**
     * Set userType
     *
     * @param string $userType
     * @return User
     */
    public function setUserType($userType) {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType() {
        return $this->userType;
    }

    /**
     * Set loginType
     *
     * @param string $loginType
     * @return User
     */
    public function setLoginType($loginType) {
        $this->loginType = $loginType;

        return $this;
    }

    /**
     * Get loginType
     *
     * @return string 
     */
    public function getLoginType() {
        return $this->loginType;
    }
 /**
     * Set lat
     *
     * @param string $lat
     * @return User
     */
    public function setLat($lat) {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * Set longitute
     *
     * @param string $longitute
     * @return User
     */
    public function setLongitute($longitute) {
        $this->longitute = $longitute;

        return $this;
    }

    /**
     * Get longitute
     *
     * @return string 
     */
    public function getLongitute() {
        return $this->longitute;
    }

	/**
	 * Set signupDate
	 * @param datetime $signupDate
	 */
	public function setSignupDate($signupDate) {
		$this->signupDate = $signupDate;

		return $this;
	}

	/**
	 * Get signupDate
	 *
	 */
	public function getSignupDate() {
		return $this->signupDate;
	}

	/**
	 * Set memberStatus
	 * @param integer $memberStatus
	 */
	public function setmemberStatus($memberStatus) {
		$this->memberStatus = $memberStatus;

		return $this;
	}

	/**
	 * Get memberStatus
	 *
	 */
	public function getmemberStatus() {
		return $this->memberStatus;
	}

    /**
     * Set isNotification
     *
     * @param string $isNotification
     * @return User
     */
    public function setIsNotification($isNotification) {
        $this->isNotification = $isNotification;

        return $this;
    }

    /**
     * Get isNotification
     *
     * @return string 
     */
    public function getIsNotification() {
        return $this->isNotification;
    }

   /**
	* added to complete the class
	*/

	public function isAccountNonExpired()
	{
		return true;
	}

	public function isAccountNonLocked()
	{
		return true;
	}

	public function isCredentialsNonExpired()
	{
		return true;
	}

	public function isEnabled()
	{
		return $this->isActive;
	}

	public function eraseCredentials()
	{
		$this->plainPassword = ''; // just blank it out
	}

	public function getPassword()
	{
		return $this->userPassword;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return array('ROLE_USER');
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		// you *may* need a real salt depending on your encoder
		//auto salt with bcrypt
		return null;
	}

}

 