<?php

class Sample_Model extends CI_Model {
        
    public function __construct(){
        parent::__construct();
        $this->load->library('amon');
        $this->amon->config(array('host' => 'http://127.0.0.1',
            'port' => 2464,
            'application_key' => ''));
        $this->amon->setup_exception_handler();
        error_reporting(E_ALL);
    }

    public function kirim($pesan){
        // Logging
        $this->amon->log("pesan dari aplikasi: ".$pesan);
    }
}
?>
