<?php

namespace ZfcUser\Controller;

use Albook\Controller\AbstractController;
use Comment\Model\Comment;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;
use ZfcUser\Service\Iser as IdentityService;
use ZfcUser\Options\IserControllerOptionsInterface;
use Zend\View\Model\JsonModel;

use Annonce\Model\Annonce;
use Albadmin\Model\Menu;
use Albadmin\Model\Banner;
use Albadmin\Model\Boutique;
use Boutique\Form\ProduitForm;
use Boutique\Model\Category;

use ZfcUser\Form\ChangePassword;

class UserController extends AbstractController
{
    const ROUTE_CHANGEPASSWD = 'zfcuser/changemypass';
    const ROUTE_CHANGEINFORMATION = 'zfcuser/changeinformation';
    const ROUTE_LOGIN        = 'zfcuser/thawourth26';
    const ROUTE_REGISTER     = 'zfcuser/register';
    const ROUTE_CHANGEEMAIL  = 'zfcuser/changeemail';

    const ROUTE_MYADS        = 'zfcuser/myads';
    const ROUTE_MYCAR        = 'zfcuser/mycar';
    const ROUTE_MYHOTEL      = 'zfcuser/myhotel';
    const ROUTE_MYPUB        = 'zfcuser/mypub';
    const ROUTE_MYBLOG       = 'zfcuser/myblog';
    const ROUTE_MYSTORE      = 'zfcuser/mystore';
    const ROUTE_PRODUITS     = 'zfcuser/produits';

    const CONTROLLER_NAME    = 'zfcuser';

    /**
     * @var UserService
     */
    protected $userService;

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
     * @var Form
     */
    protected $changeEmailForm;

    /**
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    protected $failedLoginMessage = 'Authentication failed. Please try again.';

    /**
     * @var UserControllerOptionsInterface
     */
    protected $options;

    /**
     * User page
     */
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $oAnnonce = new Annonce(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $nbAds = $oAnnonce->getCountLimit(1, ["ALB_USERS_ID = $userId"]);

        $aWhere = ["ALB_USERS_ID = $userId", "ACTIVE = 1"];
        $nbVisits = $oAnnonce->geVisitsCount($aWhere);
        $nbResponses = $oAnnonce->geResponsesCount($aWhere);
        $ratingsInfo = $oAnnonce->getRatingsInfo($aWhere);

        $oComment = new Comment(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $nbComments = $oComment->getCountLimit(["ALN_ANNONCES.ALB_USERS_ID = $userId"]);

        /*
        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);*/


        return new ViewModel(
            array(
                // 'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm(),
                'nbAds' => $nbAds,
                'nbVisits' => $nbVisits,
                'nbResponses' => $nbResponses,
                'ratingsInfo' => $ratingsInfo,
                'nbComments' => $nbComments
            )
        );
    }

    public function myadsAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $oAnnonce = new Annonce(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aAnnonces = $oAnnonce->listLimitActif(["ALB_USERS_ID = $userId"], 'a.CREATE_DATE DESC');

        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);


        return new ViewModel(
            array(
                'menus'     => $aMenu,
                'annonces'  => $aAnnonces,
                'nbAds'     => count($aAnnonces),
                'loginForm' => $this->getLoginForm()
            )
        );
    }

