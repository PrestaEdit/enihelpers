<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

class EniHelpers extends Module
{
    public function __construct()
    {
        $this->name = 'enihelpers';
        $this->bootstrap = true;

        parent::__construct();
    }

    public function getContent()
    {
        $content = '';

        $helpers = [
            'Calendar',
            'View',
            'List',
            'Options',
            'Form',
            'TreeCategories',
            'Shop',
            'KPI',
            'Uploaders'
        ];

        foreach ($helpers as $helperName) {
            $methodName = 'renderHelper' . $helperName;

            $content .= '<h2>Helper' . $helperName . '</h2>';
            $content .= $this->{$methodName}();
            $content .= '<br />';
        }

        return $content;
    }

    public function renderHelperCalendar()
    {
        $helper = new HelperCalendar();

        // Plage de dates séléctionée par défaut
        $helper->setDateFrom('2022-08-01');
        $helper->setDateTo('2022-10-31');

        // Dates utilisés dans le bloc "Comparer avec"
        $helper->setCompareDateFrom('2021-08-01');
        $helper->setCompareDateTo('2021-10-31');

        return '<button id="datepickerExpand" class="btn btn-default" type="button">Voir le calendrier <i class="icon-caret-down"></i></button>' . $helper->generate();
    }

    public function renderHelperView()
    {
        $helper = new HelperView();

        $helper->title = 'Une vue';

        return $helper->generateView();
    }

    public function renderHelperList()
    {
        $helper = new HelperList();

        $helper->simple_header = false;

        $helper->actions = ['read', 'view', 'details'];

        $helper->identifier = 'id_chapter';
        $helper->title = 'Chapitres';
        $helper->list_id = $this->name;
        $helper->module = $this;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $columns = [
            'id_chapter' => array(
                'title' => $this->l('#'),
                'width' => 140,
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
            ),
        ];
        if (Tools::getValue('submitFilter' . $helper->list_id)) {
            $filters = [];
            foreach ($columns as $columnId => $column) {
                $filters[$columnId] = Tools::getValue($helper->list_id . 'Filter_' . $columnId);
            }

            // $filters = [
            //    'id_chapter' => 7,
            //    'name' => 'Chapitre filtré',
            //]
        }

        $orderBy = Tools::getValue($helper->list_id . 'Orderby');
        $orderWay = Tools::getValue($helper->list_id . 'Orderway');

        $datas = [
            ['id_chapter' => 1, 'name' => 'Introduction'],
            ['id_chapter' => 2, 'name' => 'Au coeur de PrestaShop'],
            ['id_chapter' => 3, 'name' => 'Modules'],
            ['id_chapter' => 4, 'name' => 'Personnalisation'],
            ['id_chapter' => 5, 'name' => 'Webservice'],
            ['id_chapter' => 6, 'name' => 'Thèmes'],
            ['id_chapter' => 7, 'name' => 'Pour aller plus loin'],
        ];


        return $helper->generateList($datas, $columns);
    }

    public function displayReadLink($token, $id, $name)
    {
        // Vous pouvez évidemment utilisez un template Smarty pour gérer le rendu
        return '<a class="default">Lire #' . $id . '</a>';
    }

