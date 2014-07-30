<?php
/**
* Admin user permission page
* Admin can view and modify the user permission.
* 
* 
* @see, controllers/admin/user_permission.php  
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
    * changing the permission status using ajax     
    */
      $('input[type="checkbox"]').click(function(){
          var value=$(this).attr('value');
          var chk=$(this).prop('checked');
          $.blockUI({message: 'Saving please wait...' });
          $.post('<?php echo admin_base_url("user_permission/ajaxPermissionUpdate");?>',{permission:value, checked: chk},
            function(){
                 $.unblockUI();
              });
      })
    
    
});    
});


</script>


<? /* 
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <form action="<?=base_url("admin/user_permission/acl_update");?>" method="post">
        <div><?=$table_roles;?></div>
        <p style="margin: 10px 0;">
        <input type="submit" value="Update" class="leftmar short" />
        <input id="btn_cancel" type="reset" value="Cancel" class="short" />        
        </p>

        </form>
    </div>
</div>
*/ ?>


<div id="listing" class="widget">
<div class="whead"><h6>Listing</h6></div>
<div id="dyn2" class="shownpars">

<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
<?/*
<div class="dataTables_filter" id="DataTables_Table_0_filter">
    <form action="<?=base_url("admin/user_permission/acl_update");?>" method="post">
    <?=$table_roles;?>
    <input type="submit" value="Update" class="leftmar short" />&nbsp;<input id="btn_cancel" type="reset" value="Cancel" class="short" /> 
    </form>
</div>
*/?>
    <form action="<?=admin_base_url("user_permission/acl_update");?>" method="post">
    <?=$table_roles;?>
    <div class="clear"></div>
        <div class="formRow">
        <div class="grid9">
        <input type="submit" value="Update" class="leftmar short" />&nbsp;<input id="btn_cancel" type="reset" value="Cancel" class="short" /> 
        </div>
        </div>
    </form>

</div>
</div>
</div>