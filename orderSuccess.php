<?php
	ob_start();
	require_once('mainfile.php');
	include 'dbConfig.php';
	require_once ('includes/classes/business_detail.class.php');
	require_once ('includes/classes/review.class.php');
	require_once ('includes/classes/hot.class.php');
	require_once ('includes/classes/business.class.php');
	require_once('includes/classes/conversation.class.php');
    require_once ('includes/classes/photo.class.php');
    require_once('includes/classes/date_difference.class.php');
	require_once ('includes/classes/aboutme_profile.class.php');

?>

<html>

<head>
<div id="print">
<title>Your Order</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style type="text/css">

</style>
</head>

<body>
<br>
<br>

<div class="row">
<div class="col-sm-1"></div>
<div class="col-sm-3">
  <img src="img/logo.png" width="200" width="100">
  <p><b>Contact Number: 1234567890</b></p>
</div>
<?
$orderID= $_GET['id'];

$query = "SELECT * FROM customers INNER JOIN orders ON customers.orderid=orders.id WHERE orders.id = '$orderID'";

$result = $db->query($query);


if ($result->num_rows > 0) {

    $custRow = $result->fetch_assoc();
	
	$email = $custRow['email'];

	$deliverycharge = number_format($custRow['delivery_charge'],2) ;
	$subject = "example ::: Your Order";
	$headers.= "MIME-Version: 1.0\r\n";
	$headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers.= "From: example Team<order@example.com>\r\n";
    $headers.= "Reply-To: example Team<info@example.com>\r\n";
	$headers.= "X-MSMail-Priority: High\r\n";
			
	$order ="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

</head>
<body bgcolor='#E6E6FA'>  ";
	$shipping_details = "";
	
if(empty($custRow['s_first_name']))
{
$shipping_details =$shipping_details.''.$custRow['first_name'].' '.$custRow['last_name'].
                   '<br>'.$custRow['email']. '<br>'.$custRow['phone'].'<br>'.
				   $custRow['address'].','.$custRow['area'].' ,'.$custRow['city'].' ,'.$custRow['zip'];
}else{
$shipping_details =$shipping_details.''.$custRow['s_first_name'].' '.$custRow['s_last_name'].
                   '<br>'.$custRow['s_email']. '<br>'.$custRow['s_phone'].'<br>'.
				   $custRow['s_address'].','.$custRow['s_area'].' ,'.$custRow['s_city'].' ,'.$custRow['s_zip'];

}
	
$orderID= $_GET['id'];
$sql_order = "SELECT * FROM `order_items` WHERE order_id='$orderID'";
if($order_table = mysql_query ($sql_order))
{
while ($fetch_order = mysql_fetch_assoc($order_table)){
  $business_order="";
  $product_id= $fetch_order ['product_id'];

$sql_product_name = "SELECT * FROM business INNER JOIN product ON business.business_id=product.business_id WHERE    product.product_id='$product_id' ";
if($product_name = mysql_query ($sql_product_name))
{
while ($fetch_product_name = mysql_fetch_assoc($product_name)){

 $members_id = $fetch_product_name['members_id'];
 
 $business_email="";
 
 $sql_business_email = "SELECT * FROM `members` WHERE members_id='$members_id'";
if($order_business_email = mysql_query ($sql_business_email))
{
while ($fetch_business_email = mysql_fetch_assoc($order_business_email)){
	$business_email = $fetch_business_email['email'];
	//$business_email ="shajal16@gmail.com";
}
}
 //$business_email ="shajal16@gmail.com";
 $order_id = $fetch_order ['order_id'];
 $product_id = $fetch_order ['product_id'];
 $product_name = $fetch_product_name ['product_name'];
 $additional_instruction = $fetch_order ['additional_instruction'];
 if(empty($fetch_order ['customer_upload'])){
	$customer_upload = "no_image.jpg"; 
 }else{
 $customer_upload = $fetch_order ['customer_upload'];
 }
 $quantity = $fetch_order ['quantity'];
 $price = ($fetch_product_name ['price']-(($fetch_product_name ['price']/100)*$fetch_product_name ['discount']))*$fetch_order ['quantity'];
 $price=number_format($price,2);
  $order =$order.""."
 <tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$order_id</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$product_id</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$product_name</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$additional_instruction</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$quantity</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$price BDT</td>
 </tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'> ";
 
   $business_order =$business_order.""."
 <tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$order_id</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$product_id</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$product_name</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$additional_instruction</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$quantity</td>
 <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$price BDT</td>
 </tr> ";
 

 
}
}
 $body_1 ="
 <div class='row' style='background-color: #F0FFFF'>
  	<tr >
	<td>
    <h3>Shipping Details</h3>
     $shipping_details
	<br>
	</td>
	<td valign='left'>
	<img src='http://example.com/img/logomail.png' width='180' height='60'/>
	</td>
	</tr>
	<div class='row' >
	<h3>Order Details</h3>
	<table style='width:700px; border: 1px solid black;border-collapse: collapse;' class='table table-striped'>
    <thead>
    <tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Order ID</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Product ID</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Product Name</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Additional Instruction</th>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Quantity</th>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Total</th>
    </tr>
    </thead>
	<tbody style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
	<tr>$business_order</tr>
	</tbody>
	</table>
	</div>
	</div>
	</body>
</html>
	";
// echo $business_email;
 $subject_business = "example ::: You have new  Order";
 mail($business_email , $subject_business, $body_1, $headers);

}
}

$total_price =0;

$sql_order = "SELECT * FROM `orders` WHERE id='$orderID'";
 if($order_table = mysql_query ($sql_order))
{
while ($fetch_order = mysql_fetch_assoc($order_table)){
	$total_price = $fetch_order ['total_price'];
	}
}
 

$grandtotal = $total_price+$deliverycharge;
 $grandtotal= number_format($grandtotal,2);
     $body ="
<div class='row' style='background-color: #F0FFFF'>
	<tr >
	<td>
    <h3>Shipping Details</h3>
     $shipping_details
	<br>
	</td>
	<td valign='left'>
	<img src='http://example.com/img/logomail.png' width='180' height='60'/>
	</td>
	</tr>
	<div class='row' >
	<h3>Order Details</h3>
	<table  style='width:700px; border: 1px solid black;border-collapse: collapse;' class='table table-striped' >
    <thead>
    <tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Order ID</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Product ID</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Product Name</th>
										<th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Additional Instruction</th>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Quantity</th>
                                        <th style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>Total</th>
    </tr>
    </thead>
	<tbody style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
	<tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>$order </tr>
   <tr style='border-color: #9932CC; border: 1px solid black' class='table table-striped'>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>Sub Total<b></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>$total_price BDT</b></td>
   </tr>
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>Home Delivery<b></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>$deliverycharge BDT</b></td>
   </tr>
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>Total Price<b></td>
   <td style='border-color: #9932CC; border: 1px solid black' class='table table-striped'><b>$grandtotal BDT</b></td>
   </tr>
	</tbody>
	</table>
	</div>
	</div>
</body>
</html>";
//echo $body;
  mail($email , $subject, $body, $headers);
  $uid = $auth->get_members_id();
  header("Location: aboutme_orderhistory.php?uid=".$uid);
	
?>
<div class="col-sm-4"></div>
<div class="col-sm-3">
<h3>Invoice No: <b><?php echo  $_GET['id'];?></b></h3>
<?php echo  date("d-m-Y");?>
</div>
<div class="col-sm-1"></div>
</div>

<div class="row">
<div class="col-sm-3"></div>
<div class="col-sm-6">
<h3>Shipping Details</h3>
<?
if(empty($custRow['s_first_name']))
{
?>
<b><p><?php echo $custRow['first_name'].' '.$custRow['last_name'] ; ?></p>
<p><?php echo $custRow['email'] ; ?></p>
<p><?php echo $custRow['phone'] ; ?></p>
<p><?php echo $custRow['address'].','.$custRow['area'].' ,'.$custRow['city'].' ,'.$custRow['zip']; ?></p></b>
<?
}else{
?>
<b><p><?php echo $custRow['s_first_name'].' '.$custRow['s_last_name'] ; ?></p>
<p><?php echo $custRow['s_email'] ; ?></p>
<p><?php echo $custRow['s_phone'] ; ?></p>
<p><?php echo $custRow['s_address'].','.$custRow['s_area'].' ,'.$custRow['s_city'].' ,'.$custRow['s_zip']; ?></p></b>
<?
}
?>
</div>
<div class="col-sm-3"></div>
</div>
<?
}
?>
<!--/////////////////////////////////////////////-->
           <div class="row">
		   	<br>
	        <br>
		   <div class="col-sm-3"></div>
		   <div class="col-sm-6 container">
   <table class="table table-striped">
    <thead>
      <tr>
                                        <th>Order ID</th>
										<th>Product ID</th>
										<th>Product Name</th>
										<th>Additional Instruction</th>
										<th>Customer Upload</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
      </tr>
    </thead>
