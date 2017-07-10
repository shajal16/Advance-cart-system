<?php
ob_start();
require_once 'mainfile.php';
require_once 'includes/classes/business.class.php';
require_once ('includes/classes/business_detail.class.php');

    $id = 0;
	if(isset($_POST['id'])){
	$id = $_POST['id'];
	}
	
$previd= $id;

$demoorderid ="";	
$sql_orderid ="SELECT * FROM `order_items` WHERE id='$id'";
if($output_sql_orderid =mysql_query($sql_orderid))
{
	while($row_sql_orderid = mysql_fetch_assoc($output_sql_orderid))
	{
		$demoorderid =$row_sql_orderid['order_id'];
	}
}
$id = $demoorderid;

$output="";
$sql_product_name = "SELECT * FROM `order_items` WHERE order_id='$id' ";
if($product_name = mysql_query ($sql_product_name))
{
	$i=0;
	$product_name_chk = "";
while ($fetch_product_name = mysql_fetch_assoc($product_name)){
	$product_id = $fetch_product_name ['product_id'];
	
			$sql_product_name = "SELECT * FROM  product  WHERE product_id='$product_id' ";
            
			if($output_product_name = mysql_query ($sql_product_name))
           {
             while ($fetch_product_name = mysql_fetch_assoc($output_product_name)){

             $product_name_chk = $fetch_product_name['product_name'];
			 }
		   }
	$output=$output.""."
	<div class='radio'>
    <label><input class='radioBtn' id='radioBtn_$i' type='radio'  value='$product_id '>$product_id ($product_name_chk) </label>
    </div>";
	$i = $i+1;
}
}
   echo"
    <form id='cancelorder' method='post'>
	<h3>DELETE Whole Order</h3>
	<div onchange='orderstatus()'>
    <div class='radio'>
      <label><input type='radio'  id='orderstatus_yes' name='orderinfo' value='yes'>yes</label>
    </div>
    <div class='radio'>
      <label><input type='radio' id='orderstatus_no' name='orderinfo' value='no'>no</label>
    </div>
	</div>
	<div id='product' style='display:none'>
	<h3>DELETE Particuler Product</h3>
	$output
	</div>
	<div class='form-group'>
    <label for='text'>Account Name:</label>
    <input type='text' class='form-control' id='accountname'>
    </div>
	<div class='form-group'>
    <label for='text'>Account Number:</label>
    <input type='text' class='form-control' id='accountnumber'>
    </div>
	<div class='form-group'>
    <label for='text'>Bank Name:</label>
    <input type='text' class='form-control' id='bankname'>
    </div>
	<div class='form-group'>
    <label for='text'>Bank Address:</label>
    <input type='text' class='form-control' id='bankaddress'>
    </div>
	<div class='form-group'>
    <label for='text'>Contact Number:</label>
    <input type='text' class='form-control' id='contactnumber'>
    </div>
	<button onclick='delete_order($previd);' class='btn btn-primary btn-block'>Submit</button>
  </form>
   ";
?>