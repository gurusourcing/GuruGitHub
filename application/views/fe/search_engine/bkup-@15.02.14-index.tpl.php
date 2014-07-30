<?php //pr($posted);?>

<script type="text/javascript">
    var data11 = {};

    /* to add a new section, 

    1. create a array like below:


    *    data11['FILTERING_TYPE'] = [
    *        "OPTION 1",
    *        "OPTION 2",
    *        "OPTION N"
    *    ];

    2. Create a DIV like below (Note the id of the input and the array element name must be same):
    <div class='choose_panel'> 
    <p class='select'><input type='text' id='FILTERING_TYPE' value='Select FILTERING_TYPE'></p>
    <ul></ul>
    <a class="short_grey_button" href="#">Refine</a>
    </div>

    */
    data11['search_services'] = ["Individual", "Company"];
    data11['search_gender'] = ["Male","Female"];
    data11['search_fb_circle'] = ["1st Circle","2nd Circle","3rd Circle"];
    
    var openCheck = false;
    var idx = '';
    function _init(input) {
        $("#"+input).autocomplete({
            source: data11[input],
            minLength: 0,
            open: function(event, ui) {
                openCheck = true;
            },
            select: function(event, ui) {
                openCheck = false;
                
                /**
                * For gender DD it is repeating the fields.
                * So, checking duplicate
                */
                var duplicate=false;
                $("input[name^='filter_"+input+"']").each(function(){
                   if($(this).val()==ui.item.value)
                   {
                        duplicate=true;
                        return false;
                   }  
                });
                if(!duplicate)
                {
                  var h_input='<input type="hidden" name="filter_'+input+'[]" id="filter_'+input+'" value="'+ui.item.value+'"/>'; 
                  $("#"+input).parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='<?=site_url(get_theme_path().'images/cross.jpg');?>'>"+ui.item.value+h_input+'</li>');
                }
                
                /**
                * To match the dropdown values with our ajax autocomplete, 
                * we will not remove the aleady selected values from Droprown. 
                * Because, when we use ajax autocomplete then all values are
                * shown in the dropdown.
                */
                /*idx = '';
                idx = $.inArray(ui.item.value, data11[input]);
                data11[input].splice(idx, 1);*/
				
				
                closeIt(data11[input]);
                //$( "input#"+input ).val("");  // commented and below 2 line added to keep default value selected
				var def_val = $( "input#"+input ).attr("rel");
				$( "input#"+input ).val(def_val);
                return false;
            }
        });

    }

    function afterInit(input) {
        var button = $('<img id="drop4" src="<?=site_url(get_theme_path().'images/arrow1.jpg');?>" width="16" height="16" alt="arrow">').attr("tabIndex", -1).attr("title", "Show all items").insertAfter($("#"+input)).button({
            icons: {
                primary: "ui-icon-triangle-1-s"
            },
            text: false
        }).click(function(event) {
            /*if (openCheck) {
            $("#"+input).autocomplete("close");
            openCheck = false;
            } else {
            $("#"+input).autocomplete("search", "");
            }*/
            if ($("#"+input).autocomplete("widget").is(":visible")) {
                $("#"+input).autocomplete("close"); 

                return;
            }


            // work around a bug (likely same cause as #5265)
            $(this).blur();

            // pass empty string as value to search for, displaying all results
			
            $("#"+input).autocomplete("search", "");
            $("#"+input).focus();
            event.stopImmediatePropagation();
        });
    }
    function closeIt(elm)
    {
        $('.closeIt').click(function(){
            
            /*if($.inArray($(this).attr("alt"), elm) == -1)
            {
                elm.push($(this).attr("alt"));
            }*/
            $(this).parent("li").hide('slow', function(){ 
                //$(this).parent("li").remove(); //this not worked
                $(this).remove();
            });
        });

    }

    jQuery(function($){
        $(document).ready(function(){

            $.each(data11, function(key, value){
                _init(key);
                afterInit(key);
            });       

        }); 
    });

    /*$(function() {

    });

    $(function() {

    var id6 ='';
    $( "#service" ).autocomplete({
    source: service,
    minLength: 0,
    select: function(event, ui) {
    $("#service").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id6 = $.inArray(ui.item.value, service);
    service.splice(id6, 1);
    closeIt("service");
    $( "input#service" ).val("");
    return false;
    }
    }).click(function(){
    $(this).autocomplete('search', $(this).val());
    });
    });
    $(function() {

    var id5 ='';
    $( "#Specialization" ).autocomplete({
    source: Specialization,
    select: function(event, ui) {
    $("#Specialization").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id5 = $.inArray(ui.item.value, Specialization);
    Specialization.splice(id5, 1);
    closeIt("Specialization");
    $( "input#Specialization" ).val("");
    return false;
    }
    });
    $('#drop5').click(function() { $( "#Specialization" ).autocomplete({
    source: Specialization,
    minLength: 0,
    select: function(event, ui) {
    $("#Specialization").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id5 = $.inArray(ui.item.value, Specialization);
    Specialization.splice(id5, 1);
    closeIt("Specialization");
    $( "input#Specialization" ).val("");
    return false;
    }
    }); });
    });
    $(function() {

    var id4 ='';
    $( "#Experience" ).autocomplete({
    source: Experience,
    select: function(event, ui) {
    $("#Experience").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id4 = $.inArray(ui.item.value, Experience);
    Experience.splice(id4, 1);
    closeIt("Experience");
    $( "input#Experience" ).val("");
    return false;
    }
    });
    $('#drop4').click(function() { $( "#Experience" ).autocomplete({
    source: Experience,
    minLength: 0,
    select: function(event, ui) {
    $("#Experience").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id4 = $.inArray(ui.item.value, Experience);
    Experience.splice(id4, 1);
    closeIt("Experience");
    $( "input#Experience" ).val("");
    return false;
    }
    }); });
    });
    $(function() {

    var id3 ='';
    $( "#Gender" ).autocomplete({
    source: Gender,
    select: function(event, ui) {
    $("#Gender").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id3 = $.inArray(ui.item.value, Gender);
    Gender.splice(id3, 1);
    closeIt("Gender");
    $( "input#Gender" ).val("");
    return false;
    }
    });
    $('#drop3').click(function() { $( "#Gender" ).autocomplete({
    source: Gender,
    minLength: 0,
    select: function(event, ui) {
    $("#Gender").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id3 = $.inArray(ui.item.value, Gender);
    Gender.splice(id3, 1);
    closeIt("Gender");
    $( "input#Gender" ).val("");
    return false;
    }
    }); });
    });
    $(function() {

    var id2 ='';
    $( "#locality" ).autocomplete({
    source: locality,
    select: function(event, ui) {
    $("#locality").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id2 = $.inArray(ui.item.value, locality);
    locality.splice(id2, 1);
    closeIt("locality");
    $( "input#locality" ).val("");
    return false;
    }
    });
    $('#drop2').click(function() { $( "#locality" ).autocomplete({
    source: locality,
    minLength: 0,
    select: function(event, ui) {
    $("#locality").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id2 = $.inArray(ui.item.value, locality);
    locality.splice(id2, 1);
    closeIt("locality");
    $( "input#locality" ).val("");
    return false;
    }
    }); });
    });
    $(function() {

    var id1 ='';
    $( "#locality_circle" ).autocomplete({
    source: locality_circle,
    select: function(event, ui) {
    $("#locality_circle").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id1 = $.inArray(ui.item.value, locality_circle);
    locality_circle.splice(id1, 1);
    closeIt("locality_circle");
    $( "input#locality_circle" ).val("");
    return false;
    }
    });
    $('#drop1').click(function() { $( "#locality_circle" ).autocomplete({
    source: locality_circle,
    minLength: 0,
    select: function(event, ui) {
    $("#locality_circle").parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='images/cross.jpg'>"+ui.item.value+'</li>');
    id1 = $.inArray(ui.item.value, locality_circle);
    locality_circle.splice(id1, 1);
    closeIt("locality_circle");
    $( "input#locality_circle" ).val("");
    return false;
    }
    }); });
    });*/

