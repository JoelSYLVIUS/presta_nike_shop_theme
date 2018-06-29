<?php
/**
 * User: Joël
 * Date: 02/05/2018
 * Time: 08:22
 */


if (!defined('_PS_VERSION_'))
{
    exit;
}


class jonitrojModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
    parent::initContent();
    $this->initList();
    $this->setTemplate('module:jonitro/views/templates/front/list.tpl');
    }

    public $product;

    public function initList(){

        // création de l'objet Product
        $id_product=Tools::getValue('id_product');
        $this->product =new Product((int)$id_product, false, $this->context->cookie->id_lang);




        // Récupération du nombre de commentaires
        $nb_comments = Db::getInstance()->getValue('SELECT COUNT(`id_product`) FROM `'._DB_PREFIX_.'jonitro_comment` WHERE `id_product` = '.(int)$this->product->id);


        //PAGINATION

        $nb_per_page = 10;
        $nb_pages = ceil($nb_comments / $nb_per_page);
        $page = 1;
        if (Tools::getValue('page') != '') {
            $page = (int)Tools::getValue('page');
            }

        $limit_start = ($page - 1) * $nb_per_page;
        $limit_end = $nb_per_page;
        $comments = Db::getInstance()->executeS('
        SELECT * FROM `'._DB_PREFIX_.'jonitro_comment` WHERE `id_product` = '.(int)$this->product->id.'
        ORDER BY `date_add` DESC LIMIT '.(int)$limit_start.','.(int)$limit_end);


        // envoyer les commentaires et le produit au smarty
        $this->context->smarty->assign('product',$this->product);
        $this->context->smarty->assign('comments',$comments);

        // envoyer la page courante et le nombre de pages au smarty
        $this->context->smarty->assign('pageEnCours',$page);
        $this->context->smarty->assign('nb_pages',$nb_pages);


    }




}
