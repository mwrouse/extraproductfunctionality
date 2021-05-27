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


    private $valuesList = [];



    public function __construct()
    {
        $this->name = 'extraproductfunctionality';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Michael Rouse';

        $this->tb_min_version = '1.0.0';
        $this->tb_versions_compliancy = '> 1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => '1.6.99.99'];

        $this->need_instance = 0;
        $this->table_name = 'extraproductfunctionality';
        $this->bootstrap = true;

        $this->hooksList = [
            'actionModifyProductForExtraFunctionality',
            'displayAdminProductsExtra',
            'actionProductUpdate'
        ];

        parent::__construct();

        $this->displayName = $this->l('Extra Product Functionality');
        $this->description = $this->l('Add extra functionality to your products');

        $this->submitName = 'submitAddproduct';

        $this->createListOfFields();
    }

    /**
     * Sets all the values that this module supports
     */
    private function createListOfFields()
    {
        $this->valuesList = [
            [
                'key' => 'is_service',
                'default' => 0,
                'lang' => false, // Default is true (used when getting the input back from saving the product)
                'input' => [ // Input name is automatically set. DO NOT SPECIFY IT IN THIS
                    'type' => 'switch',
                    'label' => $this->l('Is a Service'),
                    'desc' => '',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'is_service_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'is_service_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ],
            [
                'key' => 'coming_soon',
                'default' => 0,
                'lang' => false,
                'also_sets' => [ // also_sets are additional things to set on the product.
                    1 /* when value is this */ => [
                        /* set all of these */
                        'show_price' => 0,
                        'available_for_order' => 0,
                        'new' => 0,
                    ]
                ],
                'input' => [
                    'type' => 'switch',
                    'label' => $this->l('Coming Soon?'),
                    'desc' => '',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'coming_soon_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'coming_soon_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ],
            [
                'key' => 'new',
                'setIf' => 1, // Will only write the value to $product if value matches this (override)
                'default' => 0,
                'lang' => false,
                'input' => [
                    'type' => 'switch',
                    'label' => $this->l('Force New (overrides $product->new if set to Yes)'),
                    'desc' => '',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'new_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'new_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ],
            [
                'key' => 'discontinued',
                'default' => 0,
                'lang' => false,
                'also_sets' => [ // also_sets are additional things to set on the product.
                    1 /* when value is this */ => [
                        /* set all of these */
                        'show_price' => 0,
                        'available_for_order' => 0,
                        'new' => 0,
                    ]
                ],
                'input' => [
                    'type' => 'switch',
                    'label' => $this->l('Discontinued'),
                    'desc' => '',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'discontinued_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'discontinued_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ],
            [
                'key' => 'hide_comments',
                'default' => 0,
                'lang' => false,
                'also_sets' => [],
                'input' => [
                    'type' => 'switch',
                    'label' => $this->l('Hide Comments/Reviews'),
                    'desc' => '',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'hide_comments_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'hide_comments_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ],
            [
                'key' => 'hide_all_images',
                'default' => 0,
                'lang' => false,
                'also_sets' => [],
                'input' => [
                    'type' => 'switch',
                    'label' => $this->l('Hide All Images (Use for colors/textures)'),
                    'desc' => 'Useful for hiding images when each image is a different combination (color/texture)',
                    'lang' => true,
                    'values' => [
                        [
                            'id' => 'hide_all_images_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'hide_all_images_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ]
                    ]
                ]
            ]
        ];

        // Set automatic values
        foreach ($this->valuesList as $i => $value)
        {
            if (array_key_exists('input', $value))
            {
                if (!array_key_exists('name', $value['input']))
                {
                    $this->valuesList[$i]['input']['name'] = $this->toInputName($value['key']);
                }

                if (array_key_exists('lang', $value['input']) && !array_key_exists('lang', $value))
                {
                    $this->valuesList[$i]['lang'] = $value['input']['lang'];
                }

                $this->valuesList[$i]['input']['desc'] = '$product->'.$value['key'];
            }

            if (!array_key_exists('also_sets', $value))
            {
                $this->valuesList[$i]['also_sets'] = [];
            }
        }
    }


    /***************************
     *          Hooks          *
     ***************************/

    /**
     * Hook to modify the product class
     */
    public function hookActionModifyProductForExtraFunctionality($params)
    {
        $product = $params['product'];

        if (!isset($product))
            return;

        $params['product'] = $product = $this->modifyProduct($product);


        $useCapture = (array_key_exists('capture', $params)) ? $params['capture'] : false;

        if ($useCapture) {
            return serialize($product);
        }
    }


    /**
     * Adds a tab to the product page in back office
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $productId = Tools::getValue('id_product');

        $inputs = [];

        foreach ($this->valuesList as $value)
        {
            if (array_key_exists('input', $value)) {
                array_push($inputs, $value['input']);
            }
        }

        $formFields = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Extra Product Functionality'),
                    'icon'  => 'icon-cogs',
                ],
                'input'  => $inputs,
                'buttons' => [
                    'save-and-stay' => [
                        'title' => $this->l('Save and Stay'),
                        'name' => $this->submitName.'AndStay',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-save'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = $this->submitName;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminProducts', false);
        $helper->token = Tools::getAdminTokenLite('AdminProducts');
        $helper->show_cancel_button = true;

        $values = $this->getExtraFunctionalityForProduct($productId);
        foreach ($values as $key => $value)
        {
            $values[$this->toInputName($key)] = $value;
        }

        $helper->tpl_vars = [
            'fields_value' => $values,
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        $form = $helper->generateForm([$formFields]);

        return strip_tags($form, $this->allowedHTMLTags());
    }


    /**
     * When the product page is saved
     */
    public function hookActionProductUpdate()
    {
        if (Tools::isSubmit($this->submitName) || Tools::isSubmit($this->submitName.'AndStay'))
        {
            $productId = Tools::getValue('id_product');

            $inputs = [];
            foreach ($this->valuesList as $value)
            {
                $inputs[$value['key']] = Tools::getValue($this->toInputName($value['key'], $value['lang']));
            }

            $this->setDefaultValues($inputs); // In-case of update

            if (isset($productId))
            {
                $doesExist = Db::getInstance()->getValue('SELECT * FROM '._DB_PREFIX_.$this->table_name.' WHERE (`id_product`='.$productId.')');

                $data = serialize($inputs);

                if ($doesExist)
                {
                    Db::getInstance()->update($this->table_name,
                        ['data' => $data],
                        'id_product='.$productId
                    );
                }
                else {
                    Db::getInstance()->insert($this->table_name, [
                        'id_product' => $productId,
                        'data' => $data
                    ]);
                }
            }
        }
    }




    /**
     * Returns all extra functionality for a product
     */
    public function getExtraFunctionalityForProduct($productId)
    {
        try {
            $result = Db::getInstance()->getValue('SELECT `data` FROM `'._DB_PREFIX_.$this->table_name.'` WHERE `id_product`='.$productId.'');
            if (!isset($result) || !$result || empty($result)) {
                return $this->setDefaultValues([]);
            }

            $final = unserialize($result);

            if (!is_array($final))
                return $this->setDefaultValues([]);

            return $this->setDefaultValues($final);
        }
        catch (Exception $e) {
            Logger::addLog("ExtraProductFunctionality getExtraFunctionalityForProduct Exception: {$e->getMessage()}");
            return $this->setDefaultValues([]);
        }
    }



    /**
     * Set default values so if this is ever upgraded it will be easy
     */
    public function setDefaultValues($existing)
    {
        if (!is_array($existing))
            $existing = [];

        foreach ($this->valuesList as $value)
        {
            if (!array_key_exists($value['key'], $existing))
                $existing[$value['key']] = $value['default'];
        }

        return $existing;
    }


    /**
     * Modifies the product class with the extra functionality
     */
    public function modifyProduct($product)
    {
        $functionality = $this->getExtraFunctionalityForProduct($this->get($product, 'id', 'id_product'));

        foreach ($functionality as $key => $value)
        {
            $cfg = $this->getConfigForValue($key);

            $canSet = true;

            if (!is_null($cfg)) {
                if (array_key_exists('setIf', $cfg)) {
                    $canSet = ($value == $cfg['setIf']);
                }
            }

            if ($canSet) {
                $product = $this->set($product, $key, $value);

                if (array_key_exists('also_sets', $cfg))
                {
                    foreach ($cfg['also_sets'] as $valueToCheck => $thingsToSet)
                    {
                        if ($value == $valueToCheck) {
                            foreach ($thingsToSet as $keyToSet => $valueToSet) {
                                $product = $this->set($product, $keyToSet, $valueToSet);
                            }
                        }
                    }
                }
            }
        }

        return $product;
    }

    /**
     * Returns the config for a value
     */
    public function getConfigForValue($key)
    {
        foreach ($this->valuesList as $value) {
            if ($value['key'] == $key)
                return $value;
        }

        return null;
    }


    /**
     * Generate a unique input name to not clash with anything else
     */
    private function toInputName($key, $useLanguage=false)
    {
        $prefix = 'apf_';
        $suffix = ($useLanguage) ? '_' . $lang['id_lang'] : '';

        if (substr($key, 0, strlen($prefix)) == $prefix)
            return $key.$suffix;

        return $prefix.$key.$suffix;
    }



    private function get($product, $key, $arrayKey = null)
    {
        if (is_null($arrayKey))
            $arrayKey = $key;

        if (is_object($product))
            return $product->{$key};

        return $product[$arrayKey];
    }

    private function set($product, $key, $value)
    {
        if (is_object($product))
        {
            $product->{$key} = $value;
            return $product;
        }
        else {
            $product[$key] = $value;
        }
        return $product;
    }



    private function allowedHTMLTags()
    {
        /**
         * Pretty much everything except <form>
         */
        return  [
            // basic
            '!DOCTYPE', 'html', 'title', 'body',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'p', 'br', 'hr',
            // formatting
            'acronym', 'abbr', 'address', 'b', 'bdi', 'bdo', 'big',
            'blockquote', 'center', 'cite', 'code', 'del', 'dfn', 'em',
            'font', 'i', 'ins', 'kbd', 'mark', 'meter', 'pre', 'progress',
            'q', 'rp', 'rt', 'ruby', 's', 'samp', 'small', 'strike', 'strong',
            'sub', 'sup', 'time', 'tt', 'u', 'var', 'wbr',
            // forms and input
            'input', 'textarea', 'button', 'select', 'optgroup', 'option',
            'label', 'fieldset', 'legend', 'datalist', 'keygen', 'output',
            // frames
            'frame', 'frameset', 'noframes', 'iframe',
            // images
            'img', 'map', 'area', 'canvas', 'figcaption', 'figure',
            // audio and video
            'audio', 'source', 'track', 'video',
            // links
            'a', 'link', 'nav',
            // lists
            'ul', 'ol', 'li', 'dir', 'dl', 'dt', 'dd', 'menu', 'menuitem',
            // tables
            'table', 'caption', 'th', 'tr', 'td', 'thead', 'tbody', 'tfoot', 'col', 'colgroup',
            // styles and semantics
            'style', 'div', 'span', 'header', 'footer', 'main', 'section', 'article',
            'aside', 'details', 'dialog', 'summary',
            // meta info
            'head', 'meta', 'base', 'basefont',
            // programming
            'script', 'noscript', 'applet', 'embed', 'object', 'param'
        ];
    }


    /***************************
     *    Install/Uninstall    *
     ***************************/

    public function install()
    {
        if ( ! parent::install()
            || ! $this->_createDatabases()
        ) {
            return false;
        }

        foreach ($this->hooksList as $hook) {
            if ( ! $this->registerHook($hook)) {
                error_log('Could not register hook ' . $hook);
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        if ( ! parent::uninstall()
            || ! $this->_eraseDatabases()
        ) {
            return false;
        }

        return true;
    }


    /**
     * Create Database Tables
     */
    private function _createDatabases()
    {
        $sql = 'CREATE TABLE  `'._DB_PREFIX_.$this->table_name.'` (
                `id_product` INT( 12 ) AUTO_INCREMENT,
                `data` LONGTEXT NOT NULL,
                PRIMARY KEY (  `id_product` )
                ) ENGINE =' ._MYSQL_ENGINE_;

        if (! Db::getInstance()->Execute($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Remove Database Tables
     */
    private function _eraseDatabases()
    {
        if ( ! Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.$this->table_name.'`') ) {
            return false;
        }

        return true;
    }
}