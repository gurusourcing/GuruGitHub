<?php
/**
* Admin User Role page 
* 
* @see, controllers/admin/user_role.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#user_role").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_type_cont",
                "contentContainer" : "#s_type_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("user_role/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_type"]+'<br/><small>'+values["s_desc"]+'</small>');
                },
                "afterSaveCallback" : function(values,contentContainer){},
                "beforeSaveCallback" : function(fields){
                    
                },
            }            
                      
       },
        
    });
    
    
    
});    
});


</script>



<? /* <div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="user_role">
            <ul class="name_list edit_form">
                <li>
                <span>User role : </span>
                <div id="s_type_lbl" class="alignleft">
                    <br/><small></small>
                </div>                
                <div id="s_type_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      <input id="s_type" name="s_type" type="text" value="" />
                      <p style="margin: 10px 0;">
                        <textarea cols="16" rows="4" id="s_desc" name="s_desc"></textarea>
                      </p>
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
*/ ?>

<div id="user_role" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>User role:</label></div>
        <div id="s_type_lbl" class="grid3"></div>
        <div id="s_type_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_type" name="s_type" type="text" value="" size="15" />
              <p style="margin: 10px 0;">
                 <textarea cols="16" rows="4" id="s_desc" name="s_desc"></textarea>
              </p>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
