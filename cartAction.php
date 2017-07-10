<?php
date_default_timezone_set('UTC');

$imagename=null;
if(isset($_FILES["file"]["name"])){
$allowedExts = array("gif", "jpeg", "jpg", "png");
$imagename='';
$temp = explode(".", $_FILES["file"]["name"]);

$extension = end($temp);

// check file type
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
&& ($_FILES["file"]["size"] < 2097152)  // limit the size of the file to 2mb
&& in_array($extension, $allowedExts)){

  // check if there was an error
  if($_FILES["file"]["error"] > 0){
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
  } else {
	$ran = rand();  
	//echo $ran;
    // add the date to the filename 
    $file_name = $temp[0].$ran;
    // add the extension back on. 
    $file = $file_name.".".$temp[1];
    // move the file to its new location
	//echo $file_name;
    move_uploaded_file( $_FILES["file"]["tmp_name"], "customerupload/" .$file);
       $imagename= $file;
	   //echo $imagename;
    //echo '<img src="upload/'.$_FILES["file"]["name"].'">';
  }
} else {
 // echo "You have uploaded an invalid file.";
 // header("Location: welcome.php");
}
}else {
	
	$imagename=null;
}
// initialize shopping cart class
include 'cartmain.php';
//echo "koko";
$cart = new Cart;
$uid = $auth->get_members_id();

$_SESSION['city']="dhaka";
/*if($uid!="" || $uid!=0){
$res2= db_select("select * from members where members_id='$uid'");
		while($m_row = mysql_fetch_array($res2)){

		$_SESSION['city'] = $m_row['city']; 


		}*/
//}elseif(isset($_POST['city']) ||isset($_POST['cityship']) ){
	if(isset($_REQUEST['cityship'])){
		
	$_SESSION['city'] = $_REQUEST['cityship'];
		
	}elseif(isset($_REQUEST['city'])){
	$_SESSION['city'] = $_REQUEST['city'];	
		
	}elseif(($uid!="" || $uid!=0)){
	$res2= db_select("select * from members where members_id='$uid'");
		while($m_row = mysql_fetch_array($res2)){

		$_SESSION['city'] = $m_row['city']; 


		}	
		
		
	}
	
//}
	



