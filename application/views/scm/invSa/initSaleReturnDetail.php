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
    	.box{
            box-sizing: border-box;
            font-size: 12px;
            position: relative;
            background: #ffffff;
            padding: 10px 15px 10px 15px;
            margin: 15px;
        }
        .deal{
            width: 100%;
            color: #ffffff;
            line-height: 30px;
            height: 30px;
        }
        .deal-info{
            width: 120px;
            padding-left: 40px;
            background-color: #0C6EAB;
        }
        .deal-info span{
            font-weight: bolder;
            padding-left: 20px;
        }
        .hr{
            width: 100%;
            margin: 10px 0;
            border-top: 1px solid #dddddd;
        }
        .hr-noborder{
            border-top: none;
        }
        .sale{
            width: 100%;
        }
        .sale h3{
        	font-size: 13px;
            margin-bottom: 10px;
        }
        .col-1{
            width: 25%;
            float: left;
            height: 30px;
            line-height: 30px;
        }
        .col-2{
        	width: 75%;
            float: left;
            height: 30px;
            line-height: 30px;	
        }
        .clear{
            clear: both;
        }
        .table {
            width: 100%;
            max-width: 100%;
            border: 1px solid #ddd;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .table-goods td {
  			vertical-align: middle !important;
		}
		.table-goods .col-name {
 		 	width: 30%;
		}
		.table thead th{
			padding: 8px;
			background: #f0f0f0;
		}
        .table tbody tr td{
            text-align: center;
            padding: 8px;
            border-top: 1px solid #ddd;
            vertical-align: center;
        }
        .table tbody tr td:first-child{
            text-align: left;
        }
        .table tbody tr td:first-child img{
        	float: left;
        	width: 56px;
        	height: 56px;
        	margin-right: 8px;
        }
        .des-info{
            margin: 10px 0;
        }
        .star{
            padding-right: 5px;
        }
        .text-red{
            color: #E06754;
        }
        .text-orange{
            color: #FF851B;
        }
        .deal-choice{
            display: inline-block;
            margin-left: 10px;
        }
        .label{
            display: inline-block;
            vertical-align: top;
        }
        .btn {
            display: inline-block;
            *display: inline;
            *zoom: 1;
            background-color: transparent;
            height: 30px;
            line-height: 30px;
            overflow: visible;
            padding: 0;
            border: 0 none;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            -ms-border-radius: 2px;
            border-radius: 2px;
            -webkit-box-shadow: 1px 1px 0 rgba(200, 200, 200, 0.5);
            -moz-box-shadow: 1px 1px 0 rgba(200, 200, 200, 0.5);
            box-shadow: 1px 1px 0 rgba(200, 200, 200, 0.5);
            *margin: 0 6px;
            text-decoration: none;
            vertical-align: middle;
            font-size: 13px;
            font-weight: normal;
            cursor: pointer;
        }
        .btn.btn-submit{
            background-color: #1ab394;
            padding: 0 16px;
            height: 30px;
            color: #ffffff;
            line-height: 30px;
        }
        .btn.btn-submit:hover{
        	background-color: #18a689;
        	
        }
        input[type=radio] {
    		opacity: 1;
    		left: 0px;
    		top: 0px;
    		position: initial;
    		z-index: 12;
    		width: auto;
    		height: auto;
    		cursor: pointer;
/* 			display: inline-block; */
/*     		padding: 2px 5px 0 0; */
		}
		input[type=radio]:nth-child(2){
			margin-left: 10px;
		}
		input[type="radio"]:focus{
  			outline: thin dotted;
  			outline: 5px auto -webkit-focus-ring-color;
  			outline-offset: -2px;
		}
		.hide {
    		display: none;
		}
		.col-xs-8{
		 	width: 66.66667%;
		 	min-height: 1px;
  			padding-left: 15px;
  			padding-right: 15px;
		}
		.goods img {
  			float: left;
  			width: 56px;
  			height: 56px;
  			margin-right: 15px;
  			border: 1px solid #ccc;
		}
		.pull-left {
			float:left;
		}
		.form-control {
  			display: block;
  			width: 100%;
  			height: 34px;
  			padding: 6px 12px;
  			font-size: 12px;
  			line-height: 1.42857;
  			color: #555555;
  			background-color: #fff;
  			background-image: none;
  			border: 1px solid #ccc;
  			border-radius: 4px;
  			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  			-webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  			-o-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
  			transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
		}
		.form-control:focus {
  			border-color: #66afe9;
  			outline: 0;
  			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
 			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
		}
		.form-control::-moz-placeholder {
  			color: #cccccc;
  			opacity: 1;
		}
		.form-control:-ms-input-placeholder {
  			color: #cccccc;
		}
		.form-control::-webkit-input-placeholder {
  			color: #cccccc;
		}
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
  			cursor: not-allowed;
  			background-color: #f0f0f0;
  			opacity: 1;
		}
		textarea.form-control {
  			height: auto;
		}
		.shopExp{
			width: 300%;
		}
		
