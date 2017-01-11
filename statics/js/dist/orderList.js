
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

$("#print").on("click",function(){
	var data=[];
	$('input[name="into_goods"]').each(function (){
		var into_goods = {};
		var goods = $(this).attr('goods');
		var outNum = $(this).attr('outNum');
		var waitInfo = $(this).attr('outNum2');
		var ck = $(this).attr('ck');
		var ckName= $(this).attr('ckName')
		var area = $(this).attr('areaId');
		var areaName = $(this).attr('areaName');
		into_goods.goods = goods;
		into_goods.waitInfo = waitInfo;
		into_goods.outNum = outNum;
		into_goods.ck = ck;
		into_goods.ckName = ckName;
		into_goods.area = area;
		into_goods.areaName = areaName;
		data.push(into_goods);
	})
	$('#print_data').val(JSON.stringify(data));
	$("#orderForm").submit();
})

$('#addStorage').on('click',function(){
	var data=[];
	$('label:not(.hide) input[name="into_goods"]:checked').each(function (){
		var into_goods = {};
		var id = $(this).attr('invId');
		var outNum = $(this).attr('outNum');
		var ck = $(this).attr('ck');
		var area = $(this).attr('areaId');
		into_goods.id = id;
		into_goods.outNum = outNum;
		into_goods.ck = ck;
		into_goods.area = area;
		
		data.push(into_goods);
	})
	if($("#oid").val() == ""){
		parent.Public.tips({type:1,content:'订单数据错误'});
		return;
	}
	if(data.length != 0){
		$.dialog.confirm("您确定要入库该商品吗？",
			function (){
				setTimeout(function(){
					$.dialog.tips("正在入库！，请稍候...", 10e3, "loading.gif", !0);
				},1);
				Public.ajaxPost("../scm/invOd/intoGoods?action=intoGoods",{
					data: JSON.stringify(data),
					oid : $("#oid").val()
				},function (data){
					200 === data.status ?  parent.Public.tips({content: "入库成功！"}):parent.Public.tips({type:1,content:data.msg});
					window.location.reload();
				});
				
			}
		)
	}
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