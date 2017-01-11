<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Infospty extends CI_Controller {
    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->jxcsys  = $this->session->userdata('jxcsys');
    }
    /*
     *invoice 销售单据编号查询
     *      
     */
    public function index(){
        $v = array();
        $data['status'] = 200;
        $data['msg']    = 'success'; 
        $where  = ' and transType = "150601"';
        $where  = ' and sid = '.$this->jxcsys['sid'];
        $list = $this->mysql_model->get_results(INVOICE,'(1=1) '.$where .' order by billDate desc');
        foreach ($list as $arr=>$row){
            $v[$arr]['billNo']        = $row['billNo'];
            $v[$arr]['buId']        = $row['buId'];
            $v[$arr]['id']        = $row['id'];
        }
        $data['data']['items']      = $v;
        die(json_encode($data));
        
    }
}

