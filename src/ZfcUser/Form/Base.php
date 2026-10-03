<?php

namespace ZfcUser\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;

class Base extends ProvidesEventsForm
{
    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(
                'type'    => 'radio',
                'value'   => 1,
                'onchange' => 'fctChangeAccountType();'
            ),
            'options' => array(
                'label' => 'Type',
                'label_attributes' => array(
                    'style'  => 'padding-top: 0;',
                    'class'  => 'radio-inline'
                ),
                'value_options' => array(
                    1 => 'Particular',
                    2 => 'Professional'
                ),
            ),
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
            'name' => 'company',
            'options' => array(
                'label' => 'Company',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'          => 'company',
                'class'       => 'form-control',
                'placeholder' => 'Company',
                'required'    => true
            ),
        ));
        
        $this->add(array(
            'name' => 'firstname',
            'options' => array(
                'label' => 'First name',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'          => 'firstname',
                'class'       => 'form-control',
                'placeholder' => 'First name',
                'required'    => true
            ),
        ));

        $this->add(array(
            'name' => 'lastname',
            'options' => array(
                'label' => 'Last name',
            ),
            'attributes' => array(
                'type'        => 'text',
                 'id'         => 'lastname',
                'class'       => 'form-control',
                'placeholder' => 'Last name',
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

        /*
        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'type' => 'text'
            ),
        ));

        $this->add(array(
            'name' => 'display_name',
            'options' => array(
                'label' => 'Display Name',
            ),
            'attributes' => array(
                'type' => 'text'
            ),
        ));*/

        $this->add(array(
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'type'        => 'password',
                'id'          => 'password',
                'class'       => 'form-control',
                'placeholder' => 'Password',
                'required'    => true
            ),
        ));

        /*
        $this->add(array(
            'name' => 'passwordVerify',
            'options' => array(
                'label' => 'Confirmation du mot de passe',
            ),
            'attributes' => array(
                'type'        => 'password',
                'id'          => 'passwordVerify',
                'class'       => 'form-control',
                'placeholder' => 'Password confirmation',
                'required'    => true
            ),
        )); */

        $this->add(array(
            'name' => 'mobilephone',
            'options' => array(
                'label' => 'Phone',
            ),
            'attributes' => array(
                'type'        => 'text',
                'id'        => 'mobilephone',
                'class'       => 'form-control',
                'placeholder' => 'Phone',
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

        if ($this->getRegistrationOptions()->getUseRegistrationFormCaptcha()) {
            $this->add(array(
                'name' => 'captcha',
                'type' => 'Zend\Form\Element\Captcha',
                'options' => array(
                    'label' => 'Please type the following text',
                     'class'       => 'form-control',
                     'placeholder' => 'Rcopier le texte ci-dessus',
                    'captcha' => $this->getRegistrationOptions()->getFormCaptchaOptions(),
                ),
            ));
        }

        $submitElement = new Element\Button('submit');
        $submitElement
            ->setLabel('Submit')
            ->setAttributes(array(
                'type'  => 'submit',
                'value' => 'Create my account',
                'class' => 'btn btn-albook'
            ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));

        /*$this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'type' => 'hidden'
            ),
        ));*/

        // @TODO: Fix this... getValidator() is a protected method.
        //$csrf = new Element\Csrf('csrf');
        //$csrf->getValidator()->setTimeout($this->getRegistrationOptions()->getUserFormTimeout());
        //$this->add($csrf);
    }
}
