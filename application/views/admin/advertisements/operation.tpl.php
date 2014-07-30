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

    /*$("#option").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_option_cont",
                "contentContainer" : "#s_option_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("advertisements/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["e_ads_type"]+" ("+values["e_type"]+")");
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
                "beforeShowCallback" : function(fields,values){
                    
                   $('.short').remove();
                   $('#s_image_url').attr('src',values['s_image']);
                   
                }
            },
                        
                      
       },
        
    });
    
    
    $("input:submit").click(function(){
        $("#frm_advertisement").submit();
        return false;   
    });*/
    
    $("#dt_expire").datepicker({
    "dateFormat": "yy-mm-dd",
    "showButtonPanel": true,
    "closeText": "Close",
    "changeYear": true        
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
*/ 
?>

<div id="option" class="widget fluid">
    <div class="formRow">
        <div class="grid3" style="display: none;"  ><label>Advertisement:</label></div>
        <div id="s_option_lbl" class="grid3" style="display: none;"></div>
        <div id="s_option_cont" class="grid9">
        <form id="frm_advertisement" method="post" action="<?= admin_base_url("advertisements/ajax_operation")?>" enctype="multipart/form-data">
              <input id="form_token" name="form_token" type="hidden" value="<?=$default_value["form_token"];?>">
              <input id="action" name="action" type="hidden" value="<?=$default_value["action"];?>">  
              <div class="formRow">
                  <div class="grid3"><label>Upload Image :</label> </div>
                  <div class="grid9">
                    <input id="h_image" name="h_image" type="hidden" value="<?=$default_value["h_image"];?>">  
                    <?php 
                        if($action=='edit')
                        {
                    ?>
                        <img id='s_image_url' src="<?=site_url($default_value["s_image"]);?>" alt="" style='max-width:50px;max-height:50px'/><br/>
                    <?php
                        }
                    ?>
                    <input type="file" class="styled" id="s_image" name="s_image"/>
                  </div>
              </div>

              <div class="formRow">
                  <div class="grid3"><label>Type :</label></div>
                  <div class="grid4"><?=form_dropdown("e_ads_type",dd_advertisement_type(),$default_value["e_ads_type"],'id="e_ads_type" class="stxyled"');?></div>
              </div>
              
              <div class="formRow">
                  <div class="grid3"><label>Description:</label></div>
                  <div class="grid9"><textarea rows="8"   name="s_desc" id="s_desc"><?=$default_value["s_desc"];?></textarea></div>
              </div>
              
              <div class="formRow">
                <div class="grid3"><label>Url:</label></div>
                <div class="grid9"><input class="span12" type="text" name="s_url" id="s_url" value="<?=$default_value["s_url"];?>"/></div>
              </div>
              <?php 
                if($action=="add")
                {              
              ?> 
              <div class="formRow">
                    <div class="grid3"><label>CPC/CPM:</label></div>
                    <div class="grid9"><?=form_dropdown("e_type",dd_advertisement_impresion_type(),$default_value["e_type"],'id="e_type"');?></div>
              </div>
              
              
              <div class="formRow">
                    <div class="grid3"><label>Enter Amount:</label></div>
                    <div class="grid4"><input type="text" name="d_amount" id="d_amount" value="" placeholder="Enter total amount" /></div>
              </div> 
              
              <div class="formRow">
                    <div class="grid3"><label>Enter Total Count:</label></div>
                    <div class="grid4"><input type="text" name="i_total_paidfor_count" placeholder="Enter total count" /></div>
              </div> 
              
              <div class="formRow">
                    <div class="grid3"><label>Approximate Expire Date:</label></div>
                    <div class="grid4"><input type="text" id="dt_expire" name="dt_expire" readonly="true" /></div>
              </div>               
              
              <?php 
                }
               ?>  
                 <input id="btn_" type="submit" value="Submit">   <input type="reset" value="Cancel">                           
             </form>
        </div>
        <a href="javascript:void(0);" style="display: none;"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
