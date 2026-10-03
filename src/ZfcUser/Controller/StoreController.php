<?php
/**
 * Created by PhpStorm.
 * User: makour
 * Date: 02/07/16
 * Time: 16:54
 */

namespace ZfcUser\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Albook\Controller\AbstractController;
use Albook\Library\Upload;
use Albook\Library\Enigma;
use Albook\Library\Utils;

use Annonce\Model\Annonce;
use Annonce\Model\AnnonceFile;
use Albadmin\Model\Menu;
use Albadmin\Model\Banner;
use Albadmin\Model\Boutique;

use Boutique\Form\ProduitForm;
use Boutique\Form\ProduitCategoryForm;
use Boutique\Model\Bcategory;
use Boutique\Model\Produit;

class StoreController extends AbstractController
{
    private $aRedim  = array(
        'NORMAL'     => array('width' => 600, 'height' => 400),
        'THUMBNAIL'  => array('width' => 100, 'height' => 100),
    );

    protected $_watermark1 = '/img/watermark1.png';
    protected $_watermark2 = '/img/watermark.png';


    public function produitsJsonAction()
    {
        $oContainer = $this->getContainer();
        $idboutique = $oContainer->offsetGet('IDBOUTIQUE');
        $oStore = $this->getModel('boutique','Albadmin');
        $produits = $oStore->listProduit($idboutique);
       // $produitsList = array('0'=>array('ID'=>'1','CAT'=>'Cat','PRO'=>'test','PR'=>'12.45'));

        foreach ($produits as &$produit) {
            $produit['PRIX-HTML'] = $produit['PRIX'].' '.$produit['DEVISE'];
           // $produit['DESCIPTION-HTML'] = $this->TextTruncate($produit['DESCRIPTION']);
        }
        unset($produit);
        $headerKeys = array(
            'ID',
            'BCATEGORY',
            'TITLE',
            'DESCRIPTION',
            'PRIX-HTML'
        );
        $headerValues = array(
            'Id',
            'Catégorié',
            'Produit',
            'Déscription',
            'Prix'
        );
        $result = new JsonModel(array(
            'headerKeys'   => json_encode($headerKeys),
            'headerValues' => json_encode($headerValues),
            'dataList'     => json_encode($produits),
            'success'      =>true,
        ));
        return $result;
    }

    public function produitsAction()
    {
        $oContainer = $this->getContainer();

        $translator = $this->getServiceLocator()->get('translator');
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $idboutique =  $this->params()->fromPost('idboutique');
        $oContainer->offsetSet('IDBOUTIQUE', $idboutique);
        $oMenu = $this->getModel('menu','Albadmin');
        $aMenu = $oMenu->getMenus($userId);
        $oStore = $this->getModel('boutique','Albadmin');

        $oCategory = $this->getModel('bcategory','Boutique');

        //$aCategories = $this->getCatgories($idboutique);
        $aResults = $oCategory->listAll($idboutique);
        $aCategories = array('' => 'Sélectionnez une catégorie ... ');
        foreach($aResults as $v) {
            $aCategories[$v['ID']] = $v['CATEGORY'];
        }
        //$aCategories[0] = 'Catégirie ... ';
        $data = [
            'aCategories' => $aCategories,
        ];
        $form = new produitForm('produit-frm',$data,$translator);
        $store = $oStore->load($idboutique);
        $oContainer->offsetSet('BOUTIQUE', $store);
        return new ViewModel(
            array(
                'menus'     => $aMenu,
                'loginForm' =>  $this->getLoginForm(),
                'form'      => $form,
                'store'     => $store,
            )
        );
    }

