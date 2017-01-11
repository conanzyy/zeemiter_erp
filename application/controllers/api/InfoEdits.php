<?php
/**
 * 商品信息修改
 * 
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
    
class InfoEdits extends CI_Controller{

    
    public function add(){
        $data = $this->input->post(null,TRUE);
        
           //$data = json_decode($data, true);
            $info = elements(array(
                'spec','number'
            ),$data); 
           
        var_dump($info);
        
        //$iid = $this->load->database('default',true);
        //$iid->insert('zee_goods',$info);
        /* if(!$iid){
            die("{status:error,message:添加失败}");
        }else{
            die("{status:success,message:添加成功}");
        } */
    }
    public function update() {
        if(empty($params['id'])) die("{status:error,code:shmE0002,message:修改失败}");
        $params = $this->input->post(null,true);
        $iid = $this->load->database('default',true);
        $data['spec']=$params['spec'];
        $id=$params['id'];
        $iid->where('id',$id);
        $iid->update('zee_goods',$data);
        if(!$iid){
            die("{status:error,message:更改失败}");
        }else{
            die("{status:success,message:更改成功}");
        }
}
}