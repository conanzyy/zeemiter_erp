<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> 采购订单 </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style></style>
</head>
<body>
		<table  width="900"  align="center" >
			<tr height="20px">
				<td width="50"><img width=60 src="./statics/css/img/logo.png"></td>
				<td align="center" style="font-family:'宋体'; font-size:18px; font-weight:normal;" width="610"><strong><?php echo $system['companyName']?>入库单 </strong></td>
				<td width="50"></td>
			</tr>
			<tr>
				<td align="center" colspan="3" width="710" style="font-family:'宋体'; font-size:18px; font-weight:normal;height:10px;"></td>
			</tr>
		</table>
		<table border="1" cellpadding="2" cellspacing="1" align="center" style="border-collapse:collapse;border:solid #000000;border-width:1px 0 0 1px;">   
				<tr>
				    <td width="240" style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;"  align="center">商品名称</td>
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">未入库数量</td>
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">已出库数量</td>
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">入库仓库</td>
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">入库货位</td>
				</tr>
		       <?php  foreach($list as $arr=>$row) { ?>
				<tr style="height:20px ;font-family:'宋体'; font-size:13px;">
				    <td width="240" style="border:solid #000000;border-width:0 1px 1px 0;height:15px;font-family:'宋体'; font-size:13px;" align="left"><?php echo $row['goods'] ?></td>
					<td><?php echo $row['waitInto'] ?></td>
					<td><?php echo $row['outNum'] ?></td>
					<td><?php echo $row['location'] ?></td>
					<td><?php echo $row['area'] ?></td>
				</tr>
				<?php } ?>

				  
				 
		</table>
		
		 
</body>
</html>		