<?php

namespace ZfcUser\Entity;

interface UserInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id);

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set username.
     *
     * @param string $username
     * @return UserInterface
     */
    public function setUsername($username);


    /**
     * Get password.
     *
     * @return string password
     */
    public function getPassword();

    /**
     * Set password.
     *
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password);
    
    /**
     * 
     * Enter description here ...
     */
    public function getCreateDate();

    /**
     * 
     * Enter description here ...
     * @param date $date
     */
    public function setCreateDate($createdate);

    public function getActivateDate();
    public function setActivateDate($activatedate);

    public function getDeactivateDate();
    public function setDeactivateDate($deactivatedate);

    /**
     * Get state.
     *
     * @return int
     */
    public function getState();

    /**
     * Set state.
     *
     * @param int $state
     * @return UserInterface
     */
    public function setState($state);

    public function getAlbProfilesId();
    public function setAlbProfilesId($profileid);

    public function getAlbIdentitiesId();
    public function setAlbIdentitiesId($ididentity);

    public function getType();
    public function setType($type);
    
    
}