</style>
</head>

<body>
<div class="box">
    <!-- <div class="deal">
        <div class="deal-info">
           	<?php 
                if ($info["status"] == '0')
                    echo "待处理";
                elseif ($info["status"] == '1')
                    echo "已处理";
                elseif ($info["status"] == '3')
                    echo "已驳回";
            ?> 
            <span>&gt;</span>
        </div>
        <div class="hr"></div>
    </div> -->
    <div class="sale">
        <div class="sale-return">
            <h3>退货基本信息</h3>
            <div class="sale-return-group">
                <div class="col-1">
                    <label class="label">退货编号：</label><?php echo $info["aftersales_bn"]?>
                </div>
                <div class="col-1">
                    <label class="label">申请时间：</label><?php echo date("Y-m-d H:i:s", $info["created_time"])?>
                </div>
                <div class="col-1">
                    <label class="label">申请处理进度：</label>
                    <span class="text-red">
                    <?php 
                    if ($info["progress"] == '0')
                    echo "等待服务站处理";
                    elseif ($info["progress"] == '1')
                    echo "接受申请,已退货";
                    elseif ($info["progress"] == '2')
                    echo "等待收货确认";
                    elseif ($info["progress"] == '3')
                    echo "商家已驳回";
                    elseif ($info["progress"] == '4')
                    echo "商家已处理";
                    elseif ($info["progress"] == '5')
                    echo "等待平台处理";
                    elseif ($info["progress"] == '6')
                    echo "平台已驳回";
                    elseif ($info["progress"] == '7')
                    echo "平台已处理";
                    ?>
                    </span>
                </div>
                <div class="col-1">
                    <label class="label">退货类型：</label>
                    <?php 
                    if ($info["aftersales_type"] == 'ONLY_REFUND')
                    echo "仅退款";
                    elseif ($info["aftersales_type"] == 'REFUND_GOODS')
                    echo "退货";
                    elseif ($info["aftersales_type"] == 'EXCHANGING_GOODS')
                    echo "换货";
                    ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="hr hr-noborder"></div>
        <div class="sale-info">
            <h3>订单信息</h3>
            <div class="sale-group">
                <div class="col-1">
                    <label class="label">订单编号：</label><?php echo $info["tid"]?>
                </div>
                <div class="col-1">
                    <label class="label">服务站名称：</label><?php echo $info["service_name"]?>
                </div>
                <div class="col-1">
                    <label class="label">订单状态：</label>
                    <?php
                    if ($info["trade"]["status"] == "WAIT_CONFRIM")
                    echo "待确认";
                    elseif ($info["trade"]["status"] == "WAIT_APPROVE")
                    echo "待审核";
                    elseif ($info["trade"]["status"] == "DELIVER_GOODS")
                    echo "待发货";
                    elseif ($info["trade"]["status"] == "WAIT_GOODS")
                    echo "已发货";
                    elseif ($info["trade"]["status"] == "RECEIVE_GOODS")
                    echo "已收货";
                    elseif ($info["trade"]["status"] == "TRADE_FINISHED")
                    echo "已完成";
                    elseif ($info["trade"]["status"] == "TRADE_CANCEL")
                    echo "已取消";
                    elseif ($info["trade"]["status"] == "TRADE_CLOSED")
                    echo "已关闭";
                    ?>
                </div>
                <div class="col-1">
                    <label class="label">会员名称：</label><?php echo $userName?>
                </div>
                <div class="col-1">
                    <label class="label">下单时间：</label><?php echo date("Y-m-d H:i:s", $info["trade"]["created_time"])?>
                </div>
                <div class="col-2">
                    <span class="label">收货信息：</span>
                    <span style="display: inline-block; width: 76%;">
                    	<?php 
                    		echo $info["trade"]["receiver_name"],"&nbsp;",$info["trade"]["receiver_mobile"],"&nbsp;",$info["trade"]["receiver_state"],$info["trade"]["receiver_city"],$info["trade"]["receiver_district"],$info["trade"]["receiver_address"]
                    	?>
                    </span>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="hr hr-noborder"></div>
        <div class="sale-return-num">
            <h3>退货商品数量</h3>
            <table class="table table-goods">
                <thead>
                <tr>
                    <th class="col-name">商品</th>
                    <th>单价(元)</th>
                    <th>数量</th>
                    <th>总金额(元)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                    	<img src="<?php echo $info["sku"]["pic_path"]?>" alt="">
                    	<span><?php echo $info["sku"]["title"],"&nbsp;&nbsp;",$info["sku"]["spec_nature_info"]?></span>
                    </td>
                    <td>￥<?php echo $info["sku"]["price"]?></td>
                    <td><?php echo $info["num"]?></td>
                    <td class="text-orange">￥<?php echo $info["sku"]["payment"]?></td>
                </tr>
                </tbody>
            </table>
            <div class="des-info hide">
          		<span class="order-form-w1">退货理由：</span><?php echo $info["reason"]?>
        	</div>
            <div class="des-info">
                <label class="label">问题描述：</label>
                <?php 
                if($info["description"])
                	echo $info["description"];
                else 
                	echo "未提供详细描述";
                ?>
            </div>
            <div class="des-info">
                <label class="label">图片信息：</label>
                <span class="col-xs-8">
                	<?php 
                	if($info["evidence_pic"]){
                		foreach($info['evidence_pic'] as $v){?>
                		<span class="goods pull-left">
                			<img src="<?php echo $info['sku']['pic_path'] ?>" alt="" />
                		</span>
                	<?php 
                		}
                		}else{
                			echo "无图片信息";
                		}?>
          		</span>
            </div>
            <?php 
                if($info["shop_explanation"]){?>
      			<div class="des-info">
      				<label class="label">商家处理申请说明：</label>
        			<span class="col-xs-8">
          				<span class="col-xs-8"><?php echo $info["shop_explanation"]?></span>
        			</span>
      			</div>
      		<?php }?>
            <?php 
                if($info["admin_explanation"]){?>
      			<div class="des-info">
      				<label class="label">平台审核意见：</label>
        			<span class="col-xs-8">
          				<span class="col-xs-8"><?php echo $info["admin_explanation"]?></span>
        			</span>
      			</div>
      		<?php }?>
        </div>
        <div class="hr"></div>
        <?php if ($info["status"] == '0') {?> 
        <div class="deal-detail">
            <form action="../invSa/saveAftersale" method="post" id = "afterForm">
                <div class="des-info">
                    <span class="label order-form-w1"><span class="text-red star">*</span>选择处理结果：</span>
                    <div class="deal-choice">
                        <input type="radio" name="choice" value="true" checked/>同意
                        <input type="radio" name="choice" value="false"/>不同意
                    </div>
                </div>
                <div class="des-info">
                    <span class="label" for="deal-des"><span class="text-red star">*</span>处理说明：</span>
                    <div id="deal-des" class="deal-choice">
                        <textarea class="form-control shopExp" name="shop_explanation"></textarea>
                    </div>
                </div>
                <input type="submit" class="btn btn-submit" name="submit" value="提交"/>
            </form>
        </div>
        <?php }?>
    </div>
</div>
<script>

	$("#afterForm").submit(function(e){

		e.preventDefault();

		var form = e.target;

		var data = {};

		data['billReturnNo'] = '<?php echo $info["aftersales_bn"] ?>'
		data['choice'] = $("input[name=choice]:checked").val();
		data['shop_explanation'] = $("textarea[name=shop_explanation]").val();
		
		$.post(form.action, data, function(a) {
			  200 == a.status ?  parent.Public.tips({
                  content: "订单已处理！"
              },parent.closeTab("saleReturnDetail-saleReturnDetail")): parent.Public.tips({
                  type: 1,
                  content: "订单未处理！"
              });
        },'json');
	})
</script>
</body>
</html>
