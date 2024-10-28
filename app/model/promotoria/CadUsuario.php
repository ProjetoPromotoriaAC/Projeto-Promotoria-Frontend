<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadUsuario extends TRecord { 
    const TABLENAME  = 'cad_usuario';
    const PRIMARYKEY = 'id_usuario';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('id_perfil_acesso');
        parent::addAttribute('id_jornada_trabalho');
        
        parent::addAttribute('nm_usuario');
        parent::addAttribute('nm_login');
        parent::addAttribute('nm_senha_app');
        parent::addAttribute('nm_email');
        parent::addAttribute('utc_horario');
        parent::addAttribute('fg_campo');
        parent::addAttribute('fg_app');
        parent::addAttribute('nr_imei');
        parent::addAttribute('fg_ativo');
        parent::addAttribute('fg_status');
        parent::addAttribute('nr_cep');
        parent::addAttribute('ds_logradouro');
        parent::addAttribute('cd_municipio');
        parent::addAttribute('nr_logradouro');
        parent::addAttribute('ds_complemento');
        parent::addAttribute('nr_contato');
    }
#=====================================================================================================================================
# getPerfilAcesso: Retorna objeto do perfil de acessso
#=====================================================================================================================================
    public function getPerfilAcesso() {

    }
#=====================================================================================================================================
# getJornadaTrabalho: Retorna o objeeto da jornada de trabalho
#=====================================================================================================================================
    public function getJornadaTrabalho() {
        
    }
#=====================================================================================================================================
# getMunicipio: Retorna o objeto do Municipio
#=====================================================================================================================================
    public function getMunicipio() {
        return CadMunicipio::find($this->cd_municipio);
    }
#=====================================================================================================================================
# getMunicipio: Retorna o objeto do Municipio
#=====================================================================================================================================
    public function getHolding(): CadHolding {
        return CadHolding::find($this->id_holding);
    }
}