<?php

namespace ZfcUser\Mapper;

use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcUser\Entity\UserInterface as UserEntityInterface;

class UserHydrator extends ClassMethods
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
        if (!$object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfcUser\Entity\UserInterface');
        }
        /* @var $object UserInterface */
        $data = parent::extract($object);
        if ($data['id'] !== null) {
            $data = $this->mapField('id', 'ID', $data);
        } else {
            unset($data['id']);
        }

        $identityKeys = array(
            'first_name',
            'last_name',
            'sexe',
            'birth_date',
            'phone_mobile',
            'email',
            'phone',
            'alb_address_id',
            'avatar'
        );
        $validData = array();
        foreach ($data as $k => $v) {
            if (!in_array($k, $identityKeys)) {
                $validData[$k] = $v;
            }
        }
        return $validData;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return UserInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException('$object must be an instance of ZfcUser\Entity\UserInterface');
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
