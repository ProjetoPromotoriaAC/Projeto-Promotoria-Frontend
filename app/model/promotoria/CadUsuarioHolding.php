<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadUsuarioHolding extends TRecord { 
    const TABLENAME  = 'cad_usuario_holding';
    const PRIMARYKEY = 'id_holding';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('id_holding');
        parent::addAttribute('id_usuario');
    }
#=====================================================================================================================================
# getCadUsuario: 
#=====================================================================================================================================
    public getCadUsuario(): CadUsuario {
        return CadUsuario::find($this->id_usuario);
    }
#=====================================================================================================================================
# getCadHolding: 
#=====================================================================================================================================
    public function getHolding(): CadHolding {
        return CadHolding::find($this->id_holding);
    }
}
