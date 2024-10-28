<?php 

interface ListInterface {
    public function setDataGrid(string $name, bool $datatable, bool $viewId);
    // public function setDropdownAction(array $actions);
#=====================================================================================================================================
# setForm
#=====================================================================================================================================
    public function setForm(string $name, string $title): void;
#=====================================================================================================================================
# setTvBox
#=====================================================================================================================================
    public function setTvBox(): void;
    // public function setTPanelGroup(BootstrapDatagridWrapper $datagrid, TPageNavigation $pageNavigation);
    
    // public function setFieldsForm(): void;
}