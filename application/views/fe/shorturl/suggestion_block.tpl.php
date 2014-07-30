<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
		<?/*<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>*/?>
		<base href="<?php echo base_url(); ?>" />

		<script type="text/javascript">
			jQuery(function($){
				$(document).ready(function(){
								
						/**
					    * Location AutoComplete
					    */
					     $( "#state_name" ).autocomplete({
					        source: "<?=site_url("autocomplete/ajax_stateName")?>",
					        minLength: 2,
					        select: function( event, ui ) {
					            
					            if(ui.item)
					            {
					                //$("#popular_location_ids").attr("value",ui.item.popular_location_id);
					                $("#zip_id").attr("value",ui.item.zip_id);
					                $("#city_id").attr("value",ui.item.city_id);
					                $("#state_id").attr("value",ui.item.state_id);  
					                
					                $("#zip_code").attr("value","");
					                $("#city_name").attr("value","");
					            }
					        },
					        search: function( event, ui ) {
					          ///country is mandatory  
					          if($("#country_id").attr("value")=="")
					          {
					              /**
					              * Dialogbox ceated by inedit.  
					              */					            
					              $( "#dialog-message" ).find("#dialog-message-content").html("Please select country.");
					              $( "#dialog-message" ).dialog( "open" ); 
					            
					              return false;           
					          }  
					          return true;           
					        }
					    });        
					    
					     $( "#zip_code" ).autocomplete({
					        source: "<?=site_url("autocomplete/ajax_zipCode")?>",
					        minLength: 2,
					        select: function( event, ui ) {
					            
					            if(ui.item)
					            {
					                $("#zip_id").attr("value",ui.item.zip_id);
					                $("#city_id").attr("value",ui.item.city_id);
					                $("#state_id").attr("value",ui.item.state_id);
					                
					                $("#city_name").attr("value",ui.item.city_name);
					                $("#state_name").attr("value",ui.item.state_name);
					            }
					        },
					        search: function( event, ui ) {
					          ///country is mandatory  
					          if($("#country_id").attr("value")=="")
					          {
					              /**
					              * Dialogbox ceated by inedit.  
					              */
					             
					              $( "#dialog-message" ).find("#dialog-message-content").html("Please select country.");
					              $( "#dialog-message" ).dialog( "open" ); 
					              $("#zip_code").attr("value","");					                
					              return false;           
					          }  
					          return true;           
					        }
					    });
					    
					     $( "#city_name" ).autocomplete({
					        source: "<?=site_url("autocomplete/ajax_cityName")?>",
					        minLength: 2,
					        select: function( event, ui ) {
					            
					            if(ui.item)
					            {
					                $("#zip_id").attr("value",ui.item.zip_id);
					                $("#city_id").attr("value",ui.item.city_id);
					                $("#state_id").attr("value",ui.item.state_id);  
					                
					                $("#zip_code").attr("value",ui.item.zip_code);
                                    $("#city_name").attr("value",ui.item.city_name);
                                    $("#state_name").attr("value",ui.item.state_name);					              
					            }
					        },
					        search: function( event, ui ) {
					          ///country is mandatory  
					          if($("#country_id").attr("value")=="")
					          {
					              /**
					              * Dialogbox ceated by inedit.  
					              */					             
					              $( "#dialog-message" ).find("#dialog-message-content").html("Please select country.");
					              $( "#dialog-message" ).dialog( "open" ); 
					              $("#zip_code").attr("value","");
					                
					              return false;           
					          }  
					          return true;           
					        }
					    });    
					    
					    $(document).ajaxSend(function(e, xhr, settings) {
					        //console.log(settings);
					        //passing countryid in zip auto complete//
					        //var u='<?=site_url("autocomplete/ajax_zipCode?term=");?>'+$("#zip_code").attr("value");                            
                            var u='<?=site_url("autocomplete/ajax_zipCode?term=");?>'+$("#zip_code").val();
					        var u1='<?=site_url("autocomplete/ajax_cityName?term=");?>'+$("#city_name").val();
                            var u2='<?=site_url("autocomplete/ajax_stateName?term=");?>'+$("input[name='state_name']").val();
                            
					        if (settings.url == u 
					            || settings.url == u1  
					            || settings.url == u2  
					        ) {
					            settings.url=settings.url+'&country_id='+$("#country_id").val();
					        }
					    });
					    ///end Location AutoComplete   
								
				});
			});	
		</script>		
    </head>
    <body style="width:550px !important;">
        <form id="frm_suggestion_block" action="" method="post" enctype="multipart/form-data" >
			<div class="category_panel newservice">
			<input type="hidden" name="h_suggestion_type" id="h_suggestion_type" value="<?php echo $type ?>"  />
			
			<?php if($type!='location') { ?>
       		<p>
				<label class="alignleft">Suggest <?php echo ucfirst($type); ?></label> 
                <input type="text" size="38" required="true" value="" name="txt_suggestion">				
			</p>			
			<?php } else {  ?>
			<?/*<input id="country_id" name="country_id" type="hidden" value="<?=@get_globalCountry();?>">*/?>
            <input id="zip_id" name="zip_id" type="hidden" value="">
            <input id="city_id" name="city_id" type="hidden" value="">
            <input id="state_id" name="state_id" type="hidden" value="">
						
			<p>
				<label class="alignleft">Country</label> 
                <?php echo form_dropdown("country_id",dd_country(),@get_globalCountry(),'id="country_id"');?>			
			</p>
			
			<p>
				<label class="alignleft">State Name</label> 
                <input size="38" id="state_name" name="state_name" type="text" class="alignleft top_mar5" value="" >	
			</p>		
			
			<p>
				<label class="alignleft">City Name</label> 
                <input size="38" id="city_name" name="city_name" type="text" class="alignleft top_mar5" value="" >		
			</p>
			
			<p>
				<label class="alignleft">Postal code</label> 
                <input size="38" id="zip_code" name="zip_code" type="text" class="alignleft top_mar5" value="" >	
			</p>
			
			<?php } ?>
			
            <p>
				<label class="alignleft">&nbsp;</label>
                <span class="alignleft">
                  <input type="submit" class="top_mar" value="Submit">
				</span>
            </p>
		</div>	
    </form>   
    </body>
</html>
