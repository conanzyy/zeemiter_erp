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

    .form-control {
        display: block;
        width: 100%;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
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
    .goods2 .left {
        position: absolute;
        left: 0;
    }
    .goods2 .right {
        margin-left: 71px;
        min-height: 56px;
    }
    a {
        color: #324f5f;
        font-size: 15px;
    }
    p {
        margin: 0 0 10px;
        font-size:14px;
        display: block;
        -webkit-margin-before: 1em;
        -webkit-margin-after: 1em;
        -webkit-margin-start: 0px;
        -webkit-margin-end: 0px;
    }
    .btn-primary {
        font-weight: 500;
        margin-left: 80%;
        width:81.6px;
        height:32.6px;
        color: #fff;
        background-color: #0C6EAB;
        border-color: #357ebd;
    }
    .btn.btn-default {
        width:53.6px;
        height:32.6px;
        font-weight: 500;
        background-color: #fafafa;
        color: #666;
        border-color: #ddd;
        border-bottom-color: #ddd;
    }
    .ui-table{
        width:100%;
        padding: 10px 0;
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
    .text-orange{
    	color:red;
    }
</style>
</head>

<body>
<div class="bills">
<form action="" method="post" class="bg-white" role="form" id="updateprice_form" data-validate-onsuccess="ajaxSubmit">
    <div class="modal-body">
        <h4 style="font-size: 14px;margin-top: 10px;  margin-bottom: 10px;">订单合计：<span class="text-orange" id = "total_fee"><?php echo $data['0']['total_fee'] ?></span>元</h4>
        <table class="ui-table">
            <thead>
            <tr>
                <td width = "50%">宝贝</td>
                <td width = "10%">数量</td>
                <td width = "15%">单价（元）</td>
                <td width = "15%">合计</td>
                <td width = "10%">历史价</td>
                <!-- <td nowrap>邮费（元）</td> -->
            </tr>
            </thead>
            <tbody>
            <?php foreach($data['0']['order'] as $item){ ?>
            <tr>
                <td class="col-2">
                    <div class="goods2">
                        <div class="left" style="margin-left: 20px;">
                            <img src="<?php echo $item['pic_path'] ?>" alt="<?php echo $item['title'] ?>" style="width: 56px;height: 56px;">
                        </div>
                        <div class="right" style="color: #00A5E3;">
                           <?php echo $item['title'] ?>
                            <div>
                                <?php if($item['spec_nature_info']){
                                    echo "(".$item['spec_nature_info'].")";
                                } ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td class = "order-num" style="font-size: 14px;">
                    <?php echo $item['num'] ?>
                </td>
                <td>
                    <div class="form-group">
                        <input type='text' id="price"  name="price[]" value="<?php echo $item['price'] ?>" class="form-control order-input-price" data-validate-numeric="true" size="3" style="width: 70px;height: 30px;">
                        <input type="hidden" id="oid" name="oid[]" value="<?php    $oid = $item['oid'];
                        $oid = $oid*1;
                        $oid = number_format($oid);
                        $oid = str_replace(',','',$oid);
                        echo $oid;
                        ?>" />
                    </div>
                </td>
                <td class = "order-total-fee" style="font-size: 14px;">
                    <?php echo $item['total_fee'] ?>
                </td>
                <td>
                    <div class = "history"><a  href="javascrip:;" style="font-size: 14px">历史价</a>
                        <div class = "history-detail">
                            <?php
                                foreach($list as $key=>$v){
                                    if($item['sku_id'] == $key) {
                                        foreach($v as $vo) {
                                            ?>
                                            <div class="moerve">
			              <span>
			              	  	下单时间：<?php echo date("Y-m-d h:i:s", $vo['modified_time']) ?>
			              </span>
                                                &nbsp;
                                                <span class=" text-orange">
			                  	成交价：<?php echo $vo['price'] ?>
			              </span>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                        </div>
                    </div>
                </td>

                <!-- <td class="border-left" rowspan="<{$orderItemCount}>">
                  快递:
                  <div class="form-group">
                    <input type="text" name="trade[post_fee]" value="<{$trade_detail.post_fee}>" class="form-control order-input-post" size="3">
                  </div>
                </td> -->

            </tr>
            <?php }?>
            </tbody>
        </table>

        <div>
            <p style="font-size: 12px">收货人：<?php echo $data['0']['receiver_name'] ?></p>
            <p style="font-size: 12px">收货地址：<?php echo $data['0']['receiver_state']?><?php echo $data['0']['receiver_city']?><?php echo $data['0']['receiver_district']?>&nbsp;<?php echo $data['0']['receiver_address'] ?></p>

            <!-- <p><{if $trade_detail.examine_reason }>审核答复：<span class="order-change-total text-orange"><{$trade_detail.examine_reason}></span><{/if}></p> -->

            <!-- <p>
                    买家支付：
                    <span class="order-change-total"><{$trade_detail.total_fee}></span> + <span class="text-success order-change-adjust"><{$orderitem.adjust_fee}></span> = <span class="text-orange order-change-payment"><{$trade_detail.payment}></span> 元
            </p>
            <p class="text-gray">买家实付  = 原价  + 涨价或减价</p>
            <p class="text-gray">店铺优惠**元已均摊到每个商品，并计算在单个商品的折扣中</p> -->


        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" id="tid" name="tid" value="<?php   $tid = $data['0']['tid'];
        $tid = $tid*1;
        $tid = number_format($tid);
        $tid = str_replace(',','',$tid);
        echo $tid;
        ?>">
        <!--<input type="submit" class="btn btn-primary" id="button" value="确认订单">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>-->
    </div>
</form>
    </div>


<style>


    .history{
        position: relative;
    }

    .history:hover .history-detail{
        display: block;
    }
    .history-detail{

        display: none;
        position:absolute;
        width:350px;
        border:2px solid #ddd;
        background:#fff;
        right:0px;
        text-align:left;
        z-index:999;
    }

    .history-detail .moerve{

        margin:5px 10px 5px 10px;
        padding:5px 0px 5px 0px;
        border-bottom:1px dashed #ddd;

    }

</style>

<script>
    function initPopBtns() {
        //$("#number").val(Public.getSuggestNum($("#preNum").val()));
        frameElement.api.button({
            id: "confirm",
            name: '确定',
            focus: !0,
            callback: function() {
                return postData(this),!1
            }
        }, {
            id: "cancel",
            name: '取消'
        })
    }
    function postData(b) {
        var params = $("#updateprice_form").serialize();
        var target = parent.parent.getIframe("shopSalesQuery-shopSalesQueryALL");
        Public.ajaxPost("../invSa/priceEdit", params, function(a) {
            200 === a.status ?  (parent.parent.Public.tips({
                content: "修改成功！"
            }),b.close(),target[0].contentWindow.refirsh()): parent.parent.Public.tips({
                type: 1,
                content: a.msg
            })
        })
    }

        $('.history').hover(function () {
            var item_id = $(this).attr('id');
            var tid = $(this).attr('tid');
            $('.moerve_' + item_id).css('display', 'block');
        }, function () {
            var item_id = $(this).attr('id');
            $('.moerve_' + item_id).css('display', 'none');
        });


        function ajaxSubmit(e) {
            var form = e.target;
            e.preventDefault();
            $.post(form.action, $(form).serialize(), function (rs) {
                $(form).find('button[type=submit]').prop('disabled', false);

                if (rs.error) {
                    $('#messagebox').message(rs.message);
                    return;
                }
                if (rs.success) {
                    $('#messagebox').message(rs.message, 'success');
                }
                if (rs.redirect) {
                    location.href = rs.redirect;
                }
            });
        }

        function calOrderEditPrice() {
            var totalPrices = $("#updateprice_form .order-total-fee");
            var nums = $("#updateprice_form .order-num");
            var prices = $("#updateprice_form .order-input-price");
            var seprices = $("#updateprice_form .settle_price");
            changePrice(totalPrices, prices, seprices);

            $("#updateprice_form .order-input-price").blur(function () {
                var price = this.value == "" ? 0 : parseFloat(this.value);
                var currindex = prices.index($(this));
                var curtotal = totalPrices.eq(currindex);
                var curnum = nums.eq(currindex);
                var settprice = seprices.eq(currindex);
                curtotal.text((price * 1000 * parseInt(curnum.text()) / 1000).toFixed(2));//更改总价
                this.value = price.toFixed(2);
                changePrice(totalPrices, prices, seprices);
            });
        }


        function changePrice(totalPrices, prices, seprices) {

            var tag = false;
            var totalprice = 0;
            var lp = $("#updateprice_form #lowestprice").val() == '' ? 1 : $("#updateprice_form #lowestprice").val()
            //判断是单价是否小于 结算价 * lp
            totalPrices.each(function (i) {
                var p = prices.eq(i);
                var s = seprices.eq(i);
                if (parseFloat(p.val()) < parseFloat(parseFloat(s.text()) * parseFloat(lp))) tag = true;
                totalprice += parseFloat($(this).text());
            })


            //显示需要审核提示
            if (tag) $("#updateprice_form #approvemessage").removeClass("hide");
            else  $("#updateprice_form #approvemessage").addClass("hide");

            $("#updateprice_form #total_fee").text(totalprice.toFixed(2));
        }

    initPopBtns();
    calOrderEditPrice();

</script>

</body>
</html>
