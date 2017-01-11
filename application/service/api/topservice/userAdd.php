<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 修理厂新增
 *
 */

class userAdd extends MY_Service {

	
    public function __construct(){
        parent::__construct();
    }   
    public function service($params){  
    	//业务逻辑
        switch ($params['update_type']) {
            //修理厂新增
            case '1':             
           $info['sid']=$params['login_account'];
           $info['name']=$params['user']['repairdepot_name'];
           $info['number']=$params['user']['business_encoding'];
           $info['cCategoryName']='修理厂';
           $info['isSelf']='1';

           $this->db->trans_begin();
           $iid = $this->mysql_model->insert(CONTACT,$info);

            if ($this->db->trans_status() === FALSE) {     
                $this->db->trans_rollback();
                str_alert(-1,'SQL错误');  
            } else {      
                $this->db->trans_commit(); 
                str_alert(200,'success'); 
            }
            break;
            //修理厂信息修改
            case '2':         
                      
                break;
            //修理厂信息删除
           	case 3:
                
            break;
        }
       
              
        
    	
    	$this->_error("E10001");
    }
}