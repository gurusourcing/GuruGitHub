<?php
/**
* Admin Country page 
* 
* @see, controllers/admin/country.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#country").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_country_cont",
                "contentContainer" : "#s_country_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("country/ajax_operation");?>", 
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_country"]);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
            }            
                      
       },
        
    });
    
    
    
});    
});


</script>


<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="country">
            <ul class="name_list edit_form">
                <li>
                <div class="formRow">
                <div class="grid3">
                    <label>Country : </label><div id="s_country_lbl" class="alignleft"></div>
                </div>               
                <div id="s_country_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      
                      <div class="grid9">
                      <input id="s_country" name="s_country" type="text" value="" size="15" />
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

<div id="country" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Country:</label></div>
        <div id="s_country_lbl" class="grid3"></div>
        <div id="s_country_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_country" name="s_country" type="text" value="" size="15" />
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>



