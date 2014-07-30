<?php
/**
* Admin theme page
* Admin can view and modify the them.
* 
* 
* @see, controllers/admin/theme.php  
* 
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
    $("#btn_cancel").click(function(){
       window.location.href="<?=get_destination();?>" 
    });
    
    /**
    * before update confirm from user to logout annd login again    
    */
     $("#btn_update").click(function(){
         if(confirm('This will immediately change the template. Are you sure?')) {
            return true;
        }
        else
         return false;
     });
    
});    
});


</script>



<? /* <div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <form action="<?=base_url("admin/theme/update");?>" method="post">
        <div><?=$table_themes;?></div>
        <p style="margin: 10px 0;">
        <input type="submit" value="Update" class="leftmar short" />
        <input id="btn_cancel" type="reset" value="Cancel" class="short" />        
        </p>

        </form>
    </div>
</div>
*/ ?>


<div id="listing" class="widget">
<div class="whead"><h6><?php echo $page_title;?></h6></div>
<div id="dyn2" class="shownpars">
<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
    <form action="<?=admin_base_url("theme/update");?>" method="post">
    <?=$table_themes;?>
    <div class="clear"></div>
        <div class="formRow">
        <div class="grid9">
        <input type="submit" value="Update" id="btn_update" class="leftmar short" />&nbsp;<input id="btn_cancel" type="reset" value="Cancel" class="short" /> 
        </div>
        </div>
    </form>

</div>
</div>
</div>