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
<style>
	*{ box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box; }
	.red { color: red; }
	.modal_content .bt{ border-bottom:1px solid #ccc; }
	.modal_content{ width: 95%;height: 380px;border: 1px solid #999;margin: 8px auto;background-color: #fff;border-radius: 6px;background-clip: padding-box;outline: 0;box-shadow: 0 5px 15px rgba(0,0,0,0.5);position: relative; }
	.modal_header{ padding: 12px;border-bottom: 1px solid #e5e5e5;font-size: 18px;text-align: center; }
	.modal_body_header{ padding:10px 20px; }
	.modal_body_header span{ line-height:50px;margin-right:10px;font-size:14px; }
	
	.cat_select{ overflow: hidden;border: 1px solid #ccc;color: #555; }
	.list_group{ overflow: hidden;float: left;border-right: 1px solid #ccc; }
	
	.list_group_item{ display: block;padding: 10px 15px;font-size: 14px;cursor: pointer; }
	.list_group_item:before{ content: '';float: right;width: 6px;height: 6px;overflow: hidden;border-right: 1px solid #888888;border-top: 1px solid #888888;margin-top: calc(6px);transform: rotate(45deg); }
	.list_group_item:hover,.list_group_select .active{ background-color: #f5f5f5; }
	
	.goods_show{ padding: 10px 15px;font-size: 12px;overflow: hidden;cursor:pointer;border-bottom: 1px solid #f5f5f5}
	.goods_show span{ width: 82%;display: block;height: 21px;overflow: hidden;float: left; }
	.goods_show em{ float: right;line-height: 21px; }
	
	.list_group_box{ display: block;overflow: hidden; }
	.close{ width:5%;float: left;cursor: pointer;font-size: 14px;text-align: center;line-height: 30px; }
	
	.list_group_title{ text-align:center;display:block;padding: 4px 0;height: 30px;cursor: pointer; line-height: 22px;}
	.list_group_select{ height: 270px;overflow: auto; }
	.input{ border: none;border-bottom: 1px solid #888;width: 80px;font-size: 14px;outline: none;text-align: center; }

	.btn{ width: 50px;height: 30px;border: 1px solid #eee;border-radius: 6px;text-align: center;line-height: 28px;cursor: pointer;background-color: #f8ac59;color: #fff;position: absolute;top:11px;right:15px; }
</style>
<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
</head>
<body>
	<div class="pct100 fl">
		<div class="modal_content" style="height: 115px;width:97%;">
			<div class="modal_header" style="padding: 7px;">订单详情</div>
			<div class="modal_body_header">
				<span>预订单单号：<?php echo $billNo?></span>
				<span>采购单号：<?php echo $CGOId?></span>
				<span>预订单额度：￥<?php echo $quota?></span>
				<span>预订单剩余额度：￥<em class="red"><?php echo $laveQuota?></em></span>
				<span>订单金额：<em class="totalPrice red">0</em> 元</span>
			</div>
		</div>
	</div>
	<div class="pct50 fl">
		<div class="modal_content">
			<div class="modal_header">选择商品</div>
			<div class="modal_body p15">
				<div class="cat_select">
					<div class="pct25 list_group">
						<em class="list_group_title bt">类 目</em>
						<div class="list_group_select level_1">
							<?php foreach ($category_has as $key => $value) {?>
								<em class="list_group_item" rel="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></em>
							<?php  }?>
						</div>
					</div>
					<div class="pct75 list_group" style="border:none;">	
						<em class="list_group_title bt">商 品</em>
						<div class="list_group_select level_2" >
						</div>
					</div>
				</div>  
			</div>
		</div>
	</div>
	<div class="pct50 fl">
		<div class="modal_content">
			<div class="modal_header">已选商品</div>
			<div class="modal_body p15">
				<div class="cat_select">
					<div class="pct100 list_group" style="border-right: none;">
						<div class="list_group_select hasSelect" style="height: 300px">
							<span class="list_group_box">
								<em class="list_group_title bt pct55 fl" style="padding-left:5%;">商 品</em>
								<em class="list_group_title bt pct10 fl">订货批量</em>	
								<em class="list_group_title bt pct20 fl">数 量</em>	
								<em class="list_group_title bt pct15 fl">拍 照 价</em>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="btn">确认</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	var totalPrice=0,datas=[],
		laveQuota = <?php echo $laveQuota ?>,
		CGOId = "<?php echo $CGOId ?>",
		id = "<?php echo $id ?>";

	$('.list_group_item').on('click',function(e){
		e.stopPropagation();
		var _this = $(this);
		_this.addClass('active').siblings().removeClass('active'); 
		var rel = _this.attr('rel');

		Public.ajaxPost("../scm/invPre/category_delivery?action=category_delivery",{
			rel: rel
		},function (data){
			$('.level_2').empty();
			for (var i = 0; i < data.goodsList.length; i++) {
				var node = $('<div class=goods_show invId="'+data.goodsList[i].invId+'" minNum="'+data.goodsList[i].minNum+'"><span title="'+data.goodsList[i].name+'">'+data.goodsList[i].name+'</span><em>￥'+data.goodsList[i].price+'</em></div>');
				$('.level_2').append(node);
				node.on('click',function(){
					var _this = $(this);
					var invId =  _this.attr('invId');
					var minNum =  Number(_this.attr('minNum'));

					var isAdd = checkIsAdd(invId);

					if(isAdd){
						var goods = {};
						var name  = $(_this.children()[0]).html();
						var price = $(_this.children()[1]).html().split('');
						price.splice(0,1);
						price = price.join('');
						if( price == 'null') {parent.Public.tips({type:1,content:'不能选择没有价格的商品'});return;}
						goods.price = price;
						goods.invId = invId;
						goods.qty = minNum;
						totalPrice = (totalPrice).add((minNum) . mul(price));
						if( totalPrice > laveQuota ){
							parent.Public.tips({type:1,content:'订单的总额不能高于预订单余额'});
							totalPrice = (totalPrice).minus((minNum) . mul(price));
							return;
						}
						datas.push(goods);

						var emclose = $('<em class="close fa fa-close" invId='+ invId +' ></em>');
						var emname 	= $('<em class="list_group_title f12 pct50 fl" invId='+ invId +' title="'+name+'">'+name+'</em>');
						var emspec 	= $('<em class="list_group_title pct10 fl">'+minNum+'</em>');
						var input 	= $('<input type="text" class="input" minNum="'+minNum+'">');
						var eminput = $('<em class="list_group_title pct20 fl"></em>'); eminput.append(input);
						var emprice = $('<em class="list_group_title pct15 fl">￥'+ price +'</em>');

						var span = $('<span></span>');
						span.addClass('list_group_box');
						span.append(emclose).append(emname).append(emspec).append(eminput).append(emprice);
						$('.hasSelect').append(span);
						input.val(minNum);
						input.change(function(){
							var _this = $(this);
							onchange(_this);
						})

						emclose.click(function(){
							$(this).parent().remove();
							var invId = $(this).attr('invId');
							var index = getIndex(invId);
							var price = datas[index].price;
							var qty = datas[index].qty;
							totalPrice = (totalPrice).minus((qty).mul(price));
							datas.splice(index,1);
							$('.totalPrice').text(totalPrice);
						})
						$('.totalPrice').text(totalPrice);
					}

				})
			}
		})
	})


	$('.input').change(function(){
		var _this = $(this);
		onchange(_this)
	})

	$('.btn').on('click',function(){
		if(datas.length == 0) {parent.Public.tips({type:1,content:'请选择需要提货的商品'});return;}

		var data = {};
		data.goods = datas;
		data.CGOId = CGOId;
		data.id = id;
		data.totalPrice = totalPrice;
		data.laveQuota = (laveQuota).minus(totalPrice);
		Public.ajaxPost("../scm/invPre/saveDelivery?action=saveDelivery",{
			data: data
		},function (a){
			200 === a.status ? (parent.Public.tips({content:'确认提货单成功'}),
			parent.tab.addTabItem({
				tabid: 'preorder-preOrderQuery',
				text: '预订单查询',
				url: "../scm/invPre?action=preOrderQuery&typeNumber=allStatus",
			}),window.location.reload(),parent.closeTab("preorder-preDetail")) : parent.Public.tips({type:1,content:a.msg})
		})
	})

	function checkIsAdd (invId){
		for(var i = 0;i<datas.length;i++){
			if(invId == datas[i].invId) return false;
		}
		return true;
	}

	function onchange (_this){
		var minNum = Number(_this.attr('minNum'));
		if(isNaN(_this.val())) _this.val('');
		else if(_this.val() == '' || _this.val() == '0') _this.val('1'),parent.Public.tips({type:1,content:'商品数量不能为0'});
		else{
			var siblings = _this.parent().siblings(),
				invId = $(siblings[1]).attr('invId'),
				index = getIndex(invId),
				price = $(siblings[3]).html(),
				qty   =Number( _this.val());
			if(_this.val() % minNum != 0) {
				_this.val(datas[index].qty);
				parent.Public.tips({type:1,content:'商品数量需为订单批量的倍数'});
				return;
			}
			price = price.split('');
			price.splice(0,1,'');
			price = price.join('');
			var totalPrice_n = (totalPrice).minus((datas[index].qty) . mul(price));
			totalPrice_n = (totalPrice_n).add((qty).mul(price));
			if( totalPrice_n > laveQuota ){
				parent.Public.tips({type:1,content:'订单的总额不能高于预订单余额'});
				_this.val(datas[index].qty);
				return;
			}
			datas[index].qty = qty;
			totalPrice = totalPrice_n;

		}
		$('.totalPrice').text(totalPrice);
	}
	function getIndex(invId){
		for(var i = 0;i<datas.length;i++){
			if(invId == datas[i].invId) return i;
		}
		return false;
	}

	function accMul(arg1,arg2){    
		var m=0,s1=arg1.toString(),  
		s2=arg2.toString();
		try{  
			m+=s1.split(".")[1].length}catch(e){}    
		try{  
			m+=s2.split(".")[1].length}catch(e){}    
		return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
	}
	Number.prototype.mul = function (arg){    
		return accMul(arg, this);  
	} 

	function accAdd(arg1,arg2){   
		var r1,r2,m;    
		try{  
			r1=arg1.toString().split(".")[1].length  
		}catch(e){ r1=0 } 
		try{  
			r2=arg2.toString().split(".")[1].length
		}catch(e){ r2=0 }  
		m=Math.pow(10,Math.max(r1,r2)); 
		return (arg1*m+arg2*m)/m  
	}
	Number.prototype.add = function (arg){    
		return accAdd(arg,this);  
	} 

	function accMinus(arg1,arg2){   
		var r1,r2,m;    
		try{  
			r1=arg1.toString().split(".")[1].length  
		}catch(e){ r1=0 } 
		try{  
			r2=arg2.toString().split(".")[1].length
		}catch(e){ r2=0 }  
		m=Math.pow(10,Math.max(r1,r2)); 
		return (arg1*m - arg2*m)/m  
	}
	Number.prototype.minus = function (arg){    
		return accMinus(this,arg);  
	} 
</script>
</html>