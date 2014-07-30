<?php
/**
* Admin sub category page 
* 
* @see, controllers/admin/service_extended_definition.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
		
		
		$('.extend_cat_chk').each(function(){
			var chkId = $(this).attr('id');
			var checked = $("input[id=" + chkId + "]:checked").length;
			if (checked == 0) 
			{
			   return false;
			} else
			{
			    $(this).parent('span').addClass('checked');
			}
			
		});

                /**
          * Show Category, Sub category Dropdowns 
          * if and only if, "e_doc_type" is "Service"
          */
          ////ajax populatig subcategories
          $("#cat_id").change(function(){
             var v= $(this).find("option:selected").attr("value");
             $("#sub_cat_id option[value!='']").remove();

             $.getJSON("<?=admin_base_url('category_service_extended_definition/ajax_sub_cat_list');?>",
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
          
//           ////ajax populatig service
//          $("#sub_cat_id").change(function(){
//            var cat_id= $(this).find("option:selected").attr("value");
//             var sub_cat_id= $(this).find("option:selected").attr("value");
//             $("#service_id option[value!='']").remove();
//
//             $.getJSON("<?=admin_base_url('category_service_extended_definition/ajax_service_list');?>",
//              {"sub_cat_id":sub_cat_id,'cat_id':cat_id},
//              function(data){
//                  if(data)
//                  {
//                      $.each(data,function(i,v){
//                         var opt=new Option(v,i);
//                         $("#service_id").append(opt);                    
//                      });
//                  }
//              }
//             );
//
//
//          });
          /**
          * To display the subcategory selected, 
          * by default.
          */
          $("#cat_id").change();
    });    
});


</script>
<?php if($stage=='step 1'): ?>
<div id="listing" class="widget">
    <div class="whead"><h6><?php echo $page_title;?></h6></div>
    <div id="dyn2" class="shownpars">
        <a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
            <div class="tablePars">
                <div class="dataTables_filter" id="DataTables_Table_0_filter">
                    <form id="filter" action="" method="post">
                        <div class="clear"></div>
                        <label class="show_cat">Category :</label>
                        <?=form_dropdown("cat_id",dd_category(),@$cat_id,'id="cat_id" class="show_cat" style="margin:10px"');?>
                        <?/*<label class="show_cat">Sub Category :</label>
                        <?=form_dropdown("sub_cat_id",array(""=>"--Select--"),"",'id="sub_cat_id" class="show_cat" style="margin:10px"');?>*/?>
                       <label class="show_cat">Country :</label>
                        <?=form_dropdown("country_id",dd_country(),@$country_id,'id="country_id" class="show_cat" style="margin:10px"');?>
                        
                        <input type="submit" value="Submit" name="submit">
                        <div class="clear"></div>
                      
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($stage=='step 2'): ?>
    <div id="listing" class="widget">
    <div class="whead"><h6><?php echo $page_title;?></h6></div>
    <div id="dyn2" class="shownpars">
        <a class="tOptions act" title="Options"><img src="<?=base_url(get_theme_path())."/";?>images/icons/options" alt="" /></a>
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper" role="grid">
           <form id="filter" action="<?php echo admin_base_url('category_service_extended_definition/operation');?>" method="post">
               <div class="tablePars">
                <div class="dataTables_filter" id="DataTables_Table_0_filter">
                    
                        <div class="clear"></div>
                        <label class="show_cat">Category :</label>
                        <?=$s_cat;?>
                        <input type="hidden" name="cat_id" value="<?=$cat_id?>">
                        <?/*<label class="show_cat">Sub Category :</label>
                        <?=$s_sub_cat?>*/?>
                        <input type="hidden" name="sub_cat_id" value="<?=$sub_cat_id?>">
                        <div class="clear"></div>
                        <label class="show_cat">Country :</label>
                        <?=$s_country;?>   
                        <input type="hidden" name="country_id" value="<?=$country_id;?>">
                     
                      <div class="clear"></div>
                    
                </div>
            </div>
               <div class="formRow fluid noBorderB">
               <?=$table;?>
               </div>
                   <div class="clear"></div>
           </form>
        </div>
    </div>
</div>
<?php endif; ?>


