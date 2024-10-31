<?php

class CadJornadaTrabalhoForm extends TPage {
    protected $form;
    protected $container;
    protected $kanban;

    protected $id_jornada_trabalho;
    protected $allow_jornada;

    public function __construct($param) {
        parent::__construct();
        
        $this->setParent();
        $this->setForm('form_'.__CLASS__,'JORNADA TRABALHO: Defina um apelido a jornada de trabalho');
        $this->setKanban($param);

        $this->setFields();

        $btn_salvar = $this->form->addAction('Salvar', new TAction([$this,'onSave']),'fas: fa-arrow-right');

        $this->setTvBox();
        
        #include item 

        parent::add($this->container);
    }

    public function setParent() {
        // parent::setTargetContainer('adianti_right_panel');
    }

    public function setTvBox() {
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
        $this->form->addHeaderActionLink('Voltar', new TAction(['CadJornadaTrabalhoList', 'onReload']), 'fas: fa-arrow-left red');
    }

    public function setKanban($param) {
        if (!isset($param['key'])):
            $param['key'] = 0;
        endif;

        $this->kanban = new TKanban;
        # include param to store action 
        $this->kanban->addStageShortcut('Adicionar', new TAction(['CadJornadaTrabalhoEscalaForm', 'onEdit'], ['register_state' => 'true','journey'=>$param['key'], 'new'=>true]),   'fa:plus green fa-fw');
        $this->kanban->addItemAction('Editar', new TAction(['CadJornadaTrabalhoEscalaForm', 'onEdit'], ['register_state' => 'false','journey'=>$param['key']]), 'far:edit bg-blue');
        $this->kanban->addItemAction('Excluir', new TAction([$this, 'onDelete']), 'far:trash-alt bg-red');
        $this->kanban->setItemDropAction(new TAction([__CLASS__, 'onUpdateItemDrop']));
        // $this->kanban->setStageDropAction(new TAction([__CLASS__, 'onUpdateStageDrop']));
        $this->populateKanban($param);
        
    }

    public static function onUpdateItemDrop($param) {
        try {
            if (!isset($param['stage_id']) || !isset($param['id']) || !isset($param['key'])):
                throw new Exception('Erro ao realizar mudanças');
            endif;

            TTransaction::open('base');
                $escala = CadJornadaTrabalhoEscala::find($param['key']);
                $escala->id_dia = $param['stage_id'];
                $escala->store();
            TTransaction::close();

            TToast::show('show', 'Registro editado', 'bottom right', 'far:check-circle' );

        }
        catch(Exception $e){
            new TMessage('error',$e->getMessage());
            TTransaction::rollback();
        }
    }

    // public function onUpdateStageDrop($param) {

    // }



    public function onDelete($param) {
        try {
            if (!isset($param['key'])):
                throw new Exception('Código da escala não foi localizado');
            endif;

            TTransaction::open('base');
                $escala = CadJornadaTrabalhoEscala::find($param['key']); 
                $id_jornada_trabalho = $escala->id_jornada_trabalho;
                $escala->delete();
            TTransaction::close();

            TToast::show('show', 'Registro salvo', 'bottom right', 'far:check-circle' );

            TApplication::loadPage(__CLASS__,'onEdit',['key'=>$id_jornada_trabalho,'id'=>$id_jornada_trabalho]);

        }   
        catch(Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function setFields() {
        $this->id_jornada_trabalho = new THidden('id_jornada_trabalho');

        $nm_jornada_trabalho = new TEntry('nm_jornada_trabalho');
        $ds_jornada_trabalho = new TEntry('ds_jornada_trabalho');

        $row = $this->form->addFields(
            [new TLabel('Nome'),$nm_jornada_trabalho],
            [new TLabel('Breve Descrição'),$ds_jornada_trabalho],
        );

        $row->layout = ['col-sm-4','col-sm-8'];

        $this->form->addFields([$this->id_jornada_trabalho]);
        $this->form->addFields([$this->kanban]);
        
    }

    public function populateKanban($param) {
        try {
            if(isset($param['key'])):
                TTransaction::open('base');
                    $dias = CadJornadaTrabalhoDia::all();
                    $escala = CadJornadaTrabalhoEscala::where('id_jornada_trabalho','=',$param['key'])->load();
                TTransaction::close();

                # boxes 
                foreach($dias as $stage):
                    $this->kanban->addStage($stage->id_jornada_trabalho_dia,$stage->nm_dia,$stage);
                endforeach;
                
                #itens boxes 
                #$kanban->addItem($item->id, $item->stage_id, $item->title, $item->content, $item->color, $item);
                foreach($escala as $item):
                    $content = "<b>INÍCIO EXPEDIENTE</b>". "<br>";
                    $content .= "Data: ".$item->dt_inicio . "<br>";
                    $content .= "Hora: ".$item->hr_inicio . "<br><br>";

                    $content .= "<b>FINAL EXPEDIENTE</b>". "<br>";
                    $content .= "Data: ".$item->dt_fim . "<br>";
                    $content .= "Hora: ".$item->hr_fim . "<br><br>";

                    if ($item->fg_bloqueio == 1):
                        $content .= "Bloqueia F/E: Bloquear";
                    else:
                        $content .= "Bloqueia F/E: Não Bloquear";
                    endif;

                    $this->kanban->addItem($item->id_escala,$item->id_dia,'Escala',$content,'#000',$item);
                endforeach;
            endif;
        }
        catch(Exception $e){
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onSave() {
        try {
            $data = $this->form->getData();

            TTransaction::open('base');
                $journey = new CadJornadaTrabalho();
                $journey->fromArray((array) $data);
                $journey->store();
            TTransaction::close();

            TToast::show('show', 'Registro salvo', 'bottom right', 'far:check-circle' );

            AdiantiCoreApplication::loadPage('CadJornadaTrabalhoList');
        }   
        catch(Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param) {
        try {
            TTransaction::open('base');
                if(isset($param['key'])):
                    $journey = CadJornadaTrabalho::find($param['key']);
                    
                    $this->form->setData($journey);
                else:
                    $obj = $this->onSaveNewJourney(); 
                    # redirect passando o novo id
                    TApplication::loadPage(__CLASS__,'onEdit',['key'=>$obj->id_jornada_trabalho,'id'=>$obj->id_jornada_trabalho]);
                endif;
            TTransaction::close();

        }
        catch(Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSaveNewJourney(): StdClass {
        $journey = new CadJornadaTrabalho();
        $journey->nm_jornada_trabalho = 'Jornada '. strval(random_int(0,9));
        $journey->ds_jornada_trabalho = ' ';
        $journey->id_holding = 1;
        $journey->store();

        $std = new StdClass;
        $std->id_jornada_trabalho = $journey->id_jornada_trabalho;
        $std->nm_jornada_trabalho = null;
        $std->ds_jornada_trabalho = null;
        
        return $std;
    }
}