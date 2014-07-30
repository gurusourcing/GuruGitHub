<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url(); ?>" />
<title>FoShoTime :: Twitter connect</title>
<script language="javascript" type="text/javascript" src="js/jquery/jquery-1.7.2.js"></script>
</head>
<body>

	<span id="login"></span>	
	<script src="http://platform.twitter.com/anywhere.js?id=6bTFtg02vir3L3XXlDyEZQ&v=1" type="text/javascript"></script>
	   
    <script type="text/javascript">     
	//twttr.anywhere.config({ callbackURL: "http://www.acumencs.com/foshotime/tconnect" });
 
      twttr.anywhere(function (T) {
        T("#login").connectButton({
          authComplete: function(user) {
            // triggered when auth completed successfully
			alert(111);
          },
          signOut: function() {
            // triggered when user logs out

          }
        });

      });     

    </script>
	<script type="text/javascript">
		// initialize Twitter
		/*twttr.anywhere(function (T) {
			T("#login").connectButton();
		});*/
		
		//// function to open twitter "oAuth" window in popup...
		/*function _show_twitter_window(oauth_url)
		{
		 var width = 850;
		 var height = 580;
		 
		 //half the screen width minus half the new window width (plus 5 pixel borders).
		 iMyWidth = (window.screen.width/2) - (width/2 + 10);
		 //half the screen height minus half the new window height (plus title and status bars).
		 iMyHeight = (window.screen.height/2) - (height/2 + 50);
		 //alert(iMyWidth +"--"+ iMyHeight);
		 twit_window = window.open(oauth_url, 'twitterwindow', 'location=1,status=1,scrollbars=1,width='+ width +',height=' +height +',left='+ iMyWidth +',top='+ iMyHeight +',screenX='+ iMyWidth +',screenY='+ iMyHeight);
		 
		 twit_window.focus();
		}*/
	
	</script>
</body>
<div>
	
</div>
</html> 