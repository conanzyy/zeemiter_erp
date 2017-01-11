<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->common_model->checkpurview();
		$this->load->library('lib_pinyin');
		$this->load->library('lib_cn2pinyin');
		$this->jxcsys  = $this->session->userdata('jxcsys');
    }

    //客户、供应商列表
	public function index(){
	    $v = array();
	    $data['status'] = 200;
		$data['msg']    = 'success';
		$type   = intval($this->input->get('type',TRUE))==10 ? 10 : -10;
		$skey   = str_enhtml($this->input->get_post('skey',TRUE));
		$page   = max(intval($this->input->get_post('page',TRUE)),1);
		$categoryid   = intval($this->input->get_post('categoryId',TRUE));
		$rows   = max(intval($this->input->get_post('rows',TRUE)),100);
		$where  = 'and type='.$type;
	    $where .= $skey ? ' and (contact like "%'.$skey.'%" or linkMans like "%'.$skey.'%" or number like "%'.$skey.'%" or name like "%'.$skey.'%"  )' : '';
		$where .= $categoryid>0 ? ' and cCategory = '.$categoryid.'' : '';
		$where .= ' and sid in ('.$this->jxcsys['sid'].',1)';
		$offset = $rows * ($page-1);
		$data['data']['page']      = $page;
		$data['data']['records']   = $this->mysql_model->get_count(CONTACT,'(isDelete=0) '.$where.'');
		$data['data']['total']     = ceil($data['data']['records']/$rows);
		$list = $this->mysql_model->get_results(CONTACT,'(isDelete=0) '.$where.' order by id desc,number limit '.$offset.','.$rows.'');
		
		foreach ($list as $arr=>$row){
			$id = $row['id'];
			if(!in_array($id, array('1',"2"))) continue;
			$row = $this->mysql_model->get_row(CONTACT_INIT,'(buId='.$id.') and sid = '.$this->jxcsys['sid']);
			$row['amount'] 		&& $list[$arr]['amount'] = $row['amount'];
			$row['periodMoney'] && $list[$arr]['periodMoney'] = $row['periodMoney'];
			$row['difMoney'] 	&& $list[$arr]['difMoney'] = $row['difMoney'];
			
		}
		
		
		foreach ($list as $arr=>$row) {
			
		    $v[$arr]['id']           = intval($row['id']);
		    $v[$arr]['sid']       	 = $row['sid'];
			$v[$arr]['RID']          = intval($row['id']);
			$v[$arr]['number']       = $row['number'];
			$v[$arr]['cCategory']    = intval($row['cCategory']);
			$v[$arr]['customerType'] = $row['cCategoryName'];
			$v[$arr]['pinYin']       = empty($row['pinYin']) ? $this->lib_cn2pinyin->encode($row['name']) : $row['pinYin'];
			$v[$arr]['name']         = $row['name'];
			$v[$arr]['type']         = $row['type'];
			$v[$arr]['delete']       = intval($row['disable'])==1 ? true : false;
			$v[$arr]['cLevel']       = intval($row['cLevel']);
			$v[$arr]['amount']       = (float)$row['amount'];
			$v[$arr]['periodMoney']  = (float)$row['periodMoney'];
			$v[$arr]['difMoney']     = (float)$row['difMoney'];
			$v[$arr]['remark']       = $row['remark'];
			$v[$arr]['isSelf']       = $row['isSelf'];
			$v[$arr]['taxRate']      = (float)$row['taxRate'];
			$v[$arr]['links']        = '';
			if (strlen($row['linkMans'])>0) {
				$list = (array)json_decode($row['linkMans'],true);
				foreach ($list as $arr1=>$row1) {
					if ($row1['linkFirst']==1) {
						$v[$arr]['contacter']        = isset($row1['linkName']) ? $row1['linkName'] : '';
						$v[$arr]['mobile']           = isset($row1['linkMobile']) ? $row1['linkMobile'] : '';
						$v[$arr]['telephone']        = isset($row1['linkPhone']) ? $row1['linkPhone'] : '';
						$v[$arr]['linkIm']           = isset($row1['linkIm']) ? $row1['linkIm'] : '';
						$v[$arr]['province']           = isset($row1['province']) ? $row1['province'] : '';
						$v[$arr]['city']           = isset($row1['city']) ? $row1['city'] : '';
						$v[$arr]['county']           = isset($row1['county']) ? $row1['county'] : '';
						$v[$arr]['addressStr']           = isset($row1['address']) ? $row1['address'] : '';
						$v[$arr]['firstLink']['first']   = isset($row1['linkFirst']) ? $row1['linkFirst'] : '';
					}
				}
		    }
		}
		$data['data']['rows']       = array_values($v);
		die(json_encode($data));
	}

	//校验客户编号
	public function getNextNo(){
	     $type = intval($this->input->get('type',TRUE));
		 $skey = $this->input->post('skey',TRUE);
		 str_alert(200,'success',array('number'=>$skey));
	}


	//检测客户名称
	public function checkName(){
	    $id   = intval($this->input->post('id',TRUE));
		$name = str_enhtml($this->input->post('name',TRUE));
		$oldName = str_enhtml($this->input->post('oldName',TRUE));
		
        if($name !== $oldName) {
            $where = $id > 0 ? 'and (id<>' . $id . ')' : '';
            $where .= ' and sid = ' . $this->jxcsys['sid'];
            $data = $this->mysql_model->get_row(CONTACT, '(name="' . $name . '") ' . $where . ' and isDelete=0');
            if (count($data) > 0) {
                str_alert(-1, '客户名称重复');
            }else{
                str_alert(200,'success');
            }
        }
		str_alert(200,'success');
	}


	public function getRecentlyContact(){
		$billType  = str_enhtml($this->input->post('billType',TRUE));
		$transType = intval($this->input->post('transType',TRUE));
		$where = $transType==150501 ? 'and (type=10)' :'';
		$where = $transType==150601 ? 'and (type=-10)' :'';
	    $data = $this->mysql_model->get_row(CONTACT,'(isDelete=0) '.$where.' ');
		if (count($data)>0) {
			die('{"status":200,"msg":"success","data":{"contactName":"'.$data['name'].'","buId":'.$data['id'].',"cLevel":0}}');
		} else {
		    str_alert(-1,'');
		}
	}



	//获取信息
	public function query() {
	    $id = intval($this->input->get_post('id',TRUE));
		$data = $this->mysql_model->get_row(CONTACT,'(id='.$id.') and isDelete=0');
		
		if (count($data)>0) {
		    $v = array();
		    $init = $this->mysql_model->get_row(CONTACT_INIT,'(buId='.$id.') and sid = '.$this->jxcsys['sid']);
		    
			$info['id']           = $id;
			$info['number']       = $data['number'];
			$info['name']         = $data['name'];
			$info['beginDate']    = $data['beginDate'];
			$info['remark']       = $data['remark'];
			$info['cCategory']    = intval($data['cCategory']);
			$info['cLevel']       = intval($data['cLevel']);
			$info['amount']       = (float)$data['amount'];
			$info['periodMoney']  = (float)$data['periodMoney'];
			$info['taxRate']      = (float)$data['taxRate'];
			$info['difMoney']     = (float)$data['difMoney'];
			$info['isSelf']       = (float)$data['isSelf'];
			
			$init['amount'] 	 &&  $info['amount']  = $init['amount'];
			$init['periodMoney'] &&  $info['periodMoney'] = $init['periodMoney'];
			$init['difMoney']    &&  $info['difMoney'] = $init['difMoney'];
			
			if (strlen($data['linkMans'])>0) {
				$list = (array)json_decode($data['linkMans'],true);
				foreach ($list as $arr=>$row) {
					$v[$arr]['contactId']       = $id;
					$v[$arr]['id']       		= $row['id'];
					$v[$arr]['address']         = isset($row['address']) ? $row['address'] : '';
					$v[$arr]['first']           = isset($row['linkFirst']) && $row['linkFirst']==1 ? true : '';
					$v[$arr]['city']            = isset($row['city']) ? $row['city'] : '';
					$v[$arr]['county']          = isset($row['county']) ? $row['county'] : '';
					$v[$arr]['email']           = isset($row['email']) ? $row['email'] : '';
					$v[$arr]['im']              = isset($row['linkIm']) ? $row['linkIm'] : '';
					$v[$arr]['name']            = isset($row['linkName']) ? $row['linkName'] : '';
					$v[$arr]['province']        = isset($row['province']) ? $row['province'] : '';
					$v[$arr]['mobile']          = isset($row['linkMobile']) ? $row['linkMobile'] : '';
					$v[$arr]['phone']           = isset($row['linkPhone']) ? $row['linkPhone'] : '';
				}
		    }
			$info['links']  = $v;
		    str_alert(200,'success',$info);
		}
		str_alert(-1,'没有数据');
	}

	//新增
	public function add(){
		$data = $this->input->post(NULL,TRUE);
		$data = $this->validform($data);
		//echo '<pre>';print_r($data);die;

		switch ($data['type']) {
			case 10:
				$this->common_model->checkpurview(59);
				$success = '新增客户:';
				break;
			case -10:
				$this->common_model->checkpurview(64);
				$success = '新增供应商:';
				break;
			default:
				str_alert(-1,'参数错误');
		}
		$data = array_filter(elements(array(
					'name','number','amount','beginDate','cCategory',
					'cCategoryName','cLevel','cLevelName','linkMans'
					,'periodMoney','remark','type','difMoney','sid')
					,$data));
		$sql = $this->mysql_model->insert(CONTACT,$data);
		if ($sql) {
			$data['id'] = $sql;
			$data['linkMans']  = (array)json_decode($data['linkMans'],true);
			$this->common_model->logs($success.$data['name']);
			$this->mysql_model->clean();
			str_alert(200,'success',$data);
		}
		str_alert(-1,'添加失败');
	}


	//修改
	public function update(){
		$data = $this->input->post(NULL,TRUE);
		$data = $this->validform($data);
		switch ($data['type']) {
			case 10:
				$this->common_model->checkpurview(60);
				$success = '修改客户:';
				break;
			case -10:
				$this->common_model->checkpurview(65);
				$success = '修改供应商:';
				break;
			default:
				str_alert(-1,'参数错误');
		}
		
		$data = $this->validform($data);
		$info = array_filter(elements(array(
					'name','number','amount','beginDate','cCategory',
					'cCategoryName','cLevel','cLevelName','linkMans'
					,'periodMoney','remark','type','difMoney','sid')
					,$data));
		
		if(empty($info['difMoney'])){
			$info['difMoney'] = 0;//如果
		}
		
		if($data['isSelf']){
			$init = $this->mysql_model->get_row(CONTACT_INIT,'(buId='.$data['id'].') and sid = '.$this->jxcsys['sid']);
			$iinfo = array(
					'buId'=>$data['id'],
					'sid'=>$data['sid'],
					'amount'=>$info['amount'],
					'periodMoney'=>$info['periodMoney'],
					'difMoney'=>$info['difMoney'],
					'update_time'=>time()
			);
			if(empty($init)){
				$sql = $this->mysql_model->insert(CONTACT_INIT,$iinfo);
			}else{
				$sql = $this->mysql_model->update(CONTACT_INIT,$iinfo,'(buId='.$data['id'].') and sid = '.$this->jxcsys['sid']);
			}
		}else{
			$sql = $this->mysql_model->update(CONTACT,$info,'(id='.$data['id'].')');
		}	
		
		if ($sql) {
			$data['cCategory']    = intval($data['cCategory']);
			$data['customerType'] = $data['cCategoryName'];
			$data['linkMans']     = (array)json_decode($data['linkMans'],true);
			$this->common_model->logs($success.$data['name']);
			$this->mysql_model->clean();
			str_alert(200,'success',$data);
		}
		str_alert(-1,'更新失败');
	}

	//删除
	public function delete(){
	    $id   = str_enhtml($this->input->post('id',TRUE));
		$type = intval($this->input->get_post('type',TRUE))==10 ? 10 : -10;
		switch ($type) {
			case 10:
				$this->common_model->checkpurview(61);
				$success = '删除客户:';
				break;
			case -10:
				$this->common_model->checkpurview(66);
				$success = '删除供应商:';
				break;
			default:
				str_alert(-1,'参数错误');
		}
		
        $data = $this->mysql_model->get_results(CONTACT,'(id in('.$id.'))');
        
        foreach ($data as $key => $value){
        	if($value['isSelf'] == '1'){
        		str_alert(-1,'不能删除基础业务数据！');
       		}
        }
        
        if (count($data) > 0) {
		    $info['isDelete'] = 1;
		    $this->mysql_model->get_count(INVOICE,'(isDelete=0) and (buId in('.$id.'))')>0 && str_alert(-1,'不能删除有业务往来的客户或供应商！');
		    $sql = $this->mysql_model->update(CONTACT,$info,'(id in('.$id.')) and isSelf = 0');
		    if ($sql) {
			    $name = array_column($data,'name');
				$this->common_model->logs($success.'ID='.$id.' 名称:'.join(',',$name));
				die('{"status":200,"msg":"success","data":{"msg":"","id":['.$id.']}}');
			}
		}
		str_alert(-1,'客户或供应商不存在');
	}


	//状态
	public function disable(){
		$this->common_model->checkpurview();
		$disable = intval($this->input->post('disable',TRUE));
		$id = str_enhtml($this->input->post('contactIds',TRUE));
		if (strlen($id) > 0) {
			$info['disable'] = $disable;
			$sql = $this->mysql_model->update(CONTACT,$info,'(id in('.$id.'))');
		    if ($sql) {
				$this->common_model->logs('客户'.$disable==1?'禁用':'启用'.':ID:'.$id.'');
				str_alert(200,'success');
			}
		}
		str_alert(-1,'操作失败');
	}

	//公共验证
	private function validform($data) {
	    strlen($data['name']) < 1 && str_alert(-1,'名称不能为空');
		strlen($data['number']) < 1 && str_alert(-1,'编号不能为空');
		strlen($data['cCategory']) < 1 && str_alert(-1,'类别名称不能为空');
		$data['cLevel']        = (float)$data['cLevel'];
		$data['taxRate']       = (float)$data['taxRate'];
		$data['amount']        = (float)$data['amount'];
		$data['periodMoney']   = (float)$data['periodMoney'];
		$data['sid']   		   = $this->jxcsys['sid'];
		$data['type']          = intval($this->input->get_post('type',TRUE))==10 ? 10 : -10;
		$data['pinYin']        = $this->lib_pinyin->str2pinyin($data['name']);
		$data['contact']       = $data['number'].' '.$data['name'];
		$data['difMoney']      = $data['amount'] - $data['periodMoney'];
		$data['cCategoryName'] = $this->mysql_model->get_row(CATEGORY,'(id="'.$data['cCategory'].'")','name');
		if (isset($data['id'])) {
		    $data['id'] = intval($data['id']);
			$this->mysql_model->get_count(CONTACT,'(isDelete=0) and (id <>'.$data['id'].') and (type='.$data['type'].') and (number="'.$data['number'].'") and sid='.$this->jxcsys['sid']) > 0 && str_alert(-1,'编号重复');
		} else {
		    $this->mysql_model->get_count(CONTACT,'(isDelete=0) and (type='.$data['type'].') and (number="'.$data['number'].'") and sid='.$this->jxcsys['sid'] ) > 0 && str_alert(-1,'编号重复');
		}
		return $data;
	}



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */