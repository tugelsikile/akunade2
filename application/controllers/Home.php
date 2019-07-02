<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index() {
	    if (!$this->session->userdata('rwlogin')){
            redirect(base_url('account/login'));
        }
        $data['toko']   = $this->dbase->dataRow('toko',array());
	    $this->load->view('home',$data);
    }
}
