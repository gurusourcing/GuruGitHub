<?php
/**
* Admin My Profile page
* Admin can view and modify the same profile from here.
* 
* 
* @see, controllers/admin/my_profile.php  
* 
* 
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#my_profile").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_admin_name_cont",
                "contentContainer" : "#s_admin_name_lbl",
                /* since we have set globalSaveResetButton=true, so we dont need these
                "hideButton" : "#full_name_cont input[value='Cancel']",
                "saveButton" : "#full_name_cont input[value='Save']", */
                
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("my_profile/ajax_edit_profile");?>",  
                "beforeShowCallback" : function(fields,values){
                    //fields[0].attr("value","sumit ar");
                    
                },
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["s_admin_name"]);
                },
                "onSaveCallback" : function(fields,values){
                    //return {mode:"error",message:"There is an error."};//
                },
                "afterSaveCallback" : function(values,contentContainer){
                }
                
            }, 
            1 : {
                "fieldContainer" : "#s_password_cont",
                "contentContainer" : "#s_password_lbl",
                /* since we have set globalSaveResetButton=true, so we dont need these
                "hideButton" : "#full_name_cont input[value='Cancel']",
                "saveButton" : "#full_name_cont input[value='Save']", */
                "defaultValues" : $.parseJSON('<?=$default_value[1];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("my_profile/ajax_edit_profile");?>",  
                
                "beforeShowCallback" : function(fields,values){},
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text("xxxxxxx");
                },
                "onSaveCallback" : function(fields,values){
                    //return {mode:"error",message:"There is an error."};//
                },
                "afterSaveCallback" : function(values,contentContainer){}
                
            },             
                      
       },
        
    });
    
    
    $("#s_password,#s_confirm_password").on("click",function(){
       return false;
    }); 
    
}); 
});


</script>


<?php /*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="my_profile">
            <ul class="name_list edit_form">
                <li>
                <span>User name : </span><div id="s_admin_name_lbl" class="alignleft">Sourav Sarkar</div>
                <div id="s_admin_name_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">  
                      <input id="s_admin_name" name="s_admin_name" type="text" value="" />
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
                <li>
                <span><strong>Change Password : </strong> </span><div id="s_password_lbl" class="alignleft">xxxxxxx</div>
                <div id="s_password_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">  
                      <span class="alignleft">
                       Current Password : <input id="s_current_password" name="s_current_password" type="password" value="" />
                      </span>
                      <span class="alignleft">
                       New Password : <input id="s_password" name="s_password" type="password" value="" />
                       Confirm Password : <input id="s_confirm_password" name="s_confirm_password" type="password" value="" />
                      </span>                      
                </div>
                <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
*/ ?>
<div id="my_profile" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>User name:</label></div>
        <div id="s_admin_name_lbl" class="grid3"></div>
        <div id="s_admin_name_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_admin_name" name="s_admin_name" type="text" value="" size="15" />
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
    <div class="formRow">
        <div class="grid3"><label>Current Password:</label></div>
        <div id="s_password_lbl" class="grid3"></div>
        <div id="s_password_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <label>Current Password :<input id="s_current_password" name="s_current_password" type="password" value="" size="15"  />
              <label>New Password :<input id="s_password" name="s_password" type="password" value="" size="15"  />
              <label>Confirm Password :<input id="s_confirm_password" name="s_confirm_password" type="password" value="" size="15" />
              
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
