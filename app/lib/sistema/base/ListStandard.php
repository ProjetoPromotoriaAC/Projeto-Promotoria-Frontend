<?php

class ListStandard extends TStandardList {

    public function __constructor() {
        parent::__constructor();
    }

    public function onClear()
    {
        $this->clearFilters();
        $this->onReload();
    }
}