$cityname=$_SESSION['city'];
/*$uid='';
if ( isset($_COOKIE["members_id"]) ) {
				$uid = $_COOKIE["members_id"];
}
*/
//$uid = $_COOKIE["members_id"];
$addi = null;
//$imagename=null;
if(isset($_POST['quantity'])){
   $qty= $_POST["quantity"];
 }

  
   
   
   

 
 
 if(isset($_POST['addi'])){
   $addi= $_POST["addi"];
}

 if(isset($_POST['price'])){
   $price= $_POST["price"];
}
//echo $price;
// include database configuration file
include 'dbConfig.php';
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
    if($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['id'])){
        $productID = $_REQUEST['id'];
        // get product details

      
$query = "SELECT *
FROM product
INNER JOIN product_image
ON product.product_id=product_image.product_id
WHERE product.product_id='$productID'" ;

$result = $db->query($query);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {


        $itemData = array(
            'image' => $row['product_image_id'],
            'id' => $row['product_id'],
            'name' => $row['product_name'],
            'price' => $price,
			'customerupload' => $imagename,
            'qty' => $qty,
			'addi'=> $addi,
			'weight' => $row['weight']
        );
    }
} 


       $insertItem = $cart->insert($itemData);
      $redirectLoc = $insertItem?"viewcartmain.php?id=$productID":"product_details.php?id=$productID";
	 //echo "<script> alert ('$insertItem') </script>";
      header("Location: ".$redirectLoc); 
	   
    }elseif($_REQUEST['action'] == 'updateCartItem' && !empty($_REQUEST['id'])){
		
        $itemData = array(
            'rowid' => $_REQUEST['id'],
            'qty' => $_REQUEST['qty']
        );
        $updateItem = $cart->update($itemData);
/*----------------------------------------------------*/
$output = "";
$output = $output.""."
    <table class='table table-striped'>
    <thead>
        <tr>
            <th class='col-md-1'>Image</th>
            <th class='col-md-1'>Product</th>
			<th class='col-md-2'><p id='addi'>Additional Instruction</p></th>
			<th class='col-md-1'><p id='custupload'>Customer Upload</p></th>
			<th class='col-md-1'>Price</th>
            <th class='col-md-2'>Quantity</th>
			<th class='col-md-2'>Weight</th>
            <th class='col-md-3'>Subtotal</th>
            <th class='col-md-2'></th>
        </tr>
    </thead>
    <tbody>
          ";
	$deliverycharge = 0;
	$addi = 0;
	$custupload = 0;
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$inventory_product=0;
	$count = 0;
	$diff = 0;
	$totalweight=0;
    if($cart->total_items() > 0){
		
            //get cart items from session
    $cartItems = $cart->contents();
	
    $n=0;
    foreach($cartItems as $item){
	$n=$n+1;	
	$product_name = $item["name"];
	$product_id ="";
	$sql_delivery ="SELECT * FROM product WHERE product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_sql = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_sql['business_id'];
			 $product_chk_bid_prev = $output_sql['business_id'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_sql['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
		   }
		   
		   $inventory_product = $output_sql['inventory'];
		   $product_id = $output_sql['product_id'];
		}
	}
	
	$count=$count+1;

    $totalweight = $totalweight+($item['weight']*$item['qty']);
	$image = $item['image'];
	$name = $item["name"];
	$additional ="";
	if ($item["addi"]!=''){
	$addi = $addi+1;
	$additional = $item["addi"];
	}else{
		
	}
	$customuploader ="";
	if (!empty($item['customerupload'])){
	$custupload =$custupload+1;
	$customoutput = $item['customerupload'];
	$customuploader ="<img  src='customerupload/$customoutput' style='width:50px;height:50px'>";
	}else{
	$customuploader ="";
	}
	$price = number_format((float)$item["price"], 2, '.', '').' BDT';
	$qty = $item["qty"];
	$chk =  $item["rowid"];
	$weight_product = ($item['weight']*$item['qty']);
	$subtotal = number_format((float)$item["subtotal"], 2, '.', '').' BDT';
	
	
	$output = $output.""."
	<tr>
    <td>
	<a href='product_details.php?id=$product_id'>
    <img  src='product_photo/resized/product_small_$image.jpg' style='width:50px;height:50px'></a> 
    </td>
	<td>$name</td>
	<td>$additional</td>
	<td>$customuploader</td>
	<td>$price</td>
	<td><input type='number' class='form-control' id='quantity' value='$qty' onchange='koko(this,$n,$inventory_product)' min='1' max='$inventory_product'></td>
	<td>$weight_product</td>
	<td>$subtotal</td>
	<td>
    <p class='btn btn-danger btn-circle' style='border-radius:25px;' onclick='deleteitem($n)'><i class='glyphicon glyphicon-remove'></i></p>
    </td>
	";
	}
	 if($addi==0){
	?>
	<script>
	document.getElementById("addi").style.display = "none";
	</script>
    <?	
	 }
	if($custupload==0)
	{
	?>
	<script>
	document.getElementById("custupload").style.display = "none";
	</script>	
	<?
	}
	
		
	}else{
		$output = $output.""."<td colspan='5'><p>Your cart is empty.....</p></td>";
	}
	
	$carttotal = number_format((float)$cart->total(), 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Subtotal : </b></td>
    <td>$carttotal</td>
    <td></td>
    </tr>";
	
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$count = 0;
	$different = 0;
	$deliveryday = 0;
    if($cart->total_items() > 0){
		$cartItems = $cart->contents();
	foreach($cartItems as $item){
			
	$product_name = $item["name"];
	$sql_delivery ="SELECT * FROM product INNER JOIN business ON product.business_id=business.business_id WHERE product.product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_name = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_name['business_id'];
			 $product_chk_bid_prev = $output_name['business_id'];
			 $deliveryday =$output_name['maxdeliveryday'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_name['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $different = $different+1;
			   }
			   $temp = $output_name['maxdeliveryday'];
			   if($temp>$deliveryday)
			   {
				   $deliveryday = $temp;
			   }
		   }
		   
		   
		}
	}
	
	$count=$count+1;
		}
	}

	
	$deliverycharge = 0;
	
	
	$sameshop = 0;
	$diffshop = 0;
	$demo = "";
	
	
	




	
	$sql_delivery_charge ="SELECT * FROM `delivery_charge`";
	if($result_delivery_charge = mysql_query($sql_delivery_charge))
	{
		while($output_delivery_charge = mysql_fetch_assoc($result_delivery_charge))
		{
			$minweight = $output_delivery_charge['min'];
            $maxweight = $output_delivery_charge['max'];
			
	if($totalweight ==0){
	 $deliverycharge = 0;
	}else{
	if($totalweight>=$minweight && $totalweight<=$maxweight)
	{	

		if($cityname=="dhaka" || $cityname=="Dhaka" || $cityname=="DHAKA" )
			
			{
		  $sameshop =  $output_delivery_charge['charge'];
		  $diffshop =  $output_delivery_charge['diff_shop'];
			}else{
				$sameshop =  $output_delivery_charge['outsidesame'];
		  $diffshop =  $output_delivery_charge['outsidedifferent'];
				
			}
	}	

	}
	  
	    }

		if($different>=1){
			$deliverycharge = $diffshop;			
		}else{
			$deliverycharge = $sameshop ;
		}
	}
	
	$grandtotal =number_format((float)($cart->total()+$deliverycharge), 2, '.', '').' BDT';
	$deliverycharge1 = number_format((float)$deliverycharge, 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Total Weight : </b></td>
    <td><b>$totalweight</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Home Delivery: </b></td>
    <td><b>$deliverycharge1</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Grand Total: </b></td>
    <td><b>$grandtotal</b></td>
    <td></td>
    </tr>


    </tbody>

    </table>";
	
	
/*----------------------------------------------------*/
       echo $updateItem?$output:'err';die;
    }elseif($_REQUEST['action'] == 'updatecity'){
		//echo 'asi';
$output = "";
$output = $output.""."
    <table class='table table-striped'>
    <thead>
        <tr>
            <th class='col-md-1'>Image</th>
            <th class='col-md-1'>Product</th>
			<th class='col-md-2'><p id='addi'>Additional Instruction</p></th>
			<th class='col-md-1'><p id='custupload'>Customer Upload</p></th>
			<th class='col-md-1'>Price</th>
            <th class='col-md-2'>Quantity</th>
			<th class='col-md-2'>Weight</th>
            <th class='col-md-3'>Subtotal</th>
            <th class='col-md-2'></th>
        </tr>
    </thead>
    <tbody>
          ";
	$deliverycharge = 0;
	$addi = 0;
	$custupload = 0;
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$inventory_product=0;
	$count = 0;
	$diff = 0;
	$totalweight=0;
	
    if($cart->total_items() > 0){
		
            //get cart items from session 
    $cartItems = $cart->contents();
	
    $n=0;
    foreach($cartItems as $item){
	$n=$n+1;	
	$product_name = $item["name"];
	$product_id ="";
	$sql_delivery ="SELECT * FROM product WHERE product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_sql = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_sql['business_id'];
			 $product_chk_bid_prev = $output_sql['business_id'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_sql['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
		   }
		   
		   $inventory_product = $output_sql['inventory'];
		   $product_id = $output_sql['product_id'];
		}
	}
	
	$count=$count+1;

    $totalweight = $totalweight+($item['weight']*$item['qty']);
	$image = $item['image'];
	$name = $item["name"];
	$additional ="";
	if ($item["addi"]!=''){
	$addi = $addi+1;
	$additional = $item["addi"];
	}else{
		
	}
	$customuploader ="";
	if (!empty($item['customerupload'])){
	$custupload =$custupload+1;
	$customoutput = $item['customerupload'];
	$customuploader ="<img  src='customerupload/$customoutput' style='width:50px;height:50px'>";
	}else{
	$customuploader ="";
	}
	$price = number_format((float)$item["price"], 2, '.', '').' BDT';
	$qty = $item["qty"];
	$chk =  $item["rowid"];
	$weight_product = ($item['weight']*$item['qty']);
	$subtotal = number_format((float)$item["subtotal"], 2, '.', '').' BDT';
	
	
	$output = $output.""."
	<tr>
    <td>
	<a href='product_details.php?id=$product_id'>
    <img  src='product_photo/resized/product_small_$image.jpg' style='width:50px;height:50px'></a> 
    </td>
	<td>$name</td>
	<td>$additional</td>
	<td>$customuploader</td>
	<td>$price</td>
	<td><input type='number' class='form-control' id='quantity' value='$qty' onchange='koko(this,$n,$inventory_product)' min='1' max='$inventory_product'></td>
	<td>$weight_product</td>
	<td>$subtotal</td>
	<td>
    <p class='btn btn-danger btn-circle' style='border-radius:25px;' onclick='deleteitem($n)'><i class='glyphicon glyphicon-remove'></i></p>
    </td>
	";
	}
	 if($addi==0){
	?>
	<script>
	document.getElementById("addi").style.display = "none";
	</script>
    <?	
	 }
	if($custupload==0)
	{
	?>
	<script>
	document.getElementById("custupload").style.display = "none";
	</script>	
	<?
	}
	
		
	}else{
		$output = $output.""."<td colspan='5'><p>Your cart is empty.....</p></td>";
	}
	
	$carttotal = number_format((float)$cart->total(), 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Subtotal : </b></td>
    <td>$carttotal</td>
    <td></td>
    </tr>";
	
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$count = 0;
	$different = 0;
	$deliveryday = 0;
    if($cart->total_items() > 0){
		$cartItems = $cart->contents();
	foreach($cartItems as $item){
			
	$product_name = $item["name"];
	$sql_delivery ="SELECT * FROM product INNER JOIN business ON product.business_id=business.business_id WHERE product.product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_name = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_name['business_id'];
			 $product_chk_bid_prev = $output_name['business_id'];
			 $deliveryday =$output_name['maxdeliveryday'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_name['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $different = $different+1;
			   }
			   $temp = $output_name['maxdeliveryday'];
			   if($temp>$deliveryday)
			   {
				   $deliveryday = $temp;
			   }
		   }
		   
		   
		}
	}
	
	$count=$count+1;
		}
	}

	
	$deliverycharge = 0;
	
	
	$sameshop = 0;
	$diffshop = 0;
	$demo = "";
	
	
	




	
	$sql_delivery_charge ="SELECT * FROM `delivery_charge`";
	if($result_delivery_charge = mysql_query($sql_delivery_charge))
	{
		while($output_delivery_charge = mysql_fetch_assoc($result_delivery_charge))
		{
			$minweight = $output_delivery_charge['min'];
            $maxweight = $output_delivery_charge['max'];
			
	if($totalweight ==0){
	 $deliverycharge = 0;
	}else{
	if($totalweight>=$minweight && $totalweight<=$maxweight)
	{	

		if($cityname=="dhaka" || $cityname=="Dhaka" || $cityname=="DHAKA" )
			
			{
		  $sameshop =  $output_delivery_charge['charge'];
		  $diffshop =  $output_delivery_charge['diff_shop'];
			}else{
				$sameshop =  $output_delivery_charge['outsidesame'];
		  $diffshop =  $output_delivery_charge['outsidedifferent'];
				
			}
	}	

	}
	  
	    }

		if($different>=1){
			$deliverycharge = $diffshop;			
		}else{
			$deliverycharge = $sameshop ;
		}
	}
	
	$grandtotal =number_format((float)($cart->total()+$deliverycharge), 2, '.', '').' BDT';
	$deliverycharge1 = number_format((float)$deliverycharge, 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Total Weight : </b></td>
    <td><b>$totalweight</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Home Delivery: </b></td>
    <td><b>$deliverycharge1</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Grand Total: </b></td>
    <td><b>$grandtotal</b></td>
    <td></td>
    </tr>


    </tbody>

    </table>"; 
	
	 echo $output;
/*----------------------------------------------------*/
        //echo $updatecity?$output:'err';die;
	}elseif($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['id'])){
        $deleteItem = $cart->remove($_REQUEST['id']);
        //header("Location: viewcartmain.php");
/*----------------------------------------------------*/
$output = "";
$output = $output.""."
    <table class='table table-striped'>
    <thead>
        <tr>
            <th class='col-md-1'>Image</th>
            <th class='col-md-1'>Product</th>
			<th class='col-md-2'><p id='addi'>Additional Instruction</p></th>
			<th class='col-md-1'><p id='custupload'>Customer Upload</p></th>
			<th class='col-md-1'>Price</th>
            <th class='col-md-2'>Quantity</th>
			<th class='col-md-2'>Weight</th>
            <th class='col-md-3'>Subtotal</th>
            <th class='col-md-2'></th>
        </tr>
    </thead>
    <tbody>
          ";
	$deliverycharge = 0;
	$addi = 0;
	$custupload = 0;
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$inventory_product=0;
	$count = 0;
	$diff = 0;
	$totalweight=0;
    if($cart->total_items() > 0){
		
            //get cart items from session
    $cartItems = $cart->contents();
	
    $n=0;
    foreach($cartItems as $item){
	$n=$n+1;	
	$product_name = $item["name"];
	$product_id ="";
	$sql_delivery ="SELECT * FROM product WHERE product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_sql = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_sql['business_id'];
			 $product_chk_bid_prev = $output_sql['business_id'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_sql['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
		   }
		   
		   $inventory_product = $output_sql['inventory'];
		   $product_id = $output_sql['product_id'];
		}
	}
	
	$count=$count+1;

    $totalweight = $totalweight+($item['weight']*$item['qty']);
	$image = $item['image'];
	$name = $item["name"];
	$additional ="";
	if ($item["addi"]!=''){
	$addi = $addi+1;
	$additional = $item["addi"];
	}else{
		
	}
	$customuploader ="";
	if (!empty($item['customerupload'])){
	$custupload =$custupload+1;
	$customoutput = $item['customerupload'];
	$customuploader ="<img  src='customerupload/$customoutput' style='width:50px;height:50px'>";
	}else{
	$customuploader ="";
	}
	$price = number_format((float)$item["price"], 2, '.', '').' BDT';
	$qty = $item["qty"];
	$chk =  $item["rowid"];
	$weight_product = ($item['weight']*$item['qty']);
	$subtotal = number_format((float)$item["subtotal"], 2, '.', '').' BDT';
	
	
	$output = $output.""."
	<tr>
    <td>
	<a href='product_details.php?id=$product_id'>
    <img  src='product_photo/resized/product_small_$image.jpg' style='width:50px;height:50px'></a> 
    </td>
	<td>$name</td>
	<td>$additional</td>
	<td>$customuploader</td>
	<td>$price</td>
	<td><input type='number' class='form-control' id='quantity' value='$qty' onchange='koko(this,$n,$inventory_product)' min='1' max='$inventory_product'></td>
	<td>$weight_product</td>
	<td>$subtotal</td>
	<td>
    <p class='btn btn-danger btn-circle' style='border-radius:25px;' onclick='deleteitem($n)'><i class='glyphicon glyphicon-remove'></i></p>
    </td>
	";
	}
	 if($addi==0){
	?>
	<script>
	document.getElementById("addi").style.display = "none";
	</script>
    <?	
	 }
	if($custupload==0)
	{
	?>
	<script>
	document.getElementById("custupload").style.display = "none";
	</script>	
	<?
	}
	
		
	}else{
		$output = $output.""."<td colspan='5'><p>Your cart is empty.....</p></td>";
	}
	
	$carttotal = number_format((float)$cart->total(), 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Subtotal : </b></td>
    <td>$carttotal</td>
    <td></td>
    </tr>";
	
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$count = 0;
	$different = 0;
	$deliveryday = 0;
    if($cart->total_items() > 0){
		$cartItems = $cart->contents();
	foreach($cartItems as $item){
			
	$product_name = $item["name"];
	$sql_delivery ="SELECT * FROM product INNER JOIN business ON product.business_id=business.business_id WHERE product.product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output_name = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output_name['business_id'];
			 $product_chk_bid_prev = $output_name['business_id'];
			 $deliveryday =$output_name['maxdeliveryday'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output_name['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $different = $different+1;
			   }
			   $temp = $output_name['maxdeliveryday'];
			   if($temp>$deliveryday)
			   {
				   $deliveryday = $temp;
			   }
		   }
		   
		   
		}
	}
	
	$count=$count+1;
		}
	}

	
	$deliverycharge = 0;
	
	
	$sameshop = 0;
	$diffshop = 0;
	$demo = "";
	$sql_delivery_charge ="SELECT * FROM `delivery_charge`";
	if($result_delivery_charge = mysql_query($sql_delivery_charge))
	{
			while($output_delivery_charge = mysql_fetch_assoc($result_delivery_charge))
		{
			$minweight = $output_delivery_charge['min'];
            $maxweight = $output_delivery_charge['max'];
			
	if($totalweight ==0){
	 $deliverycharge = 0;
	}else{
	if($totalweight>=$minweight && $totalweight<=$maxweight)
	{	

		if($cityname=="dhaka" || $cityname=="Dhaka" || $cityname=="DHAKA" )
			
			{
		  $sameshop =  $output_delivery_charge['charge'];
		  $diffshop =  $output_delivery_charge['diff_shop'];
			}else{
				$sameshop =  $output_delivery_charge['outsidesame'];
		  $diffshop =  $output_delivery_charge['outsidedifferent'];
				
			}
	}	

	}
	  
	    }

		if($different>=1){
			$deliverycharge = $diffshop;			
		}else{
			$deliverycharge = $sameshop ;
		}

	}
	
	$grandtotal =number_format((float)($cart->total()+$deliverycharge), 2, '.', '').' BDT';
	$deliverycharge1 = number_format((float)$deliverycharge, 2, '.', '').' BDT';
	
	$output = $output.""."
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Total Weight : </b></td>
    <td><b>$totalweight</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Home Delivery: </b></td>
    <td><b>$deliverycharge1</b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style='text-align:right'><b>Grand Total: </b></td>
    <td><b>$grandtotal</b></td>
    <td></td>
    </tr>


    </tbody>

    </table>";
	
	
