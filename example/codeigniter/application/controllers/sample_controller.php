<?php

class Sample_Controller extends CI_Controller{

	public function __construct(){
		parent::__construct();
                $this->load->model('sample_model','SampleModel', FALSE);
	}

	public function index(){
                $this->SampleModel->kirim("CodeIgniter logging to Amon Server.");
		$this->load->view('sample_view');
	}
}
?>