<?
 $orderID= $_GET['id'];
 $sql_order = "SELECT * FROM `order_items` WHERE order_id='$orderID'";
 if($order_table = mysql_query ($sql_order))
{
while ($fetch_order = mysql_fetch_assoc($order_table)){
	
  $product_id= $fetch_order ['product_id'];

$sql_product_name = "SELECT * FROM `product` WHERE product_id='$product_id' ";
if($product_name = mysql_query ($sql_product_name))
{
while ($fetch_product_name = mysql_fetch_assoc($product_name)){

?> 
    <tbody>
      <tr>
 <td><?php echo $fetch_order ['order_id']; ?></td>
 <td><?php echo $fetch_order ['product_id']; ?></td>
 <td><?php echo $fetch_product_name ['product_name'];?></td>
 <td><?php echo $fetch_order ['additional_instruction']; ?></td>
 <td>
 <?
 if(!empty($fetch_order ['customer_upload']))
 {
 ?>
 <img  src="customerupload/<?php echo $fetch_order ['customer_upload']; ?>" style="width:50px;height:50px">
 <?
 }else{
 ?>
 <img  src="customerupload/no_image.jpg" style="width:50px;height:50px">
 <?
 }
 ?>
 </td>
 <td><?php echo $fetch_order ['quantity']; ?></td>
 <td><?php echo $fetch_order ['quantity']*$fetch_product_name ['price']; ?></td>
      </tr>
<?
}
}

}
}else{
	echo mysql_error();
}
?>

