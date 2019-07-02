<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
    function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }
    function logout(){
        if (!$this->session->userdata('rwlogin')){
            redirect(base_url('account/login'));
        }
        $this->session->sess_destroy();
        redirect(base_url());
    }
    public function login() {
	    if ($this->session->userdata('rwlogin')){
            redirect(base_url());
        }
        $data['toko']   = $this->dbase->dataRow('toko',array());
        $this->load->view('account/login',$data);
    }
    function login_submit(){
        $json['t'] = 0;
        if ($this->session->userdata('rwlogin')){
            $json['t'] = 1;
        } else {
            $user_name  = $this->input->post('user_name');
            $user_pass  = $this->input->post('password');
            $data_user  = $this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1));
            if (strlen(trim($user_name)) == 0){
                $json['msg'] = 'Mohon isikan Nama Pengguna';
            } elseif (!$data_user) {
                $json['msg'] = 'Nama Pengguna tidak terdaftar';
            } elseif (strlen(trim($user_pass)) == 0) {
                $json['msg'] = 'Masukkan password';
            } elseif ($user_pass != $data_user->user_password){
                $json['msg'] = 'Kombinasi Nama Pengguna dan Password tidak sama';
            } else {
                $arr = array('rwlogin'=>true,'user_id'=>$data_user->user_id,'user_fullname'=>$data_user->user_fullname,'user_name'=>$data_user->user_name,'cab_id'=>NULL,'user_level'=>$data_user->user_level);
                if ($data_user->user_level == 55){
                    $data_cab   = $this->dbase->dataRow('cabang',array('user_id'=>$data_user->user_id,'cab_status'=>1));
                    if ($data_cab){
                        $arr['cab_id'] = $data_cab->cab_id;
                    }
                }
                $this->session->set_userdata($arr);
                $json['t']  = 1;
            }
        }
        die(json_encode($json));
    }
}
