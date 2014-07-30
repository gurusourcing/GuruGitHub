<?php
/**
* Admin category page 
* 
* @see, controllers/admin/category.php  
*/

?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
});    
});
</script>



<?php /* <div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div>
            <form id="filter" action="" method="post">
                <label>Sub Category : </label> <input id="s_sub_category" name="s_sub_category" value="<?=@$posted["s_sub_category"];?>">
                <label>Category : </label> <?=form_dropdown("cat_id",dd_category(),@$posted["cat_id"],'id="cat_id"');?>
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


<div id="listing" class="widget">
<div class="whead"><h6><?php echo $page_title;?></h6></div>
<div id="dyn2" class="shownpars">
<a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
<div class="tablePars">
    <div class="dataTables_filter" id="DataTables_Table_0_filter">
        <form id="filter" action="" method="post">
        <label>Sub Category: <input type="text" id="s_sub_category" name="s_sub_category" value="<?=@$posted["s_sub_category"];?>"  aria-controls="DataTables_Table_0"> 
        <? /*<label>Category : </label> <?=form_dropdown("cat_id",dd_category(),@$posted["cat_id"],'id="cat_id"');?>*/?>
        <input type="submit" name="submit" value="Submit">&nbsp;<input type="reset" name="reset" value="Reset">
        &nbsp;<input type="submit" name="all" value="Show All">
        </label>
        </form>
    </div>
     <div id="DataTables_Table_0_length" class="dataTables_length"><label><?=$add_link;?></label></div>   
</div>

<?=$table;?>


</div>
</div>
</div>