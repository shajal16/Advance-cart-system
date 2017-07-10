<?php
ob_start();
require_once 'mainfile.php';
require_once 'includes/classes/business.class.php';
require_once ('includes/classes/business_detail.class.php');
require_once 'includes/library.php';
include 'dbConfig.php';


	 $output=0;

if(isset($_POST['orderemail'])){
 	
	$orderemail=$_POST['orderemail'];

	
$sql_order = "SELECT * FROM `members` WHERE email ='$orderemail'";
if($order_table = mysql_query ($sql_order))
{
while ($fetch_order = mysql_fetch_assoc($order_table)){
	$dataemail = $fetch_order['email'];
	if(!empty($dataemail))
	{
	  $output=1;
	}else{
	
    }
}
}else{
	echo mysql_error();
}

}

if($output==1){
	echo "login";
}else{
	echo "nothing";
	  
}
?>