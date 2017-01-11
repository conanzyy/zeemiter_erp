<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data_model extends CI_Model{

	private $orderType;
	
	public function __construct(){
  		parent::__construct();
  		$this->jxcsys = $this->session->userdata('jxcsys');
	}
	
	
	/**
	 * 得到订单类型
	 */
	public function getOrderType($type = null){
		
		if($type == 1){
			
			return array(
					"00-Cxx-00"=>'期初库存订单',
					"30-Cxx-01"=>'普通调入申请',
					"30-Cxx-02"=>'厂家直发调入申请',
					"30-Cxx-03"=>'补单调入申请',
					"30-Cxx-07"=>'当日件调入申请',
					"30-Cxx-04"=>'虚拟退货申请',
					"30-Cxx-05"=>'滞销品退货申请',
					"30-Cxx-06"=>'不良品退货申请',
			);
		}
		return array(
			    "order"=>array(
						array("id"=>"30-Cxx-01","name"=>'普通调入申请'),
						array("id"=>"30-Cxx-02","name"=>'厂家直发调入申请'),
						array("id"=>"30-Cxx-03","name"=>'补单调入申请'),
						array("id"=>"30-Cxx-07","name"=>'当日件调入申请'),
				),
				"after"=>array(
						array("id"=>"30-Cxx-04","name"=>'虚拟退货申请'),
						array("id"=>"30-Cxx-05","name"=>'滞销品退货申请'),
						array("id"=>"30-Cxx-06","name"=>'不良品退货申请'),
				)
		);
	}
	
	
	
	//库存统计
	public function get_inventoryAll($where='',$type=2) {
		$sql = 'select
		            sum(a.qty) as qty,
					a.invId,
					a.isDelete,
		            a.locationId,
					b.id,
		            b.name,
					b.number,
					b.spec,
					b.categoryId,
					b.categoryName,
					b.unitName,
					b.unitid,
					b.lowQty,
					b.highQty,
					b.skuId,
					c.name as locationName
		        from '.INVOICE_INFO.' as a
					left join
						(select
							id,name,number,spec,unitName ,unitid ,lowQty,highQty,categoryId,categoryName,skuId
						from '.GOODS.'
						where (isDelete=0) and sid in('.$this->jxcsys['sid'].',1) 
						order by id desc) as b
					on a.invId=b.id
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as c 
					on a.locationId=c.id 
				where
					(a.isDelete=0)
					'.$where.'
				';
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type);
	}
	
	
	
	//库存统计
	public function get_inventory($where='',$type=2) {
	    $sql = 'select 
		            sum(a.qty) as qty, 
					a.invId, 
					a.isDelete, 
		            a.locationId,  
		            b.name as invName,  
					b.number as invNumber,
					b.spec as invSpec, 
					b.categoryId ,
					b.categoryName,
					b.unitName,
					b.unitid,
					b.lowQty,
					b.highQty,
					c.name as locationName
		        from '.INVOICE_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName ,unitid ,lowQty,highQty,categoryId,categoryName
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as c 
					on a.locationId=c.id 
				where 
					(a.isDelete=0)
					'.$where.' 
				';
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type);		
	}	
	
	//获取库存 用于判断库存是否满足
	public function get_invoice_info_inventory_old() {
	    $sql = 'select 
		            itemId as invId, storageId as locationId,sum(num) as qty
		        from '.GOOODS_STORAGE.$this->jxcsys['sid'].' 
				group by itemId, storageId
				';
		$v = array();
		$list = $this->mysql_model->query(INVOICE_INFO,$sql,2);
		foreach($list as $arr=>$row){
		    $v[$row['invId']][$row['locationId']] = $row['qty'];
		}		
		return $v;		
	}
	
	
	//获取库存 用于判断库存是否满足--老方式
	public function get_invoice_info_inventory() {
		$sql = 'select
		            invId,locationId,sum(qty) as qty
		        from '.INVOICE_INFO.' 
		        where isDelete = 0 and  sid = '.$this->jxcsys['sid'].' 
				group by invId, locationId
				';
		$v = array();
		$list = $this->mysql_model->query(INVOICE_INFO,$sql,2);
		foreach($list as $arr=>$row){
			$v[$row['invId']][$row['locationId']] = $row['qty'];
		}
		return $v;
	}
	
	
	//按商品查询库存
	public function get_goods_info_inventory() {
		$sql = 'select
		            invId,sum(qty) as qty
		        from '.INVOICE_INFO.'
		        where isDelete = 0 and  sid = '.$this->jxcsys['sid'].'
				group by invId
				';
		$v = array();
		$list = $this->mysql_model->query(INVOICE_INFO,$sql,2);
		$v = array_bind_key($list, "invId");
		return $v;
	}
	
	
	//按货位查询出库存
	public function get_area_info_inventory() {
		$sql = 'select 
					invId,locationId,areaId,sum(qty) as qty
		        from '.INVOICE_INFO.' 
		        where isDelete = 0 and  sid = '.$this->jxcsys['sid'].' 
				group by invId , locationId , areaId
		        order by sum(qty) desc ';
		$v = array();
		$list = $this->mysql_model->query(INVOICE_INFO,$sql,2);
		
		foreach($list as $arr=>$row){
			$v[$row['invId']][$row['locationId']][$row['areaId']] = $row['qty'];
		}
		return $v;
	}
	
	

    //获取库存 用于判断库存是否满足
    public function get_sales_info_inventory() {
        $sql = 'select 
		            invId,locationId,sum(qty) as qty
		        from '.SALES_INFO.' 
		        where isDelete = 0 and  sid = '.$this->jxcsys['sid'].' 
				group by invId, locationId
				';
        $v = array();
        $list = $this->mysql_model->query(SALES_INFO,$sql,2);
        foreach($list as $arr=>$row){
            $v[$row['invId']][$row['locationId']] = $row['qty'];
        }
        return $v;
    }

    //获取单据列表
	public function get_invoice($where='',$type=2) {
	    $sql = 'select 
		            a.*,
					b.name as contactName,
					b.number as contactNo,   
					c.number as salesNo ,c.name as salesName, 
					d.number as accountNumber ,d.name as accountName   
				from '.INVOICE.' as a 
					left join 
						(select 
							id,number, name
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as b
					on a.buId=b.id 
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.salesId=c.id 
					left join 
					(select 
						id,name,number
					from '.ACCOUNT.' 
						where (isDelete=0)) as d 
					on a.accId=d.id 
				where 
					(a.isDelete=0) '.$where;
		return $this->mysql_model->query(INVOICE,$sql,$type);
	}



	//获取手工订单
    public function get_sales($where='',$type=2) {
        $sql = 'select 
		            a.*,
					b.name as contactName,
					b.number as contactNo,   
					c.number as salesNo ,c.name as salesName, 
					d.number as accountNumber ,d.name as accountName   
				from '.SALES.' as a 
					left join 
						(select 
							id,number, name
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as b
					on a.buId=b.id 
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.salesId=c.id 
					left join 
					(select 
						id,name,number
					from '.ACCOUNT.' 
						where (isDelete=0)) as d 
					on a.accId=d.id 
				where 
					(a.isDelete=0) '.$where;
        return $this->mysql_model->query(SALES,$sql,$type);

    }

    public function get_invoice_order($where='',$type=2) {
		$sql = 'select
		            a.*,
					b.name as contactName,
					b.number as contactNo,
					c.number as salesNo ,c.name as salesName,
					d.number as accountNumber ,d.name as accountName
				from '.INVOICE_ORDER.' as a
					left join
						(select
							id,number, name
						from '.CONTACT.'
						where (isDelete=0)
						order by id desc) as b
					on a.buId=b.id
					left join
						(select
							id,name,number
						from '.STAFF.'
						where (isDelete=0)
						order by id desc) as c
					on a.salesId=c.id
					left join
					(select
						id,name,number
					from '.ACCOUNT.'
						where (isDelete=0)) as d
					on a.accId=d.id
				where
					(a.isDelete=0) '.$where;
		return $this->mysql_model->query(INVOICE_ORDER,$sql,$type);
	}
	
 
	
	//获取销售订单
	public function get_invps($where='',$type=2) {
	    $sql = 'select 
		            a.*,
					b.name as contactName,
					b.number as contactNo,   
					c.number as salesNo ,c.name as salesName
				from '.INVPS.' as a 
					left join 
						(select 
							id,number, name
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as b
					on a.buId=b.id 
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.salesId=c.id 
					
				where 
					(a.isDelete=0) '.$where;
		return $this->mysql_model->query(INVPS,$sql,$type);	
	}	

	//商品收发明细表(初始数量)  作用于report.php 商品收发明细表(接口)
	public function get_goods_ini($where='',$beginDate,$endDate,$type=2) {
	    $sql = 'select 
		            a.id,
					a.iid,
					a.billNo,
					a.billType,
					a.billDate,
					a.buId,
					a.invId,
					a.transTypeName,
					a.transType,
					a.price,
					a.locationId,
		            sum(a.qty) as qty, 
					sum(a.amount) as amount,  
					sum(case when a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then a.qty else 0 end) as qty1,
					sum(case when a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then a.amount else 0 end) as amount1,
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit,b.brand_name,b.skuId,b.minNum,
					c.name as locationName ,c.locationNo
				from '.INVOICE_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName,brand_name,skuId,minNum
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as c 
					on a.locationId=c.id 
				where 
					(a.isDelete=0) 
					'.$where; 
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type); 	
	}
 
	
	//采购、销售汇总表(按商品、客户、供应商)   作用于report.php  可用于商品初期
	public function get_invoice_info_sum($where='',$type=2) {
	    $sql = 'select 
		            a.id,
					a.iid,
					a.billNo,
					a.billType,
					a.billDate,
					a.buId,
					a.invId,
					a.transTypeName,
					a.transType,
					a.price,
					a.locationId,
					a.salesId,
		            sum(a.qty) as qty, 
					sum(a.amount) as amount,  
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit, b.brand_name,b.skuId,b.minNum,
					c.number as contactNo, c.name as contactName,
					d.name as locationName ,d.locationNo ,
					e.number as salesNo ,e.name as salesName
				from '.INVOICE_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName,brand_name,skuId,minNum  
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,number, name 
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.buId=c.id 	
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as d 
					on a.locationId=d.id 
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as e
					on a.salesId=e.id 	
				where 
					(a.isDelete=0) 
					'.$where; 
	    
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type); 	
	}
	 
	
	//获取单据列表明细
	public function get_invoice_info($where='',$type=2) {
	    $sql = 'select 
		            a.*, 
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit, b.skuId,b.brand_name,b.minNum,b.title,
					c.number as contactNo, c.name as contactName,
					d.name as locationName ,d.locationNo ,
					e.number as salesNo ,e.name as salesName,f.areaNo,f.areaName
				from '.INVOICE_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName,skuId,brand_name,minNum,title 
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,number, name 
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.buId=c.id 	
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as d 
					on a.locationId=d.id
					left join 
						(select
							id,area_code as area_code,area_name as areaName,id as areaNo
						from '.STORAGE_AREA.'
						where sid = '.$this->jxcsys['sid'].'
						order by id desc) as f
					on a.areaId = f.id
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as e
					on a.salesId=e.id 	
				where 
					(a.isDelete=0) 
				'.$where;
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type); 	
	}

    //获取手工订单列表明细
    public function get_sales_info($where='',$type=2) {
        $sql = 'select 
		            a.*, 
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit, 
					c.number as contactNo, c.name as contactName,
					d.name as locationName ,d.locationNo ,
					e.number as salesNo ,e.name as salesName,f.areaNo,f.areaName
				from '.SALES_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName 
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,number, name 
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.buId=c.id 	
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as d 
					on a.locationId=d.id
					left join 
						(select
							id,area_name as areaName,id as areaNo
						from '.STORAGE_AREA.'
						order by id desc) as f
					on a.areaId = f.id
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as e
					on a.salesId=e.id 	
				where 
					(a.isDelete=0) 
				'.$where;
        return $this->mysql_model->query(SALES_INFO,$sql,$type);
    }


    public function get_invoice_order_info($where='',$type=2) {
		$sql = 'select
		            a.*,
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit,b.minNum,b.packSpec,b.brand_name,
					c.number as contactNo, c.name as contactName,
					d.name as locationName ,d.locationNo ,
					e.number as salesNo ,e.name as salesName,f.areaNo,f.areaName
				from '.INVOICE_ORDER_INFO.' as a
					left join
						(select
							id,name,number,spec,unitName,minNum,packSpec,brand_name 
						from '.GOODS.'
						where (isDelete=0)
						order by id desc) as b
					on a.invId=b.id
					left join
						(select
							id,number, name
						from '.CONTACT.'
						where (isDelete=0)
						order by id desc) as c
					on a.buId=c.id
					left join
						(select
							id,name,locationNo
						from '.STORAGE.'
						where (isDelete=0)
						order by id desc) as d
					on a.locationId=d.id
					left join
						(select
							id,area_name as areaName,id as areaNo
						from '.STORAGE_AREA.'
						order by id desc) as f
					on a.areaId = f.id
					left join
						(select
							id,name,number
						from '.STAFF.'
						where (isDelete=0)
						order by id desc) as e
					on a.salesId=e.id
				where
					(a.isDelete=0)
				'.$where;
		return $this->mysql_model->query(INVOICE_ORDER_INFO,$sql,$type);
	}
	 
	
	
	//获取销售订单明细
	public function get_invps_info($where='',$type=2) {
	    $sql = 'select 
		            a.*, 
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit, 
					c.number as contactNo, c.name as contactName,
					d.name as locationName ,d.locationNo ,
					e.number as salesNo ,e.name as salesName
				from '.INVPS_INFO.' as a 
					left join 
						(select 
							id,name,number,spec,unitName 
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,number, name 
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.buId=c.id 	
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as d 
					on a.locationId=d.id 
					left join 
						(select 
							id,name,number 
						from '.STAFF.' 
						where (isDelete=0) 
						order by id desc) as e
					on a.salesId=e.id 	
				where 
					(a.isDelete=0) 
				'.$where;
		return $this->mysql_model->query(INVPS_INFO,$sql,$type); 	
	}
	
	
	//商品库存余额表(接口)
	public function get_invBalance($where='',$select='',$type=2) {
	    //sum(case when a.locationId=1 then qty else 0 end ) as qty5,  $select
	    $sql = 'select 
		            a.invId,
		            sum(a.qty) as qty,
					sum(a.amount) as amount,
					'.$select.'
					sum(case when a.transType=150501 or a.transType=150502 or a.transType=150706 or a.billType="INI" then a.qty else 0 end) as inqty,
					sum(case when a.transType=150501 or a.transType=150502 or a.transType=150807 or a.transType=150706 or a.billType="INI" then a.amount else 0 end) as incost,
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit, b.categoryId,b.brand_name,b.skuId,b.minNum,b.packSpec,b.productCode,
					c.locationNo 
				from '.INVOICE_INFO.' as a 
					inner join 
						(select 
							id,name,number,spec,unitName,categoryId,brand_name,skuId,minNum,productCode,packSpec
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					inner join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as c 
					on a.locationId=c.id 
				where 
					(a.isDelete=0) '.$where;	
		return $this->mysql_model->query(INVOICE_INFO,$sql,$type); 
	}
		
//sum(case when a.transType=150702 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty3,	
	//获取商品收发汇总表 
	public function get_deliverSummary($where='',$beginDate,$endDate,$type=2) {
	    $sql = 'select 
		            sum(case when a.billDate<"'.$beginDate.'" then qty else 0 end ) as qty0,
					sum(case when a.billDate<"'.$beginDate.'" then amount else 0 end ) as cost0,
					
					sum(case when (transType=150501 or transType=150502 or transType=150807 or transType=150706 or billType="INI") and a.billDate<"'.$beginDate.'" then amount else 0 end) as incost0,
					sum(case when (transType=150501 or transType=150502 or transType=150706 or billType="INI") and a.billDate<"'.$beginDate.'" then qty else 0 end)  as inqty0,
					
					sum(case when (transType=150501 or transType=150502 or transType=150807 or transType=150706 or billType="INI") and a.billDate<="'.$endDate.'" then amount else 0 end) as incost14,
					sum(case when (transType=150501 or transType=150502 or transType=150706 or billType="INI") and a.billDate<="'.$endDate.'" then qty else 0 end)  as inqty14,
					
		            sum(qty) as qty14,
					sum(case when a.transType=150501 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty2,
					sum(case when a.transType=150501 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost2,
					sum(case when a.transType=150502 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty9,
					sum(case when a.transType=150502 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost9,
					sum(case when a.transType=150601 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty10,
					sum(case when a.transType=150601 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost10,
					sum(case when a.transType=150602 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty3,
					sum(case when a.transType=150602 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost3,
					sum(case when a.transType=150701 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty4,
					sum(case when a.transType=150701 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost4,
					
					sum(case when a.transType=150801 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty11,
					sum(case when a.transType=150801 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost11,
					sum(case when a.transType=103091 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" and qty>0 then qty else 0 end ) as qty1,
					sum(case when a.transType=103091 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" and qty>0 then amount else 0 end ) as cost1,
					sum(case when a.transType=103091 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" and qty<0 then qty else 0 end ) as qty8,
					sum(case when a.transType=103091 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" and qty<0 then amount else 0 end ) as cost8,
					sum(case when a.transType=150807 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty6,
					sum(case when a.transType=150807 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost6,
					sum(case when a.transType=150706 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'"then qty else 0 end ) as qty5,
					sum(case when a.transType=150706 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost5,
					sum(case when a.transType=150806 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then qty else 0 end ) as qty12,
					sum(case when a.transType=150806 and a.billDate>="'.$beginDate.'" and a.billDate<="'.$endDate.'" then amount else 0 end ) as cost12,
					a.transType,
					a.invId,
					a.locationId,
					b.name as invName, b.number as invNumber, b.spec as invSpec, b.unitName as mainUnit,b.brand_name,b.skuId,b.minNum,
					d.name as locationName ,d.locationNo 
				from '.INVOICE_INFO.' as a 
				    left join 
						(select 
							id,name,number,spec,unitName,brand_name,skuId,minNum  
						from '.GOODS.' 
						where (isDelete=0) 
						order by id desc) as b 
					on a.invId=b.id 
					left join 
						(select 
							id,number, name 
						from '.CONTACT.' 
						where (isDelete=0) 
						order by id desc) as c
					on a.buId=c.id 	
					left join 
						(select 
							id,name,locationNo 
						from '.STORAGE.' 
						where (isDelete=0) 
						order by id desc) as d 
					on a.locationId=d.id 
				where 
					(a.isDelete=0) 
				'.$where;
		return $this->mysql_model->query(INVOICE_INFO,$sql,2); 
	}
	
	
	
	//获取供应商客户      用于应付账、收账明细、客户、供应商对账单、来往单位欠款 (期初余额) 对应多个接口
	public function get_contact($where1='',$where2='',$type=2) {
	    $sql = 'select 
					a.id,
					a.type,
					a.difMoney,
					a.difMoney + ifnull(b.arrears,0) as amount,
					a.name,
					a.number,
					b.arrears
				from '.CONTACT.' as a 
				left join 
					(select 
					    buId,
					    billType,
						sum(arrears) as arrears
					from '.INVOICE.' 
						where (isDelete=0) 
						'.$where1.'
					group by buId) as b 
			    on a.id=b.buId  
				where 
					(a.isDelete=0) 
				and a.sid in('.$this->jxcsys['sid'].',1)				
				'.$where2.'';
		return $this->mysql_model->query(INVOICE,$sql,$type); 	
	}
	

	//获取结算用户     现金银行报表(期初余额)
	public function get_account($where1='',$where2='',$type=2) {
	    $sql = 'select 
		            a.id,
					a.name as accountName,
					a.number as accountNumber,
					a.date,
					a.type,
		            b.payment,
					(a.amount + ifnull(b.payment,0)) as amount
		        from '.ACCOUNT.' as a 
				left join 
				    (select 
					    accId,
						billDate,
					    sum(payment) as payment 
					from '.ACCOUNT_INFO.' 
					where 
						(isDelete=0) 
						'.$where1.' 
					GROUP BY accId) as b 
			    on a.id=b.accId  
				where (isDelete=0) '.$where2;
		return $this->mysql_model->query(ACCOUNT_INFO,$sql,$type);		
	}	
	

	//获取结算明细     用于其他收支明细表、现金银行报表(明细)
	public function get_account_info($where='',$type=2) {
	    $sql = 'select 
		            a.id,
					a.iid,
					a.accId,
		            a.buId,
		            a.isDelete,
		            a.billType,
					a.billNo,
					a.remark,
					a.billDate,
					a.payment,
					a.wayId,
					a.settlement,
					a.transType,
					a.transTypeName,
					b.name as contactName,
					b.number as contactNo,
					c.name as categoryName,
					d.name as accountName,
					d.number as accountNumber    
				from '.ACCOUNT_INFO.' as a 
				left join 
					(select 
						id,name,number
					from '.CONTACT.' 
						where (isDelete=0)) as b 
					on a.buId=b.id 
				left join 
					(select 
						id,name
					from '.CATEGORY.' 
						where (isDelete=0)) as c 
					on a.wayId=c.id 
				left join 
					(select 
						id,name,number
					from '.ACCOUNT.' 
						where (isDelete=0)) as d 
					on a.accId=d.id 
				where (a.isDelete=0) '.$where;	
		return $this->mysql_model->query(ACCOUNT_INFO,$sql,$type); 	
	}

    //获取结算明细     用于其他收支明细表、现金银行报表(明细)
    public function get_sales_accountInfo($where='',$type=2) {
        $sql = 'select 
		            a.id,
					a.iid,
					a.accId,
		            a.buId,
		            a.isDelete,
		            a.billType,
					a.billNo,
					a.remark,
					a.billDate,
					a.payment,
					a.wayId,
					a.settlement,
					a.transType,
					a.transTypeName,
					b.name as contactName,
					b.number as contactNo,
					c.name as categoryName,
					d.name as accountName,
					d.number as accountNumber    
				from '.SALES_ACCOUNTINFO.' as a 
				left join 
					(select 
						id,name,number
					from '.CONTACT.' 
						where (isDelete=0)) as b 
					on a.buId=b.id 
				left join 
					(select 
						id,name
					from '.CATEGORY.' 
						where (isDelete=0)) as c 
					on a.wayId=c.id 
				left join 
					(select 
						id,name,number
					from '.ACCOUNT.' 
						where (isDelete=0)) as d 
					on a.accId=d.id 
				where (a.isDelete=0) '.$where;
        return $this->mysql_model->query(SALES_ACCOUNTINFO,$sql,$type);
    }
	
	
	
	
	//获取期初 结存数和成本
	public function get_invoice_info_ini($where='',$type=2) {
	    $sql = 'select
	    			id,
		            invId,
					locationId,
					billDate,
					sum(qty) as qty,
					sum(case when transType=150501 or transType=150502 or transType=150807 or transType=150706 or billType="INI" then amount else 0 end) as incost,
					sum(case when transType=150501 or transType=150502 or transType=150706 or billType="INI" then qty else 0 end) as inqty
				from '.INVOICE_INFO.' 
				where 
					(isDelete=0) and sid = '.$this->jxcsys['sid'].' '.$where;
		$v = array();
		$list = $this->mysql_model->query(INVOICE_INFO,$sql,2);
		foreach($list as $arr=>$row){
		    $v['qty'][$row['invId']]   = $row['qty'];      //结存数量（时间段） 
		    $v['inqty'][$row['invId']]  = $row['inqty'];   //期初入库数量
			$v['incost'][$row['invId']] = $row['incost'];  //期初入库成本
			$v['inunitcost'][$row['invId']] = $row['inqty']>0 ? $row['incost']/$row['inqty'] :0;  //期初单位成本	
		}		
		return $v;		
	}
	
	//获取商品明细excel
	public function get_goods_excel($where='',$type=2,$modelwhere='') {
		$sql = 'select
					a.*
			
				from '.GOODS.' as a ';
		$sql .= $where;
		 
		return $this->mysql_model->query(GOODS,$sql,$type);
	}
	
	//获取商品明细  
	public function get_goods($where='',$type=2,$modelwhere='') {
	    $sql = 'select 
					a.*,
					b.iniqty,
					b.iniunitCost,
					b.iniamount,
					b.totalqty,
	    		    c.area_id,
	    		    c.area_name,
	    			d.retailPrice,
	    			f.settle_price_p as purPrice2,
	    			g.settle_price  as purPrice3
				from '.GOODS.' as a 
				left join 
					(select 
						invId,
						sum(qty) as totalqty, 
						sum(case when billType="INI" then qty else 0 end) as iniqty,
						sum(case when billType="INI" then price else 0 end) as iniunitCost,
						sum(case when billType="INI" then amount else 0 end) as iniamount
					from '.INVOICE_INFO.' 
						where (isDelete=0) and sid = '.$this->jxcsys['sid'].'
					group by invId) as b
				on a.id = b.invId
		        left join (select str_id,id as area_id,area_name from '.STORAGE_AREA.' LIMIT 1) as c on a.locationId=c.str_id
		        left join (select distinct retailPrice,goodsId from '.RETAILPRICE.' where sid = '.$this->jxcsys['sid'].') as d on a.id = d.goodsId
		        left join (select skuId,settle_price as settle_price_p,sale_model from '.GOODS_INFO_P.') as f on a.skuId = f.skuId and a.saleModel = f.sale_model		
		       	left join (select skuId,settle_price,sale_model from '.GOODS_INFO.' where sid = "'.$this->jxcsys['sid'].'") as g on a.skuId = g.skuId and a.saleModel = g.sale_model
				where  (a.isDelete=0) and (a.saleType=0) ';
	    
	    /* if(!empty($modelwhere)){
	    	$sql .= ' and a.match_id IN ( SELECT DISTINCT c.match_id FROM '.MODELSMATCH_RELATIONSHIP_COMP.' c
						WHERE
						c.compress_id IN (
							SELECT DISTINCT d.compress_id FROM '.MODELSMATCH_INFO_COMP.' d
							WHERE 1 = 1 '.$modelwhere.'
						)
					) ';
	    } */
	    $sql .= $where;
		return $this->mysql_model->query(GOODS,$sql,$type); 	
	}
	
	public function get_goods_info($where='',$type=1) {
		
		$sql = 'select
					a.*,
	    		    c.area_id,
	    		    c.area_name,
	    			d.retailPrice,
	    			IFNULL(e.storageSum,0) as storageSum,
	    			f.settle_price_p as purPrice2,
	    			g.settle_price  as purPrice3
				from '.GOODS.' as a
		        left join (select str_id,id as area_id,area_name from '.STORAGE_AREA.' LIMIT 1) as c on a.locationId=c.str_id
		        left join (select retailPrice,goodsId from '.RETAILPRICE.' where sid = '.$this->jxcsys['sid'].') as d on a.id = d.goodsId
		        left join (select itemId,sum(num) as storageSum from '.GOOODS_STORAGE.$this->jxcsys['sid'].' group by itemId ) as e on a.id = e.itemId
		        left join (select skuId,settle_price as settle_price_p,sale_model from '.GOODS_INFO_P.') as f on a.skuId = f.skuId and a.saleModel = f.sale_model
		       	left join (select skuId,settle_price,sale_model from '.GOODS_INFO.' where sid = "'.$this->jxcsys['sid'].'") as g on a.skuId = g.skuId and a.saleModel = g.sale_model
				where
					(a.isDelete=0) and (a.saleType=0) ';
		
		$sql .= $where;
		
		return $this->mysql_model->query(GOODS,$sql,$type);
	}
	 

	//获取快准商品明细
	//$modelwhere  品牌，车系，车型，年款，排量
	//$where 分类，搜索关键字
	public function get_goods_kz($modelwhere,$where='',$type=2) {
		/* $sql = 'SELECT DISTINCT c.sku_id,a.* FROM '.SYSITEM_ITEM.' a
				 INNER JOIN '.SYSITEM_SKU.' c on c.item_id = a.item_id
				 INNER JOIN '.MODELSMATCH_RELATIONSHIP_COMP.' b on b.match_id = a.match_id
				 INNER JOIN '.MODELSMATCH_INFO_COMP.' d on d.compress_id = b.compress_id '.$modelwhere.' WHERE 1=1 AND a.match_id is not null  '.$where ;  */
		
		if($modelwhere==""){
			$kzsql = 'SELECT k.sku_id,a.* FROM '.SYSITEM_ITEM.' a
						INNER JOIN '.SYSITEM_SKU.' k on a.item_id = k.item_id
					where 1=1 '.$where;
			
		}else{
			$kzsql = 'SELECT
							a.*, k.sku_id
						FROM
							'.SYSITEM_ITEM.' a
						INNER JOIN '.SYSITEM_SKU.' k ON k.item_id = a.item_id
						WHERE
							a.match_id IN (
								SELECT DISTINCT
									c.match_id
								FROM
									'.MODELSMATCH_RELATIONSHIP_COMP.' c
								WHERE
									c.compress_id IN (
										SELECT DISTINCT
											d.compress_id
										FROM
											'.MODELSMATCH_INFO_COMP.' d
										WHERE
											1 = 1
										'.$modelwhere.'
									)
							) '.$where;
		}
		
		return $this->mysql_model->query(SYSITEM_ITEM,$kzsql,$type);
	}
	
	public function get_goods_kz2($modelwhere,$where='',$type=2) {
		if($modelwhere==""){

			$sql = 'SELECT a.* FROM '.GOODS.' a where a.sid = 1 and a.saleType = 0 '.$where;
				
		}else{
			$sql = 'SELECT
							a.*
						FROM
							'.GOODS.' a 
						WHERE a.sid = 1 
							and a.saleType = 0
							and a.match_id IN (
								SELECT DISTINCT
									c.match_id
								FROM
									'.MODELSMATCH_RELATIONSHIP_COMP.' c
								WHERE
									c.compress_id IN (
										SELECT DISTINCT
											d.compress_id
										FROM
											'.MODELSMATCH_INFO_COMP.' d
										WHERE
											1 = 1
										'.$modelwhere.'
									)
							) '.$where;
		}
		/* echo $sql;
		exit; */
		return $this->mysql_model->query(SYSITEM_ITEM,$sql,$type);
	}
	
	
	/**
	 * 得到车型所有数据
	 * @param string $where
	 * @param number $type
	 */
	public function getModelsAll($where='',$type=2){
		
		$models = $this->get_Models2($where,$type);
		$m_year = $this->get_ModelYears2($where,$type);
		$displacements = $this->get_Displacements2($where,$type);
		
		$resultY = array();
		$resultD = array();
		
		foreach ($models as $key => $value){
			
			$list1 = array();
			$list2 = array();
			
			foreach ($m_year as $key3 => $value3){
				if($value['id'] ==  $value3['parent']){
					$list1[] = $value3;
				}
			}
			foreach ($displacements as $key4 => $value4){
				if($value['id'] ==  $value4['parent']){
					$list2[] = $value4;
				}
			}
			$resultY[$value['id']] = $list1;
			$resultD[$value['id']] = $list2;
		}

		return array('models'=>$models,'years'=>$resultY,'dis'=>$resultD);
		
	}
	
	//获得车型品牌
	public function get_modelBrands($where='',$type=2) {
		//$sql = 'SELECT DISTINCT brand as id,concat(brand,"",) FROM  '.MODELSMATCH_INFO_COMP.' where 1=1 '.$where.' group by brand ORDER BY brand_sort';
		$sql = 'SELECT DISTINCT brand as id , concat(brand_sort," ",brand) as name FROM  '.MODELSMATCH_INFO_COMP.' where 1=1 '.$where.' group by brand ORDER BY brand_sort';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得车系
	public function get_Cars($where='',$type=2) {
		$sql = 'SELECT DISTINCT brand as parent, cars as id,cars as name FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by cars ORDER BY cars';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得车型
	public function get_Models($where='',$type=2) {
		$sql = 'SELECT DISTINCT cars as parent, models as id,models as name FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by models ORDER BY models';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得车型2
	public function get_Models2($where='',$type=2) {
		$sql = 'SELECT DISTINCT models as id,CONCAT(brand," ",models) as name FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by models ORDER BY brand_sort,models';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得年份
	public function get_ModelYears($where='',$type=2) {
		$sql = 'SELECT DISTINCT cars as parent, model_year as id,model_year as name  FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by cars,model_year ORDER BY cars,model_year desc';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得年份2
	public function get_ModelYears2($where='',$type=2) {
		$sql = 'SELECT DISTINCT models as parent, model_year as id,model_year as name  FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by models,model_year ORDER BY models,model_year desc';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得排量
	public function get_Displacements($where='',$type=2) {
		$sql = 'SELECT DISTINCT  cars as parent, displacement as id,displacement as name   FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by cars,displacement ORDER BY cars,displacement';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}
	
	//获得排量2
	public function get_Displacements2($where='',$type=2) {
		$sql = 'SELECT DISTINCT  models as parent, displacement as id,displacement as name   FROM  '.MODELSMATCH_INFO_COMP.' where 1=1  '.$where.' group by models,displacement ORDER BY models,displacement';
		return $this->mysql_model->query(MODELSMATCH_INFO_COMP,$sql,$type);
	}

	//采购订单详情
	public function get_order_list($where='',$type=2){
		$sql = 'select
					a.id,a.billNo,b.invId,b.qty,b.price,b.amount,b.billDate,c.name,a.nid,a.auditOpinion,c.brand_name,c.skuId,c.number,c.spec,c.unitName,c.packSpec 
				from '.INVOICE_ORDER.' as a
				left join
					(select
						invId,qty,price,amount,billDate,iid 
					from '.INVOICE_ORDER_INFO.'
						where (isDelete=0)) as b
				on a.id = b.iid
				left join
					(select
						name,id,brand_name,skuId,number,spec,unitName,packSpec 
					from '.GOODS.'
						where (isDelete=0)) as c
				on b.invId = c.id
				where 
					(a.isDelete=0) '.$where;
		return $this->mysql_model->query(INVOICE_ORDER,$sql,$type);
 	}

 	public function get_out_goods_new($where='',$type=2){
 		$sql = 'select
					a.*,b.name,b.brand_name,b.skuId,b.number,b.spec
				from '.ORDER_DETAIL_ACCEPT.' as a
				left join
					(select
						name,id,brand_name,skuId,number,spec
					from '.GOODS.'
						where (isDelete=0)) as b
				on a.invId = b.id
				where  '.$where . '
				';
 		return $this->mysql_model->query(ORDER_DETAIL,$sql,$type);
 	}
 	
 	public function get_out_goods($where='',$type=2){
		$sql = 'select
					a.*,b.name,b.brand_name,b.skuId,b.number,b.spec
				from '.ORDER_DETAIL.' as a
				left join
					(select
						name,id,brand_name,skuId,number,spec
					from '.GOODS.'
						where (isDelete=0)) as b
				on a.invId = b.id
				where  '.$where . '
				';
		return $this->mysql_model->query(ORDER_DETAIL,$sql,$type);
 	}

 	public function get_last_goods($where='',$type=2){
 		$sql = 'select
 					a.*,b.name,b.brand_name,b.skuId,b.number,b.spec
				from '.ORDER_TOTAL.' as a
				left join
					(select
						name,id,brand_name,skuId,number,spec
					from '.GOODS.'
						where (isDelete=0)) as b
				on a.invId = b.id
				where  '.$where;
		return $this->mysql_model->query(ORDER_TOTAL,$sql,$type);
 	}

 	public function get_return_goods_detail($where='',$type=2){
 		$sql = 'select
 					a.*,b.rttime,b.rtOrderId,b.status,c.name,c.brand_name,c.skuId,c.number,c.spec
				from '.RETURN_GOODS_DETAIL.' as a
				left join
					(select
						status,id,rttime,rtOrderId
					from '.RETURN_GOODS.'
						where (isDelete = 0)) as b
				on a.rtId = b.id
				left join
					(select
						name,id,brand_name,skuId,number,spec
					from '.GOODS.'
						where (isDelete = 0)) as c
				on a.invId = c.id
				where  '.$where;
				
		return $this->mysql_model->query(RETURN_GOODS_DETAIL,$sql,$type);
 	}
 	
 	
 	/**
 	 * 查询当前结算价
 	 * @param string $where
 	 * @param number $type
 	 */
 	public function get_goodsInfo_settle($where='',$type=2){
 		$sql = 'SELECT
				g.id,
				g.skuId,
				g.productCode,
				g.`NAME`,
				g.skuId,
				g.spec,
				g.purPrice,
 				g.saleModel,
 				g.number,
 				g.brand_name,
 				g.unitId,
				IFNULL(
					gip.settle_price,
					IFNULL(gi.settle_price, g.purPrice)
				) AS settle_price
			FROM
				zee_goods g
			LEFT JOIN zee_goods_info gi ON g.skuId = gi.skuId
			LEFT JOIN zee_goods_info_public gip ON g.saleModel = gip.sale_model
			AND g.skuId = gip.skuId
			WHERE
				g.sid = 1 
 				'.$where;
 		return $this->mysql_model->query(GOODS,$sql,$type);
 	}
 	
 	
 	//得到订单最近结算价
 	public function get_settle_price($where='',$type=2){
 		$sql = 'select invId,price from '.INVOICE_ORDER_INFO.' where (isDelete=0) and  sid='.$this->jxcsys['sid'].' '.$where.' group by invId order by id';
 		return $this->mysql_model->query(INVOICE_ORDER_INFO,$sql,$type);
 	}
 	
 	//得到入库单或出库单最近结算价
 	public function get_settle_price2($where='',$type=2){
 		$sql = 'select invId,price from '.INVOICE_INFO.' where (isDelete=0) and sid='.$this->jxcsys['sid'].' '.$where.' group by invId order by id desc';
 		return $this->mysql_model->query(INVOICE_INFO,$sql,$type);
 	}
 	
 	//得到入库单或出库单最近结算价
 	public function get_settle_price3($where='',$type=2){
 		$sql = 'select invId,buId,price from '.INVOICE_INFO.' where (isDelete=0) and sid='.$this->jxcsys['sid'].' '.$where.' group by invId,buId order by id desc';
 		$rs  = $this->mysql_model->query(INVOICE_INFO,$sql,$type);
 		foreach ($rs as $key=>$value){
 			$return[$value['invId']][$value['buId']] = $value['price'];
 		}
 		return $return;
 	}
 	
}