<?php

/**
 * CadHoldingList
 *
 * @author Felipe Lima
 */

class CadHoldingList extends ListStandard implements ListInterface {
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    protected $container;
    /**
     * Page constructor
     */
    public function __construct() {
        parent::__construct();
        
        $formName = 'form_'.__CLASS__;
        $this->setForm($formName,'AMBIENTES');

        $nm_holding = new TEntry('nm_holding');
        $nr_cnpj    = new TEntry('nr_cnpj');
        $rz_holding = new TEntry('rz_holding');

        $fg_ativo = new TCombo('fg_ativo');
        
        $dt_cadastro = new TDate('dt_cadastro'  );

        # add items
        $dt_cadastro->setMask('dd/mm/yyyy');
        $fg_ativo->addItems(Set::comboChoose());

        # add fields
        $row = $this->form->addFields(
            [new TLabel('Cnpj'    ),$nr_cnpj    ],
            [new TLabel('Razão'   ),$nm_holding ],
            [new TLabel('Ativo'   ),$fg_ativo   ],
            [new TLabel('Cadastro'),$dt_cadastro]
        );

        $row->layout = [
            'col-sm-2',
            'col-sm-6',
            'col-sm-2',
            'col-sm-2'
        ];

        # class button 
        // $btn_incluir->class = 'btn btn-default';
        
        # ADD TV BOX
        $this->setParent();
        $this->setDataGrid('dtg_'.__CLASS__,false,false);
        $this->setTvBox();

        TButton::enableField($formName,'btn_novo');
        parent::add($this->container);
    }

    public function setParent() {
        $this->setDatabase('base');            
        $this->setActiveRecord('CadHolding'); 

        # add filters 
        $this->addFilterField('nr_cnpj','=','nr_cnpj');
        $this->addFilterField('rz_holding','like','rz_holding');
        $this->addFilterField('fg_ativo','=','fg_ativo');
        $this->addFilterField('dt_cadastro','=','dt_cadastro');

    }

    public function setForm(string $name, string $title): void {
        $this->form = new BootstrapFormBuilder($name);
        $this->form->setFormTitle($title);    
        $this->form->setFieldSizes('100%');
        $this->form->generateAria();

        $btn_buscar  = $this->form->addAction('Buscar' , new TAction([$this,'onSearch']), 'fas: fa-search');
        $btn_limpar  = $this->form->addAction('Limpar' , new TAction([$this,'onClear']), 'fas: fa-eraser');
        $btn_novo    = $this->form->addActionLink('Novo'   , new TAction(['CadHoldingForm','onEdit']), 'fas: fa-plus');
    }

    public function setDataGrid(string $name, bool $datatable,bool $viewId) {
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(500);
        $this->datagrid->disableDefaultClick();

        $id_holding    = new TDataGridColumn('id_holding'   ,'#'         ,'right' );
        $dt_cadastro   = new TDataGridColumn('dt_cadastro'  ,'Cadastro'  ,'center');
        $dt_atualizado = new TDataGridColumn('dt_atualizado','Atualizado','center');
        $nr_cnpj       = new TDataGridColumn('nr_cnpj'      ,'Cnpj'      ,'center');
        $rz_holding    = new TDataGridColumn('rz_holding'   ,'Razão'     ,'left'  );
        $fg_ativo      = new TDataGridColumn('fg_ativo'     ,'Ativo'     ,'center');
        $qt_licenca    = new TDataGridColumn('qt_licenca'     ,'Licença'     ,'center');
        $qt_licenca_disponivel    = new TDataGridColumn('qt_licenca_disponivel'     ,'Disponível'     ,'center');

        $this->datagrid->addColumn($id_holding   )->setVisibility($viewId);
        $this->datagrid->addColumn($dt_cadastro  );
        $this->datagrid->addColumn($nr_cnpj      );
        $this->datagrid->addColumn($rz_holding   );
        $this->datagrid->addColumn($qt_licenca   );
        $this->datagrid->addColumn($qt_licenca_disponivel   );
        $this->datagrid->addColumn($fg_ativo     );
        $this->datagrid->addColumn($dt_atualizado);

        # acoes 
        $edit = new TDataGridAction(['CadHoldingForm', 'onEdit'],     ['id_holding' => '{id_holding}','view'=>false] );
        $view = new TDataGridAction(['CadHoldingForm', 'onEdit'],     ['id_holding' => '{id_holding}','view'=>true] );
        $anly = new TDataGridAction(['CadHoldingForm', 'onEdit'],     ['id_holding' => '{id_holding}','view'=>true] );
        // $action2 = new TDataGridAction([$this, 'onDelete'],   ['code' => '{code}' ] );
        // $action3 = new TDataGridAction([$this, 'onViewCity'], ['city' => '{city}' ] );
        
        $edit->setLabel('Editar');
        $edit->setImage('fa:pen #000');

        $view->setLabel('Ver');
        $view->setImage('fa:eye #000');

        $anly->setLabel('Funcionários');
        $anly->setImage('fa:people #000');

        $action_group = new TDataGridActionGroup('', 'fa:th');
        
        $action_group->addHeader('Ações');
        $action_group->addAction($edit);
        $action_group->addAction($view);
        $action_group->addSeparator();
        $action_group->addHeader('Análise');
        $action_group->addAction($anly);

        $this->datagrid->addActionGroup($action_group);

        $this->datagrid->createModel();
    }
    
    public function setTvBox(): void {
        // vertical box container
        $this->container = new TVBox;
        $this->container->style = 'width: 100%';
        $this->container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $this->container->add($this->form);

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter($this->pageNavigation);

        $this->container->add($panel);
    }

    public function setDataGridAction(
        string $class,
        string $method,
        string $icon,
        string $label,
        string $classBtn,
        array $setFields,
    ): void {
        
    }
}
 