<?php

namespace ZfcUser\Service;

use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Stdlib\Hydrator;
use ZfcBase\EventManager\EventProvider;
use ZfcUser\Mapper\IdentityInterface as IdentityMapperInterface;
use ZfcUser\Options\IdentityServiceOptionsInterface;

class Identity extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var IdentityMapperInterface
     */
    protected $identityMapper;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var Form
     */
    protected $loginForm;

    /**
     * @var Form
     */
    protected $registerForm;

    /**
     * @var Form
     */
    protected $changePasswordForm;

    /**
    * @var Form
    */
    protected $changeInformationForm;
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var IdentityServiceOptionsInterface
     */
    protected $options;

    /**
     * @var Hydrator\ClassMethods
     */
    protected $formHydrator;

    /**
     * createFromForm
     *
     * @param array $data
     * @return \ZfcUser\Entity\IdentityInterface
     * @throws Exception\InvalidArgumentException
     */
    public function register(array $data)
    {
        $class = $this->getOptions()->getIdentityEntityClass();
        
        $identity  = new $class;
        $form  = $this->getRegisterForm();
        $form->setHydrator($this->getFormHydrator());
        $form->bind($identity);
        $form->setData($data);
        if (!$form->isValid()) {
            return false;
        }

        $identity = $form->getData();
        /* @var $identity \ZfcUser\Entity\IdentityInterface */

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $identity->setPassword($bcrypt->create($identity->getPassword()));

        /*if ($this->getOptions()->getEnableUsername()) {
            $user->setUsername($data['username']);
        }
        if ($this->getOptions()->getEnableDisplayName()) {
            $user->setDisplayName($data['display_name']);
        }*/

        // If identity state is enabled, set the default state value
        if ($this->getOptions()->getEnableIdentityState()) {
            if ($this->getOptions()->getDefaultIdentityState()) {
                $identity->setState($this->getOptions()->getDefaultIdentityState());
            }
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('identity' => $identity, 'form' => $form));
        $this->getIdentityMapper()->insert($identity);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('identity' => $identity, 'form' => $form));
        return $identity;
    }

    /**
     * change the current Identitys password
     *
     * @param array $data
     * @return boolean
     */
    public function changePassword(array $data)
    {
        $currentIdentity = $this->getAuthService()->getIdentity();

        $oldPass = $data['credential'];
        $newPass = $data['newCredential'];

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (!$bcrypt->verify($oldPass, $currentIdentity->getPassword())) {
            return false;
        }

        $pass = $bcrypt->create($newPass);
        $currentIdentity->setPassword($pass);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('identity' => $currentIdentity));
        $this->getIdentityMapper()->update($currentIdentity);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('identity' => $currentIdentity));

        return true;
    }

    public function changeInformation(array $data)
    {
        $currentIdentity = $this->getAuthService()->getIdentity();

        $oldPass = $data['credential'];
        $newPass = $data['newCredential'];

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (!$bcrypt->verify($oldPass, $currentIdentity->getPassword())) {
            return false;
        }

        $pass = $bcrypt->create($newPass);
        $currentIdentity->setPassword($pass);
        $currentIdentity->setFirstname($data['firstname']);
        $currentIdentity->setLastname($data['lastname']);
        $currentIdentity->setPhoneMobile($data['phonemobile']);
        $currentIdentity->setSexe($data['sexe']);
        $currentIdentity->setAvatar($data['avatar']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('identity' => $currentIdentity));
        $this->getIdentityMapper()->update($currentIdentity);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('identity' => $currentIdentity));

        return true;
    }
    public function changeEmail(array $data)
    {
        $currentIdentity = $this->getAuthService()->getIdentity();

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (!$bcrypt->verify($data['credential'], $currentIdentity->getPassword())) {
            return false;
        }

        $currentIdentity->setEmail($data['newIdentity']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('identity' => $currentIdentity));
        $this->getIdentityMapper()->update($currentIdentity);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('identity' => $currentIdentity));

        return true;
    }

    /**
     * getIdentityMapper
     *
     * @return IdentityMapperInterface
     */
    public function getIdentityMapper()
    {
        if (null === $this->identityMapper) {
            $this->identityMapper = $this->getServiceManager()->get('zfcuser_identity_mapper');
        }
        return $this->identityMapper;
    }

    /**
     * setIdentityMapper
     *
     * @param IdentityMapperInterface $identityMapper
     * @return Identity
     */
    public function setIdentityMapper(IdentityMapperInterface $identityMapper)
    {
        $this->identityMapper = $identityMapper;
        return $this;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceManager()->get('zfcuser_auth_service');
        }
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param AuthenticationService $authService
     * @return Identity
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
        return $this;
    }

    /**
     * @return Form
     */
    public function getRegisterForm()
    {
        if (null === $this->registerForm) {
            $this->registerForm = $this->getServiceManager()->get('zfcuser_register_form');
        }
        return $this->registerForm;
    }

    /**
     * @param Form $registerForm
     * @return Identity
     */
    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * @return Form
     */
    public function getChangePasswordForm()
    {
        if (null === $this->changePasswordForm) {
            $this->changePasswordForm = $this->getServiceManager()->get('zfcuser_change_password_form');
        }
        return $this->changePasswordForm;
    }

    /**
     * @param Form $changePasswordForm
     * @return Identity
     */
    public function setChangePasswordForm(Form $changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    public function getChangeInformationForm()
    {
        if (null === $this->changeInformationForm) {
            $this->changeInformationForm = $this->getServiceManager()->get('zfcuser_change_information_form');
        }
        return $this->changeInformationForm;
    }
    public function setChangeInformation(Form $changeInformationForm)
    {
        $this->changeInformationForm = $changeInformationForm;
        return $this;
    }
    /**
     * get service options
     *
     * @return IdentityServiceOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof IdentityServiceOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('zfcuser_identity_module_options'));
        }
        return $this->options;
    }

    /**
     * set service options
     *
     * @param IdentityServiceOptionsInterface $options
     */
    public function setOptions(IdentityServiceOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return Identity
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    /**
     * Return the Form Hydrator
     *
     * @return \Zend\Stdlib\Hydrator\ClassMethods
     */
    public function getFormHydrator()
    {
        if (!$this->formHydrator instanceof Hydrator\HydratorInterface) {
            $this->setFormHydrator($this->getServiceManager()->get('zfcuser_register_form_hydrator'));
        }

        return $this->formHydrator;
    }

    /**
     * Set the Form Hydrator to use
     *
     * @param Hydrator\HydratorInterface $formHydrator
     * @return Identity
     */
    public function setFormHydrator(Hydrator\HydratorInterface $formHydrator)
    {
        $this->formHydrator = $formHydrator;
        return $this;
    }
}
