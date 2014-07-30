<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

    /**
    * Location AutoComplete
    */
     $( "#popular_location_name" ).autocomplete({
        source: "<?=site_url("autocomplete/ajax_locationName")?>",
        minLength: 2,
        select: function( event, ui ) {
            
            if(ui.item)
            {
                /*$("#zip_id").attr("value",ui.item.zip_id);
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#city_id").attr("value",ui.item.city_id);
                $("#state_id").attr("value",ui.item.state_id);*/
                
                $("#popular_location_ids").attr("value",ui.item.popular_location_id);
                $("#zip_ids").attr("value",ui.item.zip_id);
                $("#city_ids").attr("value",ui.item.city_id);
                $("#state_ids").attr("value",ui.item.state_id);  
                
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#city_name").attr("value",ui.item.city_name);
                
                //show only location name in the textbox here
                ui.item.value=ui.item.popular_location_name;
            }
        },
        search: function( event, ui ) {
          ///country is mandatory  
          if($("#country_id").attr("value")=="")
          {
              /**
              * Dialogbox ceated by inedit.  
              */
              /*$( "#dialog-message" ).find("#dialog-message-icon")
              .removeClass().addClass("ui-icon ui-icon-alert");
              $( "#dialog-message" ).prev().find(".ui-dialog-title").text("Error");*/
              $( "#dialog-message" ).find("#dialog-message-content").html("Please select country.");
              $( "#dialog-message" ).dialog( "open" ); 
              $("#popular_location_name").attr("value","");
                
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
                $("#zip_ids").attr("value",ui.item.zip_id);
                $("#city_ids").attr("value",ui.item.city_id);
                $("#state_ids").attr("value",ui.item.state_id);
                $("#popular_location_ids").attr("value",ui.item.popular_location_id);
                
                $("#city_name").attr("value",ui.item.city_name);
                $("#popular_location_name").attr("value",ui.item.popular_location_name);
                
            }
            
            /*console.log( ui.item ?
            "Selected: " + ui.item.value + " aka " + ui.item.id :
            "Nothing selected, input was " + this.value );*/
        },
        search: function( event, ui ) {
          ///country is mandatory  
          if($("#country_id").attr("value")=="")
          {
              /**
              * Dialogbox ceated by inedit.  
              */
              /*$( "#dialog-message" ).find("#dialog-message-icon")
              .removeClass().addClass("ui-icon ui-icon-alert");
              $( "#dialog-message" ).prev().find(".ui-dialog-title").text("Error");*/
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
                /*$("#zip_id").attr("value",ui.item.zip_id);
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#city_id").attr("value",ui.item.city_id);
                $("#state_id").attr("value",ui.item.state_id);*/
                
                $("#zip_ids").attr("value",ui.item.zip_id);
                $("#city_ids").attr("value",ui.item.city_id);
                $("#state_ids").attr("value",ui.item.state_id);  
                $("#popular_location_ids").attr("value",ui.item.popular_location_id);
                
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#popular_location_name").attr("value",ui.item.popular_location_name);
            }
        },
        search: function( event, ui ) {
          ///country is mandatory  
          if($("#country_id").attr("value")=="")
          {
              /**
              * Dialogbox ceated by inedit.  
              */
              /*$( "#dialog-message" ).find("#dialog-message-icon")
              .removeClass().addClass("ui-icon ui-icon-alert");
              $( "#dialog-message" ).prev().find(".ui-dialog-title").text("Error");*/
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
        var u='<?=site_url("autocomplete/ajax_zipCode?term=");?>'+$("#zip_code").attr("value");
        var u1='<?=site_url("autocomplete/ajax_cityName?term=");?>'+$("#city_name").attr("value");
        var u2='<?=site_url("autocomplete/ajax_locationName?term=");?>'+$("#popular_location_name").attr("value");
        if (settings.url == u 
            || settings.url == u1  
            || settings.url == u2  
        ) {
            settings.url=settings.url+'&country_id='+$("#country_id").attr("value");
        }
    });
    ///end Location AutoComplete    
    
});    
});

</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
			<?=theme_user_navigation();?>
            <div class="main_panel">
                <h1><?=$page_title;?></h1>
                <p><?=get_cms(12,'s_content');?></p>
                <form id="frm_service" action="" method="post">
                <input id="form_token" name="form_token" type="hidden" value="<?=$default_value["form_token"]?>">
                <input id="action" name="action" type="hidden" value="<?=$default_value["action"];?>">
                <input id="country_id" name="country_id" type="hidden" value="<?=@$default_value["country_id"];?>">
                <input id="zip_ids" name="zip_ids" type="hidden" value="">
                <input id="city_ids" name="city_ids" type="hidden" value="">
                <input id="state_ids" name="state_ids" type="hidden" value="">
                <input id="popular_location_ids" name="popular_location_ids" type="hidden" value="">
                              
                <div class="category_panel newservice">
                    <p><label class="alignleft">Service name</label> 
                        <input id="s_service_name" name="s_service_name" required="true" type="text" size="38" /></p>
                    <p><label class="alignleft">Select service category</label> 
                        <?=form_dropdown('cat_id',dd_category(),@$default_value["cat_id"],'id="cat_id" required="true"');?>
                        
                        </p>
                    <?/*<p><label class="alignleft">Select Service Country</label> 
                        <?=form_dropdown("country_id",dd_country(),@$default_value["country_id"],'id="country_id"');?>
                    </p>*/?>                        
                    <p><label class="alignleft">Select Service Location</label> 
                        <input id="popular_location_name" name="popular_location_name" required="true" type="text" class="short" />
                        </p>
                    <p><label class="alignleft">Zip</label> 
                        <input id="zip_code" name="zip_code" required="true" type="text" class="short alignleft" /> 
                        <span class="alignleft rightpad top_pad">  or  City</span> 
                        <input id="city_name" name="city_name" required="true" type="text" class="short" /></p>
                    <p><label class="alignleft"></label> 
                    <input type="submit" value="Proceed" /></p>
                </div>
               </form>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  -->
