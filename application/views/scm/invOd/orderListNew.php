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
	 body,html{height:100%;}
	 em{ olor: #787878;}
	 .bills{    padding: 15px;font-size: 13px;}
	.box {height: auto;margin: 0 auto;text-align: center;overflow: hidden; }
	.main { background-color: #fff;padding:0px 0px 10px 0px;}
	.main .sub-title{padding:6px 0px 6px 0px;text-align:left; border-bottom: 1px solid #ddd; margin-bottom: 10px}
	.main table { border-collapse: collapse; width: 99%; margin:10px auto 0px auto; font-size: 12px;position: relative;}
	
	.main .table-wapper{border:1px solid red;border:none;}
	.table-a thead td{ background-color: #f0f0f0; font-weight: bold;}
	.table-b thead td{ background-color: #e8e8e8; font-weight: bold;}
	.table-a td{ padding: 0px 5px ;height:30px;}
	.table-b td{ padding: 0px 5px ;height:30px;}
	.table-a tr.row-title td{background:#f0f0f0;}
  	.table-a td:nth-child(1){ width: 3%; }
	.table-a td:nth-child(2){ width: 37%;}
	.table-a td:nth-child(3){ width: 12%; }
	.table-a td:nth-child(4){ width: 8%; }
	.table-a td:nth-child(5){ width: 8%;}
	.table-a td:nth-child(6){ width: 8%;}
	.table-a td:nth-child(7){ width: 8%;}
	.table-a td:nth-child(8){ width: 8%;}
	.table-a,.table-a tr,.table-a td{ border:1px solid #e8e8e8; color:#686868; }
	.table-b,.table-b tr,.table-b td{ border:1px solid #e8e8e8; color:#686868;}
	.btn{ position: absolute;top: 8px;left: 8px;padding: 5px 12px;border-radius: 3px;cursor:pointer;color: #fff;background-color: #F8AC59 }
	.btnp{ position: absolute;top: 8px;left: 8px;padding: 5px 12px;border-radius: 3px;cursor:pointer;color: #fff;background-color: lightskyblue }
	.b-title{margin-left:1%;}
	.arrow{width: 4px;height: 40px;background-color: #ccc;position:absolute;top: -50px;;left:calc(50% - 3px);}
	.arrow-top{transform: rotate(33deg);}
	.arrow-down{transform: rotate(-33deg);}
	.arrow-top:before{
		content: '';
		width: 0;height: 0;
		position: absolute;
		top: -18px;left: -7px;
		border-style: solid;
    	border-width: 9px;
     	border-color: transparent transparent #ccc transparent;
	}
	.arrow-down:after{
		content: '';
		width: 0;height: 0;
		position: absolute;
		bottom: -18px;left: -7px;
		border-style: solid;
    	border-width: 9px;
     	border-color: #ccc transparent transparent transparent;
	}
	select{
		border: 1px solid #ccc;
		width: 100%;
		padding:2px;
	}
	select option{width: 90%;width:100px;}
	.ui-btn{
		padding:0px 15px;
		font-size: 13px;
		height: 28px;
		line-height: 28px;
	}
	.ui-tab {
    	border-left: none;
    	border-bottom: 2px solid #f08200;
    	background-color: #fff;
    	overflow: hidden;
    	position: absolute;
    	width:100%;
    	top:0px;
    	left:0px;
	}
	.ui-tab li,.ui-tab li.cur{
		border-bottom:none;
		height:30px;
		line-height: 30px;
	}
	.ui-tips-gray{
		color:#aaa;
	}
	.table-a-row:hover,.table-a-hover{
		position:relative;
		-webkit-box-shadow: 0 0 2px 2px rgba(136, 136, 136, 0.1);
    	-moz-box-shadow: 0 0 2px 2px rgba(136, 136, 136, 0.1);
    	box-shadow: 0 0 2px 2px rgba(136, 136, 136, 0.1);
	}
	.sotrageNum{
		font-weight:bolder !important;
  		color:#c81623 !important;
	}
	
</style>
</head>
<body>

<div class = "wrapper">
	<div class = "bills">
		<div class="hd cf">
			<ul class="ui-tab" id="tab">
				<li data-type="info" class="cur">订单信息2</li>
		        <li data-type="storage" id="tab-storage">入库信息</li>
			</ul>
		</div>
		<br/>
		
		<div class="con-header">
	      <dl class="cf">
	       	<dd class="pct25">
	          <label>订单编号：</label>
	          <?php echo $nid ?>
	          <input type = "hidden" name="oid"  id="oid" value="<?php echo $orderid ?>"/>
	       	</dd>
	        <dd class="pct25">
	        	<label>订单件数：</label><?php echo $sum ?>
	        </dd>
	        <dd class="pct25">
	        	<label>下单时间：</label><?php echo $billDate ?>
	        </dd>
	        <dd class="pct25">
	          <label>采购金额：</label>
	          <span style="color: #FF851B !important;font-weight: bold;">
	          <?php echo $totalAmount ?>
	          </span>
	       	</dd>
	      </dl>
	      <dl class="cf">

	      </dl>
	      <dl class="cf">
	      		<dd class="pct25">
		        	<label class="label">订单状态：</label>
	                <span style="color: #FF851B !important ;font-weight: bold;">
	                    <em>
							<?php if($orderStatus == 1){?>
								待审核
							<?php }else if ($orderStatus == 2){?>
								审核中
							<?php }else if ($orderStatus == 3){?>
								审核未通过
							<?php }else if ($orderStatus == 4){?>
								待出库
							<?php }else if ($orderStatus == 5){?>
								<?php if ($outkuStatus == 1){?>
									出库(部分出库)
								<?php }else if ($outkuStatus == 2){?>
									出库(全部出库)
								<?php }else if ($outkuStatus == 3){?>
									出库(部分入库)
								<?php  }?>
							<?php }else if ($orderStatus == 6){?>
								已入库
							<?php  }?>
						</em>
	                 </span>
	        	</dd>
		       <?php if($auditOpinion){?>
		      	 <dd class="pct25">
					<div>审核意见：<?php echo $auditOpinion ?></div>
				</dd>
			   <?php }?>
	      </dl>
	    </div>
	    <?php if($orderStatus > 3){?>
	    <div class="box hide" id="storage">
			<div class="main fl pct60 mt10">
				<div class = "sub-title">
					<em style="font-size: 14px;font-weight: bolder;">待入库商品</em>
				</div>
				<div style = "text-align:left;">
					<a class="ui-btn ui-btn-sp" id="addStorage">全部入库</a>
				</div>
				<div class = "table-wapper">
					<table class="table-a">
						<thead>
							<tr>
								<td>
									<label class = "<?php if ($orderStatus == 6) echo 'hide'?>"><input type="checkbox" id="checkboxAll" onclick="checkAll(this)"><span class="text"></span></label>
								</td>
								<td align="left">商品</td>
								<td>箱号</td>
								<td>订单总数</td>
								<td>入库数量</td>
								<td>出库数量</td>
								<td>入库仓库</td>
								<td>入库货位</td>
								<td>状态</td>
							</tr>
						</thead>
					</table>
					<?php foreach ($accept as $ak => $av) {?>
						<table class="table-a table-a-row">
							<tr class = "row-title">
								<td style = "border-right: 1px solid #f0f0f0">
									<label class = "<?php if ($av['status'] == 2 || $av['status'] == 3) echo 'hide' ?>">
										<input type="checkbox" onclick="checkAllb(this)" acceptId="<?php echo $av['id']?>" wid="<?php echo $av['wid']?>" class="checksubsubAll checksubAll_<?php echo $av['id']?>" for="checkbox_<?php echo $av['id']?>"/>
										<span class="text"></span>
									</label>
								</td>
								<td colspan="8" align="left">
									<span class="ui-tips-gray"><?php echo dateTime($av['time'])?></span>
									&nbsp;&nbsp;&nbsp;
									<span class="ui-tips-gray">出库单号：</span>
									<span><?php echo $av['wid']?></span>
									&nbsp;&nbsp;&nbsp;
									
									<span>状态：</span>
									<?php if($av['status'] == 0) {?>
										<span class="ui-label ui-label-important">未入库</span>
									<?php } else if($av['status'] == 1){ ?>
										<span class="ui-label ui-label-warning">部分入库</span>
									<?php } else if($av['status'] == 2){ ?>
										<span class="ui-label ui-label-success">全部入库</span>
									<?php } else if($av['status'] == 3){ ?>
										<span class="ui-label ui-label-default">已取消</span>
									<?php }?>
									
									<a class="min-btn fr btn-success btn-print" acceptId = "<?php echo $av['id']?>"><i class="fa fa-print"></i>打印单据</a>
									<?php if($av['status'] == 0 || $av['status'] == 1) {?>
										<a class="min-btn fr btn-primary mr5 btn-storage" acceptId = "<?php echo $av['id']?>" wid="<?php echo $av['wid']?>"><i class="fa fa-home"></i>入库</a>
									<?php }?>
								</td>
							</tr>
							<tbody>
							<?php foreach ($av['detail'] as $key => $value) { ?>
								<tr>
									<td>
										<label class = "checkbox_<?php echo $av['id'] ?> <?php if ($value['status'] == 1 || $value['status'] == 2) echo 'hide' ?>">
											<input type="checkbox" name="into_goods" all="checksubAll_<?php echo $av['id']?>" onclick="checkDetail(this,'checkbox_<?php echo $av['id'] ?>')"
												id="<?php echo $value['id']?>"
												goods="<?php echo $value['number'] ." ".$value['brand_name'] ." ". $value['name']." ".$value['spec']?>"
												boxNum="<?php echo $value['boxNum']?>"
												totalNum="<?php echo $value['totalNum']?>"
												haveInto="<?php echo $value['inNum']?>"
												outNum="<?php echo $value['outNum']?>"
												ck="<?php echo $value['storageId']?>"
												ckName="<?php echo $value['storageName']?>"
												areaId="<?php echo $value['areaId']?>"
												areaName="<?php echo $value['areaName']?>"
											/>
											<span class="text"></span>
										</label>
									</td>
									<td align="left">
										<?php echo $value['number'] ." ".$value['brand_name']." ".$value['skuId']." ". $value['name']." ".$value['spec']?>
									</td>
									<td><?php echo $value['boxNum']?></td>
									<td><?php echo $value['totalNum']?></td>
									<td><?php echo $value['inNum']?></td>
									<td class = "sotrageNum"><?php echo $value['outNum']?></td>
									<td><?php echo $value['storageName']?> </td> 
									<td><?php echo $value['areaName']?> </td>
									<td><?php if($value['status'] == 0) {echo '未入库';} else if($value['status'] == 1) {echo '已入库';} else if($value['status'] == 2) {echo '已取消';}?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					<?php }?>
				</div>
			</div>
			<form action="../scm/invOd/toPdfNew" method="post" target="_blank" id = "orderForm">
				<input type = "hidden"  id = "print_data" name = "print_data" value = "">
			</form>
			<div class="main fr pct39 mt10">
				<div class = "sub-title">
					<em style="font-size: 14px;font-weight: bolder;">待出库商品</em>
				</div>
				<div style = "text-align:left;margin:0px 10px 0px 10px;">
					<div class="ui-btn  close">关闭</div>
				</div>
				<div class = "table-wapper">
					<table class="table-a">
						<thead>
							<tr>
								<td>
									<label>
										<input type="checkbox" onclick="checkAlla(this)">
										<span class="text"></span>
									</label>
								</td>
								<td>商品</td>
								<td>数量</td>
							</tr>
						</thead>
						<tbody>
						<?php if($last_goods) { ?>
							<?php foreach ($last_goods as $key => $value) { ?>
								<tr>
									<td>
										<label>
											<input type="checkbox" name="close_order" invId=<?php echo $value['invId']?> iid=<?php echo $value['iid']?>>
											<span class="text"></span>
										</label>
									</td>
									<td><?php echo $value['number'] ." ".$value['brand_name']." ".$value['skuId']." ". $value['name'] . " ". $value['spec']?></td>
									<td><?php echo $value['waitOut']?></td>
								</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td colspan="3">无</td>
							</tr>
						<?php }?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php  }?>
		<div class = "box" id="info">
			<div class="main mt10">
				<div class = "sub-title">
					<em style="font-size: 14px;font-weight: bolder;">订单商品列表</em>
				</div>
				<div class = "table-wapper">
					<table class="table-b">
						<thead>
							<tr>
								<td width=40%>商品</td>
								<td>单位</td>
								<td>包装规格</td>
								<td>数量</td>
								<td>单价</td>
								<td>金额</td>
								<td>下单时间</td>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($all_goods as $key => $value) { ?>
							<tr>
								<td><?php echo $value['number'] ." ".$value['brand_name']." ".$value['skuId']." ". $value['name']." ".$value['spec']?></td>
								<td><?php echo $value['unitName']?></td>
								<td><?php echo $value['packSpec']?></td>
								<td><?php echo $value['qty']?></td>
								<td><?php echo str_money($value['price']) ?></td>
								<td><?php echo str_money($value['amount'])?></td>
								<td><?php echo $value['billDate']?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url()?>/statics/js/dist/orderListNew.js?ver=20161014"></script>
<script type="text/javascript">

	function checkAll(obj){
		$("label:not(.hide) .checksubsubAll").click();
	}
	


	function checkAlla(obj){
    	$(".box input[name='close_order']").prop('checked', $(obj).prop('checked'));
	}


	
	function checkAllb(obj){
		var cls = $(obj).attr("for");
		$("."+cls+":not(.hide) input[type=checkbox]").prop('checked', $(obj).prop('checked'));

		var countAll = $(".box input[name='into_goods']").length;
		var ccountAll = $(".box input[name='into_goods']:checked").length;

		if(countAll == ccountAll ){
			$("#checkboxAll").prop("checked",true);
		}else{
			$("#checkboxAll").prop("checked",false);
		}
	}

	function checkDetail(obj,parentId){

		var count = $("."+parentId+" input[type=checkbox]").length;
		var ccount = $("."+parentId+" input[type=checkbox]:checked").length;
		
		if(count == ccount ){
			$("."+$(obj).attr("all")).prop("checked",true);
		}else{
			$("."+$(obj).attr("all")).prop("checked",false);
		}
		
		var countAll = $(".box input[name='into_goods']").length;
		var ccountAll = $(".box input[name='into_goods']:checked").length;

		if(countAll == ccountAll ){
			$("#checkboxAll").prop("checked",true);
		}else{
			$("#checkboxAll").prop("checked",false);
		}
		
	}
	
	$("#tab li").on("click",function(){
		var $this = $(this);
		var target = $this.data('type');
		$("#tab li").removeClass("cur");
		$this.addClass("cur");
		$(".box").addClass("hide");
		$("#"+target).removeClass("hide");
	})
	
	if(<?php echo $orderStatus?> > 3){
		$("#tab-storage").click();
	}
</script>
</body>
</html>


