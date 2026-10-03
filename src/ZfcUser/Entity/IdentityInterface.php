<?php

namespace ZfcUser\Entity;

interface IdentityInterface
{

    public function getId();
    public function setId($id);

    public function getFirstName();
    public function setFirstName($firstname);

    public function getLastName();
    public function setLastName($lastname);

    public function getBirthDate();
    public function setBirthDate($birthDate);

    public function getSexe();
    public function setSexe($sexe);

    public function getEmail();
    public function setEmail($email);

    public function getPhone();
    public function setPhone($phone);

    public function getPhoneMobile();
    public function setPhoneMobile($phonemobile);

    public function getAlbAddressId();
    public function setAlbAddressId($addressid);

    public function getAvatar();
    public function setAvatar($avatar);
}