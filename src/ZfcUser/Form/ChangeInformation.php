<?php

namespace ZfcUser\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use ZfcBase\Form\ProvidesEventsForm;
use ZfcUser\Options\AuthenticationOptionsInterface;
use ZfcUser\Module as ZfcUser;

class ChangeInformation extends ProvidesEventsForm
{
    /**
     * @var AuthenticationOptionsInterface
     */
    protected $authOptions;

    public function __construct($name, AuthenticationOptionsInterface $options)
    {
        $this->setAuthenticationOptions($options);
        parent::__construct($name);
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'avatar',
            'attributes' => array(
                'id' => 'avatar',
                'type'  => 'Zend\Form\Element\File',
                'accept'=>"image/bmp,image/gif,image/png,image/jpeg",
                'class' =>'profile-img'
            )
        ));
         

        $this->add(array(
            'name' => 'sexe',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(
               'type'    => 'radio',
               'value'   => 'M'
            ),
            'options' => array(
                'label' => 'Civilité',
                'label_attributes' => array(
                    'style'  => 'padding-top: 0;',
                    'class'  => 'radio-inline'
                ),
                'value_options' => array(
                    'M' => 'Mr',
                    'F' => 'Mrs'
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => 'Nom',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'          => 'firstname',
                'class'       => 'form-control',
                'placeholder' => 'Firstname',
                'required'    => true
            ),
        ));

        $this->add(array(
            'name' => 'lastname',
            'options' => array(
                'label' => 'Prénom ',
            ),
            'attributes' => array(
                'type'        => 'text',
                 'id'         => 'lastname',
                'class'       => 'form-control',
                'placeholder' => 'Lastname'
            ),
        ));


        $this->add(array(
            'name' => 'identity',
            'options' => array(
                'label' => '',
            ),
            'attributes' => array(
                'type' => 'hidden'
            ),
        ));

        $this->add(array(
            'name' => 'credential',
            'options' => array(
                'label' => 'Current Password',
            ),
            'attributes' => array(
                'type' => 'password',
            ),
        ));

        $this->add(array(
            'name' => 'newCredential',
            'options' => array(
                'label' => 'New Password',
            ),
            'attributes' => array(
                'type' => 'password',
            ),
        ));

        $this->add(array(
            'name' => 'newCredentialVerify',
            'options' => array(
                'label' => 'Verify New Password',
            ),
            'attributes' => array(
                'type' => 'password',
            ),
        ));

        $this->add(array(
            'name' => 'mobilephone',
            'options' => array(
                'label' => 'Phone',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'        => 'mobilephone',
                'class'       => 'form-control',
                'required'    => true
            ),
        ));

        $this->add(array(
            'name' => 'username',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'         => 'username',
                'class'       => 'form-control',
                'placeholder' => 'Email',
                'required'    => true
            ),
        ));

        $this->add(array(
            'name' => 'frm_vk',
            'options' => array(
            ),
            'attributes' => array(
                'type' => 'hidden'
            ),
        ));

        

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Modifier',
                'type'  => 'submit'
            ),
        ));
        $this->getEventManager()->trigger('init', $this);
    }

    /**
     * Set Authentication-related Options
     *
     * @param AuthenticationOptionsInterface $authOptions
     * @return Login
     */
    public function setAuthenticationOptions(AuthenticationOptionsInterface $authOptions)
    {
        $this->authOptions = $authOptions;
        return $this;
    }

    /**
     * Get Authentication-related Options
     *
     * @return AuthenticationOptionsInterface
     */
    public function getAuthenticationOptions()
    {
        return $this->authOptions;
    }
}
