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
<script src="<?php echo base_url()?>statics/js/common/ichart.1.2.min.js"></script>
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
        <div class="modal-body">
            <h1 style="font-size: 18px;">客户：<?php echo $name ?></h1>
            <br/>
            <table class="ui-table">
                <thead>
                <tr>
                    <td style="width: 40%;">宝贝</td>
                    <td style="width: 30%;">仓库</td>
                    <td style="width: 30%;">当前库存</td>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($res as $vo) {   ?>
                         <tr>
                             <td class="col-2">
                                 <?php echo $vo['goodsName'] ?>
                             </td>
                             <td class="order-num" style="font-size: 14px;">
                                 <?php echo $vo['storageName'] ?>
                             </td>
                             <td>
                                 <?php echo $vo['num'] ?>
                             </td>
                         </tr>
                    <?php }?>
                </tbody>
            </table>
            <br/>
            <table class="ui-table">
                <thead>
                <tr>
                        <td style="text-align: center">销售记录</td>
                </tr>
                </thead>
                <tbody>
                <?php if($result){  ?>
                    <tr>
                        <td class="col-2">
                            <div id='canvasDivs'></div>
                        </td>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td>无</td>
                    </tr>
                <?php }  ?>
                </tbody>
            </table>
            <br/>
            <table class="ui-table">
                <thead>
                <tr>
                    <td style="text-align: center">采购记录</td>
                </tr>
                </thead>
                <tbody>
                <?php if($result1){ ?>
                    <tr>
                        <td class="col-2">
                            <div id='canvasDiv'></div>
                        </td>
                    </tr>
               <?php }else{ ?>
                    <tr>
                        <td>无</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
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
    function caigou() {
        var data = [
            {
                name: '采购',
                value: [0,<?php echo $prices ?>],
                color: '#1f7e92',
                line_width: 3
            }
        ];
        var chart = new iChart.LineBasic2D({
            render: 'canvasDiv',
            data: data,
            title: '',
            width: 835,
            height: 200,
            coordinate: {height: '90%', background_color: '#f6f9fa'},
            sub_option: {
                hollow_inside: false,//设置一个点的亮色在外环的效果
                point_size: 16
            },
            labels: [0,<?php echo $times ?>, " "]
        });
        chart.draw();
    }
    function sale() {
        var data1 = [
            {
                name: '销售',
                value: [0,<?php echo $price ?>],
                color: '#1f7e92',
                line_width: 3
            }
        ];
        var chart1 = new iChart.LineBasic2D({
            render: 'canvasDivs',
            data: data1,
            title: '',
            width: 835,
            height: 200,
            coordinate: {height: '90%', background_color: '#f6f9fa'},
            sub_option: {
                hollow_inside: false,//设置一个点的亮色在外环的效果
                point_size: 16
            },
            labels: [0,<?php echo $time ?>, " "]
        });
        chart1.draw();
    }

    if($("#canvasDiv")[0]){caigou()};
    if($("#canvasDivs")[0]){sale()};
</script>

</body>
</html>
