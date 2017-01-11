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
</style>
</head>

<body>
<div class="bills">
<form action="" method="post" class="bg-white" role="form" id="updateprice_form" data-validate-onsuccess="ajaxSubmit">
    <div class="modal-body">
        <table class="ui-table">
            <thead>
            <tr>
                <td>选择</td>
                <td>付款方式</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach($way as $item) { ?>
                <tr>
                    <td>
                        <label>
                            <input type="radio" name="wayId" id="wayId"  value="<?php echo $item['id'] ?>">
                            <span class="text"></span>
                        </label>
                    </td>
                    <td>
                        <?php echo $item['name'] ?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
        </table>
        <br/>
                        <label>结算账户:</label>
                        <select style="width: 100px;height: 30px;" id="acc">
                            <?php foreach($account as $row){ ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php }?>
                        </select>
    </div>
    <div class="modal-footer">
        <input type="hidden" id="tid" name="tid" value="<?php
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
//        alert($("#acc option:selected").val());
        var acc = $("#acc option:selected").val(),
            wayId = $("#wayId").val(),
            tid = $("#tid").val();
        var params = {accId:acc,wayId:wayId,tid:tid};
        var target = parent.parent.getIframe("shopSalesQuery-shopSalesQueryALL");
        Public.ajaxPost("../invSa/shopOrderFinish", params, function(a) {
            200 === a.status ?  (parent.parent.Public.tips({
                content: "已付款完成！"
            }),b.close(),target[0].contentWindow.refirsh()): parent.parent.Public.tips({
                type: 1,
                content: a.msg
            })
        })
    }


    initPopBtns();

</script>

</body>
</html>
