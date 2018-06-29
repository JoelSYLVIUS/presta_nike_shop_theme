<?php
/**
* Created by PhpStorm.
* User: Joël
* Date: 02/05/2018
* Time: 08:22
*/


if (!defined('_PS_VERSION_'))
{
   exit;
}


class Jonitro extends Module
{
   public function __construct()
   {
       $this->name = 'jonitro';
       $this->tab = 'front_office_features';
       $this->version = '1.0.0';
       $this->author = 'Joël Sylvius';
       $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
       $this->bootstrap = true;
       $this->title = "Commentaires";


       parent::__construct();


       $this->displayName = $this->l('Jonitro');
       $this->description = $this->l('Module Jonitro is the best!');


       $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');



   }


      public function getContent(){
              return $this->renderForm();
              //return $this->fetch(_PS_MODULE_DIR_."jonitro/views/templates/hook/getContent.tpl");
      }

   public function renderForm(){
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('My Module configuration'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                            array(
                            'type' => 'switch',
                            'label' => $this->l('Enable grades:'),
                            'name' => 'enable_grades',
                            'desc' => $this->l('Enable grades on products.'),
                            'values' => array(
                                array(
                                'id' => 'enable_grades_1',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                                ),
                                array(
                                'id' => 'enable_grades_0',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                        array(
                        'type' => 'switch',
                        'label' => $this->l('Enable comments:'),
                        'name' => 'enable_comments',
                        'desc' => $this->l('Enable comments on products.'),
                        'values' => array(
                                array(
                                'id' => 'enable_comments_1',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                                ),
                                array(
                                'id' => 'enable_comments_0',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                                )
                            ),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        )
                ),
            );

        $helper = new HelperForm();
        $helper->table = 'monmodulecomments';
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'submit_monmodule_form';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules',
        false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'enable_grades' => Tools::getValue('enable_grades',Configuration::get('MONMODULE_GRADES')),
                'enable_comments' => Tools::getValue('enable_comments',Configuration::get('MONMODULE_COMMENTS')),
            ),
            'languages' => $this->context->controller->getLanguages()
            );
        return $helper->generateForm(array($fields_form));

    }


public function install()
{
 	parent::install();
        $this->createTable();

         //Definir les clés de config par défaut
         Configuration::updateValue('JONIT_COMMENTS', 1);
         Configuration::updateValue('JONIT_GRADES', 1);

 	$this->registerHook('displayReassurance');
        $this->registerHook('actionFrontControllerSetMedia');

        $this->registerHook('productComments');
 	return true;
 }

     protected function createTable(){
         $requete= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."jonitro_comment` (
         `id_jonitro_comment` int(11) NOT NULL AUTO_INCREMENT,
         `id_product` int(11) NOT NULL,
         `grade` tinyint(1) NOT NULL,
         `comment` text NOT NULL,
         `date_add` datetime NOT NULL,
         PRIMARY KEY (`id_jonitro_comment`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
         Db::getInstance()->execute($requete);
     }


    public function hookProductComments ($params){

         $this->processCustomerForm();
         $this->assignFrontValues();

        $enable_comments=Configuration::get('JONIT_COMMENTS');
        $enable_grades=Configuration::get('JONIT_GRADES');
        $this->context->smarty->assign('enable_comments', $enable_comments);
        $this->context->smarty->assign('enable_grades', $enable_grades);
        $this->context->smarty->assign('title', $this->title);

        $id_product = Tools::getValue('id_product');
        $this->context->smarty->assign('params', array('id_product' => $id_product));

        return $this->fetch(_PS_MODULE_DIR_ ."jonitro/views/templates/hook/hookDisplayReassurance.tpl");
    }


     public function processCustomerForm(){
         if(Tools::isSubmit('submit_form_customer')){
             $grade = Tools::getValue('grade');
             $comment = Tools::getValue('comment');
             $id_product = Tools::getValue('id_product');

             $data = array(
                 'grade' => (int)$grade,
                 'comment' => pSQL($comment),
                 'id_product' => (int)$id_product,
                 'date_add' => date('Y-m-d H:i:s')
             );
             Db::getInstance()->insert("jonitro_comment", $data); // Table name without prefix

         }
     }

    public function assignFrontValues(){
         $id_product = Tools::getValue('id_product');

         $requete = "SELECT * FROM `" ._DB_PREFIX_. "jonitro_comment`
         WHERE `id_product` = " . (int)$id_product . " ORDER BY date_add DESC LIMIT 0,3";

         $comments = Db::getInstance()->executeS($requete);

         $this->context->smarty->assign('comments', $comments);
    }


    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        // Exécuter toutes les commandes SQL de désinstallation
        $requete= "DROP TABLE `"._DB_PREFIX_."jonitro_comment`";
        Db::getInstance()->execute($requete);
        // Effacer les valeurs de configuration
        Configuration::deleteByName('JONIT_GRADES');
        Configuration::deleteByName('JONIT_COMMENTS');
        // Tout s’est bien passé !
        return true;
    }
}
