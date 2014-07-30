<?php
/**
* Admin User_save_search page 
* 
* @see, controllers/admin/user_save_search.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#user_save_search").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_user_save_search_cont",
                "contentContainer" : "#s_user_save_search_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("user_save_search/ajax_operation");?>", 
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_search_tag"]);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
				"beforeShowCallback" : function(fields,values){
					
                   $('#i_lock:checked').parent('span').removeClass('checked')
				   .addClass('checked');
                }
            }            
                      
       }
        
    });
    
    
    
});    
});


</script>


<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="user_save_search">
            <ul class="name_list edit_form">
                <li>
                <div class="formRow">
                <div class="grid3">
                    <label>User_save_search : </label><div id="s_user_save_search_lbl" class="alignleft"></div>
                </div>               
                <div id="s_user_save_search_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      
                      <div class="grid9">
                      <input id="s_user_save_search" name="s_user_save_search" type="text" value="" size="15" />
                      </div>
                      
                </div>
                <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </div>
                </li>
            </ul>
            
        </div>
    </div>
</div>
*/?>

<div id="user_save_search" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>User save search:</label></div>
        <div id="s_user_save_search_lbl" class="grid3"></div>
        <div id="s_user_save_search_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">
              <?/*<label>User: </label><div class="clear"></div> <?=form_dropdown("uid", dd_user(),'','id="uid"');?><div class="clear"></div>*/?>
              <label>User: </label><div class="clear"></div><input id="s_user_name"  type="text" value="" size="15" disabled="" /><div class="clear"></div>
              
              <label>Search Tag: </label>
              <input id="s_search_tag" name="s_search_tag" type="text" value="" size="15" />
              <?/* <label>Search Field Value: </label>
              <input id="s_search_field_value" name="s_search_field_value" type="text" value="" size="15" />*/?>
               <label>Search Url: </label>
              <input id="s_url" name="s_url" type="text" value="" size="15" />
              <label>Lock: </label>
              <input id="i_lock" name="i_lock" type="checkbox" value="1"  size="15" />
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>



