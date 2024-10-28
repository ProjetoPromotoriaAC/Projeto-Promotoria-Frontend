<?php
/**
 * @author Felipe Lima
 * @package model
 * @version 1.0
 */
public class CadRegional extends TRecord { 
    const TABLENAME  = 'cad_regional';
    const PRIMARYKEY = 'id_regional';
    const IDPOLICY = 'max';

    public function __construct(...$x) {
        parent::addAttribute('id_regional');
        parent::addAttribute('id_holding');
        parent::addAttribute('nm_regional');
    }
#=====================================================================================================================================
# getHolding
#=====================================================================================================================================
    public function getHolding(): CadHolding {
        return CadHolding::find($this->id_holding);
    }

}