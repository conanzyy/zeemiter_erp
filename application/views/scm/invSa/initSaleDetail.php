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
	.ui-input, .ui-combo-wrap{border-width: 1px !important;}
    #matchCon { width: 220px; }
    #print{margin-left:10px;}
    .wrapper{    
	    position: relative;
	    overflow: auto;
	    height: 100%;
    }
    #reAudit,#audit{display:none;}
    h2{font-size: 13px !important;}
	.ui-table{
		width:100%;
		border: 1px solid #ddd;
		border-spacing: 0;
		border-collapse: collapse;
	}
	.ui-table td{
		padding: 8px;
	}
	
	.ui-table thead td{
		background:#f0f0f0;
		font-weight: bold;
	}	
	
	.ui-table tbody td{
		border-top: 1px solid #ddd;
	}	
	.goods img {
	    float: left;
	    width: 56px;
	    height: 56px;
	    margin-right: 15px;
	    border: 1px solid #ccc;
	}
	.goods a {
	    display: table-cell;
	    vertical-align: middle;
	    height: 56px;
	    color: #333;
	    line-height: 1.5;
	}

</style>
</head>

<body>

<div class = "wrapper">

	<div class = "bills">
		<div>
            <h2>订单信息</h2>
        </div>
		<div class="con-header">
	      <dl class="cf">
	        <dd class="pct25">
	          <label>订单编号：</label>
	          <?php
              $r = $data['0']['tid'];
              $r = $r*1;
              $r = number_format($r);
              $r = str_replace(',','',$r);
              echo $r;
              ?>
	          </dd>
	        <dd class="pct25">
	        	<label>订单件数：</label><?php echo $data['0']['itemnum'] ?>
	        </dd>
	        <dd class="pct25">
	        	 <label class="label">商品总额：</label>
                 <span style="color: #FF851B !important">
                    <?php echo $data['0']['total_fee'] ?>
                 </span>
	        </dd>
	        <dd class="pct25">
	        	 <label class="label">应付金额：</label>
                 <span style="color: #FF851B !important">
                    <?php echo $data['0']['payment']?>
                 </span>
	        </dd>
	      </dl>
	      <dl class="cf">
	       	<dd class="pct25">
	          <label>是否需要开发票：</label>
	          <span style="color: #FF851B !important">
                    <?php if(!$data['0']['need_invoice']){
                        echo "不开发票" ;}else{
                        echo "开发票"; } ?>
              </span>
	        </dd>
              <dd class="pct25">
                  <label>发票类型：</label>
                  <span style="color: #FF851B !important">
                    <?php if($data['0']['need_invoice'] && $data['0']['invoice_type']=='normal'){ echo"普通增值税发票";}elseif($data['0']['need_invoice'] && $data['0']['invoice_type']=='dedicated'){ echo "专用增值税发票";}else{ echo "无";} ?>
              </span>
              </dd>
              <dd class="pct25">
                  <label>发票抬头：</label>
                  <span style="color: #FF851B !important">
                   <?php
                   if(!$data['0']['need_invoice']){
                       echo "无";
                   }else{
                       if($data['0']['invoice_name']){
                           echo "个人";
                       }else{
                           echo "单位(".$data['0']['invoice_main'].")";
                       }
                   }
                   ?>
              </span>
              </dd>
              <dd class="pct25">
                  <label>联系方式：</label>
                  <span>
                    <?php echo $data['0']['receiver_mobile'] ;
                    if($data['0']['receiver_phone']){
                        echo $data['0']['receiver_phone'];
                    }
                    ?>
              </span>
              </dd>
	      </dl>
            <dl class="cf">
                <dd class="pct25">
                    <label>是否改价：</label>
                    <span>
                    <?php if($data['0']['isModify']){
                        echo "是";
                    }else{
                        echo "否";
                    }
                    ?>
              </span>
                </dd>
                <dd class="pct25">
                    <label>下单备注：</label>
                    <span>
                    <?php echo $data['0']['trade_memo']?>
              </span>
                </dd>
            </dl>
            <dl class="cf">
                </dd>
                <dd class="pct25">
                    <label>收货地址：</label>
                    <span>
                     <?php echo $data['0']['receiver_address']?>
              </span>
                </dd>
                </dd>
                <dd class="pct25">
                    <label>收货地区：</label>
                    <span>
                    <?php echo $data['0']['receiver_state']?><?php echo $data['0']['receiver_city']?><?php echo $data['0']['receiver_district']?>&nbsp;
              </span>
                </dd>
            </dl>
	    </div>
	    <div class="con-footer cf">
	      <span>备注信息</span>
	      <div class="mb10">
	      	<form  method="post" id="trade_form" action="">
                <input type="hidden" name="tid" id="tid" value="<?php   $r = $data['0']['tid'];
                $r = $r*1;
                $r = number_format($r);
                $r = str_replace(',','',$r);
                echo $r; ?>">
                <textarea type="text" name="shop_memo" class="ui-input ui-input-ph" rows="3" id="note"><?php echo $data['0']['shop_memo'] ?></textarea>
                <br/>
	            <div class="text-center">
	                <a type="submit" name="submit" class="ui-btn ui-btn-sp" id="submit_btn"  />保存</a>
	             	<a type="button" class="ui-btn ui-btn-default" id ="colse">关闭</a>
	                <!--
	                <a href="#" class="pull-right">订单导出</a>
	                -->
	            </div>
	      	</form>
	      </div>
    	</div>
    	 <br/>
    	<div>
            <h2>商品详情</h2>
        </div>
        <br/>
		<div>
			<table class="ui-table">
            <thead>
            <tr>
                <td>货号</td>
                <td class="col-name">商品名称</td>
                <!-- <td>结算价</td> -->
                <td>单价</td>
                <td>数量</td>
                <td>合计</td>
                <!--<td>状态</td>-->
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['0']['order'] as $item){ ?>
                <tr>
                    <td><?php echo $item['bn'] ?></td>
                    <td>
                        <div class="goods">
                            <a href="#">
                                <img src="<?php echo $item['pic_path'] ?>" alt="<?php echo $item['title'] ?>" style="width: 56px;height: 56px;">
                                 <?php echo $item['title'] ?>
                            </a>
                           
                        </div>
                    </td>
                    <!--
                        <td>
                             <{$item.settle_price|cur}>
                        </td>
                     -->
                    <td>
                        <?php echo $item['price'] ?>
                    </td>
                    <td>
                        <?php echo $item['num'] ?>
                    </td>
                    <td>
                        <span class="text-orange" style="color: #FF851B !important"><?php echo $item['total_fee'] ?></span>
                    </td>
                    <!-- <td>
				        <span class="text-orange" style="color: #FF851B !important">
				          <?php if ($item['status'] == "WAIT_CONFRIM"){
				              echo "待确认";
				          } elseif ($item['status'] == "WAIT_APPROVE"){
				              echo "待审核";
				          } elseif ($item['status'] == "DELIVER_GOODS"){
				              echo "待发货";
				          }elseif ($item['status'] == "WAIT_GOODS"){
				              echo "已发货";
				          }elseif ($item['status'] == "RECEIVE_GOODS"){
				              echo "已收货";
				          }elseif ($item['status'] == "TRADE_FINISHED"){
				              echo "已完成";
				          }elseif ($item['status'] == "TRADE_CLOSED"){
				              echo "<a href=\"<{url action=topservice_ctl_aftersales@detail bn=$item.tbn}>\">已关闭</a>";
				          } elseif ($item['status'] == "TRADE_CANCEL") {
				              echo "已取消";
				          }
				          ?>
				        </span>
                    </td> -->
                </tr>
            	<?php } ?>
             </tbody>
       	 </table>
		</div>
		<br/>
	</div>
	

</div>




<div class="modal fade" id="delivery_<{$trade.tid}>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#submit_btn").click(function() {
            var b = $('#tid').val();
            var c = $('#note').val();
            Public.ajaxPost("../invSa/beiZu", {
                tid: b,
                shop_memo: c
            }, function(a) {
                200 === a.status ?  parent.Public.tips({
                    content: "添加备注成功！"
                }): parent.Public.tips({
                    type: 1,
                    content: a.msg
                })
            })
        });
    });

    $("#colse").on("click",function(){

		//刷新
		var target = parent.getIframe("shopSalesQuery-shopSalesQueryALL");
		target[0].contentWindow.refirsh();


        //关闭
    	parent.closeTab("shopSalesQuery-shopSalesQueryDetail");
    })
</script>
</body>
</html>
