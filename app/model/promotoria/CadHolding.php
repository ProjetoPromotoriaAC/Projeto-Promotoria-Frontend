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
        parent::addAttribute('rz_empresa');
        parent::addAttribute('qt_licenca');
        parent::addAttribute('qt_licenca_disponivel');
        parent::addAttribute('nm_login');
        parent::addAttribute('nr_cep');
        parent::addAttribute('ds_logradouro');
        parent::addAttribute('cd_municipio');
        parent::addAttribute('nr_logradouro');
        parent::addAttribute('ds_complemento');
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

}