<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadRegionalUsuario extends TRecord { 
    const TABLENAME  = 'cad_regional_usuario';
    const PRIMARYKEY = 'id_holding';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('id_holding');
        parent::addAttribute('id_regional');
        parent::addAttribute('id_usuario');
    }
#=====================================================================================================================================
# getCadUsuario: 
#=====================================================================================================================================
    public function getCadUsuario(): CadUsuario {
        return CadUsuario::find($this->id_usuario);
    }
#=====================================================================================================================================
# getCadHolding: 
#=====================================================================================================================================
    public function getHolding(): CadHolding {
        return CadHolding::find($this->id_holding);
    }
#=====================================================================================================================================
# getCadRegional: 
#=====================================================================================================================================
    public function getHolding(): CadRegional {
        return CadRegional::find($this->id_regional);
    }
}
