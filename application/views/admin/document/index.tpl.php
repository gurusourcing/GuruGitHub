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
    
});    
});
</script>



<div id="listing" class="widget">
<div class="whead"><h6><?php echo $page_title;?></h6></div>
<div id="dyn2" class="shownpars">
<a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
<div class="tablePars">
    <div class="dataTables_filter" id="DataTables_Table_0_filter">
        <form id="filter" action="" method="post">
        <label>Document : <input type="text" id="s_document_required" name="s_document_required" value="<?=@$posted["s_document_required"];?>"  aria-controls="DataTables_Table_0">
        <? /* <label>Type : <?=form_dropdown("e_doc_type",dd_document_type(),@$posted["e_doc_type"],'id="e_doc_type"');?>  */ ?>
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