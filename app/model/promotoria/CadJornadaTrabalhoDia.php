<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
class CadJornadaTrabalhoDia extends TRecord { 
    const TABLENAME  = 'cad_jornada_trabalho_dia';
    const PRIMARYKEY = 'id_jornada_trabalho_dia';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('nm_dia'      );
    }
}