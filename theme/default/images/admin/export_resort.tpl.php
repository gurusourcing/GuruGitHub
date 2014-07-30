 <?php

/*********
* Author: Jagannath Samanta
* Date  : 6 Aug 2012
* Modified By: 
* Modified Date:
* 
* Purpose:
*  View For Category 
*  
* @package General
* @subpackage Category
* 
* @link InfController.php 
* @link My_Controller.php
* @link controler/admin/Category.php
* @link model/Category_model.php
*/



?>

<script type="text/javascript" language="javascript" >

jQuery(function($) {

	$(document).ready(function(){
		$("#tab_search").tabs({
		   cache: true,
		   collapsible: false,
		   fx: { "height": 'toggle', "duration": 500 },
		   show: function(clicked,show){ 
				$("#btn_submit").attr("search",$(show.tab).attr("id"));
				$("#tabbar ul li a").each(function(i){
				   $(this).attr("class","");
				});
				$(show.tab).attr("class","select");
		   }
		});
		
		$("#tab_search ul").each(function(i){
			$(this).removeClass("ui-widget-header");
			$("#tab_search").removeClass("ui-widget-content");
		});
	
		//////////end Clicking the tabbed search/////                                              
	
												  
	
		/////////Submitting the form//////                                              
		$("#btn_submit").click(function(){
			var formid=$(this).attr("search");
			$("#frm_search_"+formid).attr("action","<?php echo $search_action;?>");
			$("#frm_search_"+formid).submit();
		});                                              
		/////////Submitting the form//////           
	
	
	
		/////////clearing the form//////      
		$("#btn_clear").click(function(){
			var formid=$("#btn_submit").attr("search"); 
			clear_form("#frm_search_"+formid);     
		});                                        
	
		function clear_form(formid)
		{
			///////Clearing input fields///
			$(formid).find("input")
			.each(function(m){
				switch($(this).attr("type"))
				{
					case "text":
						$(this).attr("value",""); 
					break;
		
					case "password":
						$(this).attr("value",""); 
					break;            
		
					case "radio":
						 $(this).find(":checked").attr("checked",false);
					break;    
		
					case "checkbox":
						 $(this).find(":checked").attr("checked",false);
					break;                     
				}
			});
		
			///////Clearing select fields///
		
			$(formid).find("select")
			.each(function(m){
				$(this).find("option:selected").attr("selected",false); 
			}); 
		
			///////Clearing textarea fields///
			$(formid).find("textarea")
			.each(function(m){
				$(this).text(""); 
			});     
		}
		/////////clearing the form////// 
		
											
		
		///////////Submitting the form/////////
		
		/////////Submitting the form//////                                              
		$("#btn_download_category").click(function(){
			$("#download_category").submit();
		});                                              
		/////////Submitting the form//////        
		
		$("#download_category").submit(function(){
			var b_valid = true;
			var s_err="";
			$("#div_err").hide(); 
			
			if($.trim($("#download_category #txt_title").val())=="") 
			{
				s_err = 'Please provide download file name.';
				b_valid = false;
			}
			/////////validating//////
			if(!b_valid)
			{
				$("#div_err").html('<div id="err_msg" class="error_massage">'+s_err+'</div>').show("slow");
			}
			return b_valid;
		});    
		///////////end Submitting the form1/////////
		
		
		////////Submitting search all///
		$("#btn_srchall").click(function(){
		 $("#frm_search_3").submit();
		});
		////////end Submitting search all///
		
		///////////end ////
	})
});   
</script>

<div id="right_panel">

    <h2><?php echo $heading;?></h2>
	<div class="info_box">From here Admin can export Resort list as CSV.</div>
	<div class="clr"></div>
   
    <div id="accountlist" style="border-top:none;">
        <div class="mid">
        	<div id="div_err"></div>     
            <form action="<?php echo $pathtoclass?>download_category_details" method="post" name="download_category" id="download_category">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:7px;">
                    <tr>
                    	<th colspan="2" align="center">Export all Resort information to CSV</th>
                    </tr>
                    <tr>
                    	<td width="20%">Filename :</td>
                        <td><input type="text" name="txt_title" id="txt_title" value="" style="width:234px;" /></td>
                    </tr>
                    <tr>
                    	<td width="2%">Header :</td>
                        <td>
                        	<select name="opt_header" id="opt_header" style="width:240px;">
                            	<option value="1">Yes, I need header on the top of the table.</option>
                                <option value="2">No, I don't need header on the top of the table.</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" style="padding-left:200px;">
                            <input type="submit" name="btn_download_category" id="btn_download_category" value="Export Category" />
                        </td>
                	</tr>
                </table>
            </form>
        </div>
    </div>
    
  </div>