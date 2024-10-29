<?php

class ViaCep {
    private string $cep;
    private string $url;
    private StdClass $std;

    public function __construct(string $cep) {
        $this->cep = str_replace('-','',trim($cep));
        $this->url = "https://viacep.com.br/ws/" . $this->cep . "/json/";
    }

    public function getCep() {
        try {
            if (!$this->validateCep()): throw new Exception('CEP com formato inválido'); endif;

            # read the html
            $html = json_decode(file_get_contents($this->url));

            if (isset($html->erro)):
                throw new Exception('CEP não encontrado');
            endif;

            $this->std = new StdClass;
            $this->std->cep = $this->cep;
            $this->std->ds_logradouro = $html->logradouro;
            $this->std->cd_municipio = $html->ibge;
            $this->std->nm_bairro = $html->bairro;
            $this->std->sg_uf = $html->uf;

            return $this->std;
        }
        catch(Exception $e) {
            return new TMessage('error',$e->getMessage());
        }
    }

    public function validateCep(): bool {
        if (strlen($this->cep) < 8): return false; endif;
        if (
            $this->cep === '00000000' || 
            $this->cep === '11111111' || 
            $this->cep === '22222222' || 
            $this->cep === '33333333' || 
            $this->cep === '44444444' || 
            $this->cep === '55555555' || 
            $this->cep === '66666666' || 
            $this->cep === '77777777' || 
            $this->cep === '88888888' || 
            $this->cep === '99999999' 
        ): return false; endif;

        return true;
    }
}