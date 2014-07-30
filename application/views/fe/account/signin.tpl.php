<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<?php include_once(APPPATH."views/fe/common/facebook_js.php"); ?> 
<div class="full_no_sidebar">
        	<div class="main_panel">
            	<h1>Sign in</h1>
               <div class="category_panel newservice signup_formsection">
               <form action="" enctype="multipart/form-data" method="post">
                   <p><label class="alignleft">Email</label> 
                       <input name="userdata" value="<?=isset($posted['userdata'])?$posted['userdata']:''?>" required="true" type="text" size="38" /></p>
                   <p><label class="alignleft">Password</label> <input name="password" value="<?=isset($posted['password'])?$posted['password']:''?>" required="true" type="password" size="38" /></p>
                  <p><label class="alignleft">&nbsp;</label>
                    <span class="alignleft">
                    <strong><a href="<?=  base_url()?>account/forget_password/" class="black">Forgot password?</a></strong><br />
                      <input type="submit" value="Submit" class="top_mar" />
                      or <a href="<?=  base_url()?>account/signup/" class="orange">Sign up</a> for New User </span>
                  </p>
                 </form>
               </div> 
              <div class="facebook_login toppad20">
              	<p><strong>You may also sign up with your Facebook account</strong></p>
                <a href="javascript:void(0);"><img src="<?=base_url(get_theme_path())."/";?>images/facebook.png" onclick='facebook_connect_init()' width="207" height="44" alt="facebook login" /></a>                
              </div>
            </div>
</div>