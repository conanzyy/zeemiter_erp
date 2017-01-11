<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html>
<html>
<head>
<title>快准车服-店管家</title>
<meta charset="utf-8">
<meta name="viewport" content="width=1280, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit|ie-stand|ie-comp">
<link href="<?php echo base_url()?>statics/css/img/favicon.ico" type="image/x-icon" />
<link href="<?php echo base_url()?>statics/login/css/common.css" rel="stylesheet" />
<script src="<?php echo base_url()?>statics/login/scripts/minijs/jquery-1.7.1.js"></script>
<script src="<?php echo base_url()?>statics/login/scripts/minijs/common.js"></script>
<script src="<?php echo base_url()?>statics/login/scripts/minijs/minicheck.js"></script>

<style>
body{height:100%;background:#16a085;overflow:hidden;}
</style>

<body id="body">
	<div class="fix-bg"></div>
	<form action="" onSubmit="return Login()" autocomplete="off">
	<dl class="admin_login">
		<div class = "biaoyu"></div>
		<dt>
			<div class = "admin-logo"></div><strong>服务站-店管家</strong><em>Inventory Management System</em>
		</dt>
		<dd class="loginerror" id = "loginerror" >
			<p class="error_tips " style = "display:none"></p>
 		</dd>
		<dd class="user_icon form-row">
			<div class = "u-logo"><div class = "u-logo-img"></div></div>
			<div class = "form-input">
				<input type="text" class="logininput login_txtbx" name="username" id="username" value="<?php echo get_cookie('username')?>" autocomplete="off"  placeholder="输入账号" />
			</div>
 		</dd>
		<dd class="pwd_icon form-row">
			<div class = "u-logo"><div class = "u-logo-img2"></div></div>
			<div class = "form-input">
				<input type="password" class="logininput login_txtbx" name="password" id="password" value="<?php echo get_cookie('userpwd')?>" autocomplete="off" placeholder="输入密码" />
			</div>
			<input type="password"  style="visibility: hidden;"/>
		</dd>
		<dd>
			<div class = "remember-me"><label><input type = "checkbox" id="Checked" value = "1"  <?php echo get_cookie('ispwd')==1 ? 'checked=checked' : ''?>  value = "<?php echo get_cookie('ispwd')?>">记住账号</label></div>
		</dd>
		<dd>
			<input type="button" class="submit_btn" id="btnLogin" value = "立即登陆" onClick="Login()" />
		</dd>
		<dd>
			<br/>
			<br/>
			<!-- <p></p> -->
		</dd>
	</dl>
	</form>
	<script type="text/javascript">
    //加载公用的js最后面
    $(window).load(function(){
        if(navigator.userAgent.toLowerCase().indexOf("chrome") != -1){
        　　　　var selectors = document.getElementsByTagName("input");
        　　　　for(var i=0;i<selectors.length;i++){
        　　　　　　if((selectors[i].type !== "submit") && (selectors[i].type !== "button") && (selectors[i].type !== "checkbox") && (selectors[i].type !== "password")){
					var input = selectors[i];
					var inputName = selectors[i].name;
					var inputid = selectors[i].id;
					selectors[i].removeAttribute("name");
					selectors[i].removeAttribute("id");
					setTimeout(function(){
				　　input.setAttribute("name",inputName);
				　　input.setAttribute("id",inputid);
				},1)
        　　　　　　}

        　　　　}
        　　	} 
    });

    $("input.logininput").on("focus",function(){
		$(this).parent().parent(".form-row").addClass("on");
     })
     $("input.logininput").on("blur",function(){
		$(this).parent().parent(".form-row").removeClass("on");
     })
     
     
     
    
    function Login() {
        var cookieEnabled = (navigator.cookieEnabled) ? true : false;
        if (!cookieEnabled) {
            alert("该浏览器Cookie设置不正确，无法正常登录");
            return false;
        }
        var username = $.trim($("#username").val());
        var password = $.trim($("#password").val());
        var isRemmenbPassWord = $("#Checked:checked").val() != null ? 1 : 0; // 1为记住密码  0 未记住密码
        if (checkNullOrEmpty(username)) {
            $("#loginerror .error_tips").text("请输入账号").show();
            $("#username").focus();
            return false;
        }
        if (checkNullOrEmpty(password)) {
            $("#loginerror .error_tips").text("请输入密码").show();
            $("#password").focus();
            return false;
        }
        $("#btnLogin").val("登录中...")
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('login');?>",
            data: {
                username: username,
                userpwd: password,
				token: '<?php echo token()?>',
                ispwd:isRemmenbPassWord
            },
            dataType: "json",
            success: function (data) {
				if(data.error){
					$("#btnLogin").val("登录");
					$("#loginerror .error_tips").text(data.message).show();	
				}
				if(data.success){

				}
				if(data.redirect){
					window.top.location.href = data.redirect;
				}
				//$("#btnLogin").val("登录");
                return false;
            },
            timeout: 60000,
            error: function (xhr, status) {
            	 $("#btnLogin").val("登录");
                if (status == "timeout") {
                    $("#loginerror .error_tips").text("您的网络好像很糟糕，请刷新页面重试").show();
                    return false;
                }
                else {
                    $("#loginerror .error_tips").text("服务器内部错误，请重试").show();
                    return false;
                }
            }
        });
        return false;
    }
    $(function () {
        document.onkeydown = function(e) {
            var ev = document.all ? window.event : e;
            if (ev.keyCode == 13) {
                $("#btnLogin").trigger("click");
            }
        };
    });
</script>

</body>
</html>
