<?php

if (!defined('_TB_VERSION_')) {
    exit;
}

/**
 * Extra Product Functionality
 */
class ExtraProductFunctionality extends Module
{
     /* @var boolean error */
     protected $hooksList = [];

    public function __construct()
    {
        $this->name = 'extraproductfunctionality';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Michael Rouse';

        $this->tb_min_version = '1.0.0';
        $this->tb_versions_compliancy = '> 1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => '1.6.99.99'];

        $this->need_instance = 0;
        $this->table_name = 'extraproductfunctionality';
        $this->bootstrap = true;

        $this->hookList = [
            'actionModifyProductWithExtraFunctionality',
            'displayAdminProductsExtra',
            'actionProductUpdate'
        ];

        parent::__construct();

        $this->displayName = $this->l('Extra Product Functionality');
        $this->description = $this->l('Add extra functionality to your products');
    }
}