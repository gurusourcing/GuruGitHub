<?php
/**
* Admin theme page
* Admin can view and modify the theme.
* 
* 
* @see, controllers/admin/theme.php  
* 
*/

//$this->load->view("admin/theme/index.tpl.php");//tested
?>


<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
    $("#btn_cancel").click(function(){
       window.location.href="<?=get_destination();?>" 
    });
    
});    
});
</script>

<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <form id="frm_franchisee_theme" action="<?=admin_base_url("theme/save_franchisee_theme");?>" 
        method="post" enctype="multipart/form-data">
        <?/*<input type="hidden" id="form_token" name="form_token" value="<?=$form_token;?>">*/?>
        <div><span>Theme : </span><?=form_dropdown("theme_id",dd_theme(),get_adminLoggedIn("admin_theme_id"));?></div>
        <div id="logo">
            <span>Logo : </span>
            <div id="logo_field" style="border: 1px solid;width: 300px;">
             <?
                theme_jqUploader(array(
                    "upload_container"=> "franchisee_logo",
                    "field" =>  "f_logo",
                    "allow_maxUploadFiles" => 1,
                ));
             ?>
             
            </div>
        </div>
        <p style="margin: 10px 0;">
        <input type="submit" value="Update" class="leftmar short" />
        <input id="btn_cancel" type="reset" value="Cancel" class="short" />        
        </p>

        </form>
    </div>
</div>