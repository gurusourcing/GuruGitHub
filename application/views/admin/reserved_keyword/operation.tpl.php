<?php
/**
* Admin Reserved_keyword page 
* 
* @see, controllers/admin/reserved_keyword.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    $("#reserved_keyword").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_keyword_cont",
                "contentContainer" : "#s_keyword_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("reserved_keyword/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_keyword"]);
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
        <div id="reserved_keyword">
            <ul class="name_list edit_form">
                <li>
                <div class="formRow">
                <div class="grid3">
                    <label>Reserved_keyword : </label><div id="s_keyword_lbl" class="alignleft"></div>
                </div>               
                <div id="s_keyword_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      
                      <div class="grid9">
                      <input id="s_keyword" name="s_keyword" type="text" value="" size="15" />
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

<div id="reserved_keyword" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Reserved_keyword:</label></div>
        <div id="s_keyword_lbl" class="grid3"></div>
        <div id="s_keyword_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_keyword" name="s_keyword" type="text" value="" size="15" />
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>



