<?php 
/*
* File name: place_order.tpl.php
* Purpose: To submit the paypal form automatically to the paypal
* Date Created: 24-June-2014
*/
?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<?php
echo $paypal_form;
echo '<div class="greet">'.$msg.'<br/><img src="'.site_url(get_theme_path().'images/no-pic.jpg').'" alt="Wait..."/></div>';
?>

<script type="text/javascript">
$(document).ready(function(){
	$('#ppcheckoutbtn').hide().delay(1500).click();
	/*setTimeout(function(){
		$('#paypal_checkout').submit();
	},2000);*/
});
</script>
<style type="text/css">
.greet {border: 6px solid #BBBBBB; border-radius: 7px; box-shadow: 0 3px 6px #999999; font-family: georgia,arial,sans-serif; font-size: 20px; margin: 100px auto 0; padding: 6px 10px; text-align: center; width: 730px;}
.greet img{display:inline-block; margin:0 auto; margin-top:15px;}
</style>