<?
 $orderID= $_GET['id'];
 $sql_order = "SELECT * FROM `orders` WHERE id='$orderID'";
 if($order_table = mysql_query ($sql_order))
{
while ($fetch_order = mysql_fetch_assoc($order_table)){

?>
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td><b>Total Price<b></td>
   <td><b><?php echo $fetch_order ['total_price']; ?></b></td>
   </tr>
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td><b>Home Delivery<b></td>
   <td><b>100.00</b></td>
   </tr>
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td><b>Grand Total<b></td>
   <td><b><?php echo $fetch_order ['total_price']+100.00 ; ?></b></td>
   </tr>
<?
}
}else{
	//echo mysql_error();
}
?>
   </tbody>
  </table>

        <div class="col-sm-3"></div>
        </div>
<!--/////////////////////////////////////////////-->


      
</div>
</div>

<div class="row">
<div class="col-sm-6"></div>
<div class="col-sm-5"></div>
<div class="col-sm-1">

  <button type="button" onclick="print()"class="btn btn-info">print</button>
</div>
</div>




<script>
   function print() {
var prtContent = document.getElementById("print");
var WinPrint = window.open('', '',);
WinPrint.document.write(prtContent.innerHTML);
WinPrint.document.close();
WinPrint.focus();
WinPrint.print();
WinPrint.close();
}
</script>
 </body>
 
</html>