/*----------------------------------------------------*/
        echo $output;
    }elseif($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0){
	
	$deliveryday = 0;
	$deliverycharge = 0;
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$count = 0;
	$diff = 0;
	$cartItems = $cart->contents();
	$totalweight=0;
	
	foreach($cartItems as $item){
	$totalweight = $totalweight+($item['weight']*$item['qty']);
	$product_name = $item["name"];
	$sql_delivery ="SELECT * FROM product INNER JOIN business ON product.business_id=business.business_id WHERE product.product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output['business_id'];
			 $product_chk_bid_prev = $output['business_id'];
			 $deliveryday =$output['maxdeliveryday'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
			   $temp = $output['maxdeliveryday'];
			   if($temp>$deliveryday)
			   {
				   $deliveryday = $temp;
			   }
		   }
		   
		}
	}
	
	$count=$count+1;

		
	}
	
	$deliverydate= Date('Y-m-d', strtotime("+".$deliveryday."days"));
	
	$deliverycharge = 0;
	
	
	$sameshop = 0;
	$diffshop = 0;
	$demo = "";
	$sql_delivery_charge ="SELECT * FROM `delivery_charge`";
	if($result_delivery_charge = mysql_query($sql_delivery_charge))
	{
			while($output_delivery_charge = mysql_fetch_assoc($result_delivery_charge))
		{
			$minweight = $output_delivery_charge['min'];
            $maxweight = $output_delivery_charge['max'];
			
	if($totalweight ==0){
	 $deliverycharge = 0;
	}else{
	if($totalweight>=$minweight && $totalweight<=$maxweight)
	{	

		if($cityname=="dhaka" || $cityname=="Dhaka" || $cityname=="DHAKA" )
			
			{
		  $sameshop =  $output_delivery_charge['charge'];
		  $diffshop =  $output_delivery_charge['diff_shop'];
			}else{
				$sameshop =  $output_delivery_charge['outsidesame'];
		  $diffshop =  $output_delivery_charge['outsidedifferent'];
				
			}
	}	

	}
	  
	    }
	

		if($different>=1){
			$deliverycharge = $diffshop;			
		}else{
			$deliverycharge = $sameshop ;
		}
	}
		
		    $customer_id =0;
			$billing_email = $_SESSION["billing_email"];
			$sql_order="SELECT * FROM `customers`  WHERE email = '$billing_email' ORDER BY id ASC";
			if($output =mysql_query($sql_order)){
				while($row =mysql_fetch_assoc($output)){
					$customer_id = $row['id'];					
				}
			}
			
			$servicecharge =0;
			$sql_servicecharge="SELECT * FROM `servicecharge`";
			if($output1 =mysql_query($sql_servicecharge)){
				while($row1 =mysql_fetch_assoc($output1)){
					$servicecharge = $row1['charge'];					
				}
			}
			
			$bankcharge =0;
			$sql_bankcharge="SELECT * FROM `bankcharge`";
			if($output2 =mysql_query($sql_bankcharge)){
				while($row2 =mysql_fetch_assoc($output2)){
					$bankcharge = $row2['charge'];					
				}
			}
		
        // insert order details into database
        $insertOrder = $db->query("INSERT INTO orders (customer_id, total_price,total_weight,delivery_charge, created, modified,deliverydate) VALUES ('".$customer_id."', '".$cart->total()."','".$totalweight."','".$deliverycharge."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."','".$deliverydate."')");
        //echo "asiki ?";
        if($insertOrder){
            $orderID = $db->insert_id;
            $sql = '';
           // echo "asi ?";
            // get cart items
            $cartItems = $cart->contents();
			
            foreach($cartItems as $item){
				
				$result2 = $db->query("SELECT * FROM product WHERE product.product_id='".$item['id']."'");
if ($result2->num_rows > 0) {
    // output data of each row
        while($row1 = $result2->fetch_assoc()) {
				$bid=$row1['business_id'];
				$product_price = $row1['price'];
		}
}

            $product_sale = $product_price*$item['qty'];
			$product_service = ($product_sale/100)*$servicecharge;
			$product_bankcharge = ($product_sale/100)*$bankcharge;
			$sale_ref = rand(10000,100000).'_'.$orderID;
			$service_ref =rand(10000,100000).'_'.$orderID;
			$bankcharge_ref =rand(10000,100000).'_'.$orderID;
            $date = date("Y-m-d");
            
$product_id = $item['id'];
$quantity = $item['qty'];
            
$sql_update = "SELECT inventory FROM product WHERE product_id = '$product_id'";
if($output_sql_update = mysql_query($sql_update))
{
	while($fetch_sql_update = mysql_fetch_array($output_sql_update))
	{
		$past_quantity = $fetch_sql_update['inventory'];
		$recent_quantity = $past_quantity - $quantity;
		$sql_update_change = "UPDATE product SET inventory ='$recent_quantity' WHERE product_id = '$product_id'";
		mysql_query($sql_update_change);
	}
}
              
                $sql .= "INSERT INTO order_items (order_id, product_id,customer_id,business_id, quantity, product_sale,product_service,sale_ref,service_ref,created,additional_instruction,customer_upload,deliverydate,product_bankcharge,bankcharge_ref) VALUES ('".$orderID."', '".$item['id']."','".$customer_id."','".$bid."','".$item['qty']."','".$product_sale."','".$product_service."','".$sale_ref."','".$service_ref."','".$date."','".$item['addi']."','".$item['customerupload']."','".$deliverydate."','".$product_bankcharge."','".$bankcharge_ref."');";
        
            }
            // insert order items into database
      $insertOrderItems = $db->multi_query($sql);
            
            if($insertOrderItems){
                //$cart->destroy();
                header("Location: orderSuccess.php?id=$orderID");
           }else{
                header("Location: viewcartmain.php");
            }
       }
   

     else{
            header("Location: viewcartmain.php");
			//echo mysql_error();
        }
    }else{
		//$value = $cart->total_items();
		//echo "<script>alert($value)</script>";
        header("Location: viewcartmain.php");
    }
}else{
    header("Location: viewcartmain.php");
}
