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

    $("#document").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_document_cont",
                "contentContainer" : "#s_document_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("document/ajax_operation");?>",  
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success 
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel                 
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_document_required"]+" ("+values["e_doc_type"]+")");
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
                "beforeShowCallback" : function(fields,values){
                    $("#e_doc_type").change();
                }
            }            
                      
       },
        
    });
    
    
    
    /**
    * Show Category, Sub category Dropdowns 
    * if and only if, "e_doc_type" is "Service"
    */
    
    
    $("#e_doc_type").change(function(){
        
        var doctype=$(this).find("option:selected").attr("value");
        if(doctype=="service")
            $(".show_cat").show("slow");
        else
            $(".show_cat").hide("slow");
            
    });
    $("#e_doc_type").change();///onload check to show cat and subcat by default
    
    ////ajax populatig subcategories
    $("#cat_id").change(function(){
       var v= $(this).find("option:selected").attr("value");
       $("#sub_cat_id option[value!='']").remove();
       
       $.getJSON("<?=admin_base_url('document/ajax_sub_cat_list');?>",
        {"cat_id":v},
        function(data){
            if(data)
            {
                $.each(data,function(i,v){
                   var opt=new Option(v,i);
                   $("#sub_cat_id").append(opt);                    
                });
            }
        }
       );
       
        
    });
    /**
    * To display the subcategory selected, 
    * by default.
    */
    $("#cat_id").change();
    
    
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
<? dd_document_type() ?>
<div id="document" class="widget fluid">
    <div class="formRow">
        <div class="grid3"><label>Document:</label></div>
        <div id="s_document_lbl" class="grid3"></div>
        <div id="s_document_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
              <input id="s_document_required" name="s_document_required" type="text" value="" size="15" />
              <label>Type :</label><div class="clear"></div> <?=form_dropdown("e_doc_type",dd_document_type(),"",'id="e_doc_type"');?><div class="clear"></div>
              <label class="show_cat">Category :</label><div class="clear"></div> <?=form_dropdown("cat_id",dd_category(),"",'id="cat_id" class="show_cat"');?><div class="clear"></div>
             <? /* <label class="show_cat">Sub Category :</label><div class="clear"></div> 
              <?=form_dropdown("sub_cat_id",array(""=>"--Select--"),"",'id="sub_cat_id" class="show_cat"');?>
              <div class="clear"></div>
              */ ?>
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>
