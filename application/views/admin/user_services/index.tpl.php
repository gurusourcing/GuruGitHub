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
    /*$("#active_action").html('<div style="width: 49px;" class="ibutton-container"><div id="uniform-check20" class="checker"><span class="checked"><input style="opacity: 0;" id="check20" name="chbox" type="checkbox"></span></div><div style="width: 15px; left: 0px;" class="ibutton-handle"><div class="ibutton-handle-right"><div class="ibutton-handle-middle"></div></div></div><div style="width: 44px;" class="ibutton-label-off"><span style="margin-right: 0px;"><label></label></span></div><div style="width: 0px;" class="ibutton-label-on"><span style="margin-left: -28px;"><label></label></span></div><div class="ibutton-padding-left"></div><div class="ibutton-padding-right"></div></div>');*/
    
    
    /**
    * {"i_active":{ 0:$("#i_active:checked").attr("value") } } 
    */
    /*$("input[id='i_active']").each(function(){
        
        $(this).change(function(){
           $.post('<?php echo base_url('admin/user_services/ajax_changeStatus')?>',
            {"id":{ 0: $(this).attr("value") }},
            function(data){
                
           });
        });        
        
    });
    $("input[class='i_active']").each(function(){
        
        $(this).change(function(){
            var chk=$(this).is(":checked");
            if(chk)
                $(this).attr("value","1");
            else
                $(this).attr("value","0");
                
            $(this).attr("checked",true);
            
            $("#table_operation").attr('action','<?php echo admin_base_url('user_services/changeStatus')?>');
          
            $("#table_operation").submit();
        });        
        
    });
*/
});    
});

jQuery(function($){
    $(document).ready(function(){
        
            $(".i_active").change(function(){
                paramArr = {};
                paramArr[$(this).val()] = $(this).attr('checked')?1:0;
                ajax_operate(paramArr);
            });

            
    });    
});
function ajax_operate(paramArr){
    $.ajax({
                type: "POST",
                url: "<?=admin_base_url('user_services/ajax_status_update');?>",
                data:  paramArr
                }).done(function( msg ) {
                window.location.reload();
            });
}


</script>



<div id="listing" class="widget">
<div class="whead"><h6><?php echo $page_title;?></h6></div>
<div id="dyn2" class="shownpars">
<a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
<div class="tablePars">
    <div class="dataTables_filter" id="DataTables_Table_0_filter">
        <form id="filter" action="" method="post">
        <label>User : <input type="text" id="s_user_name" name="s_user_name" value="<?=@$posted["s_user_name"];?>"  aria-controls="DataTables_Table_0">
         <? /*<label>Type : <?=form_dropdown("e_doc_type",dd_document_type(),@$posted["e_doc_type"],'id="e_doc_type"');?>  */ ?>
        
        <input type="submit" name="submit" value="Submit">&nbsp;<input type="reset" name="reset" value="Reset">
        &nbsp;<input type="submit" name="all" value="Show All">
        </label>
        </form>
    </div>
    
</div>

<?=$table;?>

</div>
</div>
</div>
</div>

<?php /*<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div>
            <form id="filter" action="" method="post">
                <label>Option : </label> <input id="s_suggestion" name="s_suggestion" value="<?=@$posted["s_suggestion"];?>">
                <label>Type :</label><?=form_dropdown("e_type",dd_option_type(),@$posted["e_type"],'id="e_type"');?>  
                
                <input type="submit" name="submit" value="Submit">&nbsp;<input type="reset" name="reset" value="Reset">
                &nbsp;<input type="submit" name="all" value="Show All">
            </form>
        </div>
    
        <h4><?=$add_link;?></h4>    
        <div><?=$table;?></div>
        <div><?=$pager;?></div>
    </div>
</div>
*/?>