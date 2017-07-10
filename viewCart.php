<?php
date_default_timezone_set('UTC');
      include error_reporting(0);
      ini_set('display_errors', '1'); 
      ob_start();
	  include 'dbConfig.php';
	  include "paymentgateway/api_lib.php";
      require_once ('mainfile.php');
      require_once ('includes/classes/hot.class.php');
      require_once ('includes/classes/review.class.php');
      require_once ('includes/classes/business.class.php');
      require_once('includes/classes/conversation.class.php');
      require_once('includes/classes/date_difference.class.php');
      require_once('includes/classes/yiw.class.php');
      require_once('includes/classes/events.class.php');
      require_once('includes/classes/bookmark.class.php');
      require_once ('includes/classes/message.class.php');
      require_once ('includes/classes/friends.class.php');
      require_once('includes/classes/user.class.php');
      require_once ('includes/classes/aboutme_profile.class.php');

	  $new_api_lib = new api_lib;

		//Use a method to create a unique Order ID. Store this for later use in the receipt page or receipt function.
    $order_id = $new_api_lib->getRandomString(10); 
	  $cityname='';
	  $cityname1='';
	  
        $obj_friend = new cls_friend();
        $obj_message = new cls_message();
        $obj_bookmark = new cls_bookmarks();
        $obj_events = new cls_events();
        $obj_hot = new cls_hot();
        $obj_busi = new cls_business();
        $obj_review = new cls_review();
        $obj_yiw = new cls_yelp();
        if($_GET['succ']) myerror(1,"You have been activated successfully");
        if($_GET['su']){
          myerror(1,"Signup Success Please check your email. ");
        }
        if ($auth->is_user() && !$auth->is_auth())  myerror(1,"You Sign Up Succesfully");


        $obj_con = new cls_conversation();

        if (isset($_GET['loc_i'])){
          $vars = set_location($_GET['loc_i']);
        }
        else
        {
          $vars = get_location();
        }

        $res = $obj_hot->get_hotties($city,0,1);
        
      if($_GET['act']=="ok")
      {
        myerror(1, "You account has been successfully activated");
      }
     
// initializ shopping cart class
?>