    public function mypubAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();

        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);

        $aPub = $this->getModel('banner','Albadmin');
        //$nbrPub = $aPub->getCountPub($userId);
        $pubs = $aPub->getUserPub($userId,3);

        return new ViewModel(
            array(
                'menus'     => $aMenu,
                'pubs'      => $pubs,
                'nbPub'     => $aPub->getCountLimit(1, [['ALB_USERS_ID' => $userId]]),
                'loginForm' =>  $this->getLoginForm()
            )
        );
    }

    public function mycarAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();


        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);


        return new ViewModel(
            array(
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm()
            )
        );
    }

    public function myhotelAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();

        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);


        return new ViewModel(
            array(
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm()
            )
        );
    }

    public function myblogAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();

        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);


        return new ViewModel(
            array(
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm()
            )
        );
    }

    public function mystoreAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();

        $oMenu =  $this->getModel('menu','Albadmin');
        $aMenu = $oMenu->getMenus($userId);

        $oStore =  $this->getModel('boutique','Albadmin');
        $stores = $oStore->loadByUser($userId);

        return new ViewModel(
            array(
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm(),
                'stores' => $stores,
            )
        );
    }


    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function changemypassAction()
    {
        // if the user isn't logged in, we can't change password
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();

        $oMenu = new Menu(
            $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
            $this->getServiceLocator()
        );
        $aMenu = $oMenu->getMenus($userId);


        $form = $this->getChangePasswordForm();
        $prg = $this->prg(static::ROUTE_CHANGEPASSWD);

        $fm = $this->flashMessenger()->setNamespace('changemypass')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changePasswordForm' => $form,
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm(),
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm(),
            );
        }

        if (!$this->getUserService()->changePassword($form->getData())) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
                'menus' => $aMenu,
                'loginForm'    =>  $this->getLoginForm(),
            );
        }

        $this->flashMessenger()->setNamespace('changemypass')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEPASSWD);

    }


    /**
     * @return array|mixed|\Zend\Http\Response
     */
    public function loginJsonAction()
    {

        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return new JsonModel(array('ok' => 'ok'));
        }

        $request = $this->getRequest();
        $form    = $this->getLoginForm();

        if (!$request->isPost()) {
            return new JsonModel(
                array(
                    'errors' => 'ok',
                    'alert' => '/!\ Erreur lors de l\'envoi du formulaire !'
                )
            );
        }

        $postData = $request->getPost();
        $form->setData($postData);
        if (!$form->isValid()) {
            return new JsonModel(
                array(
                    'errors' => $form->getMessages()
                )
            );
        }

        // clear adapters
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
        if (!$auth->isValid()) {
            $adapter->resetAdapters();
            return new JsonModel(
                array(
                    'errors' => 'ok',
                    'alert' => $this->failedLoginMessage
                )
            );
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return new JsonModel(array('url' => $redirect));
        }

        $route = $this->getOptions()->getRedirectRoute(
            $this->zfcUserAuthentication()->getIdentity()->getAlbProfilesId()
        );

        if (is_callable($route)) {
            $route = $route($this->zfcUserAuthentication()->getIdentity());
        }

        return new JsonModel(array('route' => $route));
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $request = $this->getRequest();
        $form    = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (!$request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect'  => $redirect,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
            );
        }

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN).($redirect ? '?redirect='. rawurlencode($redirect) : ''));
        }

        // clear adapters
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {

    	$this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();

        $this->zfcUserAuthentication()->getAuthAdapter()->logoutAdapters();

        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        //$redirectionRoute = $this->getOptions()->getLogoutRedirectRoute();
        $redirectionRoute = "albook";
        return $this->redirect()->toRoute($redirectionRoute);
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction()
    {
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        $result = $adapter->prepareForAuthentication($this->getRequest());

        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }

        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

        if (!$auth->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            $adapter->resetAdapters();
            return $this->redirect()->toUrl(
                $this->url()->fromRoute(static::ROUTE_LOGIN) .
                ($redirect ? '?redirect='. rawurlencode($redirect) : '')
            );
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        $route = $this->getOptions()->getRedirectRoute(
            $this->zfcUserAuthentication()->getIdentity()->getAlbProfilesId()
        );

        if (is_callable($route)) {
            $route = $route($this->zfcUserAuthentication()->getIdentity());
        }
        return $this->redirect()->toRoute($route);
    }

    /**
     * Register new user
     */
    public function registerAction()
    {
        // if the user is logged in, we don't need to register
        if ($this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        // if registration is disabled
        if (!$this->getOptions()->getEnableRegistration()) {
            return array('enableRegistration' => false);
        }

        $request = $this->getRequest();
        $form = $this->getRegisterForm();

        $service = $this->getUserService();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute(static::ROUTE_REGISTER)
            . ($redirect ? '?redirect=' . rawurlencode($redirect) : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        $post = $prg;
        $user = $service->register($post);

        $redirect = isset($prg['redirect']) ? $prg['redirect'] : null;

        if (!$user) {
            return array(
                'registerForm' => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect' => $redirect,
            );
        }

        if ($service->getOptions()->getLoginAfterRegistration()) {
            $identityFields = $service->getOptions()->getAuthIdentityFields();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $post['password'];
            $request->setPost(new Parameters($post));
            return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
        }

        // TODO: Add the redirect parameter here...
        return $this->redirect()->toUrl($this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect='. rawurlencode($redirect) : ''));
    }

    /**
     * Change the users password
     */
    public function changepasswordAction()
    {
        // if the user isn't logged in, we can't change password
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $form = $this->getChangePasswordForm();
        $prg = $this->prg(static::ROUTE_CHANGEPASSWD);

        $fm = $this->flashMessenger()->setNamespace('change-password')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changePasswordForm' => $form,
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
            );
        }

        if (!$this->getUserService()->changePassword($form->getData())) {
            return array(
                'status' => false,
                'changePasswordForm' => $form,
            );
        }

        $this->flashMessenger()->setNamespace('change-password')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEPASSWD);
    }

    /**
     * Change the users information
     */
    public function changeinformationAction()
    {

        // if the user isn't logged in, we can't change password
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        
        $form = $this->getChangeInformationForm();

        $prg = $this->prg(static::ROUTE_CHANGEINFORMATION);

        $fm = $this->flashMessenger()->setNamespace('change-information')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changeInformationForm' => $form,
            );
        }die('toto');

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changeInformationForm' => $form,
            );
        }
        //var_dump($form);
        //die();
        if (!$this->getUserService()->changeInformation($form->getData())) {
            return array(
                'status' => false,
                'changeInformationForm' => $form,
            );
        }

        $this->flashMessenger()->setNamespace('change-information')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEINFORMATION);
    }
    /**
    * change users email
    */
    public function changeEmailAction()
    {
        // if the user isn't logged in, we can't change email
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $form = $this->getChangeEmailForm();
        $request = $this->getRequest();
        $request->getPost()->set('identity', $this->getUserService()->getAuthService()->getIdentity()->getEmail());

        $fm = $this->flashMessenger()->setNamespace('change-email')->getMessages();
        if (isset($fm[0])) {
            $status = $fm[0];
        } else {
            $status = null;
        }

        $prg = $this->prg(static::ROUTE_CHANGEEMAIL);
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'status' => $status,
                'changeEmailForm' => $form,
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'status' => false,
                'changeEmailForm' => $form,
            );
        }

        $change = $this->getUserService()->changeEmail($prg);

        if (!$change) {
            $this->flashMessenger()->setNamespace('change-email')->addMessage(false);
            return array(
                'status' => false,
                'changeEmailForm' => $form,
            );
        }

        $this->flashMessenger()->setNamespace('change-email')->addMessage(true);
        return $this->redirect()->toRoute(static::ROUTE_CHANGEEMAIL);
    }

    /**
     * Getters/setters for DI stuff
     */

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }

    public function getRegisterForm()
    {
        if (!$this->registerForm) {
            $this->setRegisterForm($this->getServiceLocator()->get('zfcuser_register_form'));
        }
        return $this->registerForm;
    }

    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
    }

    public function getLoginForm()
    {
        if (!$this->loginForm) {
            $this->setLoginForm($this->getServiceLocator()->get('zfcuser_login_form'));
        }
        return $this->loginForm;
    }

    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
        $fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
        if (isset($fm[0])) {
            $this->loginForm->setMessages(
                array('identity' => array($fm[0]))
            );
        }
        return $this;
    }

    public function getChangePasswordForm()
    {
        if (!$this->changePasswordForm) {
            $this->setChangePasswordForm($this->getServiceLocator()->get('zfcuser_change_password_form'));
        }
        return $this->changePasswordForm;
    }

    public function setChangePasswordForm(Form $changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;
        return $this;
    }

    public function getChangeInformationForm()
    {
        if (!$this->changeInformationForm) {
            $this->setChangeInformationForm($this->getServiceLocator()->get('zfcuser_change_information_form'));
        }
        return $this->changeInformationForm;
    }

    public function setChangeInformationForm(Form $changeInformationForm)
    {
        $this->changeInformationForm = $changeInformationForm;
        return $this;
    }
    /**
     * set options
     *
     * @param UserControllerOptionsInterface $options
     * @return UserController
     */
    public function setOptions(UserControllerOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof UserControllerOptionsInterface) {
            $this->setOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }
        return $this->options;
    }

    /**
     * Get changeEmailForm.
     *
     * @return changeEmailForm.
     */
    public function getChangeEmailForm()
    {
        if (!$this->changeEmailForm) {
            $this->setChangeEmailForm($this->getServiceLocator()->get('zfcuser_change_email_form'));
        }
        return $this->changeEmailForm;
    }

    /**
     * Set changeEmailForm.
     *
     * @param changeEmailForm the value to set.
     */
    public function setChangeEmailForm($changeEmailForm)
    {
        $this->changeEmailForm = $changeEmailForm;
        return $this;
    }
}
