<?php include_once(APPPATH."views/fe/common/facebook_js.php"); ?> 
<script type="text/javascript">
$(document).ready(function(){
  <?php /*?>$("#dt_dob").datepicker({"changeYear": true, "maxDate": '0'});  <?php */?>
  <?php /*?>$("#dt_dob").datepicker({"changeYear": true, "maxDate": ''});  <?php */?>
  $("#dt_dob").datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
			"yearRange": 'c-100:c+10' ,
        });
  $("#change_image").click(function(){
        $("#captcha").attr('src','<?php echo base_url().'captcha'?>/index/'+Math.random());
    });
	

	$("#signup_btn").click(function(){
		var b_valid = true;
		var regex = /^[a-zA-Z ]*$/;
		var alphn = /^[ A-Za-z0-9_@#&+-]*$/;
		
		if($("#s_user_name").val()!='')
		{
			if (regex.test($("#s_user_name").val())==false) {
				$("#s_user_name_err").html('Provide proper name').show();
				b_valid = false;
			}
			else
			{
				$("#s_user_name_err").html('').hide();
			}
		}
		
		if($("#s_display_name").val()!='')
		{
			if (alphn.test($("#s_display_name").val())==false) {
				$("#s_display_name_err").html('Provide proper display name').show();
				b_valid = false;
			}
			else
			{
				$("#s_display_name_err").html('').hide();
			}
		}
		return b_valid;
		
	});
	
});
</script>
<style type="text/css">
.err_span{ color:#ff0000; margin:2px 0;}
</style>

<div class="full_no_sidebar">
            <div class="main_panel">
                <h1>Sign up for new user</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero. </p>
                <div class="category_panel newservice signup_formsection">
                   <form action="<?php echo base_url('account/signup');?>" enctype="multipart/form-data" method="post" autocomplete="off">
                            <p><label class="alignleft">Name</label> <input required='true' type="text" size="38" id="s_user_name" name="s_user_name" value="<?=isset($posted['s_name'])?$posted['s_name']:''?>" />
							<span class="err_span" style="display:none;" id="s_user_name_err"></span>
							</p>
                            <p><label class="alignleft">Email</label> <input type="email" required='true' size="38" id="s_email" name="s_email" value="<?=isset($posted['s_email'])?$posted['s_email']:''?>"/></p>
                            <p><label class="alignleft">Display name</label> <input type="text" required='true' size="38" id="s_display_name"name="s_display_name" value="<?=isset($posted['s_display_name'])?$posted['s_display_name']:''?>"/>
							<span class="err_span" style="display:none;" id="s_display_name_err"></span>
							</p>
                            <p><label class="alignleft">Password</label> <input type="password" required='true' size="38" id="s_password" name="s_password" /></p>
                            <p><label class="alignleft">Confirm Password</label> <input type="password" required='true' size="38" id="s_cnf_password" name="s_cnf_password" /></p>
                            <p><label class="alignleft">Date of Birth</label><input type="text" required='true' name="dt_dob" id="dt_dob" class="calender" size="20" readonly="readonly" value="<?= $posted['dt_dob'];?>"/></p>
                            <p><label class="alignleft">Gender</label> <span class="alignleft top_mar"><input type="radio"  required='true' name="e_gender" id="e_gender" value="Male" <?php  if($posted['e_gender']=='Male') echo "checked='checked'";?> /> Male &nbsp; <input type="radio" required='true' name="e_gender" id="e_gender" value="Female" class="leftmar"  <?php  if($posted['e_gender']=='Female') echo "checked='checked'";?> /> Female</span> </p>
                            <p><label class="alignleft">&nbsp;</label>
                                <span class="alignleft">Type the code below<br />
                                <img src="<?=site_url("captcha");?>" alt="Captcha" id="captcha" style="margin-top:5px; margin-bottom:5px; border:1px solid #666;"/>
                                <a href="javascript:void(0);" id="change_image" >
                                    <img src="<?=site_url( get_theme_path()."images/reload.png");?>" title="Reload" align="Reload"/>
                                 </a><br />
                                <input name="txt_captcha" id="txt_captcha" type="text" value=""  />   <br />
                                <input type="checkbox" class="rightmar" required='true'  /> I accept all Terms &amp; Conditions<br />
                                <input type="submit" value="Proceed" id="signup_btn" class="top_mar" />
                                </span>
                            </p>
                            </form>
                          
               </div> 
              <div class="facebook_login toppad20">
                  <p><strong>Already an GURU.IN user? Please <a href="<?= site_url('account/signin');?>" class="orange">Login</a>. Or</strong></p>
                <a href="javascript:void(0)"><img onclick='facebook_connect_init()' src="<?=base_url(get_theme_path())."/";?>images/facebook.png" width="207" height="44" alt="facebook login" /></a>
                <h2 class="topmar20">Benefits for using GURU</h2>
                <div class="info skyborder">
                    <?=format_text($benefits->s_content,"decode");?>
                </div>
              </div>
            </div>
        </div>