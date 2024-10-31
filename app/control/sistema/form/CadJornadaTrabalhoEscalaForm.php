<?php
/**
 * CadJornadaTrabalhoEscalaForm
 *
 * @author Felipe Lima
 */
// require_once('app/interface/FormInterface.php');

class CadJornadaTrabalhoEscalaForm extends TWindow  {
    protected $form;
    protected $container;

    public function __construct() {
        parent::__construct();

        $this->setParent();
        $this->setForm('form_'.__CLASS__,'Escala');
        $this->setFields();

        $btn_salvar = $this->form->addAction('Salvar',new TAction([$this,'onSave']));
        
        $this->form->addHeaderAction('X',new TAction([$this,'onClose']),null);
        $this->setTvBox();

        parent::add($this->container);
    }

    public function setParent() {
        parent::setSize(0.2, null);
        parent::removePadding();
        parent::removeTitleBar();
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

        // $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
    }

    public function onEdit($param) {
        try {
            if (isset($param['key']) and !isset($param['new'])):
                TTransaction::open('base');
                    $escala = CadJornadaTrabalhoEscala::find($param['key']);
                TTransaction::close();

                $escala->fg_bloqueio = $escala->fg_bloqueio == 1 ? 1: 0;

                $this->form->setData($escala);
            endif;

            if (isset($param['key']) and (isset($param['new']))):
                $std = new StdClass;
                $std->id_jornada_trabalho = $param['journey'];
                $std->id_dia = $param['key'];

                $this->form->setData($std);
            endif;
        }
        catch(Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSave() {
        try {
            $data = $this->form->getData();

            TTransaction::open('base');
                if (!$data->id_escala):
                    if (CadJornadaTrabalhoEscala::where('id_jornada_trabalho','=',$data->id_jornada_trabalho)->where('id_dia','=',$data->id_dia)->count() > 0):
                        throw new Exception('Já existe rota para o dia que você está tentando selecionar');
                    endif;
                endif;

                $escala = new CadJornadaTrabalhoEscala();
                $escala->fromArray((array)$data);
                $escala->fg_bloqueio = $data->fg_bloqueio == 1 ? 1 : 0;
                $escala->store();
            TTransaction::close();

            TApplication::loadPage('CadJornadaTrabalhoForm','onEdit',['key'=>$escala->id_jornada_trabalho,'id'=>$escala->id_jornada_trabalho]);
        }
        catch(Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
            $this->onClose();
        }
    }

    public function setFields() {
        $id_escala = new THidden('id_escala');
        $id_dia = new THidden('id_dia');
        $id_jornada_trabalho = new THidden('id_jornada_trabalho');

        $fg_bloqueio = new TCheckButton('fg_bloqueio');

        $dt_inicio = new TDate('dt_inicio');
        $hr_inicio = new TTime('hr_inicio');

        $dt_fim = new TDate('dt_fim');
        $hr_fim = new TTime('hr_fim');

        $fg_bloqueio->setUseSwitch(true,'blue');
        $fg_bloqueio->setIndexValue(1);
        $row = $this->form->addFields(
            [new TLabel('Data para iniciar'),$dt_inicio],
            [new TLabel('Inicio Expediente'),$hr_inicio],
        );

        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields(
            [new TLabel('Data para finalizar'),$dt_fim],
            [new TLabel('Final do expediente'),$hr_fim],
        );

        $row->layout = ['col-sm-6','col-sm-6'];

        $this->form->addFields([new TLabel('Bloquear Fora Expediente'),$fg_bloqueio]);
        $this->form->addFields([$id_dia,$id_escala,$id_jornada_trabalho]);
    }

    public function onClose() {
        parent::closeWindow(parent::getId());
    }

    // public static function onClose() {
    //     TScript::create("Template.closeRightPanel()");
    // }

}