<?php

namespace ZfcUser\Form;

use ZfcBase\InputFilter\ProvidesEventsInputFilter;
use ZfcUser\Module as ZfcUser;
use ZfcUser\Options\RegistrationOptionsInterface;

class RegisterFilter extends ProvidesEventsInputFilter
{
    protected $emailValidator;
    protected $usernameValidator;

    /**
     * @var RegistrationOptionsInterface
     */
    protected $options;

    public function __construct($emailValidator, $usernameValidator, RegistrationOptionsInterface $options)
    {
        $this->setOptions($options);
        $this->emailValidator = $emailValidator;
        $this->usernameValidator = $usernameValidator;

        $this->add(array(
            'name'        => 'firstname',
            'required'    => false,
            'allow_empty' => true,
            'filters'     => array(array('name' => 'StringTrim')),
            'validators'  => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($value, $context = array()) {
                            if (isset($context['type']) && $context['type'] == 2) {
                                return true;
                            }
                            $v = trim($value);
                            return $v !== '' && mb_strlen($v) >= 3 && mb_strlen($v) <= 45;
                        },
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'        => 'lastname',
            'required'    => false,
            'allow_empty' => true,
            'filters'     => array(array('name' => 'StringTrim')),
            'validators'  => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($value, $context = array()) {
                            if (isset($context['type']) && $context['type'] == 2) {
                                return true;
                            }
                            $v = trim($value);
                            return $v !== '' && mb_strlen($v) >= 3 && mb_strlen($v) <= 45;
                        },
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'        => 'company',
            'required'    => false,
            'allow_empty' => true,
            'filters'     => array(array('name' => 'StringTrim')),
            'validators'  => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($value, $context = array()) {
                            if (!isset($context['type']) || $context['type'] != 2) {
                                return true;
                            }
                            $v = trim($value);
                            return $v !== '' && mb_strlen($v) >= 3 && mb_strlen($v) <= 150;
                        },
                    ),
                ),
            ),
        ));



        if ($this->getOptions()->getEnableUsername()) {
            $this->add(array(
                'name'       => 'username',
                'required'   => true,
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 255,
                        ),
                    ),
                    $this->usernameValidator,
                ),
            ));
        }

        $this->add(array(
            'name'       => 'username',
            'required'   => true,
            'filters'    => array(array('name' => 'StringTrim')),
            'validators' => array(
                array(
                    'name' => 'EmailAddress',
                ),
            ),
        ));

        /*if ($this->getOptions()->getEnableDisplayName()) {
            $this->add(array(
                'name'       => 'display_name',
                'required'   => true,
                'filters'    => array(array('name' => 'StringTrim')),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 128,
                        ),
                    ),
                ),
            ));
        }*/

        $this->add(array(
            'name'       => 'password',
            'required'   => true,
            'filters'    => array(array('name' => 'StringTrim')),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'passwordVerify',
            'required'   => true,
            'filters'    => array(array('name' => 'StringTrim')),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6,
                    ),
                ),
                array(
                    'name'    => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'mobilephone',
            'required'   => true,
            'filters'    => array(array('name' => 'StringTrim')),
            'validators' => array(
                array(
                    'name'    => 'Callback',
                    'options' => array(
                        'callback' => function($value) {
                            $v = preg_replace('/\s/', '', $value);
                            if (preg_match('/^0[567]/', $v)) {
                                return (bool) preg_match('/^0[567]\d{8}$/', $v);
                            }
                            return (bool) preg_match('/^\+?[\d\s\-(). ]{8,}$/', $value);
                        },
                    ),
                ),
            ),
        ));

        $this->getEventManager()->trigger('init', $this);
    }

    public function getEmailValidator()
    {
        return $this->emailValidator;
    }

    public function setEmailValidator($emailValidator)
    {
        $this->emailValidator = $emailValidator;
        return $this;
    }

    public function getUsernameValidator()
    {
        return $this->usernameValidator;
    }

    public function setUsernameValidator($usernameValidator)
    {
        $this->usernameValidator = $usernameValidator;
        return $this;
    }

    /**
     * set options
     *
     * @param RegistrationOptionsInterface $options
     */
    public function setOptions(RegistrationOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * get options
     *
     * @return RegistrationOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }
}
