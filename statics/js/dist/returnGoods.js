
$(function (){
	var a = $('.leibie li:first-child').html();$('#leibie').val(a);
	$(document).click(function(){
		$('.leibie').removeClass('show');
	})
})

$('.leibie li').on('click',function (e){
	e.stopPropagation();
	var licontent = $(this).html();
	var lbId = $(this).attr('lbId');
	$('#leibie').val(licontent).attr('leibie',lbId);
	$(this).addClass('on').siblings().removeClass('on');
	$(this).parent().removeClass('show');
})
$('.trigger_c').on('click',function(e){
	e.stopPropagation();
	var isShow = $(this).parent().siblings('ul').hasClass('show');
	if(isShow){
		$(this).parent().siblings('ul').removeClass('show');
	}else{
		$(this).parent().siblings('ul').addClass('show');
	}
})


$('.btn_left').on('click',function(e){
	e.stopPropagation();
	var num = $(this).siblings('.change').val();
	if(num == 0){
		$(this).siblings('.change').val(0);
	}else{
		num --;
		$(this).siblings('.change').val(num);
	}
	
})
$('.btn_right').on('click',function(e){
	e.stopPropagation();
	var maxNum  = Number($(this).parent().attr('maxNum'));
	var num = Number($(this).siblings('.change').val());
	num ++;
	num > maxNum ? num = maxNum : num ;
	$(this).siblings('.change').val(num);
})


$('.change').change(function(e){
	e.stopPropagation();
	var maxNum  = Number($(this).parent().attr('maxNum'));
	var num =  Number($(this).val());
	num <= maxNum ? '' : $(this).val(maxNum);
//	alert(maxNum)
})
$('.btn').on('click',function(){
	var return_data = {},i = 0;
	return_data.detail=[];
	$('input[name="return_goods"]').each(function(){
		var leibie= $('#leibie').attr('leibie');
		var invId = $(this).attr('invId');
		var iid   = $(this).attr('iid');
		var rtNum = $(this).val();
		if (rtNum != '' && rtNum != 0){
			return_data.iid = iid;return_data.leibie = leibie;
			var data = {};
			data.invId = invId;data.rtNum = rtNum;
			return_data.detail[i] = data;
			i++;
		}
	})
	if(return_data.detail.length != 0){
		 $.dialog.confirm("您确定要退货吗？",
			function (){
				Public.ajaxGet("../scm/invOd/return_goods?action=return_goods",{
					data: return_data
				},function (data){
					200 === data.status ?  parent.Public.tips({content: "已申请退货"}) : parent.Public.tips({type:1,content:data.msg});
					window.location.reload();
				})
			}
		)
	}else{
		Public.tips({
			type: 1,
			content: "没有可供退货的商品"
		});
	}
})


