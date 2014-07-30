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

    $("#featured_packages").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_featured_packages_cont",
                "contentContainer" : "#s_featured_packages_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("featured_packages/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_package_name"]);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
                "beforeShowCallback" : function(fields,values){
                   console.log(values); 
                   $('#i_active:checked').parent('span').removeClass('checked')
                   .addClass('checked');
                }
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
<? //dd_document_type() ?>
<div id="featured_packages" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Featured Packages:</label></div>
        <div id="s_featured_packages_lbl" class="grid3"></div>
        <div id="s_featured_packages_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <div id="form_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
                  <div class="formRow">
                  <div class="grid4"><label>Package :</label></div>
                  <div class="grid4"><input type="text" name="s_package_name" id="s_package_name" value=""></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Description :</label></div>
                  <div class="grid4"><textarea name="s_desc" id="s_desc" cols="10" rows="8"></textarea></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Validity :</label></div>
                  <div class="grid4"><input type="text" name="i_months_validity" id="i_months_validity" value=""><span>(months)</span></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Price :</label></div>
                  <div class="grid4"><input type="text" name="i_price" id="i_price" value=""><span>(Total Price. Customer will pay this amount for the package.)</span></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Active :</label></div>
                  <div class="grid4"><input type="checkbox" id="i_active" name="i_active" value="1"/>
                    <?/*<div id="active_action" class="floatL mr10 on_off">
                        <?=form_checkbox('i_active',1,"",'id=i_active');?>
                    </div>*/?>
                  </div>
                  </div>
                  
                 
        </div>
        <div class="clear"></div>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
