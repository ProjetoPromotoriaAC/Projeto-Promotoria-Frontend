<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
class CadJornadaTrabalho extends TRecord { 
    const TABLENAME  = 'cad_jornada_trabalho';
    const PRIMARYKEY = 'id_jornada_trabalho';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('nm_jornada_trabalho');
        parent::addAttribute('ds_jornada_trabalho');
        parent::addAttribute('id_holding');
    }
#=====================================================================================================================================
# getEscala
#=====================================================================================================================================
    public function getEscala() {
        return CadJornadaTrabalhoEscala::where('id_jornada_trabalho','=',$this->id_jornada_trabalho)->where('id_holding','=',$this->id_holding)->load();
    }
#=====================================================================================================================================
# getHolding
#=====================================================================================================================================
    public function getHolding() {
        return CadHolding::find(1);
    }

}