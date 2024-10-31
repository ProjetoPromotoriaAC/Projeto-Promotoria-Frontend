<?php
/**
 * CadHoldingForm
 *
 * @author Felipe Lima
 */
// require_once('app/interface/FormInterface.php');

class CadHoldingForm extends TStandardForm  {
    protected $form;
    protected $container;

    public function __construct() {
        parent::__construct();
        
        $this->setParent();
        $this->setForm('form_'.__CLASS__,'Holding');
        $this->setFields();
        $btn_salvar = $this->form->addAction('Salvar', new TAction([$this,'onSave']),'fas: fa-save');

        $this->setTvBox();

        parent::add($this->container);
    }

    public function setParent() {
        parent::setTargetContainer('adianti_right_panel');
    }

    public function setTvBox(): void {
        // vertical box container
        $this->container = new TVBox;
        $this->container->style = 'width: 100%';
        $this->container->add($this->form);
    }

    public function setForm(string $name, string $title) {
        $this->form = new BootstrapFormBuilder($name);
        $this->form->setFormTitle($title);    
        $this->form->setFieldSizes('100%');
        $this->form->generateAria();

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
    }

    public static function onClose() {
        TScript::create("Template.closeRightPanel()");
    }

    public function onEdit($param) {
        try {
            if (isset($param['view'])):
                $this->disableForm($param['view']);
            endif;

            if (isset($param['key'])):
                TTransaction::open('base');
                    $hld = CadHolding::find($param['key']);
                    $mun = CadMunicipio::find($hld->cd_municipio);
                    $est = $mun->getEstado();

                    $hld->sg_uf = $est->sg_uf;
                TTransaction::close();

                $this->form->setData($hld);
            endif;

        }
        catch(Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSave() {
        try {
            # Validate fields forms
            $this->form->validate();

            $data = $this->form->getData();

            TTransaction::open('base');
                $hld = new CadHolding();
                $hld->fromArray((array)$data);
                $hld->store();
            TTransaction::close();

            TToast::show('show', 'Registro salvo', 'bottom right', 'far:check-circle' );

            self::onClose();
            AdiantiCoreApplication::loadPage('CadHoldingList');
        }
        catch(Exception $e) {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }

    public function disableForm(bool $view) {
        $formName = 'form_'.__CLASS__;

        if ($view == false):
            TEntry::disableField($formName,'nr_cnpj');
            TEntry::disableField($formName,'rz_holding');
        else:
            # Form
            $this->form->setEditable(false);

            # Button
            TButton::disableField($formName,'btn_salvar');
        endif;


    }
    public function setFields() {
        $id_holding = new THidden('id_holding');

        # Entry
        $nr_cnpj    = new TEntry('nr_cnpj'   );
        $rz_holding = new TEntry('rz_holding');
        $nm_holding = new TEntry('nm_holding');
        $nm_email   = new TEntry('nm_email'  );
        $qt_licenca = new TEntry('qt_licenca');
        $nr_cep     = new TEntry('nr_cep');
        $ds_logradouro = new TEntry('ds_logradouro');
        $nr_logradouro = new TEntry('nr_logradouro');
        $qt_licenca_disponivel = new TEntry('qt_licenca_disponivel');
        $ds_bairro = new TEntry('ds_bairro');
        $nr_logradouro = new TEntry('nr_logradouro');
        
        #combos
        $cd_municipio = new TDBUniqueSearch('cd_municipio','base','CadMunicipio','cd_municipio','nm_municipio','nm_municipio');
        $sg_uf        = new TDBCombo('sg_uf'       ,'base','CadEstado'   ,'sg_uf','nm_uf','nm_uf');
        
        $fg_ativo = new TCombo('fg_ativo');
        
        $ds_caminho_imagem = new TImageCropper('ds_caminho_imagem');

        # date 
        $dt_cadastro = new TDateTime('dt_cadastro');
        $dt_atualizado = new TDateTime('dt_atualizado');

        #configs
        $ds_caminho_imagem->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
        
        $nr_cnpj->placeholder = '00.000.000/0000-00';
        $fg_ativo->addItems(Set::comboChoose());
        
        $qt_licenca->setNumericMask(0, '.', ',');
        $nr_cep->setMask('00000-000');
        
        $qt_licenca_disponivel->setNumericMask(0, '.', ',');
        
        $nm_email  ->forceLowerCase();
        $nm_holding->forceUpperCase();
        $rz_holding->forceUpperCase();
        $ds_logradouro->forceUpperCase();
        
        $qt_licenca_disponivel->setEditable(false);
        
        // $sg_uf->setMinLength(0);
        $cd_municipio->setMinLength(0);

        # action fields
        $nr_cep->setExitAction(new TAction([__CLASS__,'getCep']));
        $cd_municipio->setChangeAction(new TAction([__CLASS__,'getUfByCity']));
        #add fields
        $this->form->addFields([$id_holding]);
        
        $row = $this->form->addFields(
            [new TLabel('Cnpj'),$nr_cnpj],
            [new TLabel('Razão Social'),$rz_holding],
            [new TLabel('Fantasia'),$nm_holding],
        );

        $row->layout = ['col-sm-3','col-sm-5','col-sm-4'];

        $row = $this->form->addFields(
            [new TLabel('E-mail'),$nm_email],
        );

        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields(
            [new TLabel('Quantidade'),$qt_licenca],
            [new TLabel('Disponível'),$qt_licenca_disponivel],
            [new TLabel('Ativo' ),$fg_ativo],
        );

        $row->layout = ['col-sm-4','col-sm-4','col-sm-4'];

        $this->form->addFields([new TLabel('Logo'),$ds_caminho_imagem]);

        $separator = new TLabel('Endereço','#000000');
        $separator->style = 'border-bottom: 1px solid #000; margin-top:10px';

        $this->form->addFields([$separator]);

        $row = $this->form->addFields(
            [new TLabel('CEP'),$nr_cep],
            [new TLabel('Logradouro'),$ds_logradouro],
        );

        $row->layout = ['col-sm-2','col-sm-10'];

        $row = $this->form->addFields(
            [new TLabel('Cidade'),$cd_municipio],
            [new TLabel('UF'),$sg_uf]
        );

        $row->layout = ['col-sm-8','col-sm-4'];

        $row = $this->form->addFields(
            [new TLabel('Bairro'),$ds_bairro],
            [new TLabel('Número'),$nr_logradouro]
        );

        $row->layout = ['col-sm-9','col-sm-3'];

        $nr_cnpj   ->addValidation('CNPJ',new TCNPJValidator);
        $rz_holding->addValidation('RAZÃO SOCIAL', new TRequiredValidator);
        $nm_holding->addValidation('NOME FANTASIA', new TRequiredValidator);
        $nm_email  ->addValidation('EMAIL', new TEmailValidator);
        $fg_ativo  ->addValidation('ATIVO', new TRequiredValidator);
        $qt_licenca->addValidation('QUANTIDADE', new TRequiredValidator);
    }

    public static function getCep($param) {
        try {
            if (isset($param['nr_cep'])):
                $cep = $param['nr_cep'];
                $api = new ViaCep($cep);

                TForm::sendData('form_'.__CLASS__,$api->getCep(),false,false);
            endif;
        }
        catch(Exception $e) {
            new TMessage('error',$e->getMessage());
        }
    }

    public static function getUfByCity($param) {
        try {
            if(isset($param['cd_municipio'])):
                $cd_municipio = $param['cd_municipio'];

                if ($cd_municipio):
                    TTransaction::open('base');
                        $est = CadMunicipio::find($cd_municipio)->getEstado();
                    TTransaction::close();
                    
                    $std = new StdClass;
                    $std->sg_uf = $est->sg_uf;

                    TForm::sendData('form_'.__CLASS__,$std, false,false);
                endif;

            endif;
        }
        catch(Exception $e) {
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }
}