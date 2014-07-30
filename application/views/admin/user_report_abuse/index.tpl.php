<?php
/**
* Admin User_doc_verification page 
* 
* @see, controllers/admin/user_doc_verification.php  
*/

?>
<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
        
            $(".e_action_taken").change(function(){
                paramArr = {};
                paramArr[$(this).attr('rel')] = $(this).val();
                ajax_operate(paramArr);
            });

            
    });    
});
function ajax_operate(paramArr){
    $.ajax({
                type: "POST",
                url: "<?=admin_base_url('user_report_abuse/ajax_status_update');?>",
                data:  paramArr
                }).done(function( msg ) {
                //console.log(msg);
                window.location.reload();
            });
}
</script>


<div id="listing" class="widget">
<div class="whead"><h6>Listing</h6></div>
<div id="dyn2" class="shownpars">
<a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
<div class="tablePars">
    <div class="dataTables_filter" id="DataTables_Table_0_filter">
        <form id="filter" action="" method="post">
        <label>User report abuse: <input type="text" id="s_user_report_abuse" name="s_user_report_abuse" value="<?=@$posted["s_user_report_abuse"];?>"  aria-controls="DataTables_Table_0">
        <input type="submit" name="submit" value="Submit">&nbsp;<input type="reset" name="reset" value="Reset">
        &nbsp;<input type="submit" name="all" value="Show All">
        </label>
        </form>
    </div>
<!--     <div id="DataTables_Table_0_length" class="dataTables_length"><label><?=$add_link;?></label></div>   -->
</div>

<?=$table;?>


</div>
</div>
</div>


<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div>
            <form class="main" id="filter" action="" method="post">
            <div class="widget fluid">
                <div class="formRow">
                    <div class="grid3"><label>User_doc_verification : </label></div> 
                    <div class="grid9"><input type="text" id="s_user_doc_verification" name="s_user_doc_verification" value="<?=@$posted["s_user_doc_verification"];?>"></div>
                </div>
                <div class="formRow">
                <input type="submit" name="submit" value="Submit">&nbsp;<input type="reset" name="reset" value="Reset">
                &nbsp;<input type="submit" name="all" value="Show All">
                </div>
            </div>
            </form>
        </div>
    
        <h4><?=$add_link;?></h4>    
        <div><?=$table;?></div>
        <div><?=$pager;?></div>
    </div>
</div>

*/?>