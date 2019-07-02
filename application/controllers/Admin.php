<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function index() {
	    if (!$this->session->userdata('login')){
	        redirect(base_url(''));
        } elseif ($this->session->userdata('user_level') < 50){
            redirect(base_url(''));
        } else {
            $data['toko'] = $this->dbase->dataRow('toko',array());
            if ($this->session->userdata('user_level') == 99){
                $data['usr'] = $this->dbase->dataResult('user',array('user_status'=>1,'user_level'=>1));
                $tagih = "SELECT  SUM(tg.tg_ammount) AS jml
                      FROM    tb_tagihan AS tg
                      LEFT JOIN tb_user AS us ON tg.user_id = us.user_id
                      WHERE   MONTH(tg_month) = '".date('m')."' AND us.user_status = 1 ";
                $data['tagih'] = $this->dbase->sqlRow($tagih);
                $unpaid = "SELECT  SUM(tg.tg_ammount) AS jml
                      FROM    tb_tagihan AS tg
                      LEFT JOIN tb_user AS us ON tg.user_id = us.user_id
                      WHERE   MONTH(tg_month) = '".date('m')."' AND tg.tg_paid = 0 AND us.user_status = 1";
                $data['unpaid'] = $this->dbase->sqlRow($unpaid);
            } else {
                $data['usr'] = $this->dbase->dataResult('user',array('user_status'=>1,'user_level'=>1,'user_cab'=>$this->session->userdata('cab_id')));
                $tagih = "SELECT  SUM(tg.tg_ammount) AS jml
                      FROM    tb_tagihan AS tg
                      LEFT JOIN tb_user AS us ON tg.user_id = us.user_id
                      WHERE   MONTH(tg_month) = '".date('m')."' AND us.user_cab = '".$this->session->userdata('cab_id')."' AND us.user_status = 1 ";
                $data['tagih'] = $this->dbase->sqlRow($tagih);
                $unpaid = "SELECT  SUM(tg.tg_ammount) AS jml
                      FROM    tb_tagihan AS tg
                      LEFT JOIN tb_user AS us ON tg.user_id = us.user_id
                      WHERE   MONTH(tg_month) = '".date('m')."' AND us.user_cab = '".$this->session->userdata('cab_id')."' AND tg.tg_paid = 0 AND us.user_status = 1";
                $data['unpaid'] = $this->dbase->sqlRow($unpaid);
            }

            $data['body']   = 'admin_home';
            if (!$this->input->is_ajax_request()){
                $this->load->view('dashboard',$data);
            } else {
                $this->load->view($data['body'],$data);
            }
        }
    }
    function import_user(){
	    if ($this->session->userdata('user_level') != 99){
	        die('Forbidden page');
        } else {
            $this->load->view('form/upload_user');
        }
    }
    function submit_upload_user(){
        if (!$this->session->userdata('login')) {
            $json['t'] = 0;
        } elseif ($this->session->userdata('user_level') != 99) {
            $json['t'] = 0;
            $json['msg'] = 'Forbidden page';
        } else {
            ini_set('max_execution_time', 100000); //300 seconds = 5 minutes
            $json['t']	= 0;
            if (!$_FILES['file']['name']) {
                $json['msg'] = 'Mohon pilih filenya';
            } elseif ($_FILES['file']['error']) {
                $json['msg'] = 'Ada error pada filenya';
            } else {
                $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
                $inputFileName = $_FILES["file"]["tmp_name"];
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '2GB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                $inputFileType 	= IOFactory::identify($inputFileName);
                $objReader 		= IOFactory::createReader($inputFileType);
                $objPHPExcel 	= $objReader->load($inputFileName);
                try {
                    $inputFileType = IOFactory::identify($inputFileName);
                    $objReader = IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                } catch(Exception $e) {
                    $json['msg'] = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
                }//end try
                //  Get worksheet dimensions
                $sheet 			= $objPHPExcel->getSheet(0);
                $highestRow 	= $sheet->getHighestRow();
                $highestColumn 	= $sheet->getHighestColumn();
                $dataCount		= 0;
                $kosong         = 0;
                $sheet  = $objPHPExcel->getActiveSheet();
                for ($row = 2; $row <= $highestRow; $row++){
                    $username   = $sheet->getCell('B'.$row)->getValue();
                    $password   = $sheet->getCell('C'.$row)->getValue();
                    $fullname   = $sheet->getCell('D'.$row)->getValue();
                    $address    = $sheet->getCell('E'.$row)->getValue();
                    $bandwidth  = $sheet->getCell('F'.$row)->getValue();
                    $cabang     = $sheet->getCell('G'.$row)->getValue();
                    if (strlen(trim($username)) == 0 && strlen(trim($password)) == 0){
                        if ($kosong > 5){
                            exit();
                        }
                        $kosong++;
                    } else {
                        $dataCab = "SELECT  cb.cab_id
                                    FROM    tb_cabang AS cb
                                    LEFT JOIN tb_user AS us ON cb.user_id = us.user_id
                                    WHERE   us.user_name = '".$username."' ";
                        $dataCab    = $this->dbase->sqlRow($dataCab);
                        if ($dataCab){
                            $arr = array(
                                'user_name' => $username, 'user_password' => $password, 'user_fullname' => $fullname,
                                'user_address' => $address, 'user_bw' => $bandwidth, 'user_cab' => $cabang
                            );
                        } else {
                            $arr = array(
                                'user_name' => $username, 'user_password' => $password, 'user_fullname' => $fullname,
                                'user_address' => $address, 'user_bw' => $bandwidth, 'user_cab' => NULL
                            );
                        }

                        $chkUname = $this->dbase->dataRow('user',array('user_name'=>$username,'user_status'=>1),'user_id');
                        if ($chkUname){
                            $this->dbase->dataUpdate('user',array('user_id'=>$chkUname->user_id),$arr);
                        } else {
                            $this->dbase->dataInsert('user',$arr);
                        }
                        $dataCount++;
                    }
                }
                if ($dataCount > 0){
                    $json['t'] = 1;
                } else {
                    $json['msg'] = 'Tidak ada data pada file ini';
                }
            }
        }
        die(json_encode($json));
    }
    function logout(){
	    //$this->session->sess_destroy();
        /*$ip = $this->input->ip_address();
        $dataLogin = $this->dbase->dataRow('user',array('user_ip'=>$ip));
        if ($dataLogin){
            $this->dbase->dataUpdate('user',array('user_id'=>$dataLogin->user_id),array('user_ip'=>NULL,'user_login'=>0));
        }*/
	    //redirect('http://recordwu.net/');
        $this->session->sess_destroy();
        redirect(base_url(''));
    }
    function user(){
	    //die(var_dump($this->session->userdata('user_level')));
        if (!$this->session->userdata('login')){
            redirect(base_url(''));
        } elseif ($this->session->userdata('user_level') < 50){
            die('Forbidden Page');
        } else {
            $data['toko'] = $this->dbase->dataRow('toko',array());
            $data['body']   = 'admin_user';
            if ($this->session->userdata('user_level') == 99){
                $data_user      = $this->dbase->dataResult('user',array('user_status'=>1,'user_level'=>1));
                //die('x');
            } else {
                $data_user      = $this->dbase->dataResult('user',array('user_status'=>1,'user_level'=>1,'user_cab'=>$this->session->userdata('cab_id')));
            }
            if ($data_user){
                $i = 0;
                foreach ($data_user as $value){
                    $data_user[$i]  = $value;
                    $data_user[$i]->dibayar = $data_user[$i]->blmdibayar = $data_user[$i]->bulanini = 0;
                    $dibayar    = $this->dbase->dataRow('tagihan',array('user_id'=>$value->user_id,'tg_paid'=>1,'tg_status'=>1),'SUM(tg_ammount) AS jml');
                    if ($dibayar){
                        $data_user[$i]->dibayar = $dibayar->jml;
                    }
                    $blmdibayar = $this->dbase->dataRow('tagihan',array('user_id'=>$value->user_id,'tg_paid'=>0,'tg_status'=>1),'SUM(tg_ammount) AS jml');
                    if ($blmdibayar){
                        $data_user[$i]->blmdibayar = $blmdibayar->jml;
                    }
                    $blnini = $this->dbase->dataRow('tagihan',array('user_id'=>$value->user_id,'tg_month'=>date('Y-m').'-01'),'tg_ammount');
                    if ($blnini){
                        $data_user[$i]->bulanini = $blnini->tg_ammount;
                    }
                    $i++;
                }
            }
            $data['user']   = $data_user;
            $this->load->view($data['body'],$data);
        }
    }
    function edit_user(){
	    if (!$this->session->userdata('login')){
	        redirect(base_url());
        } elseif ($this->session->userdata('user_level') != 99){
	        die('Forbidden page');
        } else {
            $user_id    = $this->uri->segment(3);
            $dataUser   = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            if (!$dataUser){
                die('Pengguna tidak ditemukan');
            } else {
                $data['data']   = $dataUser;
                $this->load->view('form/edit_user',$data);
            }
        }
    }
    function edit_user_submit(){
	    $json['t']  = 0;
	    if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99){
	        $json['msg'] = 'Forbidden page';
        } else {
            $user_fullname  = $this->input->post('user_fullname');
            $user_id        = $this->input->post('user_id');
            $user_name      = $this->input->post('user_name');
            $user_password  = $this->input->post('user_password');
            $data_user      = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            $chk_user       = $this->dbase->dataRow('user',array('user_id !='=>$user_id,'user_name'=>$user_name,'user_status'=>1));
            $user_address   = $this->input->post('user_address');
            $user_bw        = $this->input->post('user_bw');
            if (!$user_id || !$data_user) {
                $json['msg'] = 'Tidak ada data pengguna';
            } elseif (strlen(trim($user_fullname)) == 0){
                $json['msg'] = 'Mohon isi nama lengkap';
            } elseif (strlen(trim($user_name)) == 0){
                $json['msg'] = 'Mohon isi nama pengguna';
            } elseif ($chk_user){
                $json['msg'] = 'nama pengguna sudah terdaftar';
            } elseif (strlen(trim($user_password)) == 0){
                $json['msg'] = 'Mohon isi password';
            } else {
                $arr = array(
                    'user_fullname' => $user_fullname, 'user_name' => $user_name, 'user_password' => $user_password,
                    'user_address' => $user_address, 'user_bw' => $user_bw
                );
                $this->dbase->dataUpdate('user',array('user_id'=>$user_id),$arr);
                $json['t'] = 1;
                $json['id'] = $user_id;
                $json['full'] = $user_fullname;
                $json['name'] = $user_name;
                $json['pass'] = $user_password;
            }
        }
        die(json_encode($json));
    }
    function delete_user(){
        $json['t']  = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99){
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id    = $this->input->post('id');
            $dataUser   = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            if (!$dataUser){
                $json['msg'] = 'Tidak ada data pengguna';
            } else {
                $this->dbase->dataDelete('user',array('user_id'=>$user_id));
                $this->dbase->dataDelete('tagihan',array('user_id'=>$user_id));
                //$this->dbase->dataUpdate('user',array('user_id'=>$user_id),array('user_status'=>0));
                $json['t'] = 1;
                $json['msg'] = 'Berhasil menghapus data';
            }
        }
        die(json_encode($json));
    }
    function user_bulk_delete(){
	    $json['t'] = 0;
	    if ($this->session->userdata('login') && $this->session->userdata('user_level') == 99){
	        $user_id = $this->input->post('user_id');
	        if (count($user_id) > 0){
	            foreach ($user_id as $value){
	                $dataUser = $this->dbase->dataRow('user',array('user_id'=>$value),'user_id');
	                if ($dataUser){
	                    $this->dbase->dataUpdate('user',array('user_id'=>$value),array('user_status'=>0));
                    }
                }
            }
            $json['t'] = 1;
	        $json['data'] = $user_id;
        }
	    die(json_encode($json));
    }
    function bulk_paid_tagihan(){
	    $json['t'] = 0;
	    $json['msg'] = 'Invalid data';
	    $tg_id = $this->input->post('tg_id');
        if (count($tg_id) > 0){
            foreach ($tg_id as $value){
                $data_tg = $this->dbase->dataRow('tagihan',array('tg_id'=>$value));
                if ($data_tg){
                    if ($data_tg->tg_paid == 0){
                        $this->dbase->dataUpdate('tagihan',array('tg_id'=>$value),array('tg_paid_date'=>date('Y-m-d H:i:s'),'tg_paid'=>1));
                    } else {
                        $this->dbase->dataUpdate('tagihan',array('tg_id'=>$value),array('tg_paid_date'=>NULL,'tg_paid'=>0));
                    }

                }
            }
            $json['t']  = 1;
            $json['msg'] = 'Berhasil membayar tagihan';
        }
	    die(json_encode($json));
    }
    function bulk_paid(){
        $json['t'] = 0;
        //die(var_dump($this->input->post('user_id')));
        if ($this->session->userdata('login') || $this->session->userdata('user_level') > 50){
            //die('x');
            $user_id = $this->input->post('user_id');
            if (count($user_id) > 0){
                $data = array();
                $i = 0;
                $this->load->library('conv');
                foreach ($user_id as $value){
                    $dataUser = $this->dbase->dataRow('user',array('user_id'=>$value),'user_id');
                    if ($dataUser){
                        $dataTag = $this->dbase->dataRow('tagihan',array('user_id'=>$value,'tg_month'=>date('Y-m').'-01','tg_status'=>1));
                        if ($dataTag){
                            $this->dbase->dataUpdate('tagihan',array('user_id'=>$value,'tg_month'=>date('Y-m').'-01','tg_status'=>1),array('tg_paid'=>1,'tg_paid_date'=>date('Y-m-d H:i:s')));
                            $data[$i] = array('user_id'=>$value);
                            $data[$i]['blmbayar'] = $data[$i]['sdhbayar'] = 'Rp. 0,-';
                            $blmBayar = $this->dbase->dataRow('tagihan',array('user_id'=>$value,'tg_paid'=>0),'SUM(tg_ammount) as jml');
                            if ($blmBayar->jml > 0){
                                $data[$i]['blmbayar'] = 'Rp. '.number_format($blmBayar->jml,0,"",".").',-';
                            }
                            $sdhBayar = $this->dbase->dataRow('tagihan',array('user_id'=>$value,'tg_paid'=>1),'SUM(tg_ammount) as jml');
                            if ($sdhBayar->jml > 0){
                                $data[$i]['sdhbayar'] = 'Rp. '.number_format($sdhBayar->jml,0,"",".").',-';
                            }
                            $i++;
                        }
                    }
                }
            }
            $json['t'] = 1;
            $json['data'] = $data;
        }
        die(json_encode($json));
    }
    function add_user(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99){
            die('Forbidden page');
        } else {
            $this->load->view('form/add_user');
        }
    }
    function add_user_submit(){
        $json['t']  = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99){
            $json['msg'] = 'Forbidden page';
        } else {
            $user_fullname  = $this->input->post('user_fullname');
            $user_name      = $this->input->post('user_name');
            $user_password  = $this->input->post('user_password');
            $chk_user       = $this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1,'user_level'=>1));
            $user_address   = $this->input->post('user_address');
            $user_bw        = $this->input->post('user_bw');
            if (strlen(trim($user_fullname)) == 0){
                $json['msg'] = 'Mohon isi nama lengkap';
            } elseif (strlen(trim($user_name)) == 0){
                $json['msg'] = 'Mohon isi nama pengguna';
            } elseif ($chk_user){
                $json['msg'] = 'nama pengguna sudah terdaftar';
            } elseif (strlen(trim($user_password)) == 0){
                $json['msg'] = 'Mohon isi password';
            } else {
                $arr = array(
                    'user_fullname' => $user_fullname, 'user_name' => $user_name, 'user_password' => $user_password,
                    'user_address' => $user_address, 'user_bw' => $user_bw
                );
                $user_id = $this->dbase->dataInsert('user',$arr);
                if (!$user_id){
                    $json['msg'] = 'Database error';
                } else {
                    $json['t'] = 1;
                    $json['id'] = $user_id;
                    $json['ufull'] = $user_fullname;
                    $json['uname'] = $user_name;
                    $json['upass'] = $user_password;
                }
            }
        }
        die(json_encode($json));
    }
    function admin_tagihan(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') < 50){
            die('Forbidden page');
        } else {
            $data['toko']       = $this->dbase->dataRow('toko',array());
            $data['user_id']    = $this->uri->segment(3);
            $sql_user           = "SELECT * FROM tb_user WHERE user_status = 1 AND user_level < 50 ";
            if ($this->session->userdata('user_level') < 90){
                $sql_user           = "SELECT * FROM tb_user WHERE user_status = 1 AND user_level < 50 AND user_cab = '".$this->session->userdata('cab_id')."' ";
            }
            $data['tahun']  = $this->dbase->dataRow('tagihan',array('tg_status'=>1),'MIN(YEAR(tg_month)) AS tahun');
            //die(var_dump($datatahun));
            $data['user']       = $this->dbase->sqlResult($sql_user);
            $this->load->library('conv');
            $data['body']   = 'admin_tagihan';
            $this->load->view($data['body'],$data);
        }
    }
    function admin_tagihan_data(){
        $user_id    = $this->uri->segment(3);
        $bulan      = (int)$this->uri->segment(4);
        $tahun      = (int)$this->uri->segment(5);
        $data['data'] = array();
        if (!$user_id){
            //die(json_encode($data));
        } else {
            $dataUser = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            if (!$dataUser){
                //die(json_encode($data));
            } else {
                $this->load->library('conv');
                $sql_bulan = $sql_tahun = "";
                if ($bulan > 0){
                    $bulan = str_pad($bulan,2,"0",STR_PAD_LEFT);
                    $sql_bulan = " AND MONTH(tg_month) = '".$bulan."' ";
                }
                if ($tahun > 0){
                    $sql_tahun = " AND YEAR(tg_month) = '".$tahun."' ";
                }

                $sql = "SELECT * FROM tb_tagihan WHERE user_id = '".$user_id."' AND tg_status = 1 ".$sql_bulan.$sql_tahun." ORDER BY tg_month DESC ";
                //die($sql);
                //$data_tag = $this->dbase->dataResult('tagihan',array('user_id'=>$user_id,'tg_status'=>1));
                $data_tag = $this->dbase->sqlResult($sql);
                if ($data_tag){
                    foreach ($data_tag as $value){
                        if ($value->tg_paid == 0){
                            $tg_paidDate    = "";
                            $tg_status      = '<strong class="badge badge-primary">Belum dibayar</strong>';
                        } else {
                            $tg_paidDate = $this->conv->tglIndo($value->tg_paid_date);
                            $tg_status = '<strong class="badge badge-success">Sudah dibayar</strong>';
                        }
                        $btn = '<a class="btn btn-sm btn-success" title="Bayar Tagihan" href="javascript:;" data-id="'.$value->tg_id.'" onclick="paid(this);return false"><i class="fa fa-money"></i> </a>&nbsp;<a class="btn btn-sm btn-primary" title="Cetak Tagihan" href="javascript:;" data-id="'.$value->tg_id.'" onclick="cetak_tagihan(this);return false"><i class="fa fa-print"></i> </a>';
                        if ($this->session->userdata('user_level') == 99){
                            $btn .= '&nbsp;<a class="btn btn-sm btn-danger" title="Hapus Tagihan" href="javascript:;" data-id="'.$value->tg_id.'" onclick="hapus_data(this);return false"><i class="fa fa-trash-o"></i> </a>';
                        }
                        $tags = array(
                            'DT_RowId'  => 'row_'.$value->tg_id,
                            'input'     => '<input onclick="check_cbx();" type="checkbox" name="tg_id[]" value="'.$value->tg_id.'">',
                            'tg_month'  => $this->conv->bulanIndo(date('m',strtotime($value->tg_month))).'&nbsp'.date('Y',strtotime($value->tg_month)),
                            'tg_ammount'=> 'Rp. '.number_format($value->tg_ammount,0,"","."),
                            'tg_paid'   => $tg_paidDate,
                            'tg_status' => $tg_status,
                            'tg_button' => $btn
                        );
                        array_push($data['data'],$tags);
                    }
                }
            }
        }
        die(json_encode($data));
    }
    function format_tagihan(){
    }
    function import_tagihan(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99){
            die('Forbidden page');
        } else {
            $this->load->view('form/import_tagihan');
        }
    }
    function submit_import_tagihan(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['t']	= 0; $json['msg'] = 'Forbidden page';
        } else {
            ini_set('max_execution_time', 100000); //300 seconds = 5 minutes
            $json['t']	= 0;
            if (!$_FILES['file']['name']) {
                $json['msg'] = 'Mohon pilih filenya';
            } elseif ($_FILES['file']['error']) {
                $json['msg'] = 'Ada error pada filenya';
            } else {
                $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
                $inputFileName = $_FILES["file"]["tmp_name"];
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array( 'memoryCacheSize' => '2GB');
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                $inputFileType 	= IOFactory::identify($inputFileName);
                $objReader 		= IOFactory::createReader($inputFileType);
                $objPHPExcel 	= $objReader->load($inputFileName);
                try {
                    $inputFileType = IOFactory::identify($inputFileName);
                    $objReader = IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                } catch(Exception $e) {
                    $json['msg'] = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
                }//end try
                //  Get worksheet dimensions

                $sheet 			= $objPHPExcel->getSheet(0);
                $highestRow 	= $sheet->getHighestRow();
                $highestColumn 	= $sheet->getHighestColumn();
                $dataCount		= 0;
                $kosong         = 0;
                $sheet  = $objPHPExcel->getActiveSheet();
                for ($row = 2; $row <= $highestRow; $row++){
                    $username   = $sheet->getCell('A'.$row)->getValue();
                    $bulan      = $sheet->getCell('C'.$row)->getValue();
                    $bulan      = (int)$bulan;
                    $bulan      = str_pad($bulan,2,"0",STR_PAD_LEFT);
                    $tahun      = $sheet->getCell('D'.$row)->getValue();
                    $jml        = $sheet->getCell('E'.$row)->getValue();
                    $status     = $sheet->getCell('F'.$row)->getValue();

                    if (strlen(trim($username)) == 0 && strlen(trim($bulan)) == 0 && strlen(trim($tahun)) == 0 && strlen(trim($jml)) == 0){
                        $kosong++;
                        if ($kosong > 5){
                            exit();
                        }
                    } else {
                        $dataUser   = $this->dbase->dataRow('user',array('user_name'=>$username,'user_status'=>1),'user_id');
                        if ($dataUser){
                            $tg_month = $tahun.'-'.$bulan.'-01';
                            //die($tg_month);
                            $dataTagihan = $this->dbase->dataRow('tagihan',array('user_id'=>$dataUser->user_id,'tg_month'=>$tg_month,'tg_status'=>1));
                            if ($dataTagihan){
                                $this->dbase->dataUpdate('tagihan',array('tg_id'=>$dataTagihan->tg_id),array('tg_ammount'=>$jml,'tg_paid'=>$status));
                            } else {
                                $kode   = $this->dbase->dataResult('tagihan',array('DATE(tg_date)'=>date('Y-m-d'),'tg_status'=>1),'tg_id');
                                $kode   = count($kode);
                                $kode   = $kode + 1;
                                $kode   = date('dmy').'-'.str_pad($kode,3,"0",STR_PAD_LEFT);
                                $this->dbase->dataInsert('tagihan',array('user_id'=>$dataUser->user_id,'tg_month'=>$tg_month,'tg_ammount'=>$jml,'tg_paid'=>$status,'tg_kode'=>$kode));
                            }
                            $dataCount++;
                        }
                    }
                }
                if ($dataCount > 0){
                    $json['t'] = 1;
                } else {
                    $json['msg'] = 'Tidak ada data pada file ini';
                }
            }
        }
        die(json_encode($json));
    }
    function add_tagihan(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $data['toko'] = $this->dbase->dataRow('toko',array());
            $user_id    = $this->uri->segment(3);
            $data_user  = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            if (!$user_id || !$data_user){
                die('Tidak ada data pengguna');
            } else {
                $this->load->library('conv');
                $data['data']   = $data_user;
                $this->load->view('form/add_tagihan',$data);
            }
        }
    }
    function add_tagihan_submit(){
        $json['t']  = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id    = $this->input->post('user_id');
            $bulan      = $this->input->post('bulan');
            $tahun      = $this->input->post('tahun');
            $jumlah     = $this->input->post('jumlah');
            $data_user  = $this->dbase->dataRow('user',array('user_id'=>$user_id));
            $tanggal    = $tahun.'-'.$bulan.'-01';
            $dataTag    = $this->dbase->dataRow('tagihan',array('YEAR(tg_month)'=>$tahun,'MONTH(tg_month)'=>$bulan,'user_id'=>$user_id));
            if (!$user_id || !$data_user){
                $json['msg'] = 'Tidak ada data pengguna';
            } elseif (strlen(trim($jumlah)) == 0){
                $json['msg'] = 'Mohon isikan jumlah tagihan';
            } else {
                if ($dataTag){
                    $json['t'] = 1;
                    $json['id'] = $dataTag->tg_id;
                    $json['jml'] = 'Rp. '.number_format($jumlah,0,".",".").',-';
                    $this->dbase->dataUpdate('tagihan',array('tg_id'=>$dataTag->tg_id),array('tg_ammount'=>$jumlah,'tg_status'=>1));
                } else {
                    $kode   = $this->dbase->dataResult('tagihan',array('DATE(tg_date)'=>date('Y-m-d'),'tg_status'=>1),'tg_id');
                    $kode   = count($kode);
                    $kode   = $kode + 1;
                    $kode   = date('dmy').'-'.str_pad($kode,3,"0",STR_PAD_LEFT);
                    $ar = array(
                        'user_id' => $user_id, 'tg_ammount' => $jumlah, 'tg_month' => $tanggal, 'tg_kode' => $kode,
                        'tg_date' => date('Y-m-d H:i:s')
                    );
                    $tg_id  = $this->dbase->dataInsert('tagihan',$ar);
                    if (!$tg_id){
                        $json['msg'] = 'Database error';
                    } else {
                        $this->load->library('conv');
                        $json['t'] = 2;
                        $json['id']= $tg_id;
                        $json['jml'] = 'Rp. '.number_format($jumlah,0,".",".").',-';
                        $json['bulan'] = $this->conv->bulanIndo(date('m',strtotime($tanggal))).' '.date('Y',strtotime($tanggal));
                    }
                }
            }
        }
        die(json_encode($json));
    }
    function delete_tagihan(){
        $json['t']  = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id    = $this->input->post('id');
            $dataUser   = $this->dbase->dataRow('tagihan',array('tg_id'=>$user_id));
            if (!$dataUser){
                $json['msg'] = 'Tidak ada data tagihan';
            } else {
                $this->dbase->dataUpdate('tagihan',array('tg_id'=>$user_id),array('tg_status'=>0));
                $json['t'] = 1;
                $json['msg'] = 'Berhasil menghapus data';
            }
        }
        die(json_encode($json));
    }
    function paid_tagihan(){
        $json['t']  = 0;
        if ($this->session->userdata('login') || $this->session->userdata('user_level') == 99){
            $user_id    = $this->input->post('id');
            $dataTag   = $this->dbase->dataRow('tagihan',array('tg_id'=>$user_id));
            if (!$dataTag){
                $json['msg'] = 'Tidak ada data tagihan';
            } else {
                $this->load->library('conv');
                if ($dataTag->tg_paid == 0){
                    $json['date'] = $this->conv->tglIndo();
                    $json['msg'] = 'Berhasil membayar tagihan';
                    $this->dbase->dataUpdate('tagihan',array('tg_id'=>$user_id),array('tg_paid'=>1,'tg_paid_date'=>date('Y-m-d')));
                    $json['paid_date'] = $this->conv->tglIndo(date('Y-m-d'));
                    $json['paid_status'] = '<strong class="badge badge-success">Sudah dibayar</strong>';
                } else {
                    $json['date'] = '';
                    $json['msg'] = 'Berhasil membatalkan tagihan';
                    $this->dbase->dataUpdate('tagihan',array('tg_id'=>$user_id),array('tg_paid'=>0,'tg_paid_date'=>NULL));
                    $json['paid_date'] = "";
                    $json['paid_status'] = '<strong class="badge badge-primary">Belum dibayar</strong>';
                }
                $json['t'] = 1;

            }
        }
        die(json_encode($json));
    }
    function cetak_tagihan(){
	    $id = $this->uri->segment(3);
	    $dataT  = $this->dbase->dataRow('tagihan',array('tg_id'=>$id));
	    if (!$id || !$dataT){
	        die('Pilih tagihan lebih dulu');
        } else {
	        $data_user  = $this->dbase->dataRow('user',array('user_id'=>$dataT->user_id));
	        if (!$data_user){
	            die('Eror data pengguna');
            } else {
                $this->load->library('conv');
                $data['toko'] = $this->dbase->dataRow('toko',array());
                $data['cabang'] = $data['toko']->s_cab;
                if (strlen(trim($data_user->user_cab)) > 0){
                    $dataCab = "SELECT  us.user_fullname
                                FROM    tb_cabang AS cb
                                LEFT JOIN tb_user AS us ON cb.user_id = us.user_id
                                WHERE   cb.cab_id = '".$data_user->user_cab."' ";
                    $dataCab    = $this->dbase->sqlRow($dataCab);
                    if ($dataCab){
                        $data['cabang'] = $dataCab->user_fullname;
                    }
                }
                $data['user'] = $data_user;
                $data['data']   = $dataT;
                $this->load->view('cetak_tagihan',$data);
            }
        }
    }
    function settings(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $data['data']   = $this->dbase->dataRow('toko',array());
            $data['body']   = 'settings';
            $data['toko'] = $this->dbase->dataRow('toko',array());
            $this->load->view($data['body'],$data);
        }
    }
    function set_logo(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $this->load->view('admin_logo');
        }
    }
    function submit_logo(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            if (isset($_FILES['file'])){
                $file_name  = $_FILES['file']['name'];
                $file_tmp   = $_FILES['file']['tmp_name'];
                $file_size  = $_FILES['file']['size'];
                if (($file_size/1048576) > 2){
                    $json['msg'] = 'Ukuran file terlalu besar';
                } else {
                    $data_toko  = $this->dbase->dataRow('toko',array(),'s_logo');
                    if ($data_toko){
                        $old_fname = $data_toko->s_logo;
                        if ($old_fname != 'logo.png'){
                            $urls = FCPATH . 'assets/img/'.$old_fname;
                            @unlink($urls);
                        }
                        $ext = explode(".",$file_name);
                        $ext = end($ext);
                        $new_name = md5($file_name).'.'.$ext;
                        $dest = FCPATH . 'assets/img/'.$new_name;
                        @move_uploaded_file($file_tmp,$dest);
                        $this->dbase->dataUpdate('toko',array('id'=>1),array('s_logo'=>$new_name));
                        $json['t'] = 1;
                        $json['src'] = base_url('assets/img/'.$new_name);
                    }
                }

            }
        }
        die(json_encode($json));
    }
    function set_background(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $this->load->view('admin_background');
        }
    }
    function submit_background(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            if (isset($_FILES['file'])){
                $file_name  = $_FILES['file']['name'];
                $file_tmp   = $_FILES['file']['tmp_name'];
                $file_size  = $_FILES['file']['size'];
                if (($file_size/1048576) > 2){
                    $json['msg'] = 'Ukuran file terlalu besar';
                } else {
                    $data_toko  = $this->dbase->dataRow('toko',array(),'s_background');
                    if ($data_toko){
                        $old_fname = $data_toko->s_background;
                        if ($old_fname != 'background.jpg'){
                            $urls = FCPATH . 'assets/img/'.$old_fname;
                            @unlink($urls);
                        }
                        $ext = explode(".",$file_name);
                        $ext = end($ext);
                        $new_name = md5($file_name).'.'.$ext;
                        $dest = FCPATH . 'assets/img/'.$new_name;
                        @move_uploaded_file($file_tmp,$dest);
                        $this->dbase->dataUpdate('toko',array('id'=>1),array('s_background'=>$new_name));
                        $json['t'] = 1;
                        $json['src'] = base_url('assets/img/'.$new_name);
                    }
                }

            }
        }
        die(json_encode($json));
    }
    function edit_toko(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $data['data']   = $this->dbase->dataRow('toko',array('id'=>1));
            $this->load->view('edit_toko',$data);
        }
    }
    function edit_toko_submit(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $s_name = $this->input->post('s_name');
            $s_address = $this->input->post('s_address');
            $s_cab  = $this->input->post('s_cab');
            $s_phone = $this->input->post('s_phone');
            $s_cell = $this->input->post('s_cellphone');
            $s_twitter = $this->input->post('s_twitter');
            $s_facebook = $this->input->post('s_facebook');
            $s_instagram = $this->input->post('s_instagram');
            if (strlen(trim($s_name)) == 0){
                $json['msg'] = 'Mohon masukkan nama toko';
            } elseif (strlen(trim($s_address)) == 0){
                $json['msg'] = 'Mohon masukkan alamat toko';
            } elseif (strlen(trim($s_cab)) == 0){
                $json['msg'] = 'Mohon masukkan cabang toko';
            } elseif (strlen(trim($s_phone)) == 0){
                $json['msg'] = 'Mohon masukkan No. Telp. toko';
            } elseif (strlen(trim($s_cell)) == 0){
                $json['msg'] = 'Mohon masukkan No. HP toko';
            } else {
                $json['t'] = 1;
                $this->dbase->dataUpdate('toko',array('id'=>1),array('s_name'=>$s_name,'s_address'=>$s_address,'s_cab'=>$s_cab,'s_phone'=>$s_phone,'s_cellphone'=>$s_cell,'s_twitter'=>$s_twitter,'s_facebook'=>$s_facebook,'s_instagram'=>$s_instagram));
            }
        }
        die(json_encode($json));
    }
    function edit_password(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $data['data']   = $this->dbase->dataRow('toko',array('id'=>1));
            $this->load->view('form/ganti_password',$data);
        }
    }
    function edit_password_submit(){
        $json['t'] = 0;
        $json['msg'] = '';
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $old_pass   = $this->input->post('old_pass');
            $pass1      = $this->input->post('pass1');
            $pass2      = $this->input->post('pass2');
            $dataLogin  = $this->dbase->dataRow('user',array('user_id'=>1));
            if (strlen(trim($old_pass)) == 0){
                $json['msg'] = 'Masukkan password lama';
            } elseif ($old_pass != $dataLogin->user_password){
                $json['msg'] = 'Password anda tidak valid';
            } elseif (strlen(trim($pass1)) == 0){
                $json['msg'] = 'Masukkan password baru';
            } elseif ($pass2 != $pass1){
                $json['msg'] = 'Password baru tidak sesuai';
            } else {
                $this->dbase->dataUpdate('user',array('user_id'=>$dataLogin->user_id),array('user_password'=>$pass1));
                $json['t'] = 1;
            }
        }
        die(json_encode($json));
    }
    function cabang(){
        if (!$this->session->userdata('login')){
            redirect(base_url(''));
        } elseif ($this->session->userdata('user_level') != 99){
            die('Forbidden Page');
        } else {
            $data['body']   = 'admin_cabang';
            $dataCabang = " SELECT  cb.*,us.user_name,us.user_password,us.user_fullname,us.user_address
                            FROM    tb_cabang AS cb
                            LEFT JOIN tb_user AS us ON cb.user_id = us.user_id
                            WHERE   us.user_status = 1 AND cb.cab_status = 1 ";
            $dataCabang = $this->dbase->sqlResult($dataCabang);
            if ($dataCabang){
                $i = 0;
                foreach ($dataCabang as $value){
                    $dataCabang[$i] = $value;
                    $dataCabang[$i]->jmlUser = count($this->dbase->dataResult('user',array('user_cab'=>$value->cab_id,'user_status'=>1),'user_id'));
                    $i++;
                }
            }
            $data['data']   = $dataCabang;
            $this->load->view($data['body'],$data);
        }
    }
    function add_cabang(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $this->load->view('form/add_cabang');
        }
    }
    function add_cabang_submit(){
	    $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_fullname  = $this->input->post('user_fullname');
            $user_password  = $this->input->post('user_password');
            $user_name      = $this->input->post('user_name');
            $user_address   = $this->input->post('user_address');
            $chkUname       = $this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1));
            if (strlen(trim($user_fullname)) == 0){
                $json['msg'] = 'Masukkan nama cabang';
            } elseif (strlen(trim($user_name)) == 0){
                $json['msg'] = 'Masukkan nama pengguna';
            } elseif ($chkUname){
                $json['msg'] = 'Nama pengguna sudah dipakai orang lain';
            } elseif (strlen(trim($user_password)) == 0){
                $json['msg'] = 'Masukkan password';
            } elseif (strlen(trim($user_address)) == 0){
                $json['msg'] = 'Masukkan alamat';
            } else {
                $user_id = $this->dbase->dataInsert('user',array('user_name'=>$user_name,'user_password'=>$user_password,'user_address'=>$user_address,'user_level'=>55,'user_fullname'=>$user_fullname));
                if (!$user_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $cab_id = $this->dbase->dataInsert('cabang',array('user_id'=>$user_id));
                    if (!$cab_id){
                        $json['msg'] = 'DB Error';
                    } else {
                        $json['t'] = 1;
                        $json['data'] = array('user_id'=>$user_id,'user_fullname'=>$user_fullname,'user_password'=>$user_password,'user_address'=>$user_address,'cab_id'=>$cab_id,'cab_user'=>0,'user_name'=>$user_name);
                    }
                }
            }
        }
	    die(json_encode($json));
    }
    function edit_cabang(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $cab_id = $this->uri->segment(3);
            $dataCab    = $this->dbase->dataRow('cabang',array('cab_id'=>$cab_id,'cab_status'=>1));
            if (!$cab_id || !$dataCab){
                die('Invalid data');
            } else {
                $dataUser   = $this->dbase->dataRow('user',array('user_id'=>$dataCab->user_id));
                if (!$dataUser){
                    die('Invalid data');
                } else {
                    $data['cabang'] = $dataCab;
                    $data['user'] = $dataUser;
                    $this->load->view('form/edit_cabang',$data);
                }
            }
        }
    }
    function edit_cabang_submit(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $cab_id         = $this->input->post('cab_id');
            $dataCab        = $this->dbase->dataRow('cabang',array('cab_id'=>$cab_id));
            $user_fullname  = $this->input->post('user_fullname');
            $user_password  = $this->input->post('user_password');
            $user_name      = $this->input->post('user_name');
            $user_address   = $this->input->post('user_address');

            if (!$cab_id || !$dataCab){
                $json['msg'] = 'Invalid data';
            } elseif (strlen(trim($user_fullname)) == 0){
                $json['msg'] = 'Masukkan nama cabang';
            } elseif (strlen(trim($user_name)) == 0){
                $json['msg'] = 'Masukkan nama pengguna';
            } elseif ($this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1,'user_id !='=>$dataCab->user_id))){
                $json['msg'] = 'Nama pengguna sudah dipakai orang lain';
            } elseif (strlen(trim($user_password)) == 0){
                $json['msg'] = 'Masukkan password';
            } elseif (strlen(trim($user_address)) == 0){
                $json['msg'] = 'Masukkan alamat';
            } else {
                $this->dbase->dataUpdate('user',array('user_id'=>$dataCab->user_id),array('user_name'=>$user_name,'user_fullname'=>$user_fullname,'user_password'=>$user_password,'user_address'=>$user_address));
                $json['t'] = 1;
                $json['data'] = array('user_id'=>$dataCab->user_id,'user_fullname'=>$user_fullname,'user_password'=>$user_password,'user_address'=>$user_address,'cab_id'=>$cab_id,'cab_user'=>0,'user_name'=>$user_name);
            }
        }
        die(json_encode($json));
    }
    function anggota_cabang(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $cab_id = $this->uri->segment(3);
            $dataCab = $this->dbase->dataRow('cabang',array('cab_id'=>$cab_id));
            if (!$dataCab){
                die('Invalid data');
            } else {
                $dataCabUs = $this->dbase->dataRow('user',array('user_id'=>$dataCab->user_id));
                $dataUser = $this->dbase->dataResult('user',array('user_cab'=>$cab_id,'user_status'=>1));
                $data['user']   = $dataCabUs;
                $data['cab']    = $dataCab;
                $data['data']   = $dataUser;
                $this->load->view('admin_anggota_cabang',$data);
            }
        }
    }
    function add_anggota_cabang(){
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            die('Forbidden page');
        } else {
            $cab_id = $this->uri->segment(3);
            $dataCab = $this->dbase->dataRow('cabang',array('cab_id'=>$cab_id));
            if (!$dataCab) {
                die('Invalid data');
            } else {
                $dataUser = $this->dbase->dataResult('user',array('user_status'=>1,'user_level'=>1,'user_cab'=>NULL));
                $data['cab'] = $dataCab;
                $data['data'] = $dataUser;
                $this->load->view('form/add_anggota_cabang',$data);
            }
        }
    }
    function add_anggota_cabang_submit(){
	    $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $cab_id = $this->input->post('cab_id');
            $user_id = $this->input->post('user_id');
            $dataCab = $this->dbase->dataRow('cabang',array('cab_id'=>$cab_id));
            if (!$cab_id || !$dataCab) {
                $json['msg'] = 'Invalid data cabang';
            } elseif (!$user_id){
                $json['msg'] = 'Pilih data pengguna lebih dulu';
            } elseif (count($user_id) == 0){
                $json['msg'] = 'Pilih data pengguna lebih dulu';
            } else {
                $i = 0;
                foreach ($user_id as $value){
                    $dataUs = $this->dbase->dataRow('user',array('user_id'=>$value),'user_id');
                    if ($dataUs){
                        $this->dbase->dataUpdate('user',array('user_id'=>$value),array('user_cab'=>$cab_id));
                        $i++;
                    }
                }
                if ($i == 0){
                    $json['msg'] = 'Tidak ada data tersimpan';
                } else {
                    $json['t'] = 1;
                }
            }
        }
	    die(json_encode($json));
    }
    function delete_anggota(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id = $this->input->post('user_id');
            if (!$user_id){
                $json['msg'] = 'Pilih anggota lebih dulu';
            } elseif (count($user_id) == 0){
                $json['msg'] = 'Pilih anggota lebih dulu';
            } else {
                foreach ($user_id as $value){
                    $this->dbase->dataUpdate('user',array('user_id'=>$value),array('user_cab'=>NULL));
                }
                $json['t'] = 1;
                $json['data'] = $user_id;
            }
        }
        die(json_encode($json));
    }
    function cabang_bulk_delete(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id = $this->input->post('user_id');
            if (!$user_id){
                $json['msg'] = 'Pilih cabang lebih dulu';
            } elseif (count($user_id) == 0){
                $json['msg'] = 'Pilih cabang lebih dulu';
            } else {
                foreach ($user_id as $value){
                    $dataCab = $this->dbase->dataRow('cabang',array('cab_id'=>$value));
                    if ($dataCab){
                        $this->dbase->dataUpdate('user',array('user_id'=>$dataCab->user_id),array('user_status'=>0));
                        $this->dbase->dataUpdate('cabang',array('cab_id'=>$value),array('cab_status'=>0));
                        $this->dbase->dataUpdate('user',array('user_cab'=>$value),array('user_cab'=>NULL));
                    }
                }
                $json['t'] = 1;
                $json['data'] = $user_id;
            }
        }
        die(json_encode($json));
    }
    function cabang_delete(){
        $json['t'] = 0;
        if (!$this->session->userdata('login') || $this->session->userdata('user_level') != 99) {
            $json['msg'] = 'Forbidden page';
        } else {
            $user_id = $this->input->post('id');
            if (!$user_id){
                $json['msg'] = 'Pilih cabang lebih dulu';
            } else {
                $dataCab = $this->dbase->dataRow('cabang',array('cab_id'=>$user_id));
                if ($dataCab){
                    $this->dbase->dataUpdate('user',array('user_id'=>$dataCab->user_id),array('user_status'=>0));
                    $this->dbase->dataUpdate('cabang',array('cab_id'=>$user_id),array('cab_status'=>0));
                    $this->dbase->dataUpdate('user',array('user_cab'=>$user_id),array('user_cab'=>NULL));
                    $json['t'] = 1;
                }
            }
        }
        die(json_encode($json));
    }
}
