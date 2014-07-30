<script src="https://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript"> 
FB.init({appId: '<?php echo $fb_app_id?>', status: true, cookie: true, xfbml: true, oauth: true});
/*FB.init({status: true, cookie: true, xfbml: true, oauth: true});*/
/*$(document).ready(function(){*/
//FB.XFBML.parse();
/*});    */

<?php 
if(!$user_loggedin) {
?>

FB.getLoginStatus(function(response) {
	  if (response.status === 'connected') {
		// the user is logged in and has authenticated your
		// app, and response.authResponse supplies
		// the user's ID, a valid access token, a signed
		// request, and the time the access token 
		// and signed request each expire
		//console.log(response); return false;
		var uid = response.authResponse.userID;
		var accessToken = response.authResponse.accessToken;
		window.location.href = '<?php echo base_url()?>'+'fconnect/authenticate/'+accessToken;
	  } else if (response.status === 'not_authorized') {
		// the user is logged in to Facebook, 
		// but has not authenticated your app
	  } else {
		// the user isn't logged in to Facebook.
	  }
 });
 
 <?php }  ?>

             
        
            function fblogincheck(){    
            
                        FB._initialized = false;
                        FB.init({appId: '<?php echo $fb_app_id?>', status: true, cookie: true, xfbml: true, oauth: true});
                		
                        FB.login(function(response) {
                        
                            if (response.authResponse) {
                            
                                var access_token = response.authResponse.accessToken;
                                //var encoded = enc(access_token);                                
                                //$('#loading_fconnect').show();    
                                //document.getElementById('fconnect_button_a_id').onclick = function(){ };                    
                                <?php /* if(false) { ?>document.getElementById('loading_right_img').src='<?php echo base_url(); ?>images/front/loader.gif';<?php } */?>
                                //animated_period_fn();                                                    
                                window.location.href = '<?php echo base_url()?>'+'fconnect/authenticate/'+access_token;
                                
                            } else {
                            // user cancelled login
                            }
                        },{scope: 'user_activities,user_birthday,user_education_history,user_hometown,user_interests,user_likes,user_location,user_notes,user_photos,user_videos,user_relationships,offline_access,user_relationship_details,user_religion_politics,user_status,user_website,user_work_history,email,read_stream,manage_friendlists'});
                    
            }
            
        
        function fblogoutcheck(){ 
            FB._initialized = false;
            FB.init({appId: '<?php echo $fb_app_id?>', status: true, cookie: true, xfbml: true, oauth: true});

            FB.getLoginStatus(function(response) {                                
                if (response.status === 'connected') {                                                                    
                    FB.logout(function(response) {    
                    window.location.href = '<?php echo base_url()?>'+'fconnect_test/logout';
                    });
                }else { 
                window.location.href = '<?php echo base_url()?>'+'fconnect_test/logout';
                }
            });
                                        
        }        
        
            function facebook_connect_init(){
                fblogincheck();
            }

</script>
