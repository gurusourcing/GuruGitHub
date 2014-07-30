<?php
/**
* Admin Popular_location page 
* 
* @see, controllers/admin/popular_location.php  
*/

?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
});    
});
</script>

<div id="listing" class="widget">
<div class="whead"><h6>Listing</h6></div>
<div id="dyn2" class="shownpars">
    <a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
            <div class="tablePars">
                <div class="dataTables_filter" id="DataTables_Table_0_filter">
                    <form id="filter" action="" method="post">
                    <label>Popular Location :<input type="text" id="s_location" name="s_location" value="<?=@$posted["s_location"];?>"  aria-controls="DataTables_Table_0">

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

<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div>
            <form id="filter" action="" method="post">
               
                <label>Popular Location : </label> <input id="s_location" name="s_location" value="<?=@$posted["s_location"];?>">
                
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