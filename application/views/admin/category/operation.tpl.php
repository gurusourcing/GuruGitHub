<?php
/**
* Admin category page 
* 
* @see, controllers/admin/category.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#category").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_category_cont",
                "contentContainer" : "#s_category_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("category/ajax_operation");?>", 
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel               
                
                "addMoreButton" : "#add_more_alias",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                 
                 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_category"]);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
            }            
                      
       },
        
    });
    
    
    
});    
});


</script>


<div id="category" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Category:</label></div>
        <div id="s_category_lbl" class="grid3"></div>
        <div id="s_category_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_category" name="s_category" type="text" value="" size="15" />
              <label>Description :</label><textarea id="s_desc" name="s_desc" cols="" rows=""></textarea>
              <div class="clear"></div>
              <label class="grid9">Alias names :-</label>
              <div class="clear"></div>
              <div id="add_more_wrapper" class="formRow">
              <span class="grid6"><input id="s_alias_name" name="s_alias_name" type="text" value="" /></span>
              <span class="grid6"><?=form_dropdown('s_alias_country', dd_country(),'','id="s_alias_country"');?></span>
              </div>
              <p id="add_more_alias" class="clear short">+ Add more alias names</p>  
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>

