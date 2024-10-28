<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadEstado extends TRecord { 
    const TABLENAME  = 'cad_estado';
    const PRIMARYKEY = 'cd_uf';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('sg_uf');
        parent::addAttribute('nm_uf');
    }
}
