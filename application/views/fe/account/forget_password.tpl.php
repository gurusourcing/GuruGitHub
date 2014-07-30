<script type="text/javascript">
$(document).ready(function(){
  $("#change_image").click(function(){
        $("#captcha").attr('src','<?php echo base_url().'captcha'?>/index/'+Math.random());
    });
});
</script>
<div class="full_no_sidebar">
        	<div class="main_panel">
            	<h1>Forgot Password ?</h1>
                <p>To reset your password, type the full email address that you use to <a href="<?=  base_url() ?>account/signin" class="orange">Sign in</a></p>
               <div class="category_panel newservice signup_formsection">
               			<form action="" enctype="multipart/form-data" method="post">
                            <p><label class="alignleft">Email</label> <input value="<?=isset($posted['s_email'])?$posted['s_email']:'';?>" name="s_email"  type="email" required="true" size="38" /></p>
                            <p><label class="alignleft">&nbsp;</label>
                            	<span class="alignleft">Type the code below
                                    <img src="<?php echo base_url()?>captcha" alt="Captcha" id="captcha"   width="165" height="59" alt="captcha" class="clear top_mar" />
                                <a href="javascript:void(0);" id="change_image" >
                                    <img src="<?php echo base_url()?>theme/guru_frontend/images/reload.png" title="Reload" align="Reload"/>
                                 </a><br />
                                <input type="text" required="true" name="captcha" class="top_mar botmar" size="20" /><br />
                                <input type="submit" value="Submit" class="top_mar" />
                                </span>
                            </p>
                    </form>
               </div> 
          </div>
</div>