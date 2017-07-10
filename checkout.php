<?php
require_once ('mainfile.php'); 
// include database configuration file
include 'dbConfig.php';

// initializ shopping cart class
include 'cartmain.php';
$cart = new Cart;
// redirect to home if cart is empty
if($cart->total_items() <= 0){
    header("Location: index.php");
}

// set customer ID in session
//$_SESSION['sessCustomerID'] = 1;
 

$uid = $auth->get_members_id();

// get customer details by session customer ID
//echo $_SESSION['order_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advance shopping cart</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
    .container{width: 100%;padding: 50px;}
    .table{width: 65%;float: left;}
    .shipAddr{width: 30%;float: left;margin-left: 30px;}
    .footBtn{width: 95%;float: left;}
    .orderBtn {float: right;}
    </style>
</head>
<body>
<div class="container">
    <h1>Order Preview</h1>
    <table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($cart->total_items() > 0){
            //get cart items from session
            $cartItems = $cart->contents();
            foreach($cartItems as $item){
        ?>
        <tr>
        <?
       
        $query = "SELECT * FROM members WHERE members_id = ".$uid;
//$custRow = $query->fetch_assoc();
$result = $db->query($query);

if ($result->num_rows > 0) {
    // output data of each row
    $custRow = $result->fetch_assoc();
}else{
    echo "0 results";
} 
        ?>
        <td><img  src="product_photo/resized/product_small_<?php echo $item['image']; ?>.jpg" style="width:50px;height:50px"></td>
            <td><?php echo $item["name"]; ?></td>
            <td><?php echo $item["price"].' BDT'; ?></td>
            <td><?php echo $item["qty"]; ?></td>
            <td><?php echo $item["subtotal"].' BDT'; ?></td>
        </tr>
        <?php } }else{ ?>
        <tr><td colspan="4"><p>No items in your cart......</p></td>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <?php if($cart->total_items() > 0){ ?>
            <td class="text-center"><strong>Total <?php echo '$'.$cart->total().' USD'; ?></strong></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
    <div class="shipAddr">
   
     <h4>Shipping Details</h4>
        <p><?php echo $custRow['first_name']; ?>
        <?php echo $custRow['last_name']; ?> 
        <?php echo $custRow['nick_name']; ?></p>
        <p><?php echo $custRow['email']; ?></p>
        <p><?php echo $custRow['city']; ?></p>
    </div>
    <div class="footBtn">
        <a href="index.php" class="btn btn-warning"><i class="glyphicon glyphicon-menu-left"></i> Continue Shopping</a>
        <!--<a href="cartAction.php?action=placeOrder" class="btn btn-success orderBtn">Place Order <i class="glyphicon glyphicon-menu-right"></i></a> -->
    <!--  <a href="cartAction.php?action=placeOrder" ><img src="New_checkout_With_Paypal.png" width="179" height="36"></a>
-->
         <a href="cartAction.php?action=placeOrder" ><img src="New_checkout_With_Paypal.png" width="179" height="36"></a>

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">

  <!-- Identify your business so that you can collect the payments. -->
  <input type="hidden" name="business" value="merchant@example.com">

  <!-- Specify a Buy Now button. -->
  <input type="hidden" name="cmd" value="_xclick">

  <!-- Specify details about the item that buyers will purchase. -->
  <input type="hidden" name="item_name" value="<?php echo $item["id"]; ?>">
  <input type="hidden" name="amount" value="<?php echo $cart->total() ?>">
  <input type="hidden" name="currency_code" value="USD">
  <input type="hidden" name="return" value="http://www.example.com/desherbiz/cartAction.php?action=placeOrder">


 <!-- <input type="hidden" name="return" value="http://www.example.com/desherbiz/cartAction.php?action=placeOrder"> -->
  <input type="hidden" name="cancel_return" value="http://www.example.com/desherbiz/cartAction.php?action=placeOrder">

  <!-- Display the payment button. -->
  <input type="image" name="submit" border="0"
 
    src="New_checkout_With_Paypal.png"
    alt="PayPal - The safer, easier way to pay online">
  <img alt="" width="1" height="1"
    src="New_checkout_With_Paypal.png" >

</form>

    </div>
</div>
</body>
</html>