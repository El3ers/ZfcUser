<?php
namespace ZfcUser\Entity;

class Identity implements IdentityInterface
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_firstname;

    /**
     * @var string
     */
    protected $_lastname;

    /**
     * @var string
     */
    protected $_birthDate;

    /**
     * @var string
     */
    protected $_sexe;

    /**
     * @var string
     */
    protected $_email;

    /**
     * @var int
     */
    protected $_phone;

    /**
     * @var int
     */
    protected $_phoneMobile;

    /**
     * @var int
     */
    protected $_albAddressId;

    /**
     * @var String
     */
    protected $_avatar;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getFirstName()
    {
        return $this->_firstname;
    }

    public function setFirstName($firstname)
    {
        $this->_firstname = $firstname;
    }

    public function getLastName()
    {
        return $this->_lastname;
    }

    public function setLastName($lastname)
    {
        $this->_lastname = $lastname;
    }

    public function getBirthDate()
    {
        return $this->_birthDate;
    }

    public function setBirthDate($birthDate)
    {
        $this->_birthDate = $birthDate;
    }

    public function getSexe()
    {
        return $this->_sexe;
    }

    public function setSexe($sexe)
    {
        $this->_sexe = $sexe;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = $email;
    }

    public function getPhone()
    {
        return $this->_phone;
    }

    public function setPhone($phone)
    {
        $this->_phone = $phone;
    }

    public function getPhoneMobile()
    {
        return $this->_phoneMobile;
    }

    public function setPhoneMobile($phoneMobile)
    {
        $this->_phoneMobile = $phoneMobile;
    }

    public function getAlbAddressId()
    {
        return $this->_albAddressId;
    }

    public function setAlbAddressId($albAddressId)
    {
        $this->_albAddressId = $albAddressId;
    }

    /**
     * @param String $avatar
     */
    public function setAvatar($avatar)
    {
        $this->_avatar = $avatar;
    }

    /**
     * @return String
     */
    public function getAvatar()
    {
        return $this->_avatar;
    }


}