<?php
/**
* Admin State page 
* 
* @see, controllers/admin/state.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#state").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_state_cont",
                "contentContainer" : "#s_state_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("state/ajax_operation");?>",  
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_state"]+"("+values["country_id"]+")");
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
        <div id="state">
            <ul class="name_list edit_form">
                <li>
                <span>State : </span>
                <div id="s_state_lbl" class="alignleft"></div>
                <div id="s_state_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      <input id="s_state" name="s_state" type="text" value="" />
                      <label>Country: </label> <?=form_dropdown("country_id",  dd_country(),'','id="country_id"');?>
                </div>
                <a  href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>                
                </li>
            </ul>
            
        </div>
    </div>
</div>
 * 
 */?>
<div id="state" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>State :</label></div>
        <div id="s_state_lbl" class="grid3"></div>
        <div id="s_state_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_state" name="s_state" type="text" value="" size="15" />
              <label>Country: </label><div class="clear"></div> <?=form_dropdown("country_id",  dd_country(),'','id="country_id"');?><div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit tipS" title="Edit">Edit</a>         
    </div>
</div>

