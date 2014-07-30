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

    $("#option").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_option_cont",
                "contentContainer" : "#s_option_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("option/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_suggestion"]+" ("+values["e_type"]+")");
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
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

<div id="option" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Option:</label></div>
        <div id="s_option_lbl" class="grid3"></div>
        <div id="s_option_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_suggestion" name="s_suggestion" type="text" value="" size="15" />
              
              <label>Type :</label><div class="clear"></div><?=form_dropdown("e_type",dd_option_type(),'','id="e_type"');?><div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
