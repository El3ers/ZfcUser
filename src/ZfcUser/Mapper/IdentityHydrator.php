<?php

namespace ZfcUser\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcUser\Entity\IdentityInterface as IdentityEntityInterface;

class IdentityHydrator extends ClassMethods
{

    /**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof IdentityEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfcUser\Entity\IdentityInterface');
        }
        /* @var $object IdentityInterface */
        $data = parent::extract($object);
        if ($data['id'] !== null) {
            $data = $this->mapField('id', 'ID', $data);
        } else {
            unset($data['id']);
        }
        //$data = $this->mapField('profile_id', 'ALB_PROFILES_ID', $data);
        //$data = $this->mapField('identity_id', 'ALB_IDENTITIES_ID', $data);
        
        //var_dump($data);exit;
       
        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return IdentityInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof IdentityEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfcUser\Entity\IdentityInterface');
        }
        $data = $this->mapField('ID', 'id', $data);
        return parent::hydrate($data, $object);
    }

    protected function mapField($keyFrom, $keyTo, array $array)
    {
        $array[$keyTo] = $array[$keyFrom];
        unset($array[$keyFrom]);
        return $array;
    }
}