</script>

<script type="text/javascript">
    jQuery(function($){
        $(document).ready(function(){

            /***************************Top menu bar search message************************/
            var search_type_value='<?= trim(@$posted["search_type_value"])?>';
            var location_type_value='<?= trim(@$posted["location_type_value"])?>';
            var distance='<?= trim(@$posted["distance"])?>';

            /// setting the meggase value of top bar search///
            var str="Search result";

            if(search_type_value!="" && search_type_value!="Webdesign")
            {
                str+=' for '+search_type_value;
                ///keeping the values in the serach fields///
                $("#search_type_value").val(search_type_value);
            }

            if(location_type_value!="" && location_type_value!="City/ Zip")
            {
                str+=' in '+location_type_value;
                ///keeping the values in the serach fields///
                $("#location_type_value").val(location_type_value);
            }

            if(distance!="")
            {
                //str+=' withing '+distance+' km'; 
				str+=' within '+distance+' miles'; // changed as per client requirements 11 dec
                ///keeping the values in the serach fields///
                $("#distance").attr('value',distance); 
            }

            $('#search_result_heading').html(str);        
            /***************************Top menu bar search message end************************/

            $("#save_search").click(function(){
                var params={};				
                /*params.value=[];
                params.id=[];*/
                $("input[name^='filter_']").each(function(i){
                    /*params.value[i]=$(this).attr('value');
                    params.id[i]=$(this).attr('id');*/                

                    if( !$.isArray(params[ $(this).attr('id') ]) )
                        params[ $(this).attr('id') ]=new Array();
                    params[ $(this).attr('id') ].push($(this).attr('value'));

                });
                ////top search must be added into params, @see main.tpl.php////
                params["global_country_id"]=$("#global_country_id").find("option:selected").attr("value");
                params["search_cat_id"]=$("#search_cat_id").attr("value");
                params["search_uid"]=$("#search_uid").attr("value");
                params["search_zip_id"]=$("#search_zip_id").attr("value");
                params["search_city_id"]=$("#search_city_id").attr("value");

                params["search_type"]=$("#search_type").find("option:selected").attr("value");
                params["search_type_value"]=$("#search_type_value").attr("value");
                params["location_type"]=$("#location_type").find("option:selected").attr("value");
                params["location_type_value"]=$("#location_type_value").attr("value");
                params["distance"]=$("#distance").find("option:selected").attr("value"); 
                params["keep_filter"]=$("#keep_filter").find(":checked").attr("value");


                /*$.post('<?=site_url('save_search/addSearchData')?>',params);  */  
				
				/* 27 Nov 2013 save srch dialog */
				var html='<p><label for="name">Title</label><input style="margin-left:5px;" name="s_text" id="s_text" class="text ui-widget-content ui-corner-all" type="text" size="36"/></p>';
			   $( "#dialog-srch" ).find("#dialog_msg").html(html);                
			   $( "#dialog-srch" ).dialog( "open" );

            });
			
			/* 27 Nov 2013 save srch dialog */
			
			$( "#dialog-srch" ).dialog({
				autoOpen: false,
				resizable: false,
				height:300,
				width:400,
				modal: true,
				buttons: {
					"Send": function() {
						var title=$('#s_text').val();
						
						var params={};				
						/*params.value=[];
						params.id=[];*/
						$("input[name^='filter_']").each(function(i){
							/*params.value[i]=$(this).attr('value');
							params.id[i]=$(this).attr('id');*/                
		
							if( !$.isArray(params[ $(this).attr('id') ]) )
								params[ $(this).attr('id') ]=new Array();
							params[ $(this).attr('id') ].push($(this).attr('value'));
		
						});
						////top search must be added into params, @see main.tpl.php////
						params["global_country_id"]=$("#global_country_id").find("option:selected").attr("value");
						params["search_cat_id"]=$("#search_cat_id").attr("value");
						params["search_uid"]=$("#search_uid").attr("value");
						params["search_zip_id"]=$("#search_zip_id").attr("value");
						params["search_city_id"]=$("#search_city_id").attr("value");
		
						params["search_type"]=$("#search_type").find("option:selected").attr("value");
						params["search_type_value"]=$("#search_type_value").attr("value");
						params["location_type"]=$("#location_type").find("option:selected").attr("value");
						params["location_type_value"]=$("#location_type_value").attr("value");
						params["distance"]=$("#distance").find("option:selected").attr("value"); 
						params["keep_filter"]=$("#keep_filter").find(":checked").attr("value");
						//console.log(save_search);
						params["s_search_tag"]=title;
						$.post("<?=site_url("save_search/addSearchData")?>",params, 
										function(data)
										{
											if(data=='success')
											{
												$('.ui-dialog-buttonset').find('button:first').hide();
												$('.ui-dialog-buttonset').find('button:last').find('span').html('Ok');
												$( "#dialog-srch").find("#txt_err").text('');
												$( "#dialog-srch" ).find("#dialog_msg").html('Saved successfully.');
											}
											else if(data=='login_error')
											{
												$( "#dialog-srch").find("#txt_err").text('Please login to save your search.');
											}
											else
											{
												$( "#dialog-srch").find("#txt_err").text('Please provide a title.');
											}
										}
							 );
						//$( this ).dialog( "close" );
					},
					"Cancel": function() {
						$('.ui-dialog-buttonset').find('button:first').show();
						$('.ui-dialog-buttonset').find('button:last').find('span').html('Cancel');                
						$( this ).dialog( "close" );
					}
					
				},
				 hide: {
						//effect: "explode",
						duration: 1000
				 }
			  });
			/* end 27 Nov 2013 save srch dialog */

            /**
            * Clearing all search values
            */
            $("#clear_search").click(function(){
                ////top search must be cleared and submited, @see main.tpl.php////
                $("#search_cat_id").attr("value","");
                $("#search_uid").attr("value","");
                $("#search_zip_id").attr("value","");
                $("#search_city_id").attr("value","");

                $("#search_type").find("option:first").attr("selected",true);
                $("#search_type_value").attr("value","");
                $("#location_type").find("option:first").attr("selected",true);
                $("#location_type_value").attr("value","");
                $("#distance").find("option:first").attr("selected",true); 
                $("#keep_filter").attr("checked",false);           

                $("#top_search_form").submit();
            });

            ///refine or Re-search, will submit the top search form @see main.tpl.php///
            $(".refine").each(function(){
                $(this).click(function(){
                    ///appending the extended fields into the top search box, @see main.tpl.php///
                    $("input[name^='filter_']").each(function(i){
                        //$("#top_search_form #"+$(this).attr("id")).remove();
                        $("#top_search_form").append($(this));
                    });   
                    ///end appending the extended fields into the top search box///           

                    $("#top_search_form").submit();
                    //console.log($("#top_search_form"));
                });
            });
            
            
            /*********************Extended Filds*******************************/
            <?php
            /**
            * on 13Dec13, as per client request,
            * all values from "option" table will be fetched 
            * as per category. ex- Specialization AutoComplete will 
            * show all values under "dentist" category. it will not 
            * show all specializations. 
            * >>a new column "cat_id" added in db "option" table. 
            */
            $qry_cat_id="?search_cat_id=".@$posted["search_cat_id"];
            ?>
            //Specialization AutoComplete
            <?php
                if(!empty($service_extended_def["s_specialization_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_specialization").attr("value","<?=trim($service_extended_def["s_specialization_ids"]->s_search_page_label);?>");
                $( "#search_specialization" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_specilizationName".$qry_cat_id);?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_specialization";

                        /**
                        * DD is repeating the fields in case of 
                        * showing the posted values.
                        * So, checking duplicate
                        */
                        /*var duplicate=false;
                        $("input[name^='filter_"+input+"']").each(function(){
                           if($(this).val()==ui.item.value)
                           {
                                duplicate=true;
                                return false;
                           }  
                        });
                        if(!duplicate)
                        {
                            var h_input='<input type="hidden" name="filter_'+input+'[]" id="filter_'+input+'" value="'+ui.item.value+'"/>'; 
                            $("#"+input).parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='<?=site_url(get_theme_path().'images/cross.jpg');?>'>"+ui.item.value+h_input+'</li>');
                            ////binding the close button///
                            $(".closeIt[alt='"+ui.item.value+"']").click(function(){
                                $(this).parent("li").hide('slow', function(){ $(this).parent("li").remove(); });
                            });    
                            ////end binding the close button///                          
                        }*/ 
                        
                        select_extended(input,ui);                    

                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_specialization");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_specialization").remove();
                <?php
                }
            ?>
            ///end Specialization AutoComplete///   
            
            //Highest Qualification Level AutoComplete
            <?php
                if(!empty($service_extended_def["s_qualification_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_qualification").attr("value","<?=trim($service_extended_def["s_qualification_ids"]->s_search_page_label);?>");
                $( "#search_qualification" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_degreeName".$qry_cat_id);?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_qualification";

                        /*var h_input='<input type="hidden" name="filter_'+input+'[]" id="filter_'+input+'" value="'+ui.item.value+'"/>'; 
                        $("#"+input).parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='<?=site_url(get_theme_path().'images/cross.jpg');?>'>"+ui.item.value+h_input+'</li>');

                        ////binding the close button///
                        $(".closeIt[alt='"+ui.item.value+"']").click(function(){
                            $(this).parent("li").hide('slow', function(){ $(this).parent("li").remove(); });
                        });    
                        ////end binding the close button///
                        */
                        select_extended(input,ui);        

                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_qualification");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_qualification").remove();
                <?php
                }
            ?>
            ///end Highest Qualification Level AutoComplete///    
            
            //Experience AutoComplete
            <?php
                if(!empty($service_extended_def["d_experience"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_experience").attr("value","<?=trim($service_extended_def["d_experience"]->s_search_page_label);?>");
                $( "#search_experience" ).autocomplete({
                    source: $.parseJSON('<?=save_search_experience_range();?>'),
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_experience";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_experience");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_experience").remove();
                <?php
                }
            ?>
            ///end Experience AutoComplete///           
            
            //classes AutoComplete
            <?php
                if(!empty($service_extended_def["s_classes_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_classes").attr("value","<?=trim($service_extended_def["s_classes_ids"]->s_search_page_label);?>");
                $( "#search_classes" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_className".$qry_cat_id);?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_classes";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_classes");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_classes").remove();
                <?php
                }
            ?>
            ///end classes AutoComplete///                 

            //medium AutoComplete
            <?php
                if(!empty($service_extended_def["s_medium_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_medium").attr("value","<?=trim($service_extended_def["s_medium_ids"]->s_search_page_label);?>");
                $( "#search_medium" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_medium".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_medium";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_medium");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_medium").remove();
                <?php
                }
            ?>
            ///end medium AutoComplete/// 
            
            //d_tution_fee AutoComplete
            <?php
                if(!empty($service_extended_def["d_tution_fee"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_tution_fee").attr("value","<?=trim($service_extended_def["d_tution_fee"]->s_search_page_label);?>");
                $( "#search_tution_fee" ).autocomplete({
                    source: $.parseJSON('<?=save_search_tution_fee_range();?>'),
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_tution_fee";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_tution_fee");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_tution_fee").remove();
                <?php
                }
            ?>
            ///end d_tution_fee AutoComplete///              

            //tution_mode AutoComplete
            <?php
                if(!empty($service_extended_def["s_tution_mode_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_tution_mode").attr("value","<?=trim($service_extended_def["s_tution_mode_ids"]->s_search_page_label);?>");
                $( "#search_tution_mode" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_tution_mode".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_tution_mode";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val(""); // commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_tution_mode");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_tution_mode").remove();
                <?php
                }
            ?>
            ///end tution_mode AutoComplete/// 
            //Subjects AutoComplete, s_other_subject_ids
            <?php
                if(!empty($service_extended_def["s_other_subject_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_subjects").attr("value","<?=trim($service_extended_def["s_other_subject_ids"]->s_search_page_label);?>");
                $( "#search_subjects" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_subjects".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_subjects";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_subjects");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_subjects").remove();
                <?php
                }
            ?>
            ///end Subjects AutoComplete///           
            //rate AutoComplete
            <?php
                if(!empty($service_extended_def["d_rate"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_rate").attr("value","<?=trim($service_extended_def["d_rate"]->s_search_page_label);?>");
                $( "#search_rate" ).autocomplete({
                    source: $.parseJSON('<?=save_search_tution_fee_range();?>'),
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_rate";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_rate");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_rate").remove();
                <?php
                }
            ?>
            ///end rate AutoComplete///              
            //employment_type AutoComplete, Not in use right now
            <?php
                if(!empty($service_extended_def["s_employment_type_id"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_employment_type").attr("value","<?=trim($service_extended_def["s_employment_type_id"]->s_search_page_label);?>");
                $( "#search_employment_type" ).autocomplete({
                    source: $.parseJSON('<?=save_search_tution_fee_range();?>'),
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_employment_type";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_employment_type");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_employment_type").remove();
                <?php
                }
            ?>
            ///end employment_type AutoComplete///   
            //availability AutoComplete, 
            <?php
                if(!empty($service_extended_def["s_availability_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_availability").attr("value","<?=trim($service_extended_def["s_availability_ids"]->s_search_page_label);?>");
                $( "#search_availability" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_availability".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_availability";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_availability");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_availability").remove();
                <?php
                }
            ?>
            ///end availability AutoComplete///    
            //tools AutoComplete, 
            <?php
                if(!empty($service_extended_def["s_tools_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_tools").attr("value","<?=trim($service_extended_def["s_tools_ids"]->s_search_page_label);?>");
                $( "#search_tools" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_tools".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_tools";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_tools");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_tools").remove();
                <?php
                }
            ?>
            ///end tools AutoComplete///             
            //designation AutoComplete, 
            <?php
                if(!empty($service_extended_def["s_designation_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $("#search_designation").attr("value","<?=trim($service_extended_def["s_designation_ids"]->s_search_page_label);?>");
                $( "#search_designation" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_designation".$qry_cat_id)?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_designation";
                        select_extended(input,ui); 
                        //$( "input#"+input ).val("");// commented and below 2 line added to keep default value selected
						var def_val = $( "input#"+input ).attr("rel");
						$( "input#"+input ).val(def_val);
                        return false;
                    }
                });
                afterInit("search_designation");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_designation").remove();
                <?php
                }
            ?>
            ///end designation AutoComplete///                                     
         
            /**
            * locality AutoComplete, 
            * TODO:: how this works? 
            */
            <?php
                /*
                if(!empty($service_extended_def["s_other_subject_ids"])) //// remove "|| true" portion later///
                {
                ?>
                $( "#search_locality" ).autocomplete({
                    source: "<?=site_url("autocomplete/ajax_locationName")?>",
                    minLength: 0,
                    open: function(event, ui) {
                        openCheck = true;
                    },
                    select: function(event, ui) {
                        openCheck = false;
                        var input="search_locality";
                        select_extended(input,ui); 
                        $( "input#"+input ).val("");
                        return false;
                    }
                });
                afterInit("search_locality");           
                <?
                }///end if
                else
                {
                ?>
                $("#div_search_locality").remove();
                <?php
                }
                */
            ?>
            $("#div_search_locality").remove();
            ///end locality AutoComplete///    

            
            /*********************end Extended Filds*******************************/
            


});///document ready
});


/**
* Autocomplate Select, 
* for extended fields,
* this function is also called from footer scripts
*/
function select_extended(input,ui)
{
    /*var input=inputP;
    var ui=uiP;*/
    
    //console.log("@select_extended",input,ui);                
    
    jQuery(function($){
    $(document).ready(function(){
    
        //console.log("@select_extended",input,ui);                
        /**
        * DD is repeating the fields in case of 
        * showing the posted values.
        * So, checking duplicate
        */
        var duplicate=false;
        $("input[name^='filter_"+input+"']").each(function(){
           if($(this).val()==ui.item.value)
           {
                duplicate=true;
                return false;
           }  
        });
        if(!duplicate)
        {
			
            var h_input='<input type="hidden" name="filter_'+input+'[]" id="filter_'+input+'" value="'+ui.item.value+'"/>'; 
            $("#"+input).parent().parent().find('ul').append("<li><img width='13' class='closeIt' style='cursor: pointer' height='14' alt='"+ui.item.value+"' src='<?=site_url(get_theme_path().'images/cross.jpg');?>'>"+ui.item.value+h_input+'</li>');
            ////binding the close button///
            $(".closeIt[alt='"+ui.item.value+"']").click(function(){
                $(this).parent("li").hide('slow', function(){ 
                    //$(this).parent("li").remove(); //this not worked
                    $(this).remove();
                    //console.log($(this));
                });
            });    
            ////end binding the close button///                          
        }                  

    });});
}
        

</script>

<?php
///common save search Box 27 Nov 2013///
?>
<div id="dialog-srch" style="display: block;" title="Attention">
<span id="txt_err" style="margin-bottom:5px;"></span>
    <p>	  
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end save search Box///
?>

<!--PANEL WITH RIGHT SIDEBAR START -->
<div class="col_right_sidebar">
    <!--MAIN PANEL START -->
    <div class="left_panel">
        <p class="short"><span class="alignleft" id="search_result_heading"></span> <span class="alignright"><?=$pager_heading;?></span></p> 

        <?php 
            if(!is_userLoggedIn())
            {
            ?>
            <p class="blue"><img src="<?= get_theme_path('guru_frontend/')?>images/icon10.jpg" width="24" height="24" alt="icon" class="alignleft" /> Please <u><a href="<?= site_url('account/signin')?>" class="white">Sign In</a></u> to see service provider in your connection</p> 
            <?php 
            }
        ?>

        <?php 
            if(!empty($search_result))
            {
                foreach($search_result as $k=>$v)
                {
                    $featured_listing="";

                    if($v->i_featured)
                        $featured_listing='featured_listing';
                ?>

                <div class="listing <?=$featured_listing;?>">
                    <div class="left"> <a href="general-service.html"><img src="<?=site_url($v->s_profile_image?$v->s_profile_image:'resources/no_image.jpg')?>" width="120" height="120" alt="pic" /></a>
                        <a href="<?=site_url($v->s_service_short_url)?>" class="grey_button">View full Profile</a>
                    </div>
                    <div class="right">
                        <div class="border_bottom">
                            <h2 class="alignleft"><strong class="alignleft"><a href="<?=site_url($v->s_service_short_url);?>" class="black"><?=$v->s_service_name;?></a></strong> <span class="alignleft short_grey_button"><?=levelToStr(intval($v->i_user_fb_level));?></span></h2>
                        </div>
                        <div class="border_bottom botmar20">
                            <p class="botmar20">Service Provided by <a href="<?=site_url($v->s_user_short_url);?>"><?=$v->s_provided_by;?></a> <? /*<img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /> <img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /> <img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /> */
                            if($v->i_is_company_service)
                            {
                                $service_providers=get_company_service_provider($v->comp_id,$v->service_id);
                                if(!empty($service_providers))
                                {
                                    foreach($service_providers as $k)
                                    {
                echo '<img src="'.get_theme_path('guru_frontend/').'images/pic2.png" width="14" height="13" alt="pic" />';
                                    }
                                }
                            }
                            ?></p>
                            <ul class="alignleft">
                                <li><?/*<a href="javascript:void(0);"><img src="<?=get_theme_path('guru_frontend')?>images/icon11.png" width="18" height="12" alt="icon" /></a>*/?>
                                
                                <!--Share with Friend -->
                                <?= theme_block_user_profile_share_with_friend($v->service_id,"service","icon");?>
                                 <!--Share with Friend -->                                
                                
                                </li>
                                <li><a href="javascript:void(0);" class="add_to_favourite" service_id="<?=$v->service_id;?>"><img src="<?= get_theme_path('guru_frontend')?>images/icon12.png" width="15" height="14" alt="icon" /></a></li>
                                <li><?/*<a href="javascript:void(0);"><img src="<?= get_theme_path('guru_frontend')?>images/icon13.png" width="20" height="14" alt="icon" /></a>*/?>
                                    <!--Share via Twitter -->
                                    <?=theme_block_user_profile_share_via_twitter($v->service_id,"service","icon");?>
                                    <!--Share via Twitter -->                                
                                
                                </li>
                                <li><?/*<a href="javascript:void(0);"><img src="<?= get_theme_path('guru_frontend')?>images/icon14.jpg" width="14" height="14" alt="icon" /></a>*/?>
                                     <!--Share via Facebook -->
                                     <?=theme_block_user_profile_share_via_facebook($v->service_id,"service","icon");?>
                                     <!--Share via Facebook -->                                
                                </li>
                            </ul>
                            <ul class="alignright">
                                <li><a href="javascript:void(0);" class="tooltip" title="Recommendation : <?=intval($v->i_recommendation_count);?>"><?=intval($v->i_recommendation_count);?> <img src="<?= get_theme_path('guru_frontend/')?>images/icon15.png" width="10" height="10" alt="icon" /></a></li>
                                <li><a href="javascript:void(0);" class="tooltip" title="Endorsement : <?=intval($v->i_endorse_count);?>"><?=intval($v->i_endorse_count);?> <img src="<?= get_theme_path('guru_frontend/')?>images/icon16.png" width="12" height="10" alt="icon" /></a></li>
                                <li><a href="javascript:void(0);" class="tooltip" title="Rank : <?=intval($v->i_user_rank);?>"><?=intval($v->i_user_rank);?> <img src="<?= get_theme_path('guru_frontend/')?>images/icon17.png" width="11" height="13" alt="icon" /></a></li>
                            </ul>
                        </div>
                        <div class="border_bottom">
                            <p class="botmar20"><?= $v->s_service_desc;?> <a href="<?=site_url($v->s_service_short_url);?>" class="orange">Read more</a></p>
                            <span class="short grey">Data provided by <a href="#">www.webdesign.guru.in</a></span>
                        </div>
                        <ul class="alignleft">
                            <li><span>Skills:</span></li>
                            <li><?=$v->s_skill;?></li>

                        </ul>
                    </div>
                </div>

                <?php 

                }///end for
                echo '<div class="listing">'.$search_result_pager.'</div>';
            }
            else
            {
                echo '<div class="listing">No result found! Please try again.</div>';
            }
        ?>

        <? /*       
            <div class="listing featured_listing">
            <div class="left"> <a href="general-service.html"><img src="<?= get_theme_path('guru_frontend/')?>images/pic4.jpg" width="120" height="120" alt="pic" /></a>
            <a href="general-service.html" class="grey_button">View full Profile</a>
            </div>
            <div class="right">
            <div class="border_bottom">
            <h2 class="alignleft"><strong class="alignleft"><a href="general-service.html" class="black">Experienced website designer</a></strong> <span class="alignleft short_grey_button">3rd</span></h2>
            </div>
            <div class="border_bottom botmar20">
            <p class="botmar20">Service Provided by <a href="user-profile.html">Bigfish</a> <img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /> <img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /> <img src="<?= get_theme_path('guru_frontend/')?>images/pic2.png" width="14" height="13" alt="pic" /></p>
            <ul class="alignleft">
            <li><a href="#"><img src="<?= get_theme_path('guru_frontend/')?>images/icon11.png" width="18" height="12" alt="icon" /></a></li>
            <li><a href="#"><img src="<?= get_theme_path('guru_frontend/')?>images/icon12.png" width="15" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="<?= get_theme_path('guru_frontend/')?>images/icon13.png" width="20" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="<?= get_theme_path('guru_frontend/')?>images/icon14.jpg" width="14" height="14" alt="icon" /></a></li>
            </ul>
            <ul class="alignright">
            <li><a href="#" class="tooltip" title="Recommendation : 50">50 <img src="<?= get_theme_path('guru_frontend/')?>images/icon15.png" width="10" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Endorsement : 20">20 <img src="<?= get_theme_path('guru_frontend/')?>images/icon16.png" width="12" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Rank : 16">16 <img src="<?= get_theme_path('guru_frontend/')?>images/icon17.png" width="11" height="13" alt="icon" /></a></li>
            </ul>
            </div>
            <div class="border_bottom">
            <p class="botmar20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius porta ante, vitae feugiat dolor gravida vel. Integer velit ipsum, pharetra eget cursus a, ultrices eu metus. Morbi nibh odio, vehicula pharetra convallis nec, tristique a sapien. Aliquam ac tellus quam ... <a href="general-service.html" class="orange">Read more</a></p>
            <span class="short grey">Data provided by <a href="#">www.webdesign.guru.in</a></span>
            </div>
            <ul class="alignleft">
            <li><span>Skills:</span></li>
            <li>HTML</li>
            <li>CSS</li>
            <li>PHP</li>
            <li>Wordpress</li>
            <li>Drupal</li>
            </ul>
            </div>
            </div>
            <div class="listing">
            <div class="left"> <a href="general-service.html"><img src="images/pic4.jpg" width="120" height="120" alt="pic" /></a>
            <a href="general-service.html" class="grey_button">View full Profile</a>
            </div>
            <div class="right">
            <div class="border_bottom">
            <h2 class="alignleft"><strong class="alignleft"><a href="general-service.html" class="black">Experienced website designer</a></strong> <span class="alignleft short_grey_button">3rd</span></h2>
            </div>
            <div class="border_bottom botmar20">
            <p class="botmar20">Service Provided by <a href="user-profile.html">Bigfish</a> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /></p>
            <ul class="alignleft">
            <li><a href="#"><img src="images/icon11.png" width="18" height="12" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon12.png" width="15" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon13.png" width="20" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon14.jpg" width="14" height="14" alt="icon" /></a></li>
            </ul>
            <ul class="alignright">
            <li><a href="#" class="tooltip" title="Recommendation : 50">50 <img src="images/icon15.png" width="10" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Endorsement : 20">20 <img src="images/icon16.png" width="12" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Rank : 16">16 <img src="images/icon17.png" width="11" height="13" alt="icon" /></a></li>
            </ul>
            </div>
            <div class="border_bottom">
            <p class="botmar20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius porta ante, vitae feugiat dolor gravida vel. Integer velit ipsum, pharetra eget cursus a, ultrices eu metus. Morbi nibh odio, vehicula pharetra convallis nec, tristique a sapien. Aliquam ac tellus quam ... <a href="general-service.html" class="orange">Read more</a></p>
            <span class="short grey">Data provided by <a href="#" class="grey">www.webdesign.guru.in</a></span>
            </div>
            <ul class="alignleft">
            <li><span>Skills:</span></li>
            <li>HTML</li>
            <li>CSS</li>
            <li>PHP</li>
            <li>Wordpress</li>
            <li>Drupal</li>
            </ul>
            </div>
            </div>
            <div class="listing">
            <div class="left"> <a href="general-service.html"><img src="images/pic5.jpg" width="120" height="120" alt="pic" /></a>
            <a href="general-service.html" class="grey_button">View full Profile</a>
            </div>
            <div class="right">
            <div class="border_bottom">
            <h2 class="alignleft"><strong class="alignleft"><a href="general-service.html" class="black">Experienced website designer</a></strong> <span class="alignleft short_grey_button">3rd</span></h2>
            </div>
            <div class="border_bottom botmar20">
            <p class="botmar20">Service Provided by <a href="user-profile.html">Bigfish</a> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /></p>
            <ul class="alignleft">
            <li><a href="#"><img src="images/icon11.png" width="18" height="12" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon12.png" width="15" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon13.png" width="20" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon14.jpg" width="14" height="14" alt="icon" /></a></li>
            </ul>
            <ul class="alignright">
            <li><a href="#" class="tooltip" title="Recommendation : 50">50 <img src="images/icon15.png" width="10" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Endorsement : 20">20 <img src="images/icon16.png" width="12" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Rank : 16">16 <img src="images/icon17.png" width="11" height="13" alt="icon" /></a></li>
            </ul>
            </div>
            <div class="border_bottom">
            <p class="botmar20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius porta ante, vitae feugiat dolor gravida vel. Integer velit ipsum, pharetra eget cursus a, ultrices eu metus. Morbi nibh odio, vehicula pharetra convallis nec, tristique a sapien. Aliquam ac tellus quam ... <a href="general-service.html" class="orange">Read more</a></p>
            <span class="short grey">Data provided by www.webdesign.guru.in</span>
            </div>
            <ul class="alignleft">
            <li><span>Skills:</span></li>
            <li>HTML</li>
            <li>CSS</li>
            <li>PHP</li>
            <li>Wordpress</li>
            <li>Drupal</li>
            </ul>
            </div>
            </div>
            <div class="listing">
            <div class="left"> <a href="general-service.html"><img src="images/pic6.jpg" width="120" height="120" alt="pic" /></a>
            <a href="general-service.html" class="grey_button">View full Profile</a>
            </div>
            <div class="right">
            <div class="border_bottom">
            <h2 class="alignleft"><strong class="alignleft"><a href="general-service.html" class="black">Experienced website designer</a></strong> <span class="alignleft short_grey_button">3rd</span></h2>
            </div>
            <div class="border_bottom botmar20">
            <p class="botmar20">Service Provided by <a href="user-profile.html">Bigfish</a> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /></p>
            <ul class="alignleft">
            <li><a href="#"><img src="images/icon11.png" width="18" height="12" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon12.png" width="15" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon13.png" width="20" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon14.jpg" width="14" height="14" alt="icon" /></a></li>
            </ul>
            <ul class="alignright">
            <li><a href="#" class="tooltip" title="Recommendation : 50">50 <img src="images/icon15.png" width="10" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Endorsement : 20">20 <img src="images/icon16.png" width="12" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Rank : 16">16 <img src="images/icon17.png" width="11" height="13" alt="icon" /></a></li>
            </ul>
            </div>
            <div class="border_bottom">
            <p class="botmar20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius porta ante, vitae feugiat dolor gravida vel. Integer velit ipsum, pharetra eget cursus a, ultrices eu metus. Morbi nibh odio, vehicula pharetra convallis nec, tristique a sapien. Aliquam ac tellus quam ... <a href="general-service.html" class="orange">Read more</a></p>
            <span class="short grey">Data provided by www.webdesign.guru.in</span>
            </div>
            <ul class="alignleft">
            <li><span>Skills:</span></li>
            <li>HTML</li>
            <li>CSS</li>
            <li>PHP</li>
            <li>Wordpress</li>
            <li>Drupal</li>
            </ul>
            </div>
            </div>
            <div class="listing">
            <div class="left"> <a href="general-service.html"><img src="images/pic1.jpg" width="120" height="120" alt="pic" /></a>
            <a href="general-service.html" class="grey_button">View full Profile</a>
            </div>
            <div class="right">
            <div class="border_bottom">
            <h2 class="alignleft"><strong class="alignleft"><a href="general-service.html" class="black">Experienced website designer</a></strong> <span class="alignleft short_grey_button">3rd</span></h2>
            </div>
            <div class="border_bottom botmar20">
            <p class="botmar20">Service Provided by <a href="user-profile.html">Bigfish</a> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /> <img src="images/pic2.png" width="14" height="13" alt="pic" /></p>
            <ul class="alignleft">
            <li><a href="#"><img src="images/icon11.png" width="18" height="12" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon12.png" width="15" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon13.png" width="20" height="14" alt="icon" /></a></li>
            <li><a href="#"><img src="images/icon14.jpg" width="14" height="14" alt="icon" /></a></li>
            </ul>
            <ul class="alignright">
            <li><a href="#" class="tooltip" title="Recommendation : 50">50 <img src="images/icon15.png" width="10" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Endorsement : 20">20 <img src="images/icon16.png" width="12" height="10" alt="icon" /></a></li>
            <li><a href="#" class="tooltip" title="Rank : 16">16 <img src="images/icon17.png" width="11" height="13" alt="icon" /></a></li>
            </ul>
            </div>
            <div class="border_bottom">
            <p class="botmar20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec varius porta ante, vitae feugiat dolor gravida vel. Integer velit ipsum, pharetra eget cursus a, ultrices eu metus. Morbi nibh odio, vehicula pharetra convallis nec, tristique a sapien. Aliquam ac tellus quam ... <a href="#" class="orange">Read more</a></p>
            <span class="short grey">Data provided by www.webdesign.guru.in</span>
            </div>
            <ul class="alignleft">
            <li><span>Skills:</span></li>
            <li>HTML</li>
            <li>CSS</li>
            <li>PHP</li>
            <li>Wordpress</li>
            <li>Drupal</li>
            </ul>
            </div>
            </div>

        */ ?>
    </div>      
    <!--MAIN PANEL END -->  
    <!--RIGHT SIDEBAR START -->        
    <div id="extended_fields" class="right_sidebar">
        <a class="alignright short_grey_button botmar" href="javascript:void(0);" id="save_search">Save Search</a>
        <a class="alignright short_grey_button botmar" href="javascript:void(0);" id="clear_search">Clear Search</a>
        <div class="choose_panel"> 
            <p class="select relative"><input type="text" id="search_services" rel="Select service type" name="search_services" value="Select service type" /></p>
            <ul>

                <?php /* 
                    if(!empty($posted['filter_search_services']))
                    foreach($posted['filter_search_services'] as $k=>$v)
                    {

                    ?>
                    <li>
                    <img width='13' class='closeIt' style='cursor: pointer' height='14' alt='' src='<?=site_url(get_theme_path().'images/cross.jpg');?>' ><?=$v;?>
                    <input type="hidden" name="filter_search_services[]" id="filter_search_services" value="<?=$v;?>"/>
                    </li>
                    <?php
                    }
                */ ?>


            </ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*specialization*/?>
        <div id="div_search_specialization" class="choose_panel"> 
            <p class="select"><input type="text" id="search_specialization" name="search_specialization" rel="Specialization" value="Specialization" /></p>
            <ul>
                <?php /*
                    if(!empty($posted['filter_search_specialization']))
                    foreach($posted['filter_search_specialization'] as $k=>$v)
                    {

                    ?>
                    <li>
                    <img width='13' class='closeIt' style='cursor: pointer' height='14' alt='' src='<?=site_url(get_theme_path().'images/cross.jpg');?>' ><?=$v;?>
                    <input type="hidden" name="filter_search_specialization[]" id="filter_search_specialization" value="<?=$v;?>"/>
                    </li>
                    <?php
                    }
                */ ?>

            </ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*qualification*/?>
        <div id="div_search_qualification" class="choose_panel"> 
            <p class="select"><input type="text" id="search_qualification" name="search_qualification" rel="Highest Qualification" value="Qualification" /></p>
            <ul>
                <?php /*
                    if(!empty($posted['filter_search_institution']))
                    foreach($posted['filter_search_institution'] as $k=>$v)
                    {

                    ?>
                    <li>
                    <img width='13' class='closeIt' style='cursor: pointer' height='14' alt='' src='<?=site_url(get_theme_path().'images/cross.jpg');?>' ><?=$v;?>
                    <input type="hidden" name="filter_search_institution[]" id="filter_search_institution" value="<?=$v;?>"/>
                    </li>
                    <?php
                    }
                */ ?>
            </ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*d_experience*/?>
        <div id="div_search_experience" class="choose_panel"> 
            <p class="select"><input type="text" id="search_experience" name="search_experience" rel="Experience" value="Experience" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*s_classes_ids*/?>
        <div id="div_search_classes" class="choose_panel"> 
            <p class="select"><input type="text" id="search_classes" name="search_classes" rel="Classes Teach" value="Classes" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>  
        <?/*s_medium_ids*/?>
        <div id="div_search_medium" class="choose_panel"> 
            <p class="select"><input type="text" id="search_medium" name="search_medium" rel="Language Medium" value="Classes" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>          
        <?/*d_tution_fee*/?>
        <div id="div_search_tution_fee" class="choose_panel"> 
            <p class="select"><input type="text" id="search_tution_fee" name="search_tution_fee" rel="Tution fee" value="Classes" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>          
        <?/*s_tution_mode_ids*/?>
        <div id="div_search_tution_mode" class="choose_panel"> 
            <p class="select"><input type="text" id="search_tution_mode" name="search_tution_mode" rel="Tution mode" value="Classes" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>               
        <?/*s_other_subject_ids*/?>
        <div id="div_search_subjects" class="choose_panel"> 
            <p class="select"><input type="text" id="search_subjects" name="search_subjects" rel="Subject" value="Subject" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*d_rate*/?>
        <div id="div_search_rate" class="choose_panel"> 
            <p class="select"><input type="text" id="search_rate" name="search_rate" rel="Hourly Rate" value="Rate" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>        
        <?/*s_employment_type_id*/?>
        <div id="div_search_employment_type" class="choose_panel"> 
            <p class="select"><input type="text" id="search_employment_type" name="search_employment_type" rel="Employment type" value="Employment type" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>             
        <?/*s_availability_ids*/?>
        <div id="div_search_availability" class="choose_panel"> 
            <p class="select"><input type="text" id="search_availability" name="search_availability" rel="Availability"  value="Availability" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>    
        <?/*s_tools_ids*/?>
        <div id="div_search_tools" class="choose_panel"> 
            <p class="select"><input type="text" id="search_tools" name="search_tools" rel="Tools" value="Tools" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>         
        <?/*s_designation_ids*/?>
        <div id="div_search_designation" class="choose_panel"> 
            <p class="select"><input type="text" id="search_designation" name="search_designation" rel="Designation"  value="Designation" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>           
        <?/*e_gender*/?>              
        <div id="div_search_gender" class="choose_panel"> 
            <p class="select"><input type="text" id="search_gender" name="search_gender" rel="Select Gender" value="Select Gender" /></p>
            <ul>
                <?php /*
                    if(!empty($posted['filter_search_gender']))
                    foreach($posted['filter_search_gender'] as $k=>$v)
                    {

                    ?>
                    <li>
                    <img width='13' class='closeIt' style='cursor: pointer' height='14' alt='' src='<?=site_url(get_theme_path().'images/cross.jpg');?>' ><?=$v;?>
                    <input type="hidden" name="filter_search_gender[]" id="filter_search_gender" value="<?=$v;?>"/>
                    </li>
                    <?php
                    }
                */?>
            </ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*locality*/?>
        <div id="div_search_locality" class="choose_panel"> 
            <p class="select"><input type="text" id="search_locality" name="search_locality" rel="Narrow by locality" value="Narrow by locality" /></p>
            <ul>
                <?php /*
                    if(!empty($posted['filter_search_locality']))
                    foreach($posted['filter_search_locality'] as $k=>$v)
                    {

                    ?>
                    <li>
                    <img width='13' class='closeIt' style='cursor: pointer' height='14' alt='' src='<?=site_url(get_theme_path().'images/cross.jpg');?>' ><?=$v;?>
                    <input type="hidden" name="filter_search_locality[]" id="filter_search_locality" value="<?=$v;?>"/>
                    </li>
                    <?php
                    }
                */ ?>
            </ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
        <?/*fb circle*/?>
        <div id="div_search_fb_circle" class="choose_panel"> 
            <p class="select"><input type="text" id="search_fb_circle" name="search_fb_circle" rel="Narrow by fb circle" value="Narrow by fb circle" /></p>
            <ul></ul>
            <a class="short_grey_button refine" href="javascript:void(0);">Refine</a>
        </div>
    </div>
    <!--RIGHT SIDEBAR END -->                     
</div>
<!--PANEL WITH RIGHT SIDEBAR END -->     


<script type="text/javascript">
    jQuery(function($){
        $(window).ready(function(){

            /*$(".choose_panel .closeIt").click(function(){
                $(this).parent("li").hide('slow', function(){ $(this).parent("li").remove(); });

            });*/

            var def;
            var timer={};
            var wgt={};
            /*not working for multipl selection*/
            /*function autoClick(wgtC,input,idx)
            {
                var clk=$(wgtC).find("li a");
                console.log("@outer",clk,clk.is("a"),idx);                
                if(clk.is("a"))
                {
                    clk.click();                                
                    clearInterval(timer[idx]);
                    //console.log(clk);
                }
            }*/
            
            function autoClick(input,value)
            {
                //console.log("@outer",input,value);                
                openCheck = false;
                //var input="search_designation";
                var ui={"item":{"value":value}};
                select_extended(input,ui); 
                $( "input#"+input ).val("");                    
                //console.log(clk);
            }
            
            ///For search_services autocomplete, on posted values
            def=$("#search_services").val();
            <?php  
                if(!empty($posted['filter_search_services']))
                {
                    $fldS="search_services";
                    foreach($posted['filter_search_services'] as $k=>$v)
                    {
                    ?>
                    /*$("#<?=$fldS;?>").autocomplete( "search", "<?=$v;?>" );
                    wgt["<?=$fldS;?>"]=$("#<?=$fldS;?>").autocomplete("widget");
                    $(wgt).find("li a").click();    
                    */
                    
                    //2nd approach, worked
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    
                    <?php
                    }///end for
                    
                }//end if
            ?>
            $("#search_services").val(def);
			
            ///For search_services autocomplete, on posted values

            ///For search_specialization autocomplete, on posted values
            def=$("#search_specialization").val();
            <?php  
                if(!empty($posted['filter_search_specialization']))
                {
                    $fldS="search_specialization";
                    foreach($posted['filter_search_specialization'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_specialization").val(def);
            ///For search_specialization autocomplete, on posted values   
            
            ///For search_qualification autocomplete, on posted values
            def=$("#search_qualification").val();
            <?php  
                if(!empty($posted['filter_search_qualification']))
                {
                    $fldS="search_qualification";
                    foreach($posted['filter_search_qualification'] as $k=>$v)
                    { 
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_qualification").val(def);
            ///For search_qualification autocomplete, on posted values              
            
            ///For search_experience autocomplete, on posted values
            def=$("#search_experience").val();
            <?php  
                if(!empty($posted['filter_search_experience']))
                {
                    $fldS="search_experience";
                    foreach($posted['filter_search_experience'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_experience").val(def);
            ///For search_experience autocomplete, on posted values              
            
            ///For search_classes autocomplete, on posted values
            def=$("#search_classes").val();
            <?php  
                if(!empty($posted['filter_search_classes']))
                {
                    $fldS="search_classes";
                    foreach($posted['filter_search_classes'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_classes").val(def);
            ///For search_classes autocomplete, on posted values              
            
            ///For search_medium autocomplete, on posted values
            def=$("#search_medium").val();
            <?php  
                if(!empty($posted['filter_search_medium']))
                {
                    $fldS="search_medium";
                    foreach($posted['filter_search_medium'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_medium").val(def);
            ///For search_medium autocomplete, on posted values             
            
            ///For search_tution_fee autocomplete, on posted values
            def=$("#search_tution_fee").val();
            <?php  
                if(!empty($posted['filter_search_tution_fee']))
                {
                    $fldS="search_tution_fee";
                    foreach($posted['filter_search_tution_fee'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_tution_fee").val(def);
            ///For search_tution_fee autocomplete, on posted values               
            
            ///For search_tution_mode autocomplete, on posted values
            def=$("#search_tution_mode").val();
            <?php  
                if(!empty($posted['filter_search_tution_mode']))
                {
                    $fldS="search_tution_mode";
                    foreach($posted['filter_search_tution_mode'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_tution_mode").val(def);
            ///For search_tution_mode autocomplete, on posted values               
            
            ///For search_subjects autocomplete, on posted values
            def=$("#search_subjects").val();
            <?php  
                if(!empty($posted['filter_search_subjects']))
                {
                    $fldS="search_subjects";
                    foreach($posted['filter_search_subjects'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_subjects").val(def);
            ///For search_subjects autocomplete, on posted values
            
            ///For search_rate autocomplete, on posted values
            def=$("#search_rate").val();
            <?php  
                if(!empty($posted['filter_search_rate']))
                {
                    $fldS="search_rate";
                    foreach($posted['filter_search_rate'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_rate").val(def);
            ///For search_rate autocomplete, on posted values    
            
            ///For search_employment_type autocomplete, on posted values
            def=$("#search_employment_type").val();
            <?php  
                if(!empty($posted['filter_search_employment_type']))
                {
                    $fldS="search_employment_type";
                    foreach($posted['filter_search_employment_type'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_employment_type").val(def);
            ///For search_employment_type autocomplete, on posted values                       
            
            ///For search_availability autocomplete, on posted values
            def=$("#search_availability").val();
            <?php  
                if(!empty($posted['filter_search_availability']))
                {
                    $fldS="search_availability";
                    foreach($posted['filter_search_availability'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_availability").val(def);
            ///For search_availability autocomplete, on posted values              
                        
            ///For search_tools autocomplete, on posted values
            def=$("#search_tools").val();
            <?php  
                if(!empty($posted['filter_search_tools']))
                {
                    $fldS="search_tools";
                    foreach($posted['filter_search_tools'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_tools").val(def);
            ///For search_tools autocomplete, on posted values 
            
            ///For search_designation autocomplete, on posted values
            def=$("#search_designation").val();
            <?php  
                if(!empty($posted['filter_search_designation']))
                {
                    $fldS="search_designation";
                    foreach($posted['filter_search_designation'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_designation").val(def);
            ///For search_designation autocomplete, on posted values             

            ///For search_gender autocomplete, on posted values
            def=$("#search_gender").val();
            <?php
                //pr($posted['filter_search_gender']);  
                if(!empty($posted['filter_search_gender']))
                {
                    $fldS="search_gender";
                    foreach($posted['filter_search_gender'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_gender").val(def);
            ///For search_gender autocomplete, on posted values

            ///For search_locality autocomplete, on posted values
            <?/* TODO:: how this works? 
            def=$("#search_locality").val();
            <?php  
                if(!empty($posted['filter_search_locality']))
                {
                    $fldS="search_locality";
                    foreach($posted['filter_search_locality'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_locality").val(def);
            */?>
            ///For search_locality autocomplete, on posted values 

            ///For search_fb_circle autocomplete, on posted values
            def=$("#search_fb_circle").val();
            <?php  
                if(!empty($posted['filter_search_fb_circle']))
                {
                    $fldS="search_fb_circle";
                    foreach($posted['filter_search_fb_circle'] as $k=>$v)
                    {
                    ?>
                    autoClick("<?=$fldS;?>","<?=$v;?>");
                    <?php
                    }///end for
                }//end if
            ?>
            $("#search_fb_circle").val(def);
            ///For search_fb_circle autocomplete, on posted values





        });
    });
</script>
