<?php 
header("Content-type: text/html; charset=utf-8"); 
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 服务商信息修改
 *
 */
class serviceEdit extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }
    
    public function service($params){
      
    	//业务逻辑
        //服务商修改 
        $sid = $params['login_account'];
        $admin['service_type']= $params['service_type'];
       
       //zee_admin 表中更新数据
        $this->db->trans_begin();
        $iid = $this->mysql_model->update(ADMIN,$admin,'(sid='.$sid.')');
        echo $this->db->last_query();
        if ($this->db->trans_status() === FALSE) {
        	$this->db->trans_rollback();
        	$this->_error("E10001","SQL错误");
        } else {
        	$this->db->trans_commit();
        }
       
    	$this->_success("修改成功");
    }
}
    
    

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */