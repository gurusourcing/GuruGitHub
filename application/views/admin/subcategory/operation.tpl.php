<?php
/**
* Admin sub category page 
* 
* @see, controllers/admin/subcategory.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#subcategory").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_subcategory_cont",
                "contentContainer" : "#s_subcategory_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("subcategory/ajax_operation");?>", 
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel
                "addMoreButton" : "#add_more_alias",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_wrapper']",/*please use this syntax*/                
                "addMoreShow" : "bottom", //top|bottom                 
                 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_sub_category"]);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
            }            
                      
       },
        
    });
    
    
    
});    
});


</script>



<? /* <div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="subcategory">
            <ul class="name_list edit_form">
                <li>
                <span>Sub Category : </span>
                <div id="s_subcategory_lbl" class="alignleft"></div>                
                <div id="s_subcategory_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      <input id="s_sub_category" name="s_sub_category" type="text" value="" />
                      <div class="clr"></div>
                      <label>Category :</label><?=form_dropdown("cat_id",dd_category(),'','id="cat_id"');?>                      
                      <div class="clr"></div>
                      <label>Description :</label><textarea id="s_desc" name="s_desc" cols="" rows=""></textarea>
                      <div class="clr"></div>
                      <label>Alias names :-</label>
                      <div id="add_more_wrapper">
                        <input id="s_alias_name" name="s_alias_name" type="text" value="" />
                        <?=form_dropdown('s_alias_country', dd_country(),'','id="s_alias_country"');?>
                      </div>
                      <p id="add_more_alias" class="clear short">+ Add more alias names</p>  
                      <div class="clr"></div>                    
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
                
            </ul>
        </div>
    </div>
</div>
*/ ?>


<div id="subcategory" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Sub Category:</label></div>
        <div id="s_subcategory_lbl" class="grid3"></div>
        <div id="s_subcategory_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <div class="clear"></div>
              <input id="s_sub_category" name="s_sub_category" type="text" value="" size="15" />
              <label>Category :</label><?=form_dropdown("cat_id",dd_category(),'','id="cat_id"');?>
              <div class="clear"></div>
              <label>Description :</label><textarea id="s_desc" name="s_desc" cols="" rows=""></textarea>
              <div class="clear"></div>
              <label class="grid9">Alias names :-</label>
              <div class="clear"></div>
                      <div id="add_more_wrapper" class="formRow">
                        <span class="grid6"><input id="s_alias_name" name="s_alias_name" type="text" value="" />&nbsp;</span>
                        <span class="grid6"><?=form_dropdown('s_alias_country', dd_country(),'','id="s_alias_country"');?></span>
                      </div>
                      <p id="add_more_alias" class="clear short">+ Add more alias names</p>  
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