    public function addProduitJsonAction()
    {

        $oContainer = $this->getContainer();
        $translator = $this->getServiceLocator()->get('translator');

        $idboutique = $oContainer->offsetGet('IDBOUTIQUE');
        $boutique = $oContainer->offsetGet('BOUTIQUE');
        $oBCat = $this->getModel('bcategory','Boutique');
        $oCat = $this->getModel('category','Annonce');
        //$aCategories = $this->getCatgories($idboutique);
        $aResults = $oBCat->listAll($idboutique);
        $aCategories = array('' => 'Sélectionnez une catégorie ... ');
        foreach($aResults as $v) {
            $aCategories[$v['ID']] = $v['CATEGORY'];
        }
        //$aCategories[0] = 'Catégirie ... ';
        $datas = [
            'aCategories' => $aCategories,
        ];
        $form = new produitForm('produit-frm',$datas,$translator);
        $request = $this->getRequest();
       /***** debut if *****/
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($data);
            if($form->isValid()){

                $cat = $oBCat->produitCategory($data['ALS_BOUTIQUES_CATS_ID']);
                /** Traitement du chargement de l'image */
                // Save Annonce Images
                $oUpload = new Upload(
                    array(
                        'model' => $this->getModel('file', 'Albook'),
                        'module'      => 'BOUTIQUE',
                        'category'    => $boutique['ALN_CATEGORIES_ID'], // VARIOUS
                        'subCategory' => $cat['ALN_CATEGORIES_ID']
                    )
                );

                    $oUpload->setFormElement("IMAGE");
                    $oUpload->setName('El3ers ' . $data['TITLE']);
                    $fileData = $oUpload->save($this->aRedim['NORMAL'], true, false);
                    // var_dump($oUpload->getErrors());
                    $fileId = $oUpload->getId();
                    $imgPath = APPLICATION_PATH . '/images/annonces/' . $boutique['ALN_CATEGORIES_ID'] . '/' . $cat['ALN_CATEGORIES_ID'] . '/' . $fileData['URL'] . '.' . $fileData['EXTENSION'];
                    // var_dump($imgPath);
                    Utils::watermarkImage($imgPath, APPLICATION_PATH . $this->_watermark1, $imgPath, false);
                    Utils::watermarkImage($imgPath, APPLICATION_PATH . $this->_watermark2, $imgPath);

                /** fin image */
                /** Save annonce  */
                // Save Annonce
                $oAnnonce = $this->getModel('annonce','Annonce');
                $data['TITLE'] = str_replace("<script", "#######", $data['TITLE']);
                $data['DESCRIPTION'] = str_replace("<script", "#######", $data['DESCRIPTION']);
                $data['ALB_USERS_ID'] = $boutique['ALB_USERS_ID'];
                $data['CREATE_DATE']  = date('Y-m-d H:i:s');
                $data['ALN_CATEGORIES_ID'] = $boutique['ALN_CATEGORIES_ID'];
                $data['ALN_TYPES_ID'] = $cat['ALN_CATEGORIES_ID'];
                if (isset($data['PRIX_UNIT']) && $data['PRIX_UNIT'] == 1) {
                    $data['DEVISE'] = 'DZD';
                } else {
                    $data['DEVISE'] = 'EURO';
                }
                $oStore = $this->getModel('boutique','Albadmin');
                $adresse = $oStore->getAdresse($boutique['ID']);

                $data['ALB_COMMUNES_ID'] = $adresse['ALB_COMMUNES_ID'];
                $data['ALB_WILAYAS_ID'] = $adresse['ALB_WILAYAS_ID'];
                $data['ADRESSE'] = $adresse['ADRESSE'];
                $data['ACTION'] = 'vente';
                $data['ACTIVE'] = 1;
                $data['SOCIAL_NETWORKS']='';
                $data2['ALS_BOUTIQUES_CATS_ID'] = $data['ALS_BOUTIQUES_CATS_ID'];
                unset($data['BOUTIQUE']);
                unset($data['PRIX_UNIT']);
                unset($data['ALS_BOUTIQUES_CATS_ID']);
                unset($data['IMAGE']);
                unset($data['submit']);


                if (!$oAnnonce->insert($data))
                {
                    $this->_errors = $oAnnonce->getErrors();
                    return false;
                }

                // Save Details
                $annonceId = $oAnnonce->getId();
                $data['ALN_ANNONCES_ID'] = $annonceId;
                $name = $oCat->getCName($boutique['ALN_CATEGORIES_ID']);
                $model = $this->_modelName($name['NAME']);
                $_model = $this->getModel($model,'Annonce');
                $_model->insert($data);

                // Save produit
                $data2['ALN_ANNONCES_ID'] = $annonceId;
                $data2['ALS_BOUTIQUES_ID'] = $boutique['ID'];
                $produitModel = $this->getModel('produit','Boutique');
                $produitModel->insert($data2);

                // Save files
                $annonceFileModel = $this->getModel('annonceFile','Annonce');
                $annonceFileModel->setAlbFilesId($fileId);
                $annonceFileModel->setAlnAnnoncesId($annonceId);
                $annonceFileModel->insert();
                /** fin annonce */
                $result = new JsonModel(array(
                    'success' => true,
                ));
                return $result;

            } else {
                // Form not valid
                $result = new JsonModel(array(
                    'errors' => $errors
                ));
                return $result;
            }

        }
        /***** fin if *****/
    }

    public function categoriesJsonAction()
    {
        $oContainer = $this->getContainer();
        $oStore = $this->getModel('boutique','Albadmin');
        $idboutique = $oContainer->offsetGet('IDBOUTIQUE');
        $store = $oStore->load($idboutique);
        $oBCat = $this->getModel('bcategory','Boutique');

        $oCategory = $this->getModel('category','Annonce');

        $oStore = $this->getModel('boutique','Albadmin');
        //$produitsList = $oStore->listAll();
        $categories = $oBCat->liste($idboutique);

        foreach($categories as &$categorie){
            $cat = $oCategory->getCValue($categorie['ALN_CATEGORIES_ID']);
            $categorie['TYPE']= $cat['VALUE'];
        }
        unset($categoire);
        $headerKeys = array(
            'ID',
            'CATEGORY',
            'TYPE',
        );
        $headerValues = array(
            'Id',
            'Catégorié',
            'Type',
        );
        $result = new JsonModel(array(
            'headerKeys'   => json_encode($headerKeys),
            'headerValues' => json_encode($headerValues),
            'dataList'     => json_encode($categories),
            'success'      =>true,
        ));
        return $result;
    }

    public function categoriesAction()
    {
        $oContainer = $this->getContainer();

        $translator = $this->getServiceLocator()->get('translator');
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $oMenu = $this->getModel('menu','Albadmin');
        $aMenu = $oMenu->getMenus($userId);


        $idboutique =  $this->params()->fromPost('idboutique');
        $oContainer->offsetSet('IDBOUTIQUE', $idboutique);
        $oStore = $this->getModel('boutique','Albadmin');
        $store = $oStore->load($idboutique);

        $data = array('ALN_CATEGORIES_ID' => $store['ALN_CATEGORIES_ID']);
       // var_dump($data); die();
        $form = new ProduitCategoryForm('category-frm',$data,$translator);

        return new ViewModel(
            array(
                'menus'     => $aMenu,
                'loginForm' =>  $this->getLoginForm(),
                'form'      => $form,
                'store'     => $store,
            )
        );
    }

    public function addCategoryJsonAction()
    {

        $oContainer = $this->getContainer();
        $translator = $this->getServiceLocator()->get('translator');
        $oStore = $this->getModel('boutique','Albadmin');

        $idboutique = $oContainer->offsetGet('IDBOUTIQUE');
        $store = $oStore->load($idboutique);
        $oCategory = $this->getModel('bcategory','Boutique');


        $data = array('ALN_CATEGORIES_ID' => $store['ALN_CATEGORIES_ID']);

        $form = new ProduitCategoryForm('produit-frm',$data,$translator);
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$form->setInputFilter(new AddUserFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
               unset($data['submit']);
                $oCategory->insert($data);


                $result = new JsonModel(array(
                    'success' => true,
                ));
                return $result;
            } else {
                $errors = $form->getMessages();
            }
        }

        $result = new JsonModel(array(
            'errors' => $errors
        ));
        return $result;
        //return $this->redirect()->toRoute(static::ROUTE_PRODUITS);
    }

    private function _saveProduit($boutique,$form, $post)
    {
        $oBCat = $this->getModel('bcategory','boutique');
        $oCat = $this->getModel('category','Annonce');

        $cat = $oBCat->produitCategory($post['ALS_BOUTIQUES_CATS_ID']);

        $form->setData($post);
        if($form->isValid()){
            $data = $form->getData();

        } else {
            // Form not valid
            $this->_errors = $form->getMessages();
            return false;
        }

        // Save Annonce Images
        $oUpload = new Upload(
            array(
                'model' => $this->getModel('file', 'Albook'),
                'module'      => 'BOUTIQUE',
                'category'    => $boutique['ALN_CATEGORIES_ID'], // VARIOUS
                'subCategory' => $cat['ALN_CATEGORIES_ID']
            )
        );
        if (array_key_exists("IMAGE", $data)) {
            $oUpload->setFormElement("IMAGE");
            $oUpload->setName('El3ers ' . $data['TITLE']);
            $fileData = $oUpload->save($this->aRedim['NORMAL'], true, false);
            var_dump($oUpload->getErrors());
            $fileId = $oUpload->getId();
            $imgPath = APPLICATION_PATH . '/images/annonces/' . $boutique['ALN_CATEGORIES_ID'] . '/' . $cat['ALN_CATEGORIES_ID'] . '/' . $fileData['URL'] . '.' . $fileData['EXTENSION'];

            Utils::watermarkImage($imgPath, APPLICATION_PATH . $this->_watermark1, $imgPath, false);
            Utils::watermarkImage($imgPath, APPLICATION_PATH . $this->_watermark2, $imgPath);
        }


        // Save Annonce
        $oAnnonce = $this->getModel('annonce','Annonce');
        $data['TITLE'] = str_replace("<script", "#######", $data['TITLE']);
        $data['DESCRIPTION'] = str_replace("<script", "#######", $data['DESCRIPTION']);
        $data['ALB_USERS_ID'] = $boutique['ALB_USERS_ID'];
        $data['CREATE_DATE']  = date('Y-m-d H:i:s');
        $data['ALN_CATEGORIES_ID'] = $boutique['ALN_CATEGORIES_ID'];
        $data['ALN_TYPES_ID'] = $cat['ALN_CATEGORIES_ID'];
        if (isset($data['PRIX_UNIT']) && $data['PRIX_UNIT'] == 1) {
            $data['DEVISE'] = 'DZD';
        } else {
            $data['DEVISE'] = 'EURO';
        }
        $oStore = $this->getModel('boutique','Albadmin');
        $adresse = $oStore->getAdresse($boutique['ID']);

        $data['ALB_COMMUNES_ID'] = $adresse['ALB_COMMUNES_ID'];
        $data['ALB_WILAYAS_ID'] = $adresse['ALB_WILAYAS_ID'];
        $data['ADRESSE'] = $adresse['ADRESSE'];
        $data['ACTION'] = 'vente';
        $data['ACTIVE'] = 1;
        $data['SOCIAL_NETWORKS']='';
        $data2['ALS_BOUTIQUES_CATS_ID'] = $data['ALS_BOUTIQUES_CATS_ID'];
        unset($data['BOUTIQUE']);
        unset($data['PRIX_UNIT']);
        unset($data['ALS_BOUTIQUES_CATS_ID']);
        unset($data['IMAGE']);
        unset($data['submit']);


        if (!$oAnnonce->insert($data))
        {
            $this->_errors = $oAnnonce->getErrors();
            return false;
        }

        // Save Details
        $annonceId = $oAnnonce->getId();
        $data['ALN_ANNONCES_ID'] = $annonceId;
        $name = $oCat->getCName($boutique['ALN_CATEGORIES_ID']);
        $model = $this->_modelName($name['NAME']);
        $_model = $this->getModel($model,'Annonce');
        $_model->insert($data);

        $data2['ALN_ANNONCES_ID'] = $annonceId;
        $data2['ALS_BOUTIQUES_ID'] = $boutique['ID'];
       // $data2['ALS_BOUTIQUES_CATS_ID'] = $data['ALS_BOUTIQUES_CATS_ID'];

        $produitModel = $this->getModel('produit','Boutique');
        $produitModel->insert($data2);

        // Save files
        $annonceFileModel = $this->getModel('annonceFile','Annonce');
        $annonceFileModel->setAlbFilesId($fileId);
        $annonceFileModel->setAlnAnnoncesId($annonceId);
        $annonceFileModel->insert();
        return true;
       // return $this->_sendConfirmationNotif($oAnnonce->getId(), $userId);
    }

    private function _modelName($str)
    {

        $str = strtolower($str);
        $str = substr($str,4);
        $exs = explode('_',$str);
        $strs = '';
        for($i=0; $i < count($exs); $i++){
            $strs .= ucfirst($exs[$i]);
        }
        $str = lcfirst($strs);

        return($str);
    }
}
