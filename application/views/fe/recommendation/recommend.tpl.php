<script type="text/javascript">
$(document).ready(function(){
  $("#dt_dob").datepicker();  
  $("#change_image").click(function(){
        $("#captcha").attr('src','<?php echo base_url().'captcha'?>/index/'+Math.random());
    });
});
</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
		<?=theme_user_navigation();?>
            <div class="main_panel">
                <h1>Recommend <?=$name;?>'s Service</h1>               
  <div class="category_panel newservice signup_formsection">
                <form method="post" name="frm_recommend" id="frm_recommend" action="" enctype="multipart/form-data">
                            <p><label class="alignleft">Select Service</label> 
                             
                            <?=form_dropdown("service_id",dd_service(array('uid'=>$form_token ,'i_active'=>'1')),"",'id="service_id" class="show_cat" required="true"');?>
                            </p>
                            <p><label class="alignleft">Write Recommendation</label> <textarea rows="5" cols="32" name="s_message" id="s_message"></textarea></p>
                            <p>
								<?php /*?><label class="alignleft">&nbsp;</label>
                                <span class="alignleft">Type the code below<br />
                                <img src="<?=site_url("captcha");?>" alt="Captcha" id="captcha" />
                                <a href="javascript:void(0);" id="change_image" >
                                    <img src="<?=site_url( get_theme_path()."images/reload.png");?>" title="Reload" align="Reload"/>
                                 </a><br />
                                <input name="txt_captcha" id="txt_captcha" type="text" value=""  />   <br /><?php */?>
								
								<label class="alignleft">&nbsp;</label>
                                <input type="checkbox" class="rightmar" name="accept" id="accept"  required='true'/> I accept all Terms &amp; Conditions<br />
                                <input type="submit" value="Proceed" class="top_mar"/>
                                </span>
                            </p>
                </form>
               </div> 
              <div class="facebook_login toppad20">
                <h2 class="topmar20"><?=$cms->s_menu;?></h2>
                <div class="info skyborder">
                    <?=format_text($cms->s_content);?>
                </div>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 