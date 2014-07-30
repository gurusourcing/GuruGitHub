<script type="text/javascript">
$(document).ready(function(){
  $("#change_image").click(function(){
        $("#captcha").attr('src','<?php echo base_url().'captcha'?>/index/'+Math.random());
    });
});
</script>
<div class="full_no_sidebar">
        	<div class="main_panel">
            	<h1>Contact us</h1>
               <div class="category_panel newservice signup_formsection">
               		<form  method="post" enctype="multipart/form-data" action="">
                            <p><label class="alignleft">Name</label> <input required="true" name="name" type="text" size="38" value="<?php if(is_userLoggedIn()) echo get_userLoggedIn("s_name");?>"></p>
                            <p><label class="alignleft">Email</label> <input required="true" name="email" type="email" size="38" value="<?php if(is_userLoggedIn()) echo get_userLoggedIn("s_email");?>"></p>
                            <p><label class="alignleft">Purpose</label> <select required="true" name="purpose"><option value="Advertise With Us">Advertise With Us</option><option value="Others">Others</option></select></p>
                            <p><label class="alignleft">Description</label> <textarea name="description" required="true" class="with240" cols="32" rows="5"></textarea></p>
                            <p><label class="alignleft">&nbsp;</label>
                            	<span class="alignleft top_pad">Type the code below
                                    <img src="<?php echo base_url()?>captcha"  alt="Captcha" id="captcha"   width="165" height="59" alt="captcha" class="clear top_mar" />
                                    <a href="javascript:void(0);" id="change_image" >
                                        <img src="<?php echo base_url()?>theme/guru_frontend/images/reload.png" title="Reload" align="Reload"/>
                                    </a><br />
                                <input type="text" required="true" name="captcha" size="20" class="top_mar botmar"><br>
                                <input type="submit" class="top_mar" value="Submit">
                                </span>
                            </p>
                    </form>
               </div> 
              <div class="facebook_login toppad20">
                <div class="info skyborder"><?=  format_text($page_content); ?></div>
              </div>
            </div>
        </div>
