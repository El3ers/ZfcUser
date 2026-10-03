<?php

namespace ZfcUser\Entity;

class User extends Identity implements UserInterface
{
    
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_username;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @var date
     */
    protected $_createDate;

    /**
     * @var date
     */
    protected $_activateDate;

    /**
     * @var date
     */
    protected $_deactivateDate;

    /**
     * @var int
     */
    protected $_state;

    /**
     * @var int
     */
    protected $_profileId;

    /**
     * @var int
     */
    protected $_identityId;

    /**
     * @var int
     */
    protected $_type;


    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = null) {
        $this->_createDate = date('Y-m-d');
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid Method');
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Exception('Invalid Method');
        }
        return $this->$method();
    }

    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Getter for ID
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Setter for ID
     * @param int $id
     */
    public function setId($id) {
        $this->_id = $id;
        return $this;
    }
    
    /**
     * Getter for USERNAME
     */
    public function getUsername() {
        return $this->_username;
    }

    /**
     * Setter for USERNAME
     * @param string $userName
     */
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }
    
    /**
     * Getter for PASSWORD
     */
    public function getPassword() {
        return $this->_password;
    }

    /**
     * Setter for PASSWORD
     * @param string $password
     */
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Getter for CREATE_DATE
     */
    public function getCreateDate() {
        return $this->_createDate;
    }

    /**
     * Setter for CREATE_DATE
     * @param date $createDate
     */
    public function setCreateDate($createdate) {
        $this->_createDate = $createdate;
        return $this;
    }

    /**
     * Getter for ACTIVATE_DATE
     */
    public function getActivateDate() {
        return $this->_activateDate;
    }

    /**
     * Setter for ACTICATE_DATE
     * @param date $activateDate
     */
    public function setActivateDate($activateDate) {
        $this->_activateDate = $activateDate;
        return $this;
    }
    
    /**
     * Getter for DEACTIVATE_DATE
     */
    public function getDeactivateDate() {
        return $this->_deactivateDate;
    }

    /**
     * Setter for DEACTICATE_DATE
     * @param date $deactivateDate
     */
    public function setDeactivateDate($deactivateDate) {
        $this->_deactivateDate = $deactivateDate;
        return $this;
    }

    /**
     * Getter for STATE
     */
    public function getState() {
        return $this->_state;
    }

    /**
     * Setter for STATE
     * @param boolean $state
     */
    public function setState($state) {
        $this->_state = $state;
        return $this;
    }
    
    /**
     * Getter for ALB_PROFILES_ID
     */
    public function getAlbProfilesId() {
        return $this->_profileId;
    }

    /**
     * Setter for ALB_PROFILES_ID
     * @param int $profileId
     */
    public function setAlbProfilesId($profileId) {
        $this->_profileId = $profileId;
        return $this;
    }

    /**
     * Getter for ALB_IDENTITIES_ID
     */
    public function getAlbIdentitiesId() {
        return $this->_identityId;
    }

    /**
     * Setter for ALB_IDENTITIES_ID
     * @param int $identityId
     */
    public function setAlbIdentitiesId($identityId) {
        $this->_identityId = $identityId;
        return $this;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }



}