<?
include 'cartmain.php';
$cart = new Cart;
$id = $auth->get_members_id();

	$deliveryday = 0;
	$product_chk_bid =0;
	$product_chk_bid_prev =0;
	$count = 0;
	$diff = 0;
	$productoutput ="";
	
    if($cart->total_items() > 0){
    $cartItems = $cart->contents();
    foreach($cartItems as $item){
	
	$product_name = $item["name"];
	$product_id = $item["id"];
	$sql_delivery ="SELECT * FROM product WHERE product_id='$product_id'";
	if($result = mysql_query($sql_delivery))
	{

		while($output = mysql_fetch_assoc($result))
		{
			$initial = $item['qty'];
			$rowid = $item["rowid"];
			$inventory_product = $output['inventory'];
			if($initial> $inventory_product){
		    echo "<script>alert('$inventory_product Items are available for $product_name')</script>";
			
			if($inventory_product == 0)
			{
			  $cart->remove($rowid);
			}else{
	    $itemData = array(
            'rowid' => $item["rowid"],
            'qty' => $inventory_product
        );
        $updateItem = $cart->update($itemData);
			}
		
			}
			
		}
	}
	
	$deliverysql ="SELECT * FROM product INNER JOIN business ON product.business_id=business.business_id WHERE product.product_id='$product_id'";
	if($deliveryoutput = mysql_query($deliverysql))
	{
        while($output1= mysql_fetch_assoc($deliveryoutput))
		{
			$temp1 = $output1['maxdeliveryday'];
		   if($temp1>5)
		   {
		    $productoutput =$productoutput." ".$product_name."(".$product_id.")";
		   }			
	       if($count ==0){
			 $product_chk_bid = $output1['business_id'];
			 $product_chk_bid_prev = $output1['business_id'];
			 $deliveryday =$output1['maxdeliveryday'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output1['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
			   $temp = $output1['maxdeliveryday'];
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
	
	if($deliveryday>5)
	{
	echo "<script>alert('Delivery time for $productoutput is $deliveryday days. If you order all products together; you will receive all products in $deliveryday days. If you want other products to be delivered earlier you need to create sepearte order for this $productoutput')</script>";
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advance Shopping  Cart</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script> 
    <script>tinymce.init({ selector:'textarea.editorsam' });</script>
    <style>
    </style>
    <script>
	function koko(obj,id,max){
		//alert(obj.value+" "+id+" "+max);

                    jQuery.ajax({
                    type:'POST',
                    url:'cartid.php',
                    data:'pass='+id,
                    success: function(data){
						
                     //alert(data+"/"+obj.value+"/"+max);
		 var maxvalue = parseInt(max);
         var realvalue = parseInt(obj.value);
		 if(realvalue >= maxvalue){
			 obj.value = maxvalue;
		 }else{

		 }					  

        $.get("cartAction.php", {action:"updateCartItem", id:data, qty:obj.value}, function(data){

			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");

        });

					  
                    }

                     });

	}
	
	function deleteitem(id){

                    jQuery.ajax({
                    type:'POST',
                    url:'cartid.php',
                    data:'pass='+id,
                    success: function(data){
											  

        $.get("cartAction.php", {action:"removeCartItem", id:data}, function(data){
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
        });

					  
                    }

                     });

	}
	
    function updateCartItem(obj,id,max){
		
         //alert(obj.value+" "+id+" "+max);
		 var maxvalue = parseInt(max);
         var realvalue = parseInt(obj.value);
		 if(realvalue >0)
		 {
		 if(realvalue >= maxvalue){
			 obj.value = maxvalue;
		 }else{

		 }
		 
        $.get("cartAction.php", {action:"updateCartItem", id:id, qty:obj.value}, function(data){
            //if(data == 'ok'){
               // location.reload();
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
            /*}else{
                alert('Cart update failed, please try again.');
            }*/
        });
		 }else{
			 
			 $.get("cartAction.php", {action:"removeCartItem", id:id}, function(data){
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
        });
		
		 }
    }
	
	    function deleteCartItem(id){
         //alert(id);
        $.get("cartAction.php", {action:"removeCartItem", id:id}, function(data){
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
        });
    }
	
	    function detail_login(){
		

      var value = document.getElementById('showshipping').checked;

      if(value==true)
      {
        document.getElementById("shippingaddress").style.display = "block";
      }
      if(value==false)
      {
        document.getElementById("shippingaddress").style.display = "none";
      }
     
    }
	
	 function detail_withoutlogin(){
		

      var value = document.getElementById('showshipping_withoutlogin').checked;

      if(value==true)
      {
        document.getElementById("shippingaddress_withoutlogin").style.display = "block";
      }
      if(value==false)
      {
        document.getElementById("shippingaddress_withoutlogin").style.display = "none";
      }
     
    }
	
    </script>
	
	
	
<script>

    function updatecity(selectObject){
         //alert(obj.value+" "+id+" "+max);
		 var value = selectObject.value;
			//alert(value);	 
			var balue = document.getElementById('showshipping_withoutlogin').checked;
		if(balue==false)
      {
        $.get("cartAction.php", {action:"updatecity", city:value}, function(data){
            //if(data == 'ok'){
               // location.reload();
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
            /*}else{
                alert('Cart update failed, please try again.');
            }*/
	  });}
    }

</script>	
<script>

    function updatecityship(selectObject){
		//alert ("ami marakhabo plz");
         //alert(obj.value+" "+id+" "+max);
		 var value = selectObject.value;
				//alert(value); 
        $.get("cartAction.php", {action:"updatecity", cityship:value}, function(data){
            //if(data == 'ok'){
               // location.reload();
			   //alert(data);
			   jQuery( "div#yourcart" ).html(data);
			   $("#yourcarts").load(location.href + " #yourcarts");
            /*}else{
                alert('Cart update failed, please try again.');
            }*/
        });
    }

</script>
</head>

<body>

<form name="Form2" id="payp"  method="post" action="paymentgateway/payment.php" onsubmit="return validateForm()">

<div class="row">
<br>

<div class="col-sm-4">

<div class="panel panel-primary">
<div class="panel-heading" style="color:white"><b>Billing address</b></div>
<div class="panel-body">

<? 
if($id==0) 
{	
?>

  <div class="form-group">
    <label for="firstname">First Name:</label>
    <input type="text" class="form-control" name="billing_firstname" id="billing_firstname" required>
  </div>

  <div class="form-group">
    <label for="lastname">Last Name:</label>
    <input type="text" class="form-control" name="billing_lastname" id="billing_lastname" required>
  </div>

  <div class="form-group">
    <label for="email">Email address:</label>
    <input type="email" class="form-control" name="billing_email" id="billing_email"  onchange ="mailchk()" required>
  </div>

  <div class="form-group">
    <label for="address">Address:</label>
    <input type="text" class="form-control" name="billing_address" id="billing_address" required>
  </div>

  <div class="form-group">
    <label for="area">Area:</label>
    <input type="text" class="form-control" name="billing_area" id="billing_area" required>
  </div>
<div class="form-group">
 <label for="City">City:</label>
 <select type="text" class="form-control" name="billing_city" id="billing_city" onchange="updatecity(this)">
  <option value=""></option>
  <option value="Dhaka">Dhaka</option>
                            <option value="Faridpur">Faridpur</option>
                            <option value="Gazipur">Gazipur</option>
                            <option value="Gopalganj">Gopalganj</option>
                            <option value="Kishoreganj">Kishoreganj</option>
                            <option value="Madaripur">Madaripur</option>
                            <option value="Manikganj">Manikganj</option>
                            <option value="Munshiganj">Munshiganj</option>
                            <option value="Narayanganj">Narayanganj</option>
                            <option value="Narsingdi">Narsingdi</option>
                            <option value="Rajbari">Rajbari</option>
                            <option value="Shariatpur">Shariatpur</option>
                            <option value="Tangail">Tangail</option>
							<option value="Barguna">Barguna</option>
                            <option value="Barisal">Barisal</option>
                            <option value="Bhola">Bhola</option>
                            <option value="Jhalokati">Jhalokati</option>
                            <option value="Patuakhali">Patuakhali</option>
                            <option value="Pirojpur">Pirojpur</option>
							<option value="Bandarban">Bandarban</option>
                            <option value="Brahmanbaria">Brahmanbaria</option>
                            <option value="Chandpur">Chandpur</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Comilla">Comilla</option>
                            <option value="Coxs Bazar">Cox's Bazar</option>
                            <option value="Feni">Feni</option>
                            <option value="Khagrachhari">Khagrachhari</option>
                            <option value="Lakshmipur">Lakshmipur</option>
                            <option value="Noakhali">Noakhali</option>
                            <option value="Rangamati">Rangamati</option>
							<option value="Bagerhat">Bagerhat</option>
                            <option value="Chuadanga">Chuadanga</option>
                            <option value="Jessore">Jessore</option>
                            <option value="Jhenaidah">Jhenaidah</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Kushtia">Kushtia</option>
                            <option value="Magura">Magura</option>
                            <option value="Meherpur">Meherpur</option>
                            <option value="Narail">Narail</option>
                            <option value="Satkhira">Satkhira</option>
							<option value="Jamalpur">Jamalpur</option>
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Netrakona">Netrakona</option>
                            <option value="Sherpur">Sherpur</option>
							<option value="Bogra">Bogra</option>
                            <option value="Joypurhat">Joypurhat</option>
                            <option value="Naogaon">Naogaon</option>
                            <option value="Natore">Natore</option>
                            <option value="Chapainawabganj">Chapainawabganj</option>
                            <option value="Pabna">Pabna</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Sirajgonj">Sirajgonj</option>
							<option value="Dinajpur">Dinajpur</option>
                            <option value="Gaibandha">Gaibandha</option>
                            <option value="Kurigram">Kurigram</option>
                            <option value="Lalmonirhat">Lalmonirhat</option>
                            <option value="Nilphamari">Nilphamari</option>
                            <option value="Panchagarh">Panchagarh</option>
                            <option value="Rangpur">Rangpur</option>
                            <option value="Thakurgaon">Thakurgaon</option>
							<option value="Habiganj">Habiganj</option>
                            <option value="Moulvibazar">Moulvibazar</option>
                            <option value="Sunamganj">Sunamganj</option>
                            <option value="Sylhet">Sylhet</option>
</select>
 </div>

  <div class="form-group">
    <label for="zipcode">Zip/Postal:</label>
    <input type="text" class="form-control" name="billing_zipcode" id="billing_zipcode" required>
  </div>
  
    <div class="form-group">
    <label for="phone">Phone:</label>
    <input type="text" class="form-control" name="billing_phone" id="billing_phone" required>
  </div>


  <div class="checkbox">
    <label><input type="checkbox" id="showshipping_withoutlogin" onclick="detail_withoutlogin()">Ship to another address</label>
  </div>


</div>
</div>

<div class="panel panel-primary" id="shippingaddress_withoutlogin" style="display:none">
<div class="panel-heading" style="color:white"><b>Shipping address</b></div>
<div class="panel-body">


  <div class="form-group">
    <label for="firstname">First Name:</label>
    <input type="text" class="form-control" name="shipping_firstname" id="shipping_firstname" >
  </div>

  <div class="form-group">
    <label for="lastname">Last Name:</label>
    <input type="text" class="form-control" name="shipping_lastname" id="shipping_lastname" >
  </div>

  <div class="form-group">
    <label for="email">Email address:</label>
    <input type="email" class="form-control" name="shipping_email" id="shipping_email" >
  </div>

  <div class="form-group">
    <label for="address">Address:</label>
    <input type="text" class="form-control" name="shipping_address" id="shipping_address" >
  </div>

  <div class="form-group">
    <label for="area">Area:</label>
    <input type="text" class="form-control" name="shipping_area" id="shipping_area" >
  </div>

<div class="form-group">
    <label for="City">City:</label>
     
<select type="text" class="form-control" name="shipping_city" id="shipping_city" onchange="updatecityship(this)" >
   <option value=""></option>
  <option value="Dhaka">Dhaka</option>
                            <option value="Faridpur">Faridpur</option>
                            <option value="Gazipur">Gazipur</option>
                            <option value="Gopalganj">Gopalganj</option>
                            <option value="Kishoreganj">Kishoreganj</option>
                            <option value="Madaripur">Madaripur</option>
                            <option value="Manikganj">Manikganj</option>
                            <option value="Munshiganj">Munshiganj</option>
                            <option value="Narayanganj">Narayanganj</option>
                            <option value="Narsingdi">Narsingdi</option>
                            <option value="Rajbari">Rajbari</option>
                            <option value="Shariatpur">Shariatpur</option>
                            <option value="Tangail">Tangail</option>
							<option value="Barguna">Barguna</option>
                            <option value="Barisal">Barisal</option>
                            <option value="Bhola">Bhola</option>
                            <option value="Jhalokati">Jhalokati</option>
                            <option value="Patuakhali">Patuakhali</option>
                            <option value="Pirojpur">Pirojpur</option>
							<option value="Bandarban">Bandarban</option>
                            <option value="Brahmanbaria">Brahmanbaria</option>
                            <option value="Chandpur">Chandpur</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Comilla">Comilla</option>
                            <option value="Coxs Bazar">Cox's Bazar</option>
                            <option value="Feni">Feni</option>
                            <option value="Khagrachhari">Khagrachhari</option>
                            <option value="Lakshmipur">Lakshmipur</option>
                            <option value="Noakhali">Noakhali</option>
                            <option value="Rangamati">Rangamati</option>
							<option value="Bagerhat">Bagerhat</option>
                            <option value="Chuadanga">Chuadanga</option>
                            <option value="Jessore">Jessore</option>
                            <option value="Jhenaidah">Jhenaidah</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Kushtia">Kushtia</option>
                            <option value="Magura">Magura</option>
                            <option value="Meherpur">Meherpur</option>
                            <option value="Narail">Narail</option>
                            <option value="Satkhira">Satkhira</option>
							<option value="Jamalpur">Jamalpur</option>
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Netrakona">Netrakona</option>
                            <option value="Sherpur">Sherpur</option>
							<option value="Bogra">Bogra</option>
                            <option value="Joypurhat">Joypurhat</option>
                            <option value="Naogaon">Naogaon</option>
                            <option value="Natore">Natore</option>
                            <option value="Chapainawabganj">Chapainawabganj</option>
                            <option value="Pabna">Pabna</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Sirajgonj">Sirajgonj</option>
							<option value="Dinajpur">Dinajpur</option>
                            <option value="Gaibandha">Gaibandha</option>
                            <option value="Kurigram">Kurigram</option>
                            <option value="Lalmonirhat">Lalmonirhat</option>
                            <option value="Nilphamari">Nilphamari</option>
                            <option value="Panchagarh">Panchagarh</option>
                            <option value="Rangpur">Rangpur</option>
                            <option value="Thakurgaon">Thakurgaon</option>
							<option value="Habiganj">Habiganj</option>
                            <option value="Moulvibazar">Moulvibazar</option>
                            <option value="Sunamganj">Sunamganj</option>
                            <option value="Sylhet">Sylhet</option>
</select>
 </div>


  <div class="form-group">
    <label for="zipcode">Zip/Postal:</label>
    <input type="text" class="form-control" name="shipping_zipcode" id="shipping_zipcode" >
  </div>

    <div class="form-group">
    <label for="phone">Phone:</label>
    <input type="text" class="form-control" name="shipping_phone" id="shipping_phone">
  </div>
  

</form>
<?} else {
//echo $_SESSION['city'];
$res2= db_select("select * from members where members_id='$id'");
		while($m_row = mysql_fetch_array($res2)){

		$cityname1 = $m_row['city']; 
if($cityname1 == ""){
	
	echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Please update your profile information. you will be redirected to your update profile option now ')
        window.location.href='account_profile.php'
        </SCRIPT>");
	
}

	?>

<!----------------------------------------------------------------------//-->


  <div class="form-group">
    <label for="firstname">First Name:</label>
    <input type="text" class="form-control" name="billing_firstname" id="billing_firstname"  value="<?php echo $m_row['first_name']; ?>" required readonly>
  </div>

  <div class="form-group">
    <label for="lastname">Last Name:</label>
    <input type="text" class="form-control" name="billing_lastname" id="billing_lastname" value="<?php echo $m_row['last_name']; ?>" required readonly>
  </div>

  <div class="form-group">
    <label for="email">Email address:</label>
    <input type="email" class="form-control" name="billing_email" id="billing_email" value="<?php echo $m_row['email']; ?>" required readonly>
  </div>

  <div class="form-group">
    <label for="address">Address:</label>
    <input type="text" class="form-control" name="billing_address" id="billing_address" value="<?php echo $m_row['address']; ?>" required readonly>
  </div>

  <div class="form-group">
    <label for="area">Area:</label>
    <input type="text" class="form-control" name="billing_area" id="billing_area" value="<?php echo $m_row['area']; ?>" required readonly>
  </div>
<? if ($m_row['city']==null){ ?>
 <div class="form-group">
    <label for="City">City:</label>
     
<select type="text" class="form-control" name="billing_city" id="billing_city">
   <option value=""></option>
  <option value="Dhaka">Dhaka</option>
                            <option value="Faridpur">Faridpur</option>
                            <option value="Gazipur">Gazipur</option>
                            <option value="Gopalganj">Gopalganj</option>
                            <option value="Kishoreganj">Kishoreganj</option>
                            <option value="Madaripur">Madaripur</option>
                            <option value="Manikganj">Manikganj</option>
                            <option value="Munshiganj">Munshiganj</option>
                            <option value="Narayanganj">Narayanganj</option>
                            <option value="Narsingdi">Narsingdi</option>
                            <option value="Rajbari">Rajbari</option>
                            <option value="Shariatpur">Shariatpur</option>
                            <option value="Tangail">Tangail</option>
							<option value="Barguna">Barguna</option>
                            <option value="Barisal">Barisal</option>
                            <option value="Bhola">Bhola</option>
                            <option value="Jhalokati">Jhalokati</option>
                            <option value="Patuakhali">Patuakhali</option>
                            <option value="Pirojpur">Pirojpur</option>
							<option value="Bandarban">Bandarban</option>
                            <option value="Brahmanbaria">Brahmanbaria</option>
                            <option value="Chandpur">Chandpur</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Comilla">Comilla</option>
                            <option value="Coxs Bazar">Cox's Bazar</option>
                            <option value="Feni">Feni</option>
                            <option value="Khagrachhari">Khagrachhari</option>
                            <option value="Lakshmipur">Lakshmipur</option>
                            <option value="Noakhali">Noakhali</option>
                            <option value="Rangamati">Rangamati</option>
							<option value="Bagerhat">Bagerhat</option>
                            <option value="Chuadanga">Chuadanga</option>
                            <option value="Jessore">Jessore</option>
                            <option value="Jhenaidah">Jhenaidah</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Kushtia">Kushtia</option>
                            <option value="Magura">Magura</option>
                            <option value="Meherpur">Meherpur</option>
                            <option value="Narail">Narail</option>
                            <option value="Satkhira">Satkhira</option>
							<option value="Jamalpur">Jamalpur</option>
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Netrakona">Netrakona</option>
                            <option value="Sherpur">Sherpur</option>
							<option value="Bogra">Bogra</option>
                            <option value="Joypurhat">Joypurhat</option>
                            <option value="Naogaon">Naogaon</option>
                            <option value="Natore">Natore</option>
                            <option value="Chapainawabganj">Chapainawabganj</option>
                            <option value="Pabna">Pabna</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Sirajgonj">Sirajgonj</option>
							<option value="Dinajpur">Dinajpur</option>
                            <option value="Gaibandha">Gaibandha</option>
                            <option value="Kurigram">Kurigram</option>
                            <option value="Lalmonirhat">Lalmonirhat</option>
                            <option value="Nilphamari">Nilphamari</option>
                            <option value="Panchagarh">Panchagarh</option>
                            <option value="Rangpur">Rangpur</option>
                            <option value="Thakurgaon">Thakurgaon</option>
							<option value="Habiganj">Habiganj</option>
                            <option value="Moulvibazar">Moulvibazar</option>
                            <option value="Sunamganj">Sunamganj</option>
                            <option value="Sylhet">Sylhet</option>
</select>
 </div>


<? }else { ?>

<div class="form-group">
    <label for="City">City:</label>
    <input type="text" class="form-control" name="billing_city" id="billing_city" value="<?php echo $m_row['city']; ?>" required readonly>
  </div>
<? }?>
 



  <div class="form-group">
    <label for="zipcode">Zip/Postal:</label>
    <input type="text" class="form-control" name="billing_zipcode" id="billing_zipcode" value="<?php echo $m_row['zipcode']; ?>" required readonly>
  </div>
 <div class="form-group">
    <label for="phone">Phone:</label>
    <input type="text" class="form-control" name="billing_phone"  id="billing_phone" value="<?php echo $m_row['phone']; ?>" required readonly>
  </div>

  <div class="checkbox">
    <label><input type="checkbox"   id="showshipping" onclick="detail_login()">Ship to Another Address</label>
  </div>



  </div>
</div>

<div class="panel panel-primary" id="shippingaddress" style="display:none">
<div class="panel-heading" style="color:white"><b>Shipping address</b></div>
<div class="panel-body">


  <div class="form-group">
    <label for="firstname">First Name:</label>
    <input type="text" class="form-control" name="shipping_firstname" id="shipping_firstname" >
  </div>

  <div class="form-group">
    <label for="lastname">Last Name:</label>
    <input type="text" class="form-control" name="shipping_lastname" id="shipping_lastname" >
  </div>

  <div class="form-group">
    <label for="email">Email address:</label>
    <input type="email" class="form-control" name="shipping_email" id="shipping_email" >
  </div>

  <div class="form-group">
    <label for="address">Address:</label>
    <input type="text" class="form-control" name="shipping_address" id="shipping_address" >
  </div>

  <div class="form-group">
    <label for="area">Area:</label>
    <input type="text" class="form-control" name="shipping_area" id="shipping_area" >
  </div>

  <div class="form-group">
    <label for="City">City:</label>
    <select type="text" class="form-control" name="shipping_city" id="shipping_city" onchange="updatecityship(this)">
   <option value=""></option>
  <option value="Dhaka">Dhaka</option>
                            <option value="Faridpur">Faridpur</option>
                            <option value="Gazipur">Gazipur</option>
                            <option value="Gopalganj">Gopalganj</option>
                            <option value="Kishoreganj">Kishoreganj</option>
                            <option value="Madaripur">Madaripur</option>
                            <option value="Manikganj">Manikganj</option>
                            <option value="Munshiganj">Munshiganj</option>
                            <option value="Narayanganj">Narayanganj</option>
                            <option value="Narsingdi">Narsingdi</option>
                            <option value="Rajbari">Rajbari</option>
                            <option value="Shariatpur">Shariatpur</option>
                            <option value="Tangail">Tangail</option>
							<option value="Barguna">Barguna</option>
                            <option value="Barisal">Barisal</option>
                            <option value="Bhola">Bhola</option>
                            <option value="Jhalokati">Jhalokati</option>
                            <option value="Patuakhali">Patuakhali</option>
                            <option value="Pirojpur">Pirojpur</option>
							<option value="Bandarban">Bandarban</option>
                            <option value="Brahmanbaria">Brahmanbaria</option>
                            <option value="Chandpur">Chandpur</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Comilla">Comilla</option>
                            <option value="Coxs Bazar">Cox's Bazar</option>
                            <option value="Feni">Feni</option>
                            <option value="Khagrachhari">Khagrachhari</option>
                            <option value="Lakshmipur">Lakshmipur</option>
                            <option value="Noakhali">Noakhali</option>
                            <option value="Rangamati">Rangamati</option>
							<option value="Bagerhat">Bagerhat</option>
                            <option value="Chuadanga">Chuadanga</option>
                            <option value="Jessore">Jessore</option>
                            <option value="Jhenaidah">Jhenaidah</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Kushtia">Kushtia</option>
                            <option value="Magura">Magura</option>
                            <option value="Meherpur">Meherpur</option>
                            <option value="Narail">Narail</option>
                            <option value="Satkhira">Satkhira</option>
							<option value="Jamalpur">Jamalpur</option>
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Netrakona">Netrakona</option>
                            <option value="Sherpur">Sherpur</option>
							<option value="Bogra">Bogra</option>
                            <option value="Joypurhat">Joypurhat</option>
                            <option value="Naogaon">Naogaon</option>
                            <option value="Natore">Natore</option>
                            <option value="Chapainawabganj">Chapainawabganj</option>
                            <option value="Pabna">Pabna</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Sirajgonj">Sirajgonj</option>
							<option value="Dinajpur">Dinajpur</option>
                            <option value="Gaibandha">Gaibandha</option>
                            <option value="Kurigram">Kurigram</option>
                            <option value="Lalmonirhat">Lalmonirhat</option>
                            <option value="Nilphamari">Nilphamari</option>
                            <option value="Panchagarh">Panchagarh</option>
                            <option value="Rangpur">Rangpur</option>
                            <option value="Thakurgaon">Thakurgaon</option>
							<option value="Habiganj">Habiganj</option>
                            <option value="Moulvibazar">Moulvibazar</option>
                            <option value="Sunamganj">Sunamganj</option>
                            <option value="Sylhet">Sylhet</option>
</select>
  </div>

  <div class="form-group">
    <label for="zipcode">Zip/Postal:</label>
    <input type="text" class="form-control" name="shipping_zipcode" id="shipping_zipcode" >
  </div>

   <div class="form-group">
    <label for="phone">Phone:</label>
    <input type="text" class="form-control" name="shipping_phone" id="shipping_phone" >
  </div>

</form>
		<?

		}

}		


?>

  </div>
</div>

</div>
<div class="col-sm-8">
<div class="alert alert-danger">
   <p style="font-size:16px" >Please make sure your card is Ecommerce enabled and you have enough balance in your bank account to make the transaction. 
If your card is not Ecommerce enabled please call your bank's call center and enable it.</p>
<p style="color:blue ;font-size:16px">If you are paying from outside Bangladesh please make the transaction with BDT. You can use any international credit card to make transactions.</p>
  
</div>

</div>
<div class="col-sm-8">

<div class="panel panel-primary">
<div class="panel-heading" style="color:white"><b>Your Order</b></div>

<div class="panel-body">
<div class="col-sm-12" id="yourcart" >
    <table class="table table-striped" >
    <thead>
        <tr>
            <th class="col-md-1">Image</th>
            <th class="col-md-1">Product</th>
            <th class="col-md-2"><p id="addi">Additional Instruction</p></th>
			<th class="col-md-1"><p id="custupload">Customer Upload</p></th>
			<th class="col-md-1">Price</th>
            <th class="col-md-2">Quantity</th>
			<th class="col-md-2">Weight</th>
            <th class="col-md-3">Subtotal</th>
            <th class="col-md-2"></th>
        </tr>
    </thead>

    <tbody>

    <?php 

	
    //$product_image_lookup = mysql_query ("select * from product_image where product_id='".$pid."'");
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
	

    foreach($cartItems as $item){
		
	$product_name = $item["name"];
	$product_id ="";
	$sql_delivery ="SELECT * FROM product WHERE product_name='$product_name'";
	if($result = mysql_query($sql_delivery))
	{

		while($output = mysql_fetch_assoc($result))
		{	   
	       if($count ==0){
			 $product_chk_bid = $output['business_id'];
			 $product_chk_bid_prev = $output['business_id'];
		   }
		   
		   if($count>=1){
			   
			   $product_chk_bid = $output['business_id'];
			   if($product_chk_bid !=$product_chk_bid_prev)
			   {
				   $diff = $diff+1;
			   }
		   }
		   
		   $inventory_product = $output['inventory'];
		   $product_id = $output['product_id'];
		}
	}
	
	$count=$count+1;

    $totalweight = $totalweight+($item['weight']*$item['qty']);	
    ?>
    <tr>
    <td>
	<a href="product_details.php?id=<? echo $product_id; ?>">
    <img  src="product_photo/resized/product_small_<?php echo $item['image']; ?>.jpg" style="width:50px;height:50px"></a> 
    </td>
    <td><?php echo $item["name"]; ?></td>
  		<?php
if ($item["addi"]!=''){
	$addi = $addi+1;
	?>
	<td><?php echo $item["addi"] ?></td>
<? } else {
	?>
	<td></td>
<?} ?>
	
	<?php
if (!empty($item['customerupload'])){
	$custupload =$custupload+1;
	//echo $item['customerupload'];
		?>
		
	<td><img  src="customerupload/<?php echo $item['customerupload']; ?>" style="width:50px;height:50px"> </td>
<? 
}else { 
?>
	<td></td>
<?} ?>


	<td><?php echo $item["price"].' BDT'; ?></td>
    <td><input type="number" class="form-control" id="quantity" value="<?php echo $item["qty"]; ?>" onchange="updateCartItem(this,'<?php echo $item["rowid"];?>','<?php echo $inventory_product;?>')" min="1" max="<?php echo $inventory_product; ?>" >  </td>
	<td><?php echo ($item['weight']*$item['qty']); ?></td>
    <td><?php echo number_format((float)$item["subtotal"], 2, '.', '').' BDT'; ?></td>
    <td>
    <p class="btn btn-danger btn-circle" style="border-radius:25px;" onclick="deleteCartItem('<?php echo $item["rowid"];?>')"><i class="glyphicon glyphicon-remove"></i></p>
    </td>

    <?php 
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
	?>

    <td colspan="5"><p>Your cart is empty.....</p></td>
    </tr>

    <?php } ?>

    <tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style="text-align:right"><b>Subtotal : </b></td>
    <td><b><?php echo number_format((float)$cart->total(), 2, '.', '').' BDT'; ?></b></td>
    <td></td>
    </tr>
	
	<tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style="text-align:right"><b>Total Weight : </b></td>
    <td><b><?php echo $totalweight; ?></b></td>
    <td></td>
    </tr>

    <tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style="text-align:right"><b>Home Delivery: </b></td>
    <td><b>
	<?php
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
				   $different = $different+1;
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

		if($cityname1=="dhaka" || $cityname1=="Dhaka" || $cityname1=="DHAKA" )
			
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
	echo number_format((float)$deliverycharge, 2, '.', '')." BDT";
	?></b></td>
    <td></td>
    </tr>

    <tr>
    <td></td>
    <td></td>
    <td></td>
	<td></td>
    <td></td>
	<td></td>
    <td style="text-align:right"><b>Grand Total: </b></td>
    <td><b><?php $gtotal =$cart->total()+$deliverycharge;
	echo number_format((float)$gtotal, 2, '.', '')." BDT";?></b></td>
	<? $formatedtotal = number_format($gtotal, 2);
    ?>
	<td></td>
    </tr>


    </tbody>

    </table>

</div>

</div>

</div>



<div class="row">
<div class="col-sm-6">
<a href="index.php" class="btn btn-warning btn-block"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a>
</div>

<div class="col-sm-6" id ="yourcarts">


  <input type="hidden" name="business" value="merchant@example.com">

  <input type="hidden" name="cmd" value="_xclick">
  <input type="hidden" name="order_id" value="<?PHP echo $order_id ?>">
  <input type="hidden" name="item_name" value="ORDER">
  <input type="hidden" name="order_amount" value="<?php echo $formatedtotal ?>">
  <input type="hidden" name="order_currency" value="BDT">
  
  <button type="submit" class="btn btn-primary btn-block">Submit</button>

</div>
<div class="row">
<div class="col-sm-1"></div>
<div class="col-sm-10">
<p>		<br>
<br>
<br>
<br>
<br>
        <strong><font color="#0000FF">PAY WITH:</font></strong> 
		<br>
        <font size="1"> 

          <?
       $files = glob("cardimages/*.*");
for ($i=0; $i<count($files); $i++)
{
	
	$num = $files[$i];
	echo '<img src="'.$num.'"  style="height: 45px;margin-bottom: 5px" >'."&nbsp;&nbsp; ";
	}
	
?>
       
        
    
       <!--<font color="#FF0000">
        <b> | </b> 
        </font> 
        <a class="footer" href="business_listing_more.php" style="height: 40px;">Newly Added</a>-->
        </font>
        </p>
</div>
<div class="col-sm-1"></div>
</div>
</div>

</div>

</div>

</form>

<script language="Javascript">

/*
function validateForm(form , id) {
    var x = document.forms[form][id].value;
    var atpos = x.indexOf("@");
    var dotpos = x.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
        //alert("Not a valid e-mail address");
		document.getElementById("dangeralert").style.display = "block";
		document.getElementById("alertmsg").innerHTML="Not a valid e-mail address";
		document.forms[form][id].value="";
		document.getElementById(id).style.border = "solid 2px red";
    }else{
		document.getElementById("dangeralert").style.display = "none";
		document.getElementById(id).style.border = "solid 1px black";
	}
}

function OnButton1()
{
    //document.Form1.action = "response1.php"
    //document.Form1.target = "_blank";    // Open in a new window

    document.Form1.submit();             // Submit the page

    //return true;

}

function OnButton2()
{
	
	  <?
	  $id = $auth->get_members_id();
	  if(empty($id)){
	  ?>

	 var value = document.getElementById('showshipping_withoutlogin').checked;
     <?
	  }else{
     ?>	 
	  var value = document.getElementById('showshipping').checked;
     <?
	  }
     ?>

      if(value==false)
      {
        //alert(value);
	var billing_firstname = document.getElementById('billing_firstname').value;
	var billing_lastname = document.getElementById('billing_lastname').value;
	var billing_email = document.getElementById('billing_email').value;
	var billing_address = document.getElementById('billing_address').value;
	var billing_area = document.getElementById('billing_area').value;
	var billing_city = document.getElementById('billing_city').value;
	var billing_zipcode = document.getElementById('billing_zipcode').value;
	var billing_phone = document.getElementById('billing_phone').value;
	
	if(billing_firstname != 0 && billing_lastname != 0 && billing_email != 0 && billing_address != 0&& billing_area != 0 && billing_city != 0 && billing_zipcode != 0 && billing_phone != 0){
		document.getElementById("dangeralert").style.display = "none";
		//alert(value);
		
		             jQuery.ajax({
                    type:'POST',
                    url:'customeradd.php',
                    data:'value='+value+'&billing_firstname='+billing_firstname+'&billing_lastname='+billing_lastname+'&billing_email='+billing_email+'&billing_address='+billing_address+'&billing_area='+billing_area+'&billing_city='+billing_city+'&billing_zipcode='+billing_zipcode+'&billing_phone='+billing_phone,
                    success: function(data){
						//alert(data);
                    }

                     });

					 
	document.Form2.action = "paymentgateway/HostedCheckoutReturnToMerchant_NVP.php"
    //document.Form2.target = "_blank";    // Open in a new window


    document.Form2.submit();             // Submit the page
    
    return true;
					 

	}else{
		//alert('Please fill up form');
	    document.getElementById("dangeralert").style.display = "block";
		document.getElementById("alertmsg").innerHTML="Please fill up form";
	}
	

	
	}
	
	  if(value==true)
      {
	    //alert(value);
	var billing_firstname = document.getElementById('billing_firstname').value;
	var billing_lastname = document.getElementById('billing_lastname').value;
	var billing_email = document.getElementById('billing_email').value;
	var billing_address = document.getElementById('billing_address').value;
	var billing_area = document.getElementById('billing_area').value;
	var billing_city = document.getElementById('billing_city').value;
	var billing_zipcode = document.getElementById('billing_zipcode').value;
	var billing_phone = document.getElementById('billing_phone').value;
	
	var shipping_firstname = document.getElementById('shipping_firstname').value;
	var shipping_lastname = document.getElementById('shipping_lastname').value;
	var shipping_email = document.getElementById('shipping_email').value;
	var shipping_address = document.getElementById('shipping_address').value;
	var shipping_area = document.getElementById('shipping_area').value;
	var shipping_city = document.getElementById('shipping_city').value;
	var shipping_zipcode = document.getElementById('shipping_zipcode').value;
	var shipping_phone = document.getElementById('shipping_phone').value;
	
	if(billing_firstname != 0 && billing_lastname != 0 && billing_email != 0 && billing_address != 0&& billing_area != 0 && billing_city != 0 && billing_zipcode != 0 && billing_phone != 0 && shipping_firstname != 0&& shipping_lastname != 0 && shipping_email != 0 && shipping_address != 0&& shipping_area != 0 && shipping_city != 0 && shipping_zipcode != 0 && shipping_phone != 0){
		
		document.getElementById("dangeralert").style.display = "none";
		//alert(value);
		
		             jQuery.ajax({
                    type:'POST',
                    url:'customeradd.php',
                    data:'value='+value+'&billing_firstname='+billing_firstname+'&billing_lastname='+billing_lastname+'&billing_email='+billing_email+'&billing_address='+billing_address+'&billing_area='+billing_area+'&billing_city='+billing_city+'&billing_zipcode='+billing_zipcode+'&billing_phone='+billing_phone+'&shipping_firstname='+shipping_firstname+'&shipping_lastname='+shipping_lastname+'&shipping_email='+shipping_email+'&shipping_address='+shipping_address+'&shipping_area='+shipping_area+'&shipping_city='+shipping_city+'&shipping_zipcode='+shipping_zipcode+'&shipping_phone='+shipping_phone,
                    success: function(data){
						//alert(data);
                    }

                     });

	
	document.Form2.action = "https://www.sandbox.paypal.com/cgi-bin/webscr"
    //document.Form2.target = "_blank";    // Open in a new window

    document.Form2.submit();             // Submit the page
    
    return true;			 

	}else{
	    document.getElementById("dangeralert").style.display = "block";
		document.getElementById("alertmsg").innerHTML="Please fill up form";
		//alert('Please fill up form');
	}
	
        
      }
	  
  

}*/


	 function mailchk()
    {
	
	var billing_email = document.getElementById('billing_email').value;
	
	 		        jQuery.ajax({
                    type:'POST',
                    url:'orderemail.php',
                    data :'orderemail='+billing_email,
                    success: function(data){
						if(data=="login"){
							alert('you already have account , please login');
							location.href="login.php";
						}
                    }

                    });
		
	}
	

	
	
	
	
	
function validateForm() {
	
	<?
	  $id = $auth->get_members_id();
	  if(empty($id)){
	  ?>

	 var value = document.getElementById('showshipping_withoutlogin').checked;
     <?
	  }else{
     ?>	 
	  var value = document.getElementById('showshipping').checked;
     <?
	  }
     ?>
	 
	 	  if(value==true)
      {
	 
    var shipping_firstname = document.forms["Form2"]["shipping_firstname"].value;
    if (shipping_firstname == "") {
        alert("shipping_firstname must be filled out");
        return false;
    }
	
	var shipping_lastname = document.forms["Form2"]["shipping_lastname"].value;
    if (shipping_lastname == "") {
        alert("shipping_lastname must be filled out");
        return false;
    }
	
    var shipping_email = document.forms["Form2"]["shipping_email"].value;
    var atpos = shipping_email.indexOf("@");
    var dotpos = shipping_email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=shipping_email.length) {
        alert("Not a valid e-mail address");
        return false;
    }
	
	var shipping_address = document.forms["Form2"]["shipping_address"].value;
    if (shipping_address == "") {
        alert("shipping_address must be filled out");
        return false;
    }
	
	var shipping_area = document.forms["Form2"]["shipping_area"].value;
    if (shipping_area == "") {
        alert("shipping_area must be filled out");
        return false;
    }
	
	var shipping_city = document.forms["Form2"]["shipping_city"].value;
    if (shipping_city == "") {
        alert("shipping_city must be filled out");
        return false;
    }
	
	var shipping_zipcode = document.forms["Form2"]["shipping_zipcode"].value;
    if (shipping_zipcode == "") {
        alert("shipping_zipcode must be filled out");
        return false;
    }
	
	var shipping_phone = document.forms["Form2"]["shipping_phone"].value;
    if (shipping_phone == "") {
        alert("shipping_phone must be filled out");
        return false;
    }
	
	
	  }
	
	return true;
}

</script>

</body>
</html>
<!-- TemplateEndEditable -->
<?php
/* DO NOT EDIT THE FOLLOWING (2) LINES*/
$main_content = ob_get_contents();
ob_end_clean();
/*DO NOT EDIT ANYTHING BELOW THIS LINE!*/
include("template_card.php");?>