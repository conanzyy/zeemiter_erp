
$('.close').on('click',function(){
	var close_goods = [];
	var iid ;
	$('input[name="close_order"]:checked').each(function (){
		close_goods.push($(this).attr('invId'));
		iid = $(this).attr('iid');
	})
	var data = {};
	data['iid'] = iid ;data['close_goods'] = close_goods;
	console.log(data);
	if(close_goods.length != 0){
		$.dialog.confirm("您确定要关闭该订单记录吗？",
			function (){
				Public.ajaxGet("../scm/invOd/closeGoods?action=closeGoods",{
					data: data
				},function (data){
					200 === data.status ?  parent.Public.tips({content: "关闭成功！"}):parent.Public.tips({type:1,content:data.msg});
					window.location.reload();
				})
			}
		)
	}
}) 

$(".btn-print").on("click",function(){
	var data=[];
	var info = {"id":$(this).attr("acceptId"),"detail":Array()};
	$(this).parent().parent().parent().parent().find('label input[name="into_goods"]').each(function (){
		var into_goods = {};
		into_goods.id = $(this).attr('id');
		into_goods.goods = $(this).attr('goods');
		into_goods.boxNum=$(this).attr('boxNum');
		into_goods.totalNum=$(this).attr('totalNum');
		into_goods.haveInto=$(this).attr('haveInto');
		into_goods.outNum=$(this).attr('outNum');
		into_goods.ck = $(this).attr('ck');
		into_goods.ckName = $(this).attr('ckName');
		into_goods.area = $(this).attr('areaId');
		into_goods.areaName = $(this).attr('areaName');
		info.detail.push(into_goods);
	})
	data.push(info);
	$('#print_data').val(JSON.stringify(data));
	$("#orderForm").submit();
})

$(".btn-storage").on("click",function(){
	var data=[];
	var info = {"id":$(this).attr("acceptId"),"wid":$(this).attr("wid"),"detail":Array()};
	$(this).parent().parent().parent().parent().find('label:not(.hide) input[name="into_goods"]:checked').each(function (){
		var into_goods = {};
		into_goods.id = $(this).attr('id');
		into_goods.wid = $(this).attr('wid');
		into_goods.goods = $(this).attr('goods');
		into_goods.boxNum=$(this).attr('boxNum');
		into_goods.totalNum=$(this).attr('totalNum');
		into_goods.haveInto=$(this).attr('haveInto');
		into_goods.outNum=$(this).attr('outNum');
		into_goods.ck = $(this).attr('ck');
		into_goods.ckName = $(this).attr('ckName');
		into_goods.area = $(this).attr('areaId');
		into_goods.areaName = $(this).attr('areaName');
		info.detail.push(into_goods);
	})
	data.push(info);
	
	if(info.detail == 0){
		Public.tips({type: 1, content : "请选择商品进行入库操作！"});
		return;
	}
	if($("#oid").val() == ""){
		parent.Public.tips({type:1,content:'订单数据错误'});
		return;
	}
	$.dialog.confirm("您确定要入库该商品吗？",
		function (){
			var load;
			setTimeout(function(){ load = $.dialog.tips("正在入库！，请稍候...", 10e3, "loading.gif", !0)},1);
			Public.ajaxPost("../scm/invOd/intoGoodsNew?action=intoGoodsNew",{ data: JSON.stringify(data), oid : $("#oid").val() },function (data){
				if(200 === data.status){
					parent.Public.tips({content: "入库成功！"})
					window.location.reload();
				} else {
					parent.Public.tips({type:1,content:data.msg});
					load.close();
				}
			});
		}
	)
	
})

$('#addStorage').on('click',function(){
	var data=[];
	$("label:not(.hide) .checksubsubAll").each(function(){
		var info = {"id":$(this).attr("acceptId"),"wid":$(this).attr("acceptId"),"detail":Array()};
		$(this).parent().parent().parent().parent().parent().find("input[name=into_goods]:checked").each(function(){
			var into_goods = {},detail = [];
			into_goods.id = $(this).attr('id');
			into_goods.wid = $(this).attr('wid');
			into_goods.goods = $(this).attr('goods');
			into_goods.boxNum=$(this).attr('boxNum');
			into_goods.totalNum=$(this).attr('totalNum');
			into_goods.haveInto=$(this).attr('haveInto');
			into_goods.outNum=$(this).attr('outNum');
			into_goods.ck = $(this).attr('ck');
			into_goods.ckName = $(this).attr('ckName');
			into_goods.area = $(this).attr('areaId');
			into_goods.areaName = $(this).attr('areaName');
			info.detail.push(into_goods);
		})
		if(info.detail.length > 0){
			data.push(info);
		}
	});
	
	if(data.length == 0){
		Public.tips({type: 1, content : "请选择商品进行入库操作！"});
		return;
	}
	
	if($("#oid").val() == ""){
		parent.Public.tips({type:1,content:'订单数据错误'});
		return;
	}
	
	$.dialog.confirm("您确定要入库该商品吗？",
		function (){
			setTimeout(function(){
				$.dialog.tips("正在入库！，请稍候...", 10e3, "loading.gif", !0);
			},1);
			Public.ajaxPost("../scm/invOd/intoGoodsNew?action=intoGoodsNew",{
				data: JSON.stringify(data),
				oid : $("#oid").val()
			},function (data){
				200 === data.status ?  parent.Public.tips({content: "入库成功！"}):parent.Public.tips({type:1,content:data.msg});
				window.location.reload();
			});
			
		}
	)
})

$('.area_rel').change(function(){
	var select_id = $(this).attr('id');
	var id = select_id.split('_')[1];
	var invId = $(this).attr('goods_id');
	var str_id = $('#'+select_id+' option:selected').val();
	Public.ajaxPost("../scm/invOd/goods_area?action=goods_area",{
		strId:str_id,
		invId:invId
	},function(a){
		if(200 === a.status){
			var options = '';
			for(var i in a.areas){
				if(!isNaN(i)){
					if(a.areas[i].id == a.areaId){
						options += '<option value="'+a.areas[i].id+'" selected>'+a.areas[i].area_name+'</option>'
					}else{
						options += '<option value="'+a.areas[i].id+'">'+a.areas[i].area_name+'</option>'
					}
				}
			}
			$('#area_'+id).empty().append(options);
		}
	});
})