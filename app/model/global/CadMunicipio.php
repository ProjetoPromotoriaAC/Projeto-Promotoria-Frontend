<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadMunicipio extends TRecord { 
    const TABLENAME  = 'cad_municipio';
    const PRIMARYKEY = 'cd_municipio';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('cd_uf');
        parent::addAttribute('nm_municipio');
    }
#=====================================================================================================================================
# getMunicipio: Retorna o objeto do Municipio
#=====================================================================================================================================
    public function getEstado(): CadEstado {
        return CadEstado::find($this->cd_uf);
    }

}
