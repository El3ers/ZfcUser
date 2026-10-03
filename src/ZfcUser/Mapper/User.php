<?php

namespace ZfcUser\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use ZfcUser\Entity\UserInterface as UserEntityInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

class User extends AbstractDbMapper implements UserInterface
{
    protected $tableName  = 'ALB_USERS';

    public function findByEmail($email)
    {
        return $this->findByUsername($email);
        $select = $this->getSelect()
                       ->where(array('email' => $email));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByUsername($username, $loadIdentity = false)
    {
        $select = $this->getSelect()
                       ->where(array('USERNAME' => $username));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        // Load identity
        if ($loadIdentity) {
            $identityId = $entity->getAlbIdentitiesId();
            $this->tableName = 'ALB_IDENTITIES';
            $select = $this->getSelect()->where(array('ID' => $identityId));
            $oIdentity = $this->select($select)->current();
            $entity->setFirstname($oIdentity->getFirstname());
            $entity->setLastname($oIdentity->getLastname());
            $entity->setEmail($oIdentity->getEmail());
            $entity->setPhoneMobile($oIdentity->getPhoneMobile());
            $entity->setBirthDate($oIdentity->getBirthDate());
            $entity->setSexe($oIdentity->getSexe());
            $entity->setAvatar($oIdentity->getAvatar());
            $this->tableName = 'ALB_USERS';
        }
        return $entity;
    }

    public function findById($id, $loadIdentity = false)
    {
        $select = $this->getSelect()
                       ->where(array('ID' => $id));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        // Load identity
        if ($loadIdentity) {
            $identityId = $entity->getAlbIdentitiesId();
            $this->tableName = 'ALB_IDENTITIES';
            $select = $this->getSelect()->where(array('ID' => $identityId));
            $oIdentity = $this->select($select)->current();
            $entity->setFirstname($oIdentity->getFirstname());
            $entity->setLastname($oIdentity->getLastname());
            $entity->setEmail($oIdentity->getEmail());
            $entity->setPhoneMobile($oIdentity->getPhoneMobile());
            $entity->setBirthDate($oIdentity->getBirthDate());
            $entity->setSexe($oIdentity->getSexe());
            $entity->setAvatar($oIdentity->getAvatar());
            $this->tableName = 'ALB_USERS';
        }
        return $entity;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $result = parent::insert($entity, $tableName, $hydrator);
        $entity->setId($result->getGeneratedValue());

        $this->_insertUser($entity);

        return $result;
    }

    private function _insertUser($entity)
    {
        $data = $this->prepareUserData($entity);

    }

    protected function prepareUserData($entity)
    {
        // for Zend\Db\TableGateway\TableGateway we need the data in array not object
        $data = array(
            'NAME'              => $this->getName(),
            'EMAIL'             => $this->getEmail(),
            'SEND_DATE'         => $this->getSendDate(),
            'SUBJECT'           => $this->getSubject(),
            'CORPS'             => $this->getCorps(),

        );
        return $data;
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        if (!$where) {
            $where = array('ID' => $entity->getId());
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }
}
