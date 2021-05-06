<?php

if (!defined('_TB_VERSION_')) {
    exit;
}

/**
 * Extra Product Features
 */
class ExtraProductFeatures extends Module
{
     /* @var boolean error */
     protected $hooksList = [];

    public function __construct()
    {
        $this->name = 'extraproductfeatures';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Michael Rouse';

        $this->tb_min_version = '1.0.0';
        $this->tb_versions_compliancy = '> 1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => '1.6.99.99'];

        $this->need_instance = 0;
        $this->table_name = 'extraproductfeatures';
        $this->bootstrap = true;

        $this->hookList = [
            'actionModifyProductIfAService',
            'displayAdminProductsExtra',
            'actionProductUpdate'
        ];

        parent::__construct();

        $this->displayName = $this->l('Extra Product Features');
        $this->description = $this->l('Add extra features to your products');
    }
}