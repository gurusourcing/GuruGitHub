     
<?php
/**
* User/Individual Service Profile,
* User/Individual Service Profile Edit
* 
* user cannot recommed his own service. 
* user must be loggedin to recommend.
*/
  
?>

<link  type="text/css" rel="stylesheet" media="screen" href="<?=site_url(get_theme_path()."/js/jquery.more/jquery.more.css");?>" /> 
<script language="javascript" type="text/javascript" src="<?=site_url(get_theme_path()."/js/jquery.more/jquery.more.min.js");?>"></script>

<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
/*$('body').css('width','100%');*/
    
    $.datepicker.setDefaults({ yearRange: "c-50:c+20" });
    /**
    * Inedit defination
    */
    var sections={
            0 : {
                "fieldContainer" : "#frm_service_name",
                "contentContainer" : "#lbl_service_name",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["s_service_name"]);
                }
            },   
            1 : {
                "fieldContainer" : "#frm_service_desc",
                "contentContainer" : "#lbl_service_desc",
                "showButton" : "#edit-service_desc",
                "defaultValues" : $.parseJSON('<?=$default_value[1];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
    
                    contentContainer.find("#longtxt_service_desc").html(values["s_service_desc"]);
                    ///showmore text//
                    $('#longtxt_service_desc').more('destroy');
                    $('#longtxt_service_desc').more({
                        length: 300,
                        ellipsisText: '', 
                        moreText: '+ Show more',
                        lessText: '- Show less',
                    });
                    ///end showmore text//
                },
                "beforeShowCallback" : function(fields,default_values){}
                
            }, 
            2 : {
                "fieldContainer" : "#frm_language",
                "contentContainer" : "#lbl_language",
                "showButton" : "#edit-language",
                "defaultValues" : $.parseJSON('<?=$default_value[2];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_lang",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_lang_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str="";
                    $.each(values["add_more_lang"],function(i,v){
                        //console.log(v);
                        //str+=($.trim(str)!=""?",":"")+v["lang"];
                        str+='<ul class="name_list">';
                        str+='<li><span>'+v["lang"]+'</span>'+v['proficency']+'</li>';
                        str+='</ul>';
                    });
                   
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    autoLang();  
                },*/
                "afterShowCallback" : function(fields){
                    autoLang();  
                }                
                
            },
            3 : {
                "fieldContainer" : "#frm_location",
                "contentContainer" : "#lbl_location",
                "defaultValues" : $.parseJSON('<?=$default_value[3];?>'),
                "showButton" : "#edit-location",
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    //var disp=($.trim(values["city_name"])!=""?values["city_name"]+",":"");
                    //disp=disp+($.trim(values["country_id"])!=""?values["country_id"]:"");
                    
                    //console.log(values);
                    
                    var disp='';
                     //disp+=' <div class="facebook nodevider nopad"><p><span>Country:</span>'+values["country_id"]+'</p>';
                     disp+='<p><span>City: </span> '+values["city_name"]+'</p>';
                     disp+='<p><span>State: </span> '+values["state_name"]+'</p>';
                     disp+='<p><span>Zip: </span> '+values["zip_code"]+'</p>';
                     disp+='<p><span>Phone no: </span> '+values["s_phone"]+' </p>';
                     disp+='<p><span>Mobile no: </span> '+values["s_mobile"]+' </p>';
                     disp+='<p><span>Email:</span> '+values["s_email"]+' </p>';
                     disp+='<p><span>Postal address: </span><br /> '+values["s_address"]+'</p></div>';                
                    contentContainer.html(disp);
					
					 /*to hide contact view 15 feb 2014*/
					<?php if(!$contact_view){ ?>				
					$("#lbl_location").parent('div').css('display','none');
					<?php } ?>
					
                }
            },
            4: {
                "fieldContainer" : "#frm_online",
                "contentContainer" : "#lbl_online",
                "defaultValues" : $.parseJSON('<?=$default_value[4];?>'),
                "showButton" : "#edit-online",
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    
                    //console.log(values['i_online']);
                    var disp='';
                    
                    disp+='<h3>';
                    if($.trim(values['i_online'])=='Online')
                    {
                        disp+='<img src="<?=site_url(get_theme_path()."images/green-right-big.png")?>" width="19" height="19" alt="icon" class="alignleft" />'+values['i_online']+' Service';
                    }
                    else
                    {
                        disp+='<img src="<?=site_url(get_theme_path()."images/cross2.png")?>" width="19" height="19" alt="icon" class="alignleft" />'+values['i_online']+' Service';
                    }
                    
                    disp+='</h3>';
                    
                    contentContainer.html(disp);
                }
            },             
            
    };
    
    ////date pickers added on 24 feb///
    $("input[id^='dt_from'],input[id^='dt_to']").each(function(){
        
        $(this).removeClass("hasDatepicker");               
        
        var is_from=($(this).attr("id").search(/from/)>=0?true:false);
        
        $(this).datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
			"yearRange": 'c-100:+0' ,
            "onClose": function( selectedDate ) {
                ///validating date range//
                if(is_from)
                    $( "input[id^='dt_to']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "input[id^='dt_from']" ).datepicker( "option", "maxDate", selectedDate );
            }
        });                         
    });
	
    var tot_sections=0;
    $.each(sections,function(nm,se){
        tot_sections++;
    });
    /**
    * company links
    */
   <?if(!empty($default_value["company_link"]))
   {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_company_link",
                "contentContainer" : "#lbl_company_link",
                "showButton" : "#edit-company_link",
                "defaultValues" : $.parseJSON('<?=$default_value['company_link'];?>'),
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_link",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_link_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str=''; 
                   
                   /* console.log(values["add_more_link"]);*/
                  
                  
                   $.each(values["add_more_link"],function(i,v){
                       //console.log(v['s_links']);
                        str+='<ul class="name_list">';
                       str+='<li><a href="http://'+v['s_links']+'" class="black">'+v['s_links']+'</a></li>';        
                      str+='</ul>';
                    });
                     
                   
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                /*"afterShowCallback" : function(fields){
                    linkFldSettings();///default call
                }                */
              }; 
            
    <?  
    }
    else
    {?>
        $("#company_link").remove();
    <?}///end else
    ?>
    
    /**
    * end company links
    */
     
    /**
    * service provider
    */
      <?if(!empty($default_value["service_provider"]))
      {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_employee",
                "contentContainer" : "#lbl_employee",
                "showButton" : "#edit-employee",
                "defaultValues" : $.parseJSON('<?=$default_value["service_provider"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                   // console.log("@beforeHide:",values);
                    
                    if( !$.isEmptyObject(values.service_provider) 
                        && contentContainer.attr("rel")!="loaded"
                    )
                    {
                        var str='<ul class="name_list">';
                        $.each(values.service_provider,function(i,sp){
                            str+='<li>'+sp.s_profile+'</li>';
                        });
                        str+='</ul>';
                        contentContainer.html(str);
                        contentContainer.attr("rel","loaded");                        
                    }

                    
                    <?
                    /**
                    * if the service is a company service then only show the 
                    * edit button and form. 
                    * For individual user's service no need to show the 
                    * edit or form fields.
                    */
                    if(!$user_service->i_is_company_service)
                    {
                        ?>
                        $("#frm_employee").remove();
                        $("#edit-employee").remove();
                        <?
                    }
                    ////end if company service
                    ?>
                    
                },
                "afterSaveCallback" :  function(values,contentContainer,ajaxReturn){
                    //console.log("@afterSave:",values,ajaxReturn);
                    ///Add the new li into it contentContainer///
                    if(!$.isEmptyObject(ajaxReturn.new_service_provider))
                    {
                        contentContainer.find("ul.name_list li:last")
                            .after('<li>'+ajaxReturn.new_service_provider.s_profile+'</li>');                        
                    }
                }
            }; 
            
    <?  
    }
    else
    {?>
        $("#service_provider").remove();
    <?}///end else
    ?>
    
    
    /**
    * end of service provider
    */
    
    /**
    * experience field
    */   
    <?if(!empty($default_value["d_experience"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_experience",
                "contentContainer" : "#lbl_experience",
                "showButton" : "#edit-experience",
                "defaultValues" : $.parseJSON('<?=$default_value["d_experience"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["d_experience"]);
                }
            };
      <?  
    }
    else
    {?>
        $("#experience").remove();
    <?}///end else
    ?>
    ////end experience field///    
        
    /**
    * tution_fee field
    */   
    <?if(!empty($default_value["d_tution_fee"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_tution_fee",
                "contentContainer" : "#lbl_tution_fee",
                "showButton" : "#edit-tution_fee",
                "defaultValues" : $.parseJSON('<?=$default_value["d_tution_fee"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text('$ '+values["d_tution_fee"]+' per Hour');
                }
            };
      <?  
    }
    else
    {?>
        $("#tution_fee").remove();
    <?}///end else
    ?>
    ////end tution_fee field///
    
    /**
    * d_rate field
    */   
    <?if(!empty($default_value["d_rate"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_rate",
                "contentContainer" : "#lbl_rate",
                "showButton" : "#edit-rate",
                "defaultValues" : $.parseJSON('<?=$default_value["d_rate"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text('<?=$currency;?>'+values["d_rate"]+' per Hour');
                }
            };
      <?  
    }
    else
    {?>
        $("#rate").remove();
    <?}///end else
    ?>
    ////end d_rate field///    
    
    
    /**
    * classes field
    */
    <?if(!empty($default_value["s_classes_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_classes",
                "contentContainer" : "#lbl_classes",
                "showButton" : "#edit-classes",
                "defaultValues" : $.parseJSON('<?=$default_value["s_classes_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_classes",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_classes_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_classes"],function(i,v){                     
                        str+='<li>'+v["s_classes_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    classesFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#classes").remove();
    <?}///end else
    ?>
    ////end classes field///    
    
    /**
    * subjects field
    */
    <?if(!empty($default_value["s_other_subject_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_subjects",
                "contentContainer" : "#lbl_subjects",
                "showButton" : "#edit-subjects",
                "defaultValues" : $.parseJSON('<?=$default_value["s_other_subject_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_subjects",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_subjects_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_subjects"],function(i,v){                     
                        str+='<li>'+v["s_other_subject_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    subjectsFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#subjects").remove();
    <?}///end else
    ?>
    ////end subjects field///    
    
    /**
    * s_qualification_ids field
    */
    <?if(!empty($default_value["s_qualification_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_qualification",
                "contentContainer" : "#lbl_qualification",
                "showButton" : "#edit-qualification",
                "defaultValues" : $.parseJSON('<?=$default_value["s_qualification_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_qualification",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_qualification_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_qualification"],function(i,v){                     
                        str+='<li>'+v["s_qualification_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    qualificationFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#qualification").remove();
    <?}///end else
    ?>
    ////end s_qualification_ids field///  
    
    /**
    * s_medium_ids field
    */
    <?if(!empty($default_value["s_medium_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_medium",
                "contentContainer" : "#lbl_medium",
                "showButton" : "#edit-medium",
                "defaultValues" : $.parseJSON('<?=$default_value["s_medium_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_medium",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_medium_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_medium"],function(i,v){                     
                        str+='<li>'+v["s_medium_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    mediumFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#medium").remove();
    <?}///end else
    ?>
    ////end s_medium_ids field///  
    
    /**
    * s_tution_mode_ids field
    */
    <?if(!empty($default_value["s_tution_mode_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_tution_mode",
                "contentContainer" : "#lbl_tution_mode",
                "showButton" : "#edit-tution_mode",
                "defaultValues" : $.parseJSON('<?=$default_value["s_tution_mode_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_tution_mode",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_tution_mode_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_tution_mode"],function(i,v){                     
                        str+='<li>'+v["s_tution_mode_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    tution_modeFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#tution_mode").remove();
    <?}///end else
    ?>
    ////end s_tution_mode_ids field///  
    
    /**
    * s_availability_ids field
    */
    <?if(!empty($default_value["s_availability_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_availability",
                "contentContainer" : "#lbl_availability",
                "showButton" : "#edit-availability",
                "defaultValues" : $.parseJSON('<?=$default_value["s_availability_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_availability",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_availability_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_availability"],function(i,v){                     
                        str+='<li>'+v["s_availability_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    availabilityFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#availability").remove();
    <?}///end else
    ?>
    ////end s_availability_ids/////
    
    /**
    * s_tools_ids field
    */
    <?if(!empty($default_value["s_tools_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_tools",
                "contentContainer" : "#lbl_tools",
                "showButton" : "#edit-tools",
                "defaultValues" : $.parseJSON('<?=$default_value["s_tools_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_tools",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_tools_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_tools"],function(i,v){                     
                        str+='<li>'+v["s_tools_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    toolsFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#tools").remove();
    <?}///end else
    ?>
    ////end s_tools_ids/////
    
    /**
    * s_designation_ids field
    */
    <?if(!empty($default_value["s_designation_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_designation",
                "contentContainer" : "#lbl_designation",
                "showButton" : "#edit-designation",
                "defaultValues" : $.parseJSON('<?=$default_value["s_designation_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_designation",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_designation_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_designation"],function(i,v){                     
                        str+='<li>'+v["s_designation_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    designationFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#designation").remove();
    <?}///end else
    ?>
    ////end s_tools_ids/////
        
    
    /**
    * education field
    */
    <?if(!empty($default_value["service_provider_education"]))
    {
        foreach($default_value["service_provider_education"] as $in=>$v)
        {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_education_<?=$in?>",
                "contentContainer" : "#lbl_education_<?=$in?>",
                "showButton" : "#edit-education-<?=$in?>",
                "defaultValues" : $.parseJSON('<?=$default_value["service_provider_education"][$in];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                //addMoreButton" : "#add_more_subjects",/*please use this syntax*/
                //ddMoreContainer" : "[id='add_more_subjects_wrapper']",/*please use this syntax*/
                //ddMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                     var str='';               
                    //console.log(values);
                    
                    str+='<p><strong><a href="'+values['short_code']+'">'+values['s_profile_name']+'</a></strong>';
                    str+='  <span class="short greytext">'+values['designation']+'</span></p>';
                    str+='<ul class="name_list">';
                    str+='<li><span>School:</span> '+values["s_instutite"]+'</li>';
                    str+='<li><span>Field of Study :</span> '+values["s_specilization"]+'</li>';
                    str+='<li><span>Duration :</span> '+values["dt_from"]+" To "+values["dt_to"]+'</li>';
                    str+='<li><span>Degree :</span> '+values["s_degree"]+'</li>';
                    str+='</ul>';
                    str+='<p>DESCRIPTION: '+values["s_desc"]+'</p>';
                    str+='<div class="border_bottom"></div>'
                        
                    contentContainer.html(str);
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    //educationFldSettings();///default call
                }                
            };
      <?  
        }
    }
    else{
    ?>
    $("#education").remove();
    <?
    }
    ?>
    ////end education field///    

    
    /**
    * specialization field
    */
    <?if(!empty($default_value["s_specialization_ids"]))
    {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_specialization",
                "contentContainer" : "#lbl_specialization",
                "showButton" : "#edit-specialization",
                "defaultValues" : $.parseJSON('<?=$default_value["s_specialization_ids"];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_specialization",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_specialization_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    str+='<ul class="twocolor">';
                    $.each(values["add_more_specialization"],function(i,v){                     
                        str+='<li>'+v["s_specialization_ids"]+'</li>';
                    });
                    str+='</ul>';                        
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    specializationFldSettings();///default call
                }                
            };
      <?  
    }
    else
    {?>
        $("#specialization").remove();
    <?}///end else
    ?>
    ////end specialization field///      
    
    
    
    /**
    * company certificate field
    */
    <?if(!empty($default_value["company_certificate"]))
    {
        foreach($default_value["company_certificate"] as $in=>$v)
        {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_company_certificate_<?=$in?>",
                "contentContainer" : "#lbl_certificate_<?=$in?>",
                "showButton" : "#edit-certificate-<?=$in?>",
                "defaultValues" : $.parseJSON('<?=$default_value["company_certificate"][$in];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",      
                "beforeHideCallback" : function(contentContainer,values){
                     var str='';               
                    //console.log(values);
                    str+='<ul class="name_list">';
                    str+='<li><span>Certification on:</span> '+values["s_certificate_name"]+'</li>';
                    str+='<li><span>Number:</span> '+values["s_certificate_number"]+'</li>';
                    str+='<li><span>Organigation:</span> '+values["s_certified_from"]+'</li>';
                    str+='<li><span>Duration :</span> '+values["dt_from_certificate"]+" To "+values["dt_to_certificate"]+'</li>';
                    str+='</ul>';
                    str+='<p>DESCRIPTION: '+values["s_desc"]+'</p>';
                        
                    contentContainer.html(str);
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    //educationFldSettings();///default call
                }                
            };
      <?  
        }
    }
    else
    {
    ?>
        $("#company_certificate").remove();
    <?
    }
    ?>
    ////end company certificate field///   
    
    
    /**
    * employee certificate field
    */
    <?if(!empty($default_value["service_provider_certificate"]))
    {
        foreach($default_value["service_provider_certificate"] as $in=>$v)
        {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_employee_certificate_<?=$in?>",
                "contentContainer" : "#lbl_employee_certificate_<?=$in?>",
                "showButton" : "#edit-employee_certificate-<?=$in?>",
                "defaultValues" : $.parseJSON('<?=$default_value["service_provider_certificate"][$in];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_employee_certificate_license_operation");?>",      
                "beforeHideCallback" : function(contentContainer,values){
                     var str='';               
                    //console.log(values);
                    str+='<p><strong><a href="'+values['short_code']+'">'+values['s_profile_name']+'</a></strong>';
                    str+='  <span class="short greytext">'+values['designation']+'</span></p>';
                    str+='<ul class="name_list">';
                    str+='<li><span>Certification on:</span> '+values["s_certificate_name"]+'</li>';
                    str+='<li><span>Number:</span> '+values["s_certificate_number"]+'</li>';
                    str+='<li><span>Organigation:</span> '+values["s_certified_from"]+'</li>';
                    str+='<li><span>Duration :</span> '+values["dt_from_certificate"]+" To "+values["dt_to_certificate"]+'</li>';
                    str+='</ul>';
                    str+='<p>DESCRIPTION: '+values["s_desc"]+'</p>';
                        
                    contentContainer.html(str);
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    //educationFldSettings();///default call
                }                
            };
      <?  
        }
    }
    else
    {
    ?>
        $("#user_certificate").remove();
    <?
    }
    ?>
    ////end employee certificate field///  
    
    /**
    * company license field
    */
    <?if(!empty($default_value["license"]))
    {
        foreach($default_value["license"] as $in=>$v)
        {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_company_license_<?=$in?>",
                "contentContainer" : "#lbl_company_license_<?=$in?>",
                "showButton" : "#edit-company_license-<?=$in?>",
                "defaultValues" : $.parseJSON('<?=$default_value["license"][$in];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_operation");?>",      
                "beforeHideCallback" : function(contentContainer,values){
                     var str='';               
                    //console.log(values);
                    str+='<ul class="name_list">';
                    str+='<li><span>Name:</span> '+values["s_license_name"]+'</li>';
                    str+='<li><span>Number:</span> '+values["s_license_number"]+'</li>';
                    str+='<li><span>Authority:</span> '+values["s_licensed_from"]+'</li>';
                    str+='<li><span>Duration :</span> '+values["dt_from_license"]+" To "+values["dt_to_license"]+'</li>';
                    str+='</ul>';
                    str+='<p>DESCRIPTION: '+values["s_desc"]+'</p>';
                        
                    contentContainer.html(str);
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    //educationFldSettings();///default call
                }                
            };
      <?  
        }
    }
    else
    {
    ?>
        $("#company_licence").remove();
    <?
    }
    ?>
    ////end company license field///    
    
    /**
    * service provider license field
    */
    <?if(!empty($default_value["service_provider_license"]))
    {
        foreach($default_value["service_provider_license"] as $in=>$v)
        {
      ?>
      sections[ tot_sections++ ]={
                "fieldContainer" : "#frm_company_employee_license_<?=$in?>",
                "contentContainer" : "#lbl_company_employee_license_<?=$in?>",
                "showButton" : "#edit-company_employee_license-<?=$in?>",
                "defaultValues" : $.parseJSON('<?=$default_value["service_provider_license"][$in];?>'),
                "ajaxSaveUrl"   : "<?=base_url("service_profile/ajax_employee_certificate_license_operation");?>",      
                "beforeHideCallback" : function(contentContainer,values){
                     var str='';               
                    //console.log(values);
                    str+='<p><strong><a href="'+values['short_code']+'">'+values['s_profile_name']+'</a></strong>';
                    str+='  <span class="short greytext">'+values['designation']+'</span></p>';
                    str+='<ul class="name_list">';
                    str+='<li><span>Name:</span> '+values["s_license_name"]+'</li>';
                    str+='<li><span>Number:</span> '+values["s_license_number"]+'</li>';
                    str+='<li><span>Authority:</span> '+values["s_licensed_from"]+'</li>';
                    str+='<li><span>Duration :</span> '+values["dt_from_license"]+" To "+values["dt_to_license"]+'</li>';
                    str+='</ul>';
                    str+='<p>DESCRIPTION: '+values["s_desc"]+'</p>';
                        
                    contentContainer.html(str);
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    //educationFldSettings();///default call
                }                
            };
      <?  
        }
    }
    else
    {
    ?>
        $("#user_license").remove();
    <?
    }
    ?>
    ////end service provider license field///              
    
    //console.log(sections);
    
    /*$("#user_service_profile , #contact, #online").inedit*/
    $("#inline_edit").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "globalPrivacySettings": {
            "Public" : {"icon":"<?=trim( get_theme_path().'images/icon19.jpg');?>","css":""},
            "Private" : {"icon":"<?=trim( get_theme_path().'images/icon20.jpg');?>","css":""},
        },       
       "sections" : sections
        
    });
    ///end Inedit defination///
    
        /*//showmore text//
        $('#longtxt_recommendation').more('destroy');
        $('#longtxt_recommendation').more({
            length: 35,
            ellipsisText: '', 
            moreText: '+ Show more',
            lessText: '- Show less',
        });
        ///end showmore text/*/    
    
    
    ///REMOVE FROM BELOW///
    ////Dob date picker///
    $("#dt_dob").datepicker({
        "dateFormat": "dd-mm-yy",
        "changeYear": true,
        "showButtonPanel": true,
        "closeText": "Close"        
    });
    
    /**
    * Location AutoComplete
    */
     $( "#state_name" ).autocomplete({
        source: "<?=site_url("autocomplete/ajax_stateName")?>",
        minLength: 2,
        select: function( event, ui ) {
            
            if(ui.item)
            {
                /*$("#zip_id").attr("value",ui.item.zip_id);
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#city_id").attr("value",ui.item.city_id);
                $("#state_id").attr("value",ui.item.state_id);*/
                
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
              /*$( "#dialog-message" ).find("#dialog-message-icon")
              .removeClass().addClass("ui-icon ui-icon-alert");
              $( "#dialog-message" ).prev().find(".ui-dialog-title").text("Error");*/
              $( "#dialog-message" ).find("#dialog-message-content").html("Please select country.");
              $( "#dialog-message" ).dialog( "open" ); 
             // $("#popular_location_name").attr("value","");
                
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
                //$("#popular_location_name").attr("value",ui.item.popular_location_name);
                
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
                
                $("#zip_id").attr("value",ui.item.zip_id);
                $("#city_id").attr("value",ui.item.city_id);
                $("#state_id").attr("value",ui.item.state_id);  
               
                $("#zip_code").attr("value",ui.item.zip_code);
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
        var u2='<?=site_url("autocomplete/ajax_stateName?term=");?>'+$("#state_name").attr("value");
        if (settings.url == u 
            || settings.url == u1  
            || settings.url == u2  
        ) {
            settings.url=settings.url+'&country_id='+$("#country_id").attr("value");
        }
    });
    ///end Location AutoComplete      
    
    /**
    * Language Autocomplete
    */
    $("[id='add_more_lang']").on("click",function(){
        autoLang();  
    });
    //autoLang();  
    ///end Language AutoComplete///     
    
    /**
    * User Profession add more
    */
    $("[id='add_more_profession']").on("click",function(){
        profesionFldSettings();  
    });
    ///end User Profession    
    
    /**
    * User education add more
    */
    $("[id='add_more_education']").on("click",function(){
        educationFldSettings();  
    });
    ///end User education  
        
    /**
    * User certificate add more
    */
    $("[id='add_more_certificate']").on("click",function(){
        certificateFldSettings();  
    });
    ///end User certificate 
    
    /**
    * User license add more
    */
    $("[id='add_more_license']").on("click",function(){
        licenseFldSettings();  
    });
    ///end User certificate   


    /**
    * s_specialization_ids add more
    */
    $("[id='add_more_specialization']").on("click",function(){
        specializationFldSettings();  
    });
    ///end s_specialization_ids   
    
    /**
    * s_qualification_ids add more
    */
    $("[id='add_more_qualification']").on("click",function(){
        qualificationFldSettings();  
    });
    ///end s_qualification_ids   

    /**
    * s_classes_ids add more
    */
    $("[id='add_more_classes']").on("click",function(){
        classesFldSettings();  
    });
    ///end s_classes_ids   
    
    /**
    * s_other_subject_ids add more
    */
    $("[id='add_more_subjects']").on("click",function(){
        subjectsFldSettings();  
    });
    ///end s_other_subject_ids       
    
    /**
    * s_medium_ids add more
    */
    $("[id='add_more_medium']").on("click",function(){
        mediumFldSettings();  
    });
    ///end s_medium_ids       
    
    /**
    * s_tution_mode_ids add more
    */
    $("[id='add_more_tution_mode']").on("click",function(){
        tution_modeFldSettings();  
    });
    ///end s_tution_mode_ids
    
    /**
    * s_availability_ids add more
    */
    $("[id='add_more_availability']").on("click",function(){
        availabilityFldSettings();  
    });
    ///end s_availability_ids
    
    /**
    * s_tools_ids add more
    */
    $("[id='add_more_tools']").on("click",function(){
        toolsFldSettings();  
    });
    ///end s_tools_ids
    
    /**
    * s_designation_ids add more
    */
    $("[id='add_more_designation']").on("click",function(){
        designationFldSettings();  
    });
    ///end s_designation_ids
        
    
    //// get quotation     ///
    $("#quotation").click(function(){
       var s_visitor_email = $.trim($('#req_quotation #s_visitor_email').val());
       var s_message_thread = $.trim($('#req_quotation #s_message_thread').val());
       var uid = $.trim($('#req_quotation #uid').val());
       var service_id = $.trim($('#req_quotation #service_id').val());
     
       if(s_visitor_email != '')
       {
            $.post("<?=site_url('service_profile/ajax_operation');?>",{"s_visitor_email" : s_visitor_email, "s_message_thread" :s_message_thread, "uid": uid, "service_id" : service_id},
                             function(data){
                                if(data=='success')
                                {
                                  $('#req_quotation #s_visitor_email').attr('value', '');
                                  $('#req_quotation #s_message_thread').attr('value', '');
                                  $( "#dialog-message" ).find("#dialog-message-content").html("Request for quotation send successfully.");
                                  $( "#dialog-message" ).dialog( "open" );   
                                }
                                else if(data=='failed')
                                {
                                    $( "#dialog-message" ).find("#dialog-message-content").html("Request for quotation fail to send.");
                                    $( "#dialog-message" ).dialog( "open" );   
                                }
                                else
                                {
                                  $( "#dialog-message" ).find("#dialog-message-content").html(data);
                                  $( "#dialog-message" ).dialog( "open" );     
                                } 
                            }
             );           
       }
      

      
    });
    
});    
});

function autoLang()
{
 jQuery(function($){
 $(document).ready(function(){
     
     $( "input[id^='lang']" ).each(function(m){
         
         $(this).autocomplete({
            source: "<?=site_url("autocomplete/ajax_language")?>",
            minLength: 2,
            select: function( event, ui ) {},
            search: function( event, ui ) {}
        });    
              
     });     
     
 });
 });   
        
}

function profesionFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
       
     ////////Job Location ///
     $("input[id^='s_location']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_locationName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     });           
    
    ////date pickers///
    $("input[id^='dt_from'],input[id^='dt_to']").each(function(){
        
        $(this).removeClass("hasDatepicker");               
        
        var is_from=($(this).attr("id").search(/from/)>=0?true:false);
        
        $(this).datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
			"yearRange": 'c-100:+0' ,
            "onClose": function( selectedDate ) {
                ///validating date range//
                if(is_from)
                    $( "input[id^='dt_to']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "input[id^='dt_from']" ).datepicker( "option", "maxDate", selectedDate );
            }
        });                         
    });        
    
    /////Currently Working////
    $("input[id^='i_currently_working']").each(function(i){
       var $parentCont=$(this).parents('ul');
       
       $(this).off("change").on("change",function(){
              
          if($(this).is(":checked"))
          {
              $parentCont.find("input[id^='dt_to']").fadeOut(function(){
                  $parentCont.find("#span_currently_working").fadeIn();
              });               
          }
          else
          {
              $parentCont.find("#span_currently_working").fadeOut(function(){
                  $parentCont.find("input[id^='dt_to']").fadeIn();
              });            
          }          
              
       });
       
       $(this).change();
        
    });
    /////end Currently Working////       
       
   }); 
});
    

    
    
}

function educationFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
       
     ////////Institute ///
     $("#frm_education input[id^='s_instutite']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_instituteName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     });           

     ////////specilization ///
     $("#frm_education input[id^='s_specilization']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_specilizationName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     });    

     ////////degree ///
     $("#frm_education input[id^='s_degree']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_degreeName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
    
    ////date pickers///
    $("div[id='frm_education'] input[id^='dt_from'],div[id='frm_education'] input[id^='dt_to']").each(function(){
        
        $(this).removeClass("hasDatepicker");   
        
        var is_from=($(this).attr("id").search(/from/)>=0?true:false);            
        
        $(this).datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
            "onClose": function( selectedDate ) {
                ///validating date range//
                if(is_from)
                    $( "div[id='frm_education'] input[id^='dt_to']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "div[id='frm_education'] input[id^='dt_from']" ).datepicker( "option", "maxDate", selectedDate );
            }            
        });                         
    });        
       
   }); 
});
}

function certificateFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
           
    ////date pickers///
    $("div[id='frm_certificate'] input[id^='dt_from'],div[id='frm_certificate'] input[id^='dt_to']").each(function(){
        
        $(this).removeClass("hasDatepicker");       
        
        var is_from=($(this).attr("id").search(/from/)>=0?true:false);        
        
        $(this).datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
            "onClose": function( selectedDate ) {
                ///validating date range//
                if(is_from)
                    $( "div[id='frm_certificate'] input[id^='dt_to']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "div[id='frm_certificate'] input[id^='dt_from']" ).datepicker( "option", "maxDate", selectedDate );
            }             
        });                         
    });        
       
   }); 
});
}

function licenseFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
           
    ////date pickers///
    $("div[id='frm_license'] input[id^='dt_from'],div[id='frm_license'] input[id^='dt_to']").each(function(){
        
        $(this).removeClass("hasDatepicker");    
        
        var is_from=($(this).attr("id").search(/from/)>=0?true:false);                   
        
        $(this).datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true,
            "onClose": function( selectedDate ) {
                ///validating date range//
                if(is_from)
                    $( "div[id='frm_license'] input[id^='dt_to']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "div[id='frm_license'] input[id^='dt_from']" ).datepicker( "option", "maxDate", selectedDate );
            }            
        });                         
    });        
       
   }); 
});
}

function skillFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
           
    ////cross button///
    $("div[id='frm_skill'] .delete").each(function(){
        
       $(this).off("click").on("click",function(){
           var id=$(this).attr('rel');
           var form_token=$("div[id='frm_skill'] #form_token").attr("value"); 
           
           var element=$(this);
            $.post('<?=site_url('user_profile/ajax_skillDelete_operation')?>',
                    {"rel": id,"form_token":form_token}, 
                    function(data){
                        if(data=='success')
                        {
                           element.parent().remove();
                           ///removing from displaying//
                           //endorse_ulist_
                           $("div[id='lbl_skill']").find("#endorse_ulist_"+id).parent().remove();
                            
                        }
                    });
       });        
        
                                 
    });        
    
    ///clearing the txtbox
    $("#frm_skill input[id='s_skill_name']").attr('value','');
       
   }); 
});
}

function qualificationFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
       
     $("#frm_qualification input[id^='s_qualification_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_degreeName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function specializationFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_specialization input[id^='s_specialization_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_specilizationName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function classesFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_classes input[id^='s_classes_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_className")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function subjectsFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_subjects input[id^='s_other_subject_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_subjects")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function mediumFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_medium input[id^='s_medium_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_medium")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function tution_modeFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_tution_mode input[id^='s_tution_mode_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_tution_mode")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function availabilityFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_availability input[id^='s_availability_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_availability")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function toolsFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_tools input[id^='s_tools_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_tools")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}

function designationFldSettings()
{
jQuery(function($){
   $(document).ready(function(){

     $("#frm_designation input[id^='s_designation_ids']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_designation")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
   }); 
});
}


</script>



<?=theme_user_navigation();?>
<!--PANEL WITH LEFT SIDEBAR START -->    
<div class="col_left_sidebar" id="inline_edit">
<!--LEFT SIDEBAR START -->        
<div class="left_sidebar">

    <!--profile pic start-->
    <?
    if( !empty($user_service->comp_id) && intval($user_service->i_is_company_service)==1)//for company service
    {
        echo theme_block_company_profile_pic(get_userCompany($uid));
    }else // for individual service
       { 
          echo theme_block_user_profile_pic($uid); 
       }
    ?>         
    <? /* (intval(is_not_company_owner($uid))==0 ? theme_block_company_profile_pic(get_userCompany($uid)) : theme_block_user_profile_pic($uid)); */?>
    <!--profile pic end-->
    <!--short url -->
    <?=theme_block_service_profile_short_url($service_id);?>
    <!--short url -->
    <!-- online service-->
    <div class="panel_info" id='online'>
        <a id="edit-online" href="javascript:void(0);" class="alignright edit" title="Edit">Edit</a> 
        <div id="lbl_online">
        <? /* 
        <h3>
            <img src="images/green-right-big.png" width="19" height="19" alt="icon" class="alignleft" />Online Service
        </h3>
        */ ?>
        </div>
        <div id="frm_online">
             <input id="form_token" name="form_token" type="hidden" value="">
             <input id="action" name="action" type="hidden" value="">    
             <input id="i_online" name="i_online" type="radio" class="" value="1"/> Online
             &nbsp;&nbsp;
             <input id="i_online" name="i_online" type="radio" class="" value="0"/> Offline
             <div class="clear"></div>
        </div>
    </div>
    
    
        
    
    <!-- online service-->
    
    <!-- contact info-->
    <div class="panel_info facebook_fans nopad" id="contact">
                    <p class="name">Contact Info<a id="edit-location" rel="edit-location" href="javascript:void(0);" class="alignright edit" title="Edit">Edit</a></p>
                    <div id="lbl_location"></div>
                    <div id="frm_location" class="edit_section">  
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <input id="country_id" name="country_id" type="hidden" value="<?=@get_globalCountry();?>">
                        <input id="zip_id" name="zip_id" type="hidden" value="">
                        <input id="city_id" name="city_id" type="hidden" value="">
                        <input id="state_id" name="state_id" type="hidden" value="">
                            
                  <? /* <span class="alignleft"> Country<?=form_dropdown("country_id",dd_country(),"",'id="country_id" style="width:180px;"');?></span> */ ?>
                   <span class="clear top_pad padbot"></span>
                   <span class="alignleft">
                        City<br/><input id="city_name" name="city_name" required="true" type="text" class="short" />
                   <span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        State<br/><input id="state_name" name="state_name" required="true" type="text" class="short" />
                    </span>
                    </span><span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        Zip<br/><input id="zip_code" name="zip_code" required="true" type="text" class="short" />
                    </span>
                    <span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        Phone no<br/><input id="s_phone" name="s_phone" type="text" class="top_mar5" value="" size="24" />
                    </span>
                    <span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        Mobile no<br/><input id="s_mobile" name="s_mobile" type="text" class="top_mar5" value="" size="24" />
                    </span>
                    <span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        Email<br/><input id="s_email" name="s_email" type="text" class="top_mar5" value="" size="24" />
                    </span>
                    <span class="clear top_pad padbot"></span>
                    <span class="alignleft">
                        Postal address: <br/><textarea id="s_address" name="s_address" class="top_mar5" /></textarea>
                    </span>
                    <span class="clear top_pad padbot"></span>
                 <? /*<div class="facebook nodevider nopad">
<p><span>Country:</span> India</p>
                            <p><span>City: </span> Kolkata</p>
                            <p><span>Zip: </span> 700030</p>
                            <p><span>Contact no: </span> 1234567890 </p>
                            <p><span>Email:</span> abcd@gmail.com</p>
                            <p><span>Postal address: </span><br /> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero.</p>              
                        </div>*/?>
                        </div>
                 </div>
   <!-- contact info-->
    
    <!--Share with Friend -->
    <?= theme_block_user_profile_share_with_friend($service_id,"service");?>
     <!--Share with Friend -->
     
     <!--Share via Facebook -->
     <?=theme_block_user_profile_share_via_facebook($service_id,"service");?>
    <!--Share via Facebook -->
    
    <!--Share via Twitter -->
    <?=theme_block_user_profile_share_via_twitter($service_id,"service");?>
    <!--Share via Twitter -->
    
    <!--Report Abuse -->
    <?=theme_block_service_profile_report_abuse($service_id,$uid);?>
    <!--Report Abuse -->
    
    <!-- Company User-->
   
   <?
    if( !empty($user_service->comp_id) && intval($user_service->i_is_company_service)==1)
   { 
   ?> 
    <div class="panel_info facebook_fans">
        <p class="name">Company Users</p>
        <div class="facebook">
            <p class="botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
            <? /*<img src="images/test2.jpg" width="173" height="174" alt="company user" />*/ ?>
            <?php 
                if(!empty($company_user))
                {
                    foreach($company_user as $k=>$pic)
                    {
                        echo theme_user_thumb_picture($pic->uid,'','style="margin: 5px;"');
                    }
                }
            ?>
        </div>
        <a href="<?=site_url('company_employee/other_company_employee/'.encrypt(get_userCompany($uid)));?>" class="orange_button" >View All</a>
    </div>
   <?
    }
   ?>

    <!-- Company User-->
    
    <!--Facebook Fan -->
    <?=theme_block_user_profile_facebook_fan($uid)?>
    <!--Facebook Fan -->
    
  </div>
<!--LEFT SIDEBAR END -->   
<!--MAIN PANEL START -->
<div class="main_panel">
<!--LEFT PANEL START -->            
    <div id="user_service_profile" class="left_panel">
        <h1 class="alignleft relative right40 no-bot-pad">
            <strong id="lbl_service_name" class="alignleft"></strong>
            <div id="frm_service_name">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">                
                <input id="s_service_name" name="s_service_name" type="text" value="" />
                <?/*<input type="submit" value="Save" class="leftmar short" /><input cancel="cancel" type="reset" value="Cancel" class="short" />*/?>
            </div> 
            <a id="edit-service_name" rel="edit-service_name" href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>
        </h1>                   
        <?php /*?><p class="clear_left botmar20">Service Provided by <?=get_profile_name($uid);?><br /><?php */?>
		<p class="clear_left" style="padding-bottom:5px;">Service Provided by <?=get_profile_name($uid);?><br />
            <div id="tution_fee">
                <span class="orange right40 relative">
                    <span id="lbl_tution_fee"><?=$currency;?> 12 per Hour</span> 
                <a id="edit-tution_fee" rel="edit-tution_fee" href="javascript:void(0);" title="Edit" class="right-top edit">Edit</a>
                </span>
                <div id="frm_tution_fee"  class="edit_section edit_section_big">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <span class="right40 relative">
                        <?=$currency;?> <input id="d_tution_fee" name="d_tution_fee" type="text" value="" size="3"  /> per Hour 
                        </span>
                </div>
            </div>
            <div id="rate">
                <span class="orange right40 relative">
                    <span id="lbl_rate"><?=$currency;?> 12 per Hour</span> 
                <a id="edit-rate" rel="edit-rate" href="javascript:void(0);" title="Edit" class="right-top edit">Edit</a>
                </span>
                <div id="frm_rate"  class="edit_section edit_section_big">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <span class="right40 relative">
                        <?=$currency;?><input id="d_rate" name="d_rate" type="text" value="" size="3"  /> per Hour 
                        </span>
                </div>
            </div>
			
			<?php if($designation_since) { ?>
            <span class="short top_pad"><?=@$designation_since->s_title;?> - Since <?=format_date(@$designation_since->dt_from,"M, Y");?></span>       
			<?php } ?> 
		
			
        </p>
        <h2 class="relative">Service Description <a href="javascript:void(0);" id="edit-service_desc" class="right-top edit" title="Edit">Edit</a></h2>
        <div class="info">
            <div id="lbl_service_desc">
                <p id="longtxt_service_desc"></p>
            </div>
            <div id="frm_service_desc">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">     
                <textarea id="s_service_desc" name="s_service_desc" rows="3" cols="55" class="clear botmar"></textarea>
            </div>
        </div>
        <div id="service_provider">
            <h2 class="alignleft">Service Providers</h2>
            <?/*<a id="edit-profession" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>*/?>
            <a id="edit-employee" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>
            <div class="info">
            <div id="lbl_employee"></div>
            <div id="frm_employee">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">    
                <div class="edit_section edit_section_big no-bot-mar">
                <?=form_dropdown("employee_uid",dd_company_service_provider($user_service->comp_id),"",
                        'id="employee_uid" class="alignleft"');?>
                         <a href="<?=site_url("company_employee/add_company_employee");?>" class="leftmar black" target="_blank">+ Add new User</a>
                </div>

            </div>
        </div>  
        </div>
        
        <div id="experience"> 
            <h2 class="alignleft">Experience</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-experience" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_experience">
                    <?/*
                    <ul class="twocolor">
                        <li>Class IX</li>
                        <li>B. A.</li>
                        <li>B. Sc.</li>
                        <li>B. Com</li>
                        <li>JEE Examination</li>
                    </ul>
                    */?> 
              </div>
              <div id="frm_experience">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value=""> 
                    <?=form_dropdown("d_experience",dd_experience_range(),"",'id="d_experience"');?> 
              </div>
              
            </div>
        </div>
        <div id="classes"> 
            <h2 class="alignleft">Classes</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-classes" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_classes">
                    <?/*
                    <ul class="twocolor">
                        <li>Class IX</li>
                        <li>B. A.</li>
                        <li>B. Sc.</li>
                        <li>B. Com</li>
                        <li>JEE Examination</li>
                    </ul>
                    */?> 
              </div>
              <div id="frm_classes">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_classes_wrapper"  class="edit_section">
                        <p><input id="s_classes_ids" name="s_classes_ids" type="text" class="alignleft" value="Class IX" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_classes" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>
        <div id="subjects"> 
            <h2 class="alignleft">Subjects</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-subjects" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_subjects">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_subjects">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_subjects_wrapper"  class="edit_section">
                        <p><input id="s_other_subject_ids" name="s_other_subject_ids" type="text" class="alignleft" value="Class IX" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_subjects" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>
        <div id="medium"> 
            <h2 class="alignleft">Language Medium</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-medium" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_medium">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_medium">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_medium_wrapper"  class="edit_section">
                        <p><input id="s_medium_ids" name="s_medium_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_medium" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div> 
        <div id="tution_mode"> 
            <h2 class="alignleft">Tution mode</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-tution_mode" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_tution_mode">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_tution_mode">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_tution_mode_wrapper"  class="edit_section">
                        <p><input id="s_tution_mode_ids" name="s_tution_mode_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_tution_mode" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>  
        <div id="availability"> 
            <h2 class="alignleft">Availability</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-availability" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_availability">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_availability">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_availability_wrapper"  class="edit_section">
                        <p><input id="s_availability_ids" name="s_availability_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_availability" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>     
        <div id="tools"> 
            <h2 class="alignleft">Knowledge of Tool</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-tools" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_tools">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_tools">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_tools_wrapper"  class="edit_section">
                        <p><input id="s_tools_ids" name="s_tools_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_tools" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>
        <div id="designation"> 
            <h2 class="alignleft">Work Experience</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-designation" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_designation">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_designation">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_designation_wrapper"  class="edit_section">
                        <p><input id="s_designation_ids" name="s_designation_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_designation" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>
        <div id="qualification"> 
            <h2 class="alignleft">Highest Qualification Level</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-qualification" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_qualification">
                    <?/*
                    <ul class="twocolor">
                          <li>Physics</li>
                          <li>CHemistry</li>
                          <li>Mathematics</li>
                          <li>Botany</li>
                          <li>Economics</li>
                      </ul>
                    */?> 
              </div>
              <div id="frm_qualification">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_qualification_wrapper"  class="edit_section">
                        <p><input id="s_qualification_ids" name="s_qualification_ids" type="text" class="alignleft" value="" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_qualification" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
              
            </div>
        </div>
        
        <div id="education">
        <h2 class="alignleft">Education</h2>
        <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
        <div class="info">
        
        <div id="edu_wrapper">
          <?php
          if(!empty($service_provider_education))
          {
            foreach($service_provider_education as $k=>$v)
            {
          ?>  
        <a id="edit-education-<?=$k;?>" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>    
          <div id="lbl_education_<?=$k;?>">
              <?/* <ul class="name_list">
                  <li><span>School:</span> La Martiniere </li>
                  <li><span>Field of Study:</span> Science</li>
                  <li><span>Duration:</span> Aug 2010 - Sep 2011</li>
                  <li><span>Degree:</span> Rabindra Sadan, Kolkata</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper.</p> */ ?>
          </div>
          
            <div id="frm_education_<?=$k;?>">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">  
                <input id="user_id" name="user_id" type="hidden" value="">  
                  
                      <ul class="name_list edit_panel">
                          <li class="nodevider"><span>School:</span> <div class="edit_section">
                          <input id="s_token" name="s_token" type="hidden" value="">
                          <input id="s_instutite" name="s_instutite" type="text" class="botmar" value="La Martiniere" size="40" />
                          </div></li>
                          <li class="nodevider"><span>Field of Study:</span> <div class="edit_section">
                <input id="s_specilization" name="s_specilization" type="text" class="botmar" value="Science" size="40"/>
                          </div></li>
                          <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                <input id="dt_from" name="dt_from" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                           <p class="alignleft rightpad">-</p> 
                <input id="dt_to" name="dt_to" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                          <li class="nodevider"><span>Degree:</span> <div class="edit_section">
                          <input id="s_degree" name="s_degree" type="text" class="botmar" value="B. Sc." size="40" />
                          </div></li>
                          <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                          <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea>
                            </div></li>
                      </ul>                    
          </div>
         <?php  
            }
          }
          ?>
         </div>
        </div>
        </div>
        <? /* LINK */?>
        <div id="company_link">
        <h2>Links<a id="edit-company_link" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a></h2>
        <div class="info">
            <div id="lbl_company_link">
            </div>
            <div id="frm_company_link">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value=""> 
                <div id="add_more_link_wrapper">
                    <ul class="name_list edit_panel">
                        <li class="nodevider"><span>Link:</span> <div class="edit_section">
                        <input id="s_links" name="s_links" type="text" class="botmar" value="" size="40" />
                        </div></li>
                    </ul>                    
                </div>
                <a id="add_more_link" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>               </div>
       </div>
       </div>             
       <? /* LINK end*/?> 
       <? //// Specialization ///////////?>    
       <div id="specialization"> 
            <h2 class="alignleft">Specialization</h2>
            <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
            <a id="edit-specialization" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
            <div class="info">
              <div id="lbl_specialization">
                    <?/*
                    <ul class="twocolor">
                        <li>Brain (nervous system)</li> 
                        <li>Ear, nose, throat </li>
                        <li>Hormone imbalances </li>
                        <li>Urinary tract, kidneys</li> 
                        <li>Heart </li>
                    </ul>                     
                    */?> 
              </div>
              <div id="frm_specialization">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">    
                                
                    <div id="add_more_specialization_wrapper"  class="edit_section">
                        <p><input id="s_specialization_ids" name="s_specialization_ids" type="text" class="alignleft" value="Class IX" size="40" /></p>
                    </div>
                    <div class="clear"></div>
                    <a id="add_more_specialization" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
              </div>
            </div>
       </div>
        <? //// specialization ends///// ?>

                      
       <? /* COMPANY CERTIFICATE */ ?> 
       <div id="company_certificate">
           <h2>Company Certification</h2>
           <div class="info">
                   <div id="edu_wrapper">
                  <?php
                  if(!empty($company_certificate))
                  {
                    foreach($company_certificate as $k=>$v)
                    {
                  ?>  
                        <a id="edit-certificate-<?=$k;?>" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>    
                        <div id="lbl_certificate_<?=$k;?>">
                          <? /* <div id="lbl_company_certificate">
                                    <ul class="name_list">
                                        <li><span>Certification on:</span> ISO 9001</li>
                                        <li><span>Number:</span></li>
                                        <li><span>Organigation:</span></li>
                                        <li><span>Duration:</span></li>
                                    </ul>
                                    <p>DESCRIPTION: </p>
                                </div>   */ ?>
                        </div>
                        <div id="frm_company_certificate_<?=$k;?>">
                        <input id="form_token" name="form_token" type="hidden" value="">
                         <input id="comp_id" name="comp_id" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">   
                        
                            <ul class="name_list edit_panel">
                                <li class="nodevider"><span>Certification on:</span> <div class="edit_section">
                                    <input id="s_token" name="s_token" type="hidden" value="">
                                    <input id="s_certificate_name" name="s_certificate_name" type="text" class="botmar" value="La Martiniere" size="40" /> 
                          </div></li>
                                <li class="nodevider"><span>Number:</span> <div class="edit_section">
                                    <input id="s_certificate_number" name="s_certificate_number" type="text" class="botmar" value="346345243" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Organigation:</span> <div class="edit_section">
                                    <input id="s_certified_from" name="s_certified_from" type="text" class="botmar" value="ABCD Org" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                                    <input id="dt_from_certificate" name="dt_from_certificate" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" /> 
                                    <p class="alignleft rightpad">-</p> 
                                    <input id="dt_to_certificate" name="dt_to_certificate" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                                <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                                    <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                      </ul>                                
                
                
                   </div>
                   <?php  
                    }
                  }
                  ?>
                </div>
                </div>
       </div>
       <? /* COMPANY CERTIFICATE end */ ?> 

       <? /* service provider CERTIFICATE */ ?> 
       <div id="user_certificate">
       <h2>Certificate for Service Provider</h2>
       <div class="info">
                   <div id="edu_wrapper">
                  <?php
                  if(!empty($service_provider_certificate))
                  {
                    foreach($service_provider_certificate as $k=>$v)
                    {
                  ?>  
                        <a id="edit-employee_certificate-<?=$k;?>" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>    
                        <div id="lbl_employee_certificate_<?=$k;?>">
                          <? /* <div id="lbl_company_certificate">
                                    <ul class="name_list">
                                        <li><span>Certification on:</span> ISO 9001</li>
                                        <li><span>Number:</span></li>
                                        <li><span>Organigation:</span></li>
                                        <li><span>Duration:</span></li>
                                    </ul>
                                    <p>DESCRIPTION: </p>
                                </div>   */ ?>
                        </div>
                        <div id="frm_employee_certificate_<?=$k;?>">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="user_id" name="user_id" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">   
                        
                            <ul class="name_list edit_panel">
                                <li class="nodevider"><span>Certification on:</span> <div class="edit_section">
                                    <input id="s_token" name="s_token" type="hidden" value="">
                                    <input id="s_certificate_name" name="s_certificate_name" type="text" class="botmar" value="La Martiniere" size="40" /> 
                          </div></li>
                                <li class="nodevider"><span>Number:</span> <div class="edit_section">
                                    <input id="s_certificate_number" name="s_certificate_number" type="text" class="botmar" value="346345243" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Organigation:</span> <div class="edit_section">
                                    <input id="s_certified_from" name="s_certified_from" type="text" class="botmar" value="ABCD Org" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                                    <input id="dt_from_certificate" name="dt_from_certificate" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" /> 
                                    <p class="alignleft rightpad">-</p> 
                                    <input id="dt_to_certificate" name="dt_to_certificate" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                                <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                                    <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                      </ul>                                
                
                
                   </div>
                   <?php  
                    }
                  }
                  ?>
                </div>
                </div>
       </div>
       <? /* service provider CERTIFICATE end */ ?>      

       <? /* company license */ ?> 
       <div id="company_licence">
               <h2>Company License</h2>
                <div class="info">
                   <div id="edu_wrapper">
                  <?php
                  if(!empty($license))
                  {
                    foreach($license as $k=>$v)
                    {
                  ?>  
                        <a id="edit-company_license-<?=$k;?>" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>    
                        <div id="lbl_company_license_<?=$k;?>">
                          <? /* <div id="lbl_company_certificate">
                                    <ul class="name_list">
                                        <li><span>Certification on:</span> ISO 9001</li>
                                        <li><span>Number:</span></li>
                                        <li><span>Organigation:</span></li>
                                        <li><span>Duration:</span></li>
                                    </ul>
                                    <p>DESCRIPTION: </p>
                                </div>   */ ?>
                        </div>
                        <div id="frm_company_license_<?=$k;?>">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="comp_id" name="comp_id" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">   
                        
                            <ul class="name_list edit_panel">
                                <li class="nodevider"><span>Name:</span> <div class="edit_section">
                                    <input id="s_token" name="s_token" type="hidden" value="">
                                    <input id="s_license_name" name="s_license_name" type="text" class="botmar" value="La Martiniere" size="40" /> 
                          </div></li>
                                <li class="nodevider"><span>Number:</span> <div class="edit_section">
                                    <input id="s_license_number" name="s_license_number" type="text" class="botmar" value="346345243" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Authority:</span> <div class="edit_section">
                                    <input id="s_licensed_from" name="s_licensed_from" type="text" class="botmar" value="ABCD Org" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                                    <input id="dt_from_license" name="dt_from_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" /> 
                                    <p class="alignleft rightpad">-</p> 
                                    <input id="dt_to_license" name="dt_to_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                                <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                                    <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                      </ul>                                
                
                
                   </div>
                   <?php  
                    }
                  }
                  ?>
                </div>
                </div>
       </div>
       <? /* company license end */ ?>    
               
               
       <? /* service provider license */ ?>
       <div id="user_license"> 
       <h2>License for Service Provider</h2>
       <div class="info">
                   <div id="edu_wrapper">
                  <?php
                  if(!empty($service_provider_license))
                  {
                    foreach($service_provider_license as $k=>$v)
                    {
                  ?>  
                        <a id="edit-company_employee_license-<?=$k;?>" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>    
                        <div id="lbl_company_employee_license_<?=$k;?>">
                          <? /* <div id="lbl_company_certificate">
                                    <ul class="name_list">
                                        <li><span>Certification on:</span> ISO 9001</li>
                                        <li><span>Number:</span></li>
                                        <li><span>Organigation:</span></li>
                                        <li><span>Duration:</span></li>
                                    </ul>
                                    <p>DESCRIPTION: </p>
                                </div>   */ ?>
                        </div>
                        <div id="frm_company_employee_license_<?=$k;?>">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="user_id" name="user_id" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">   
                        
                            <ul class="name_list edit_panel">
                                <li class="nodevider"><span>Name:</span> <div class="edit_section">
                                    <input id="s_token" name="s_token" type="hidden" value="">
                                    <input id="s_license_name" name="s_license_name" type="text" class="botmar" value="La Martiniere" size="40" /> 
                          </div></li>
                                <li class="nodevider"><span>Number:</span> <div class="edit_section">
                                    <input id="s_license_number" name="s_license_number" type="text" class="botmar" value="346345243" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Authority:</span> <div class="edit_section">
                                    <input id="s_licensed_from" name="s_licensed_from" type="text" class="botmar" value="ABCD Org" size="40" />
                          </div></li>
                                <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                                    <input id="dt_from_license" name="dt_from_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" /> 
                                    <p class="alignleft rightpad">-</p> 
                                    <input id="dt_to_license" name="dt_to_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                                <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                                    <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                      </ul>                                
                
                
                   </div>
                   <?php  
                    }
                  }
                  ?>
                </div>
                </div>
       </div>
       <? /* service provider license end */ ?> 
       
       <? /* language */ ?>   
               
       <h2>Language <a id="edit-language" rel="edit-language" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a></h2> 
       <div class="info">
                  <div id="lbl_language">English, Hindi</div>  
                  <div id="frm_language" class="edit_section">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="user_id" name="user_id" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">        
                    <div id="add_more_lang_wrapper">
                       <input id="lang" name="lang" type="text" class="alignleft" value="" size="15" /> 
                       <?=form_dropdown("proficency",dd_langProficency(),"",'id="proficency" class="short"');?>
                    </div>
                    <!--<p id="add_more_lang" class="clear short">+ Add more language</p>-->
					<a id="add_more_lang" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
                  </div>
                </div> 
                
      <? /* language end */ ?>          
      
      <? /*  recommendation */ ?>                                                                                    
            <h2>Recommendation</h2> 
            <div class="info">
                <ul class="name_list">
               <?php 
                   ///user must be loggedin to recommend this service 
                   if(!is_userLoggedIn()) 
                   {
                       ?>
                       <li>
                        <div class="colorbg botmar">
                        <strong>To recommend  "<?=@$user_service->s_service_name;?>" of <?=get_profile_name($uid,FALSE);?>
                            please <a href="<?=site_url('account/signin');?>">login</a> </strong>
                        </div>
                        </li>                       
                       <?
                   }
                   elseif($uid!=get_userLoggedIn('id') 
                    && !in_array(get_userLoggedIn('id'),$service_provider_uid)
                   )///user is loggedin, but user cannot recommed his own service
                   {
                       ?>
                    <li>
                    <div class="colorbg botmar">
                    <?= theme_user_thumb_picture(get_userLoggedIn('id'),'','class="alignleft"');?>
                        <strong><?= get_user_display_name(get_userLoggedIn('id'),'anchor'); ?> would you like to recommend 
                        "<?=@$user_service->s_service_name;?>" of <?=get_profile_name($uid,FALSE);?>
                        <a href="<?= site_url('recommendation/addRecommendation/'.encrypt($uid)); ?> ">Click here.</a></strong>
                    </div>
                    </li>                       
                       <?
                   }
               
                ///listing the recomeded records/// 
                if(!empty($recommendation))
                {
                    $cnt=count($recommendation);
                    foreach($recommendation as $k=>$rec)
                    {
                       $class=($cnt==$k+1) ? 'class="nodevider"' : '';
               ?>
                    <li <?=$class;?>>
                        <?= theme_user_thumb_picture($rec->uid_recommended_by,'','class="alignleft"');?>
                        <a href=" <?= site_url(short_url_code($rec->uid_recommended_by));?>">
                            <strong class="alignleft"><?= get_user_display_name($rec->uid_recommended_by,''); ?></strong>
                        </a>
                        <strong class="black">&nbsp; has recommended this service.</strong>
                        <span class="grey short"><?= $rec->designation;?></span>
                        <p id="longtxt_recommendation" class="top_pad clear">DESCRIPTION: <?= $rec->s_message[0]['s_msg']; ?> </p>
                    </li>
               <?php 
                    }
                    
                }///end listing the recomeded records/// 
               ?>                  
            </ul> 
				<?php if(!empty($recommendation))
                		{
				 ?>
                 <a href="<?=site_url('recommendation/general_recommendation/'.encrypt($service_id))?>">+ Show more</a>  
				 <?php
				 		}
				 ?>
           </div>
           
           
           <? /* SKILLs*/ ?>
           <h2>Skills</h2> 
           <div class="info nomar">
                        <div class="skil_edit">
                            
                           <?php 
                                if($user_skill)
                                {
                                    foreach($user_skill as $k=>$v)
                                    {
                           ?>
                           <?php /*?><a href="#"><?=ucfirst($v);?></a><?php */?>
						   <a href="javascript:void(0);"><?=ucfirst($v);?></a>
                           
                           <?
                                    }
                                }                           
                            ?>
                        </div>
                  </div>

    </div>
<!--LEFT PANEL END -->      
<!--RIGHT PANEL START -->                                  
    <div class="right_panel">
        <!--rank-->
        <?=theme_block_user_profile_rank($service_id,'service');?>
        <!-- rank -->
       
        <!-- connection -->
        <?= theme_block_user_profile_connection(intval($uid));?>
        <!-- connection -->
        
        <!-- Quotation -->
        <?php
        if($uid!=get_userLoggedIn('id'))
        {
        ?>
        <div id="req_quotation" class="info form aligncenter">
            <p class="name">Request a Quote</p>
            <div class="form_panel">
            <input type="hidden" class="textbox" name="uid" id="uid" value="<?=@$uid;?>" />
            <input type="hidden" class="textbox" name="service_id" id="service_id" value="<?=@$service_id;;?>" />
            <input type="text" class="textbox" name="s_visitor_email" required="true" id="s_visitor_email" value="<?=@get_userLoggedIn('s_email')?>" />
            <textarea rows="2" cols="10" name="s_message_thread" id="s_message_thread"></textarea>
            <input type="button" value="Request a Quote" class="grey_button" id="quotation" />
            
            </div>
        <?php 
        }    
         ?>  
        </div>
        <!-- Quotation -->
        
    </div>
<!--RIGHT PANEL END -->                 
</div>          
<!--MAIN PANEL END -->                       
</div>
<!--PANEL WITH LEFT SIDEBAR END -->    