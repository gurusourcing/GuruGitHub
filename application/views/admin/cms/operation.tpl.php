<?php
/**
* Admin Cms page 
* 
* @see, controllers/admin/cms.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
      editor = $("#s_content").cleditor({width:'100%',height:'100%'})[0].focus();
   /* $("#cms").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#s_cms_cont",
                "contentContainer" : "#s_cms_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("cms/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.html(values["s_menu"]);
                    console.log(values);
                },
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},
                "beforeSaveCallback" : function(fields){},
                "beforeShowCallback" : function(fields,values){
                    
                    ifrm = $('iframe').contents().find("body").html('');
                    //console.log($(".cleditorButton[title='Show Source']").click());
                    setTimeout(function(){$(window).trigger('resize');},1000, '', '');
                }
            }            
                      
       }
        
    });
    
    ///hacking for htmlentities using php for cli editor/////
    $(".cleditorButton[title='Show Source']").click();
    $(".cleditorButton[title='Show Rich Text']").click();                        
    ///hacking for htmlentities using php for cli editor/////    */
   
});    

/*$(window).resize(function(){
    editor.refresh();
});*/






});


</script>


<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="cms">
            <ul class="name_list edit_form">
                <li>
                <div class="formRow">
                <div class="grid3">
                    <label>Cms : </label><div id="s_cms_lbl" class="alignleft"></div>
                </div>               
                <div id="s_cms_cont" class="edit_section">
                      <input id="form_token" name="form_token" type="hidden" value="">
                      <input id="action" name="action" type="hidden" value="">  
                      
                      <div class="grid9">
                      <input id="s_cms" name="s_cms" type="text" value="" size="15" />
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

<div id="cms" class="widget fluid">
    <div class="formRow">
<!--        <div class="grid3"><label>Cms:</label></div>
        <div id="s_cms_lbl" class="grid3"></div>-->
        <div id="s_cms_cont" class="grid9">
        <form id="frm_advertisement" method="post" action="<?= admin_base_url("cms/ajax_operation")?>" enctype="multipart/form-data">
              <input id="form_token" name="form_token" type="hidden" value="<?=$default_value['form_token']?>">
              <input id="action" name="action" type="hidden" value="<?=$default_value['action']?>">  
              <label>Menu :</label>
              <input id="s_menu" name="s_menu" type="text" size="15" value="<?= $default_value['s_menu'];?>"/>
              <?/*<label>Url :</label>
              <input id="s_url" name="s_url" type="text" value="" size="15" />*/?>
              <label>Content :</label>
    <textarea  id="s_content" name="s_content"  ><?=format_text($default_value['s_content']);?></textarea>
                 
           <input id="btn_" type="submit" value="Submit">   <input type="reset" value="Cancel">                           
           </form>    
        </div>
        <!--<a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>  -->
        
        
    </div>
    
</div>



