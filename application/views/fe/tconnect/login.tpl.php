<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url(); ?>" />
<title>FoShoTime :: Twitter connect</title>
<script language="javascript" type="text/javascript" src="js/jquery/jquery-1.7.2.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	
	$('#twitter_login').bind('click',function(e){
	
		e.preventDefault();
		var loc = $(this).attr('href');
		window.open(loc,'twitterwindow', 'height=450, width=450, top='+($(window).height()/2 - 225) +', left='+($(window).width()/2 - 125) +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=1');
	});
	
	
});


</script>
</head>
<body>	
</body>
<div>
	<a href="<?php echo base_url().'tconnect/login' ?>" id="twitter_login"><img src="images/tw.png" ></a>

</div>
</html> 