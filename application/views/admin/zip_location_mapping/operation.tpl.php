<?php
/**
* Admin Zip_location_mapping page 
* 
* @see, controllers/admin/zip_location_mapping.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#zip_location_mapping").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_zip_location_mapping_cont",
                "contentContainer" : "#s_zip_location_mapping_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("zip_location_mapping/ajax_operation");?>", 
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["popular_location_id"]+"("+values["zip_id"]+")");
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){}
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
        <div id="zip_location_mapping">
            <ul class="name_list edit_form">
                <li>
                <span>Zip Location Mapping : </span>
                <div id="s_zip_location_mapping_lbl" class="alignleft"></div>
                <div id="s_zip_location_mapping_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value=""> 
                      
                      <label>Popular Location: </label>
                      <?=form_dropdown("popular_location_id", dd_popular_location(),'','id="popular_location_id"');?>
                      <label>Zip: </label> <?=form_dropdown("zip_id",  dd_zip(),'','id="zip_id"');?>
                   
                      
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
*/?>
<div id="zip_location_mapping" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Zip Location Mapping :</label></div>
        <div id="s_zip_location_mapping_lbl" class="grid3"></div>
        <div id="s_zip_location_mapping_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <label>Popular Location: </label><div class="clear"></div>
              <?=form_dropdown("popular_location_id", dd_popular_location(),'','id="popular_location_id"');?>
              <div class="clear"></div>
              <label>Zip: </label><div class="clear"></div>
              <?=form_dropdown("zip_id",  dd_zip(),'','id="zip_id"');?>
              <div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit tipS" title="Edit">Edit</a>         
    </div>
</div>