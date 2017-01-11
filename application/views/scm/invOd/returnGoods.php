<?php $this->load->view('header');?>
 

<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
//ctrl+F5 增加版本号来清空iframe的缓存的
$(document).keydown(function(event) {
	/* Act on the event */
	if(event.keyCode === 116 && event.ctrlKey){
		var defaultPage = Public.getDefaultPage();
		var href = defaultPage.location.href.split('?')[0] + '?';
		var params = Public.urlParam();
		params['version'] = Date.parse((new Date()));
		for(i in params){
			if(i && typeof i != 'function'){
				href += i + '=' + params[i] + '&';
			}
		}
		defaultPage.location.href = href;
		event.preventDefault();
	}
});
</script>

<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
<style>
	em{ olor: #C81623;font-size: 19px}
	.fl{float: left;}.fr{float: right;}
	html,body{height: 100%;}
	.box{width: 100%;height: 100%;color: #999;background-color: #fff; }
	.main { padding: 0 0 0;background-color: #fff;overflow: hidden;padding-bottom: 30px; }
	.main table{ border-collapse: collapse;width: 100%;margin: 10px auto;text-align: center; }
	.table-a thead td{ background-color: #e8e8e8; }
	.table-a td{ padding: 4px 3px; }
	.table-a td:nth-child(1){ width: 45%; }
	.table-a td:nth-child(2){ width: 10%; }
	.table-a td:nth-child(3){ width: 10%; }
	.table-a td:nth-child(4){ width: 10%; }
	.table-a td:nth-child(5){ width: 10%; }
	.table-a,.table-a tr,.table-a td{ border:1px solid #ddd; }
	.btn{ padding: 6px 20px;border-radius: 3px;cursor:pointer;color: #fff;background-color: #F8AC59;display: inline-block;float: right;}
	
	.num_change{overflow: hidden;margin:0 auto;width: 120px;}
	.change_btn{width: 25px;height: 25px;line-height: 23px;border:1px solid #ddd;border-radius: 50%;cursor:pointer;-moz-user-select:none;}
	.change{width: 45px;height: 25px;border:1px solid #ddd;border-radius: 4px;margin-left: 7px;line-height: 25px;padding-left:5px;}
	.leibie{width: 62%;float: right;text-align: left;border: 1px solid #ccc;margin-right: 15px;position: absolute;top: 30px;right: 23px;z-index: 10000;display: none;}
	.show{ display: block; }
	
	.leibie li{padding:5px 0 5px 5px;background-color: #fff;}
	.leibie li.on{ background-color: #d2d2d2; }
	.leibie li:hover{background-color: #eee;}
	.trigger_c{ cursor: pointer; }
	.danju { position:relative;width:20%;display:block;float:right;margin-right:5%;color: #555;}
</style>
</head>
<body>

	<div class="wrapper">
		 <div class="bills cf">
		    <div class="con-header">
		      <dl class="cf">
		        <dd class="pct24 danju">
					<label>单据类型:</label>
		          	<span class="ui-combo-wrap" style="padding:0;">
		            	<input type="text" class="input-txt" id='leibie' leibie='30-Cxx-04' readonly='readonly'>
		            	<i class="trigger trigger_c"></i>
		          	</span>
		          	<ul class="leibie ">
		            	<li lbId="30-Cxx-04" class="on">虚拟退货申请</li>
		            	<li lbId="30-Cxx-05">滞销品退货申请</li>
		            	<li lbId="30-Cxx-06">不良品退货申请</li>
		          	</ul>
		        </dd>
		      </dl>
		    </div>
		    <div class="main">
		    	<table class="table-a">
					<thead>
						<tr>
							<td>商品</td>
							<td>商品库存</td>
							<td>子仓入库数量</td>
							<td>可退货数量</td>
							<td>退货数量</td>
						</tr>					
					</thead>
					<tbody>
						<?php  
							foreach ($returnGoods as $key => $value) {
						?>
						<tr>
							<td align="left"><?php echo $value['goods']['number'] ." ".$value['goods']['brand_name']." ".$value['goods']['skuId']." ". $value['goods']['name']." ".$value['goods']['spec']?></td>
							<td><?php echo $value['goods_storage']?></td>
							<td><?php echo $value['haveInto']?></td>
							<td><?php echo $value['maxNum']?></td>
							<td>
								<div class="num_change" maxNum=<?php echo $value['maxNum']?>>
									<em class="fl change_btn btn_left" unselectable="on" onselectstart="return false;">-</em>
									<input type="text" name="return_goods" class="fl change" placeholder="0" iid="<?php echo $value['iid']?>" invId="<?php echo $value['invId']?>"></input>
									<em class="fr change_btn btn_right" unselectable="on" onselectstart="return false;">+</em>
								</div>
							</td>
						</tr>
						<?php 
							}
						?>
					</tbody>
				</table>
		    </div>
		    <div class = 'con-footer cf'>
		    	<div class="btn">提交</div>
		    </div>
		 </div>
	</div>
<script src="<?php echo base_url()?>/statics/js/dist/returnGoods.js?ver=20161018"></script>
</body>
</html>


