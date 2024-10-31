<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
class CadJornadaTrabalhoEscala extends TRecord { 
    const TABLENAME  = 'cad_jornada_trabalho_escala';
    const PRIMARYKEY = 'id_escala';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('id_jornada_trabalho');
        parent::addAttribute('id_dia');
        parent::addAttribute('dt_inicio');
        parent::addAttribute('dt_fim');
        parent::addAttribute('hr_inicio');
        parent::addAttribute('hr_fim');
        parent::addAttribute('fg_bloqueio');
    }
#=====================================================================================================================================
# getHolding
#=====================================================================================================================================
    public function getHolding() {
        return CadHolding::find(1);
    }


}