<?php

/**
 * CadHoldingList
 *
 * @author Felipe Lima
 */

class CadJornadaTrabalhoList extends ListStandard implements ListInterface {
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    protected $container;

    private $nm_jornada_trabalho;
    /**
     * Page constructor
     */
    public function __construct() {
        parent::__construct();
        
        $formName = 'form_'.__CLASS__;
        
        $this->setForm($formName,'JORNADA DE TRABALHO');
        $this->setFields();
        // $btn_buscar  = $this->form->addAction('Buscar' , new TAction([$this,'onSearch']), 'fas: fa-search');
        // $btn_limpar  = $this->form->addAction('Limpar' , new TAction([$this,'onSearch']), 'fas: fa-eraser');
        // $btn_novo    = $this->form->addActionLink('Novo'   , new TAction(['CadHoldingForm','onEdit']), 'fas: fa-plus');
        # class button 
        // $btn_incluir->class = 'btn btn-default';
        
        # ADD TV BOX
        $this->setParent();
        $this->setDataGrid('dtg_'.__CLASS__,false,false);
        $this->setTvBox();

        parent::add($this->container);
    }

    public function setParent() {
        $this->setDatabase('base');            
        $this->setActiveRecord('CadJornadaTrabalho'); 

        parent::setAfterSearchCallback( [$this, 'onAfterSearch' ] );
        parent::addFilterField('nm_jornada_trabalho','like','nm_jornada_trabalho');
    }

    public function setForm(string $name, string $title): void {
        $this->form = new BootstrapFormBuilder($name);
        $this->form->setFormTitle($title);    
        $this->form->setFieldSizes('100%');
        $this->form->generateAria();

        $btn_buscar = $this->form->addAction('Buscar', new TAction([$this,'onSearch']),'fas: fa-search');
        $btn_limpar = $this->form->addAction('Limpar', new TAction([$this,'onClear']),'fas: fa-eraser');
    }

    public function setFields() {
        $this->nm_jornada_trabalho = new TEntry('nm_jornada_trabalho');
        $this->form->addFields([$this->nm_jornada_trabalho]);
    }

    public function setDataGrid(string $name, bool $datatable,bool $viewId) {
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(500);
        $this->datagrid->disableDefaultClick();

        $id_jornada_trabalho = new TDataGridColumn('id_jornada_trabalho'   ,'#'        ,'right');
        $nm_jornada_trabalho = new TDataGridColumn('nm_jornada_trabalho'   ,'Apelido'  ,'left' );
        $ds_jornada_trabalho = new TDataGridColumn('ds_jornada_trabalho'   ,'Descrição','left' );

        $this->datagrid->addColumn($id_jornada_trabalho)->setVisibility($viewId);
        $this->datagrid->addColumn($nm_jornada_trabalho);
        $this->datagrid->addColumn($ds_jornada_trabalho);

        # acoes 
        $edit   = new TDataGridAction(['CadJornadaTrabalhoForm', 'onEdit'],     ['id_jornada_trabalho' => '{id_jornada_trabalho}','view'=>false] );
        $delete = new TDataGridAction([$this, 'onDelete'],['id_jornada_trabalho' => '{id_jornada_trabalho}'] );
        // $anly = new TDataGridAction(['CadJornadaTrabalhoForm', 'onEdit'],     ['id_holding' => '{id_holding}','view'=>true] );
        // $action2 = new TDataGridAction([$this, 'onDelete'],   ['code' => '{code}' ] );
        // $action3 = new TDataGridAction([$this, 'onViewCity'], ['city' => '{city}' ] );
        
        $edit->setLabel('Editar');
        $edit->setImage('fa:pen #000');

        $delete->setLabel('Excluir');
        $delete->setImage('fa:trash red');

        // $view->setLabel('Ver');
        // $view->setImage('fa:eye #000');

        // $anly->setLabel('Funcionários');
        // $anly->setImage('fa:people #000');

        $action_group = new TDataGridActionGroup('', 'fa:th');
        
        $action_group->addHeader('Ações');
        $action_group->addAction($edit);
        $action_group->addAction($delete);
        // $action_group->addAction($view);
        // $action_group->addSeparator();
        // $action_group->addHeader('Análise');
        // $action_group->addAction($anly);

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

    public function Delete($param) {
        try {
            if (isset($param['key'])):
                TTransaction::open('base');
                    $journey = CadJornadaTrabalho::find($param['key'])->delete();
                TTransaction::close();    
            endif;

            parent::onReload();
        }
        catch(Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    // public function

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
 