    public function renderHelperOptions()
    {
        $helper = new HelperOptions();
        $helper->id = $this->context->controller->id;

        $options = [
            'indexation' => [
                'title' => $this->trans('Indexing', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-cogs',
                'top' => 'Texte avant panel',
                'description' => 'Description',
                'fields' => [
                    'ENI_RADIO' => [
                        'type' => 'radio',
                        'title' => $this->l('Radio'),
                        'validation' => 'isInt',
                        'choices' => [
                            'yes' => $this->l('Yes'),
                            'no' => $this->l('No'),
                        ],
                    ],
                    'ENI_CHECKBOX' => [
                        'type' => 'checkbox',
                        'title' => $this->l('Checkbox'),
                        'validation' => 'isInt',
                        'choices' => [
                            'yes' => $this->l('Yes'),
                            'no' => $this->l('No'),
                        ],
                    ],
                    'ENI_SELECT' => [
                        'type' => 'select',
                        'title' => $this->l('Select'),
                        'validation' => 'isInt',
                        'identifier' => 'id',
                        'list' => [
                            ['id' => 'yes', 'name' => $this->l('Yes')],
                            ['id' => 'no', 'name' => $this->l('No')],
                        ],
                    ],
                    'ENI_PRICE' => [
                        'type' => 'price',
                        'title' => $this->l('Price'),
                        'validation' => 'isInt',
                    ],
                    'ENI_COLOR' => [
                        'type' => 'color',
                        'title' => $this->l('Color'),
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
            'search' => [
                'title' => $this->trans('Search', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-search',
                'info' => 'Texte d\'en-tête du panel',
                'fields' => [
                    'PS_SEARCH_MAX_WORD_LENGTH' => [
                        'title' => $this->trans(
                            'Maximum word length (in characters)',
                            [],
                            'Admin.Shopparameters.Feature'
                        ),
                        'hint' => $this->trans(
                            'Only words fewer or equal to this maximum length will be searched.',
                            [],
                            'Admin.Shopparameters.Help'
                        ),
                        'desc' => $this->trans(
                            'This parameter will only be used if the fuzzy search is activated: the lower the value, the more tolerant your search will be.',
                            [],
                            'Admin.Shopparameters.Help'
                        ),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval',
                        'required' => true,
                    ],
                    'PS_SEARCH_BLACKLIST' => [
                        'title' => $this->trans('Blacklisted words', [], 'Admin.Shopparameters.Feature'),
                        'validation' => 'isGenericName',
                        'hint' => $this->trans(
                            'Please enter the index words separated by a "|".',
                            [],
                            'Admin.Shopparameters.Help'
                        ),
                        'type' => 'textareaLang',
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];

        return $helper->generateOptions($options);
    }
    
    public function renderHelperForm()
    {
        $helper = new HelperForm();

        $form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Edit'),
                        'icon' => 'icon-cogs'
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'example_field',
                        ],
                    ],
                    'submit' => [
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right'
                    ],
                ],
            ],
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Edit'),
                        'icon' => 'icon-cogs'
                    ],
                ],
            ],
        ];

        // Associations des données
        $helper->fields_value = [
            'example_field' => 'Ceci est un exemple',
        ];

        $helper->submit_action = 'configure_module';
        // Permet d'afficher le bouton Annuler
        // Qui retourne à la page précédente
        $helper->show_cancel_button = true;

        return $helper->generateForm($form);
    }

    public function renderHelperTreeCategories()
    {
        $rootCategory = 3; // Vêtements
        $helper = new HelperTreeCategories( 
            'enihelpers-tree',
            'Arbre de catégories',
            $rootCategory
        );

        $selectCategories = [
            4, // Hommes
            5, // Femmes
        ];
        $helper->setSelectedCategories([4,5]);

        // Utilisation des cases à cocher et non des boutons radios
        $helper->setUseCheckBox(true);

        return $helper->render();
    }

    public function renderHelperShop()
    {
        //$helper = new HelperShop();

        //return $helper->getRenderedShopList();

        $helper = new HelperTreeShops( 
            'enihelpers-tree-shops',
            'Arbre des boutiques'
        );

        $selectedShops = [
            3,
        ];
        $helper->setSelectedShops($selectedShops);

        return $helper->render();
    }

    public function renderHelperKPI()
    {
        $needRefresh = false;

        $kpis = [];

        // Listes des couleurs disponibles : 
        // color1: bleu
        // color2: rouge
        // color3: violet
        // color4: vert

        $helper = new HelperKpi();
        $helper->id = 'box-example-one';
        $helper->icon = 'icon-envelope';
        $helper->color = 'color1';
        $helper->href = $this->context->link->getAdminLink('AdminCustomerThreads');
        $helper->title = $this->l('KPI #1');
        $helper->value = 42;
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=pending_messages';
        $helper->refresh = (bool) $needRefresh;
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-age';
        $helper->icon = 'icon-time';
        $helper->color = 'color2';
        $helper->title = $this->l('KPI #2');
        $helper->subtitle = $this->l('Subtitle #1');
        $helper->value = 42;
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-messages-per-thread';
        $helper->icon = 'icon-copy';
        $helper->color = 'color3';
        $helper->title = $this->l('KPI #3');
        $helper->subtitle = $this->l('Subtitle #1');
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }
    
    public function renderHelperUploaders()
    {
        $helper = new HelperUploader();
        $helper->setId('file_uploader');
        $helper->setName('file_uploader');
        $helper->setMultiple(true);
        $helper->setUseAjax(true);

        //return $helper->render();

        $helper = new HelperImageUploader();

        return $helper->render();

    }
}
