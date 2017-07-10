<<<<<<< HEAD
<?
ob_start();
require_once 'mainfile.php';
require_once 'includes/classes/business.class.php';
require_once ('includes/classes/business_detail.class.php');
include 'cartmain.php';
$cart = new Cart;



	$id=0;

	if(isset($_POST['pass'])){
		$id= $_POST['pass'];		
	}

$chk =0;
    if($cart->total_items() > 0){
    $cartItems = $cart->contents();
	
    foreach($cartItems as $item){
		$chk = $chk+1;
		if($chk == $id)
		{
			echo $item["rowid"];
			break;
		}
		
	}
}

=======
<?
ob_start();
require_once 'mainfile.php';
require_once 'includes/classes/business.class.php';
require_once ('includes/classes/business_detail.class.php');
include 'cartmain.php';
$cart = new Cart;



	$id=0;

	if(isset($_POST['pass'])){
		$id= $_POST['pass'];		
	}

$chk =0;
    if($cart->total_items() > 0){
    $cartItems = $cart->contents();
	
    foreach($cartItems as $item){
		$chk = $chk+1;
		if($chk == $id)
		{
			echo $item["rowid"];
			break;
		}
		
	}
}

>>>>>>> 914537a0aee8c5444caa0c77efbe4193ac3be72c
?>