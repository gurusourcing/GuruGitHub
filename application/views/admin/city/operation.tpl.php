<?php
/**
* Admin City page 
* 
* @see, controllers/admin/city.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#city").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_city_cont",
                "contentContainer" : "#s_city_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("city/ajax_operation");?>",  
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_city"]+"("+values["state_id"]+', '+values["country_id"]+")");
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
        <div id="city">
            <ul class="name_list edit_form">
                <li>
                <span>City : </span>
                <div id="s_city_lbl" class="alignleft"></div>
                <div id="s_city_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value=""> 
                      <input id="s_city" name="s_city" type="text" value="" /> 
                      
                      <label>Longitude: </label>
                      <input id="s_longitude" name="s_longitude" type="text" value="" />
                      <label>Latitude: </label>
                      <input id="s_latitude" name="s_latitude" type="text" value="" />
                      
                      <label>State: </label> <?=form_dropdown("state_id",  dd_state(),'','id="state_id"');?>
                      <label>Country: </label> <?=form_dropdown("country_id",  dd_country(),'','id="country_id"');?>
                      
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
 */?>
<div id="city" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>City:</label></div>
        <div id="s_city_lbl" class="grid3"></div>
        <div id="s_city_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_city" name="s_city" type="text" value="" size="15" />
              <label>Longitude: </label>
              <input id="s_longitude" name="s_longitude" type="text" value="" />
              <label>Latitude: </label>
              <input id="s_latitude" name="s_latitude" type="text" value="" />

              <label>State: </label><div class="clear"></div> <?=form_dropdown("state_id",  dd_state(),'','id="state_id"');?><div class="clear"></div>
              <label>Country: </label><div class="clear"></div> <?=form_dropdown("country_id",  dd_country(),'','id="country_id"');?><div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>



