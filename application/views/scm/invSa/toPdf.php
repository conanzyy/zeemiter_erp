<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $transType==150601 ? '销售单' :'销售退货单'?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style></style>
</head>
<body>
		<table  width="900"  align="center" >
		    <tr height="20px">
		    	<td width="50"><img width=70 src="./statics/css/img/logo.png"></td>
				<td align="center" style="font-family:'宋体'; font-size:18px; font-weight:normal;" width="610"><strong><?php echo $system['companyName']?><?php echo $transType==150601 ? '销售单' :'销售退货单'?></strong></td>
				<td width="50"></td>
			</tr>
			<tr>
				<td align="center" colspan="3" width="710" style="font-family:'宋体'; font-size:18px; font-weight:normal;height:10px;"></td>
			</tr> 
		</table>	
		<table width="900" align="center">
			<tr height="15" align="left">
				<td align="left" width="140" style="font-family:'宋体'; font-size:13px;height:20px;">客户编码：<?php echo $contactNo ?> </td>
				<td align="left" width="240" style="font-family:'宋体'; font-size:13px;height:20px;">客户名称：<?php echo $contactName?> </td>
				<td align="left" width="190" style="font-family:'宋体'; font-size:13px;height:20px;">单据编号：<?php echo $billNo?></td>
				<td align="left" width="120" style="font-family:'宋体'; font-size:13px;height:20px;">单据日期：<?php echo $billDate?></td>
			</tr>
			<tr>
				<td align="left" width="150" style="font-family:'宋体'; font-size:13px;height:20px;">客户电话：<?php echo empty($contact['linkMoblie']) ? $contact['linkPhone'] : $contact['linkMoblie'] ?></td>
				<td align="left" colspan="3" style="font-family:'宋体'; font-size:13px; height:20px;">客户地址：<?php echo $contact['linkAddress'] ?></td>
			</tr>
		</table>	
		<table border="1" cellpadding="2" cellspacing="1" align="center" style="border-collapse:collapse;border:solid #000000;border-width:1px 0 0 1px;">   
				<tr>
				    <td width="240" style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;"  align="center">商品名称</td>
					<td width="30"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">单位</td>
					<td width="40"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">数量</td>
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">销售单价</td>	
					<td width="60"  style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">销售金额</td>
					<td width="60" 	style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">货位</td>
					<td width="80" style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">适用车型</td>	
					<td width="80" style="padding:2px; font-family:'宋体'; font-size:13px;height:15px;" align="center">备注</td>
				</tr>
		       <?php  foreach($list as $arr=>$row) { ?>
				<tr style="height:20px ;font-family:'宋体'; font-size:13px;">
				    <td width="240" style="border:solid #000000;border-width:0 1px 1px 0;height:15px;font-family:'宋体'; font-size:13px;" align="left">
				    	<?php echo ($row['goods']['brand_name']  ? ($row['goods']['brand_name'] . '/')  : '') .
						    	   ($row['goods']['productCode'] ? ($row['goods']['productCode'] . '/') : '') .
						    	   $row['goods']['name'].($row['goods']['spec'] ? ('/'.$row['goods']['spec']) : '')
				    	?>
				    </td>
					<td width="30"  align="center" style="font-family:'宋体';"><?php echo $row['goods']['unitName'] ?></td>
					<td width="40"  align="center" style="font-family:'宋体';"><?php echo $transType==150601 ? '' : '-'?><?php echo $row['qty'] ?></td>
					<td width="60"  align="center" style="font-family:'宋体';"><?php echo $transType==150601 ? '' : '-'?><?php echo str_money(abs($row['price'])) ?></td>	
					<td width="60"  align="center" style="font-family:'宋体';"><?php echo $transType==150601 ? '' : '-'?><?php echo str_money(abs($row['amount']), 2) ?></td>
					<td width="60"  align="center" style="font-family:'宋体';"><?php echo $row['area_code'] ?></td>
					<td width="80" align="left"  style="font-family:'宋体';font-size:11px;"><?php echo str_replace('/', " ", $row['carModel']) ?></td>
					<td width="80" align="left"  style="font-family:'宋体';font-size:12px;"><?php echo $row['description'] ?></td>
				</tr> 
				<?php } ?>
				 <tr>
				    <td colspan="2" style="padding:3px;" align=left>合计</td> 
					<td align="center"><?php echo $transType==150601 ? '' :'-'?><?php echo str_money(abs($totalQty),$system['qtyPlaces'])?></td>
					<td width="60" align="center"></td>
					<td width="60" align="center"><?php echo $transType==150601 ? '' :'-'?><?php echo str_money(abs($totalAmount),2)?></td>
					<td align="center" colspan=3 style = "border-width:0 1px 1px 0;">金额大写: <?php echo str_num2rmb(abs($totalAmount))?></td>
				</tr>
				  
				 
		</table>
		
<!--		<table  width="800" align="center">-->
<!--		  <tr height="15" align="left">-->
<!--				<td align="left" width="700" style="font-family:'宋体'; font-size:12px;height:25px;">备注： --><?php //echo $description?><!--</td>-->
<!--				<td width="0" ></td>-->
<!--				<td width="0" ></td>-->
<!--				<td width="0" ></td>-->
<!--				<td width="0" ></td>-->
<!---->
<!--		  </tr>-->
<!--		</table>-->
		
		<table width="900"  align="center" >
			<tr style="height:15px;">
				<td align="left" colspan="2"  style="font-family:'宋体'; height:20px;font-size:13px;	">服务站地址：<?php echo $system['companyAddr'] ?> </td>
				<td align="left" colspan="2" >联系电话：<?php echo $system['phone'] ?></td>
			</tr>
			<tr>
				<td width="240" style="font-family:'宋体'; font-size:13px;height:25px;">结算账户号：____________________</td>
				<td width="160" style="font-family:'宋体'; font-size:13px;height:25px;">仓检员：____________</td>
				<td width="150" style="font-family:'宋体'; font-size:13px;height:25px;">送货员:____________</td>
				<td width="150" style="font-family:'宋体'; font-size:13px;height:25px;">客户签字：____________</td>
			</tr>
			<tr>
				<td colspan=4 style="font-family:'宋体'; font-size:13px;">支付方式：
					<span style = "">□</span>现金&nbsp;
					<span style = "">□</span>支付宝&nbsp;
					<span style = "">□</span>微信&nbsp;
					<span style = "">□</span>银行转账&nbsp;
				</td>
			</tr>
		</table>
		
		 
</body>
</html>		