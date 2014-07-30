<?php
/**
* Admin Option page 
* 
* @see, controllers/admin/option.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#user").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_user_cont",
                "contentContainer" : "#s_user_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("manage_user/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel  
                "beforeHideCallback" : function(contentContainer,values){},
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
                "beforeShowCallback" : function(fields,values){
                   
                }
            }            
                      
       },
        
    });
    
    
    
    
});    
});


</script>



<?php /*<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="option">
            <ul class="name_list edit_form">
                <li>
                <span>Option : </span>
                <div id="s_option_lbl" class="alignleft"></div>                
                <div id="s_option_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      <input id="s_suggestion" name="s_suggestion" type="text" value="" />
                      <label>Type :</label><?=form_dropdown("e_type",dd_option_type(),'','id="e_type"');?>  
                </div>
                <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
*/ ?>
<? //dd_document_type() ?>
<div id="user" class="widget fluid">
    <div class="formRow">
    <div class="grid3"><?/*<label>User </label>*/?></div>
        <div id="s_user_lbl" class="grid3"></div>
        <div id="s_user_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <div id="form_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
                  <div class="formRow">
                  <div class="grid4"><label>Name :</label></div>
                  <div class="grid4"><input type="text" name="s_user_name" id="s_user_name" value=""></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Email :</label></div>
                  <div class="grid4"><input type="text" name="s_email" id="s_email" value=""></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>New password :</label></div>
                  <div class="grid4"><input id="s_password" name="s_password" type="password" value="" /></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Confirm password :</label></div>
                  <div class="grid4"><input id="s_confirm_password" name="s_confirm_password" type="password" value="" /></div>
                  </div> 
        </div>
        <div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
