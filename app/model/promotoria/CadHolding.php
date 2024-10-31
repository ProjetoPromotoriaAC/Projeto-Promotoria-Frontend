<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
class CadHolding extends TRecord { 
    const TABLENAME  = 'cad_holding';
    const PRIMARYKEY = 'id_holding';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('nm_holding');
        parent::addAttribute('ds_caminho_imagem');
        parent::addAttribute('nr_cnpj');
        parent::addAttribute('fg_ativo');
        parent::addAttribute('dt_cadastro');
        parent::addAttribute('dt_atualizado');
        parent::addAttribute('rz_holding');
        parent::addAttribute('qt_licenca');
        parent::addAttribute('qt_licenca_disponivel');
        parent::addAttribute('nm_login');
        parent::addAttribute('nr_cep');
        parent::addAttribute('ds_logradouro');
        parent::addAttribute('cd_municipio');
        parent::addAttribute('nr_logradouro');
        parent::addAttribute('ds_complemento');
        parent::addAttribute('ds_bairro');
        parent::addAttribute('nm_email');
    }
#=====================================================================================================================================
# getMunicipio: Retorna o objeto do Municipio
#=====================================================================================================================================
    public function getMunicipio(): CadMunicipio {
        return CadMunicipio::find($this->cd_municipio);
    }

#=====================================================================================================================================
# getMunicipio: Retorna o objeto do Municipio
#=====================================================================================================================================
    public function getEstado(): CadEstado {
        return CadEstado::find(CadMunicipio::find($this->cd_municipio)->cd_uf);
    }
#=====================================================================================================================================
# store
#=====================================================================================================================================
    public function store() {
        $this->nr_cnpj = Format::cleanString($this->nr_cnpj);
        $this->nr_cep = Format::cleanString($this->nr_cep);

        if (!$this->id_holding): 
            $this->dt_cadastro = date('Y-m-d H:i:s');
        endif;

        $this->dt_atualizado = date('Y-m-d H:i:s'); 
        $this->nm_login = TSession::getValue('login');
        
        $usuario_ativo = CadUsuario::where('fg_app','=',1)->where('fg_ativo','=',1)->count();
        $qt_licenca_atual = $this->qt_licenca - $usuario_ativo;

        if ($usuario_ativo > $this->qt_licenca):
            throw new Exception('Para diminuir a quantidade de licenças para holding, é preciso remover os usuários que utilizam o APP.');
        endif;

        if ($usuario_ativo > 0 and $this->fg_ativo == 0):
            throw new Exception('Para inativar a holding, é preciso remover os usuários que utilizam o APP.');
        endif;

        $this->qt_licenca_disponivel = $qt_licenca_atual;


        parent::store();
    }
}