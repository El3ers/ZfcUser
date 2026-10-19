<?php

namespace ZfcUser\Mapper;

use ZfcBase\Mapper\AbstractDbMapper;
use ZfcUser\Entity\UserInterface as UserEntityInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\Filter\FilterComposite;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;

class User extends AbstractDbMapper implements UserInterface
{
    protected $tableName  = 'alb_users';

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new ClassMethods(true);
            // Exclure les anciennes méthodes remplacées par leurs alias (getName, getAlbIdentitesId)
            $this->hydrator->addFilter('excludeOldColumns', new MethodMatchFilter('getUsername'), FilterComposite::CONDITION_AND);
            $this->hydrator->addFilter('excludeOldIdentityId', new MethodMatchFilter('getAlbIdentitiesId'), FilterComposite::CONDITION_AND);
        }
        return $this->hydrator;
    }

    public function findByEmail($email)
    {
        $select = $this->getSelect()
                       ->where(array('email' => $email));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function findByUsername($username, $loadIdentity = false)
    {
        return $this->findByEmail($username, $loadIdentity);
    }

    public function findById($id, $loadIdentity = false)
    {
        $select = $this->getSelect()
                       ->where(array('id' => $id));

        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));

        // Load identity (firstname, lastname, phone, etc. depuis ALB_IDENTITIES)
        if ($loadIdentity) {
            $identityId = $entity->getAlbIdentitiesId();
            $this->tableName = 'ALB_IDENTITIES';
            $select = $this->getSelect()->where(array('ID' => $identityId));
            $oIdentity = $this->select($select)->current();
            if ($oIdentity) {
                $entity->setFirstname($oIdentity->getFirstname());
                $entity->setLastname($oIdentity->getLastname());
                $entity->setPhoneMobile($oIdentity->getPhoneMobile());
                $entity->setBirthDate($oIdentity->getBirthDate());
                $entity->setSexe($oIdentity->getSexe());
                $entity->setAvatar($oIdentity->getAvatar());
            }
            $this->tableName = 'alb_users';
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

    protected function entityToArray($entity, HydratorInterface $hydrator = null)
    {
        $data = parent::entityToArray($entity, $hydrator);
         unset($data['username']);
         unset($data['alb_identities_id']);
         unset($data['first_name']);
         unset($data['last_name']);
         unset($data['birth_date']);
         unset($data['sexe']);
         unset($data['phone']);
         unset($data['phone_mobile']);
         unset($data['alb_address_id']);
         unset($data['avatar']);
         return $data;
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
            $where = array('id' => $entity->getId());
        }

        return parent::update($entity, $where, $tableName, $hydrator);
    }
}
