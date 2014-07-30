<?php
  /**
  * User Profile,
  * User Profile Edit
  */
  
?>

<link  type="text/css" rel="stylesheet" media="screen" href="<?=site_url(get_theme_path()."/js/jquery.more/jquery.more.css");?>" /> 
<script language="javascript" type="text/javascript" src="<?=site_url(get_theme_path()."/js/jquery.more/jquery.more.min.js");?>"></script>

<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
/*$('body').css('width','100%');*/
$.datepicker.setDefaults({ yearRange: "c-50:c+20" });

//$("#dt_from_row0").datepicker({ changeMonth: true, changeYear: true, yearRange: '50:+0' });

    /**
    * Inedit defination
    */
    $("#user_profile").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "globalPrivacySettings": {
            "Public" : {"icon":"<?=trim( get_theme_path().'images/icon19.jpg');?>","css":""},
            "Private" : {"icon":"<?=trim( get_theme_path().'images/icon20.jpg');?>","css":""},
        },       
       "sections" : {
            0 : {
                "fieldContainer" : "#frm_full_name",
                "contentContainer" : "#lbl_full_name",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["s_name"]);
                }
            },            
            1 : {
                "fieldContainer" : "#frm_gender",
                "contentContainer" : "#lbl_gender",
                "defaultValues" : $.parseJSON('<?=$default_value[1];?>'),
                "privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_gender)?>"},
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["e_gender"]);	
					
					<?php if(!$private_prof && user_public_private(@$user_pp->i_gender)=="Private"){ ?>				
					$("#lbl_gender").parent('li').css('display','none');
					<?php } ?>
                }
            },     
            2 : {
                "fieldContainer" : "#frm_age",
                "contentContainer" : "#lbl_age",
                "defaultValues" : $.parseJSON('<?=$default_value[2];?>'),
                "privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_dob)?>"},
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    //contentContainer.text(values["dt_dob"]);
									
                    var dob=moment(values["dt_dob"], "MM-DD-YYYY");
                    var now=moment();
                    //console.log("@age"+now+"=="+dob+"=="+now.diff(dob, 'years'));
					//contentContainer.text(now.diff(dob, 'years')+" years");
					
					/* modified on 28 Nov 2013 */
					var dob_arr = values["dt_dob"].split('-');
					var birthdate = new Date(dob_arr[2]+'/'+dob_arr[1]+'/'+dob_arr[0]);
					var cur = new Date();					
					var diff = cur-birthdate; // This is the difference in milliseconds
					var age = Math.floor(diff/31557600000); // Divide by 1000*60*60*24*365
					if(values["i_age"]!=0)
                    	contentContainer.text(age+" years");
					
					<?php if(!$private_prof && user_public_private(@$user_pp->i_dob)=="Private"){ ?>				
					$("#lbl_age").parent('li').css('display','none');
					<?php } ?>
                }
            }, 
            3 : {
                "fieldContainer" : "#frm_location",
                "contentContainer" : "#lbl_location",
                "defaultValues" : $.parseJSON('<?=$default_value[3];?>'),
                "privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_location)?>"},
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    var disp=($.trim(values["city_name"])!=""?values["city_name"]+",":"");
                    disp=disp+($.trim(values["country_id"])!=""?values["country_id"]:"");
                    contentContainer.text(disp);
					
					<?php if(!$private_prof && user_public_private(@$user_pp->i_location)=="Private"){ ?>				
					$("#lbl_location").parent('li').css('display','none');
					<?php } ?>
                }
            },             
            4 : {
                "fieldContainer" : "#frm_language",
                "contentContainer" : "#lbl_language",
                "defaultValues" : $.parseJSON('<?=$default_value[4];?>'),
                "privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_language)?>"},                
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                
                "addMoreButton" : "#add_more_lang",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_lang_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str="";
                    $.each(values["add_more_lang"],function(i,v){
                        str+=($.trim(str)!=""?",":"")+v["lang"];
                    });
                    contentContainer.text(str);
                    ///end add more///
					<?php if(!$private_prof && user_public_private(@$user_pp->i_language)=="Private"){ ?>				
					$("#lbl_language").parent('li').css('display','none');
					<?php } ?>
					
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    autoLang();  
                },*/
                "afterShowCallback" : function(fields){
                    autoLang();  
					
					
                }                
                
            },                                               
            5 : {
                "fieldContainer" : "#frm_about",
                "contentContainer" : "#lbl_about",
                "showButton" : "#edit-about",
                "defaultValues" : $.parseJSON('<?=$default_value[5];?>'),
                /*"privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_language)?>"},*/                
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    //console.log(values);
                    //contentContainer.find("#longtxt_about").text(values["s_about_me"]);
                    contentContainer.find("#longtxt_about").html(values["s_about_me"]);
                    ///showmore text//
                    $('#longtxt_about').more('destroy');
                    $('#longtxt_about').more({
                        length: 300,
                        ellipsisText: '', 
                        moreText: '+ Show more',
                        lessText: '- Show less',
                    });
                    ///end showmore text//
                },
                "beforeShowCallback" : function(fields,default_values){}
                
            },
            6 : {
                "fieldContainer" : "#frm_professional",
                "contentContainer" : "#lbl_professional",
                "showButton" : "#edit-profession",
                "defaultValues" : $.parseJSON('<?=$default_value[6];?>'),
                /*"privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_professional)?>"},*/
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_profession_operation");?>",  
                
                "addMoreButton" : "#add_more_profession",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_profession_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';
                    //console.log(values);
                    $.each(values["add_more_profession"],function(i,v){                     
                        str+='<ul class="name_list botmar30">';
                        str+='<li><span>By Profession:</span> <span style="width:357px;"><strong>'+v["s_title"]+'</strong></span></li>';
                        str+='<li><span>Organization :</span> '+v["s_company"]+'</li>';
                        str+='<li><span>Duration :</span> '+v["dt_from"]+" To "
                                +(v["i_currently_working"]=="1"?"Present":v["dt_to"])+'</li>';
                        str+='<li><span>Job location :</span> '+v["s_location"]+'</li>';
                        str+='</ul>';
                    });
                    
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    profesionFldSettings();///default call
                }                
            },
            7 : {
                "fieldContainer" : "#frm_education",
                "contentContainer" : "#lbl_education",
                "showButton" : "#edit-education",
                "defaultValues" : $.parseJSON('<?=$default_value[7];?>'),
                /*"privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_education)?>"},*/
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_education_operation");?>",  
                
                "addMoreButton" : "#add_more_education",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_education_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';               
                    
                    //console.log(values);
                    $.each(values["add_more_education"],function(i,v){                     
                        str+='<ul class="name_list">';
                        str+='<li><span>School:</span> '+v["s_instutite"]+'</li>';
                        str+='<li><span>Field of Study :</span> '+v["s_specilization"]+'</li>';
                        str+='<li><span>Duration :</span> '+v["dt_from_education"]+" To "
                                +v["dt_to_education"]+'</li>';
                        <?php /*?>str+='<li><span >Degree :</span> '+v["s_degree"]+'</li>';<?php */?>
						str+='<li><span style="float:left;width:140px;">Degree :</span><span style="float:left;width:360px;"> '+v["s_degree"]+'</span></li>';
                        str+='</ul>';
                        <?php /*?>str+='<p><span style="float:left;width:140px;">DESCRIPTION:</span><span style="float:left;width:360px;"> '+(v["s_desc"]?v["s_desc"]:"")+'</span></p>';<?php */?>
						
						 str+='<p>DESCRIPTION: '+(v["s_desc"]?v["s_desc"]:"")+'</p>';
                        
                    });
                    
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    educationFldSettings();///default call
                }                
            },    
            8 : {
                "fieldContainer" : "#frm_certificate",
                "contentContainer" : "#lbl_certificate",
                "showButton" : "#edit-certificate",
                "defaultValues" : $.parseJSON('<?=$default_value[8];?>'),
                /*"privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_certificate)?>"},*/
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_certificate_operation");?>",  
                
                "addMoreButton" : "#add_more_certificate",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_certificate_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';                                  
                    
                    //console.log(values);
                    $.each(values["add_more_certificate"],function(i,v){                     
                        str+='<ul class="name_list">';
                        str+='<li><span>Certification on:</span> '+v["s_certificate_name"]+'</li>';
                        str+='<li><span>Number:</span> '+v["s_certificate_number"]+'</li>';
                        str+='<li><span>Organigation:</span> '+v["s_certified_from"]+'</li>';
                        str+='<li><span>Duration :</span> '+v["dt_from_certificate"]+" To "
                                +v["dt_to_certificate"]+'</li>';
                        str+='</ul>';
                        str+='<p>DESCRIPTION: '+v["s_desc"]+'</p>';
                    });
                    
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    certificateFldSettings();///default call
                }                
            },                                  
            9 : {
                "fieldContainer" : "#frm_license",
                "contentContainer" : "#lbl_license",
                "showButton" : "#edit-license",
                "defaultValues" : $.parseJSON('<?=$default_value[9];?>'),
                /*"privacy" : {"set":true,"value":"<?=user_public_private(@$user_pp->i_license)?>"},*/
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_license_operation");?>",  
                
                "addMoreButton" : "#add_more_license",/*please use this syntax*/
                "addMoreContainer" : "[id='add_more_license_wrapper']",/*please use this syntax*/
                "addMoreShow" : "bottom", //top|bottom                   
                
                "beforeHideCallback" : function(contentContainer,values){
                    ///add more///
                    var str='';                                   
                    
                    //console.log(values);
                    $.each(values["add_more_license"],function(i,v){                     
                        str+='<ul class="name_list">';
                        str+='<li><span>Certification on:</span> '+v["s_license_name"]+'</li>';
                        str+='<li><span>Number:</span> '+v["s_license_number"]+'</li>';
                        str+='<li><span>Organigation:</span> '+v["s_licensed_from"]+'</li>';
                        str+='<li><span>Duration :</span> '+v["dt_from_license"]+" To "
                                +v["dt_to_license"]+'</li>';
                        str+='</ul>';
                        str+='<p>DESCRIPTION: '+v["s_desc"]+'</p>';
                    });
                    
                    contentContainer.html(str);
                    ///end add more///
                },
                /*"beforeShowCallback" : function(fields,default_values){
                    profesionFldSettings();  
                },*/                
                "afterShowCallback" : function(fields){
                    licenseFldSettings();///default call
                }                
            },      
            10 : {
                "fieldContainer" : "#frm_skill",
                "contentContainer" : "#lbl_skill",
                "showButton" : "#edit-skill",
                "defaultValues" : $.parseJSON('<?=$default_value[10];?>'),
                "ajaxSaveUrl"   : "<?=base_url("user_profile/ajax_skill_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                   
                    /**
                    * Endorse this profile, 
                    * if and only if viewed by other users.
                    */
                    contentContainer.find(".plus").each(function(){
                       
                       $(this).off("click").on("click",function(){
                            var skill_id=$(this).attr('id');
                            var $endrCount=$(this).parent().find("#endorse_count");
                            //console.log($endrCount);
                            
                            $.post('<?=site_url('user_profile/ajax_skillEndorse_operation')?>',
                                {"rel": skill_id,"form_token":values["form_token"]}, 
                                function(data){
                                    if(data)
                                    {
                                        data=$.parseJSON(data);
                                        if(data.mode=="success")
                                        {
                                            $('#lbl_skill ul#endorse_ulist_'+skill_id+'')
                                            .append('<li>'+data.endorsed_user+'</li>');
                                            
                                            $endrCount.html(data.endorsed_count);
                                            
                                        }
                                    }
                                }
                            );  
                       });                        
                   }); 
                   /////end Endorse///
                    
                },
				
                "afterSaveCallback" : function(values,contentContainer,ajaxReturn){
                    //console.log(contentContainer,ajaxReturn,values);
                    if(ajaxReturn.mode=="success")
                    {
                        /**
                        * Adding into the form box
                        */
                        /*$("#frm_skill input[id='s_skill_name']").attr('value','');
                        console.log($("#frm_skill input[id='s_skill_name']").attr('value',''));*/
						
                        var str='<a href="javascript:void(0);">'+values["s_skill_name"]+'<img src="<?=get_theme_path('guru_frontend/images')?>cross1.png" width="7" height="8" alt="cross" class="delete" rel="'+ajaxReturn.rel+'"  /></a>';
                        $("#frm_skill a:last").after(str);             
                                          
                        /**
                        * Adding new value into display area
                        */
                        str='<div class="skills nomar">';
                        str+='<p class="with162 alignleft no-bot-pad"><a href="javascript:void(0);" class="alignleft skili_count"><span id="endorse_count">0</span>'+values["s_skill_name"]+'</a></p>';
                        str+='<ul id="endorse_ulist_'+ajaxReturn.rel+'" class="alignright"></ul>'
                        str+='</div>'; 
                        
                        contentContainer.find(".nomar").removeClass("nomar");
                        /**
                        * When there is no skills atall
                        */
                        if(contentContainer.find(".skills").length==0)
                            contentContainer.append(str);
                        else
                            contentContainer.find(".skills:last").after(str);    
                        //console.log(contentContainer.find(".skills").length);                   
                    }
                },
                "afterShowCallback" : function(fields){
                    skillFldSettings();///default call
                }                                     
            }                     
       }
        
    });
    
	
    ////Dob date picker///
    $("#dt_dob").datepicker({
        "dateFormat": "dd-mm-yy",
        "changeYear": true,
        "showButtonPanel": true,
        "closeText": "Close"  ,
		"beforeShow": function(input, inst)
		{
			var calendar = inst.dpDiv;

			setTimeout(function() {
				calendar.position({
					my: 'left top',
					at: 'left bottom',
					collision: 'none',
					of: input
				});
			}, 1);
		}      
    });
    
    /**
    * Location AutoComplete
    */
     $( "#zip_code" ).autocomplete({
        source: "<?=site_url("autocomplete/ajax_zipCode")?>",
        minLength: 2,
        select: function( event, ui ) {
            
            if(ui.item)
            {
                $("#zip_id").attr("value",ui.item.zip_id);
                $("#city_id").attr("value",ui.item.city_id);
                $("#city_name").attr("value",ui.item.city_name);
                $("#state_id").attr("value",ui.item.state_id);
                
            }
            
            /*console.log( ui.item ?
            "Selected: " + ui.item.value + " aka " + ui.item.id :
            "Nothing selected, input was " + this.value );*/
        },
        search: function( event, ui ) {
          ///country is mandatory  
          if($("#country_id").find("option:selected").attr("value")=="")
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
                $("#zip_id").attr("value",ui.item.zip_id);
                $("#zip_code").attr("value",ui.item.zip_code);
                $("#city_id").attr("value",ui.item.city_id);
                $("#state_id").attr("value",ui.item.state_id);
            }
        },
        search: function( event, ui ) {
          ///country is mandatory  
          if($("#country_id").find("option:selected").attr("value")=="")
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
        if (settings.url == u 
            || settings.url == u1  
        ) {
            settings.url=settings.url+'&country_id='+$("#country_id").find("option:selected").attr("value");
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
       
     /*$(document).on( "autocompletesearch", function( event, ui ) {
            //console.log("@profesionFldSettings",$(event.target));   
     } );*/  
	 
	 
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
			"yearRange": 'c-100:+0' ,
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
			"yearRange": 'c-100:+0' ,
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
  
   	////////skill 30 nov 2013 ///
     $("#frm_skill input[id^='s_skill_name']").each(function(){
         $( this ).autocomplete({
            source: "<?=site_url("autocomplete/ajax_skillName")?>",
            minLength: 2,
            select: function( event, ui ) {}
        });              
     }); 
	 
	 // edit skill 07 feb 2014
	 <?php /*?>$("div[id='skill_edit']").children('a').each(function(){
        
       $(this).off("click").on("click",function(){
           var id=$(this).attr('rel');
           var form_token=$("div[id='frm_skill'] #form_token").attr("value");            
           var element=$(this);
		  
		   var txt = $(this).text();
		   var htm = '<input type="text" class="skl_txt" value="'+txt+'" />';
		   $(this).html(htm);
            
       });                                    
    }); <?php */?>// edit skill 07 feb 2014  
           
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

</script>



<?=theme_user_navigation();?>
<!--PANEL WITH LEFT SIDEBAR START -->    
<div class="col_left_sidebar" id="inline_edit">
<!--LEFT SIDEBAR START -->        
<div class="left_sidebar">
    <!--profile pic start-->
    <?= theme_block_user_profile_pic($uid)?>
    <!--profile pic end-->
    <!--short url -->
    <?=theme_block_user_profile_short_url($uid);?>
    <!--short url -->
    <!--Share with Friend -->
    <?= theme_block_user_profile_share_with_friend($uid)?>
     <!--Share with Friend -->
     <!--Share via Facebook -->
     <?=theme_block_user_profile_share_via_facebook($uid)?>
    <!--Share via Facebook -->
    
    <!--Share via Twitter -->
    <?=theme_block_user_profile_share_via_twitter($uid)?>
    <!--Share via Twitter -->
    
    <!--Report Abuse -->
    <?=theme_block_user_profile_report_abuse($uid)?>
    <!--Report Abuse -->
    
    <!--Facebook Fan -->
    <?=theme_block_user_profile_facebook_fan($uid)?>
    <!--Facebook Fan -->
    
  </div>
<!--LEFT SIDEBAR END -->   
<!--MAIN PANEL START -->
<div class="main_panel">
    <div class="border_bottom">
        <?php /*?><a class="short_grey_button grey-bg" href="javascript:void(0);">Sync with Linkedin</a><?php */?>
		<a class="short_grey_button grey-bg" href="<?php echo base_url().'user_profile/connect_linkedin' ?>">Sync with Linkedin</a>
        <?php //pr(user_profile_prc_calculation(intval(get_userLoggedIn('id'))));?>

    </div>
<!--LEFT PANEL START -->            
    <div id="user_profile" class="left_panel">
        <h1 class="alignleft relative right40">
            <strong id="lbl_full_name" class="alignleft"></strong>
            <div id="frm_full_name">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">                
                <input id="s_name" name="s_name" type="text" value="" />
                <?/*<input type="submit" value="Save" class="leftmar short" /><input cancel="cancel" type="reset" value="Cancel" class="short" />*/?>
            </div> 
            <a id="edit-full_name" rel="edit-full_name" href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>
        </h1> 
        <?/*                   
        <p class="clear_left botmar20"><?=@$user_service->s_service_name;?> <a href="general-service-edit.html" class="orange_button">View Service Profile</a></p>*/?>
        <div class="info">
            <ul class="name_list edit_form">
                <!-- gender -->
                <li>
                    <span>Gender: </span> <div id="lbl_gender">Male</div>  
                    <div id="frm_gender" class="edit_section">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <input type="radio" name="e_gender" id="e_gender" value="Male" />Male 
                        <input type="radio" name="e_gender" id="e_gender" value="Female" />Female
                        <?/*<input type="submit" value="Save" class="leftmar short" /> <input type="reset" value="Cancel" class="short" cancel="cancel" />
                    <div class="right-top eye_panel">
                        <a href="#" class="eye_icon"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /></a>
                        <ul class="link">
                            <li><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /> Public</a></li>
                            <li class="nodevider"><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /> Private</a></li>
                      </ul>
                    </div>*/?>                           
                    </div>
                    <a id="edit-gender" rel="edit-gender" href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>
                </li>
                <!-- age -->
                <li><span>Age:</span> <div id="lbl_age"></div> 
                    <div id="frm_age" class="edit_section">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <input type="text" id="dt_dob" name="dt_dob" class="calender alignleft" value="01 / 07 / 1980" /> 
                        <?/*<input type="submit" value="Save" class="leftmar short" /> 
                      <input type="reset" value="Cancel" class="short" cancel="cancel" /> 
                      <div class="right-top eye_panel">
                            <a href="#" class="eye_icon"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /></a>
                            <ul class="link">
                                <li><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /> Public</a></li>
                                <li class="nodevider"><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /> Private</a></li>
                        </ul>
                        </div>*/?>
                      </div>
                    <a id="edit-age" rel="edit-age" href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>
                </li>
                <!-- location -->
                <li><span>City:</span> <div id="lbl_location">Kolkata, India</div> 
                    <div id="frm_location" class="edit_section">  
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <input id="zip_id" name="zip_id" type="hidden" value="">
                        <input id="city_id" name="city_id" type="hidden" value="">
                        <input id="state_id" name="state_id" type="hidden" value="">
                                                                      
                    Country<br />
                     <?=form_dropdown("country_id",dd_country(),"",'id="country_id"');?>
                    <span class="alignleft">
                        Postal Code<br />
                        <input id="zip_code" name="zip_code" type="text" class="alignleft top_mar5" value="" size="8" />
                    </span>
                    <span class="alignleft toppad20 rightpad">Or</span>
                    <span class="alignleft">
                        City<br />
                        <input id="city_name" name="city_name" type="text" class="alignleft top_mar5" value="" size="8" />
                    </span>
					
					
                    <span class="clear top_pad padbot"></span>
					
                    <?/*
                    <span class="clear top_pad padbot">
                    <input type="submit" value="Save" class="leftmar short" /> 
                    <input type="reset" value="Cancel" class="short" /> 
                    </span>
                    <div class="right-top eye_panel">
                        <a href="#" class="eye_icon"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /></a>
                        <ul class="link">
                            <li><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /> Public</a></li>
                            <li class="nodevider"><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /> Private</a></li>
                        </ul>
                    </div>
                    */?>
					
					 <?php echo theme_block_user_suggestion_block($uid,'location') ?>
                  </div>
				 
                  <a id="edit-location" rel="edit-location" href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>
                </li>
                <!-- Language -->
                <li class="nodevider"><span>Language proficient:</span> <div id="lbl_language">English, Hindi</div>  
                    <div id="frm_language" class="edit_section">
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">        
                                    
                        <div id="add_more_lang_wrapper">
                            <input id="lang" name="lang" type="text" class="alignleft" value="" size="15" /> 
                            <?=form_dropdown("proficency",dd_langProficency(),"",'id="proficency" class="short"');?>
                        </div>
                        <!--<p id="add_more_lang" class="clear short">+ Add more language</p>-->
						<a id="add_more_lang" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
						<?php echo theme_block_user_suggestion_block($uid,'language') ?>
                      <?/*<input type="submit" value="Save" class="short" /> 
                      <input type="reset" value="Cancel" class="short" /> 
                      <div class="right-top eye_panel">
                            <a href="#" class="eye_icon"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /></a>
                            <ul class="link">
                                <li><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon19.jpg" width="16" height="10" alt="icon" /> Public</a></li>
                                <li class="nodevider"><a href="#"><img src="<?=base_url(get_theme_path())."/";?>images/icon20.jpg" width="10" height="15" alt="icon" /> Private</a></li>
                            </ul>
                        </div>*/?>
                      </div>
                      <a id="edit-language" rel="edit-language" href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>
                
                </li>
            </ul>
        </div>
        <h2 class="relative">About <a href="javascript:void(0);" id="edit-about" class="right-top edit" title="Edit">Edit</a></h2>
        <div class="info">
            <div id="lbl_about">
                <p id="longtxt_about"></p>
                <?/*<a href="javascript:void(0);" class="showmore">+ Show more</a>*/?>
            </div>
            <div id="frm_about">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">     
                <textarea id="s_about_me" name="s_about_me" rows="3" cols="72" class="clear botmar"></textarea>
            </div>
        </div>
        <h2 class="alignleft">Professional Infomation</h2>
        <?/*<a id="edit-profession" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>*/?>
        <a id="edit-profession" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>
        <div class="info">
            <div id="lbl_professional">
            <ul class="name_list botmar30">
                <li><span>By Profession:</span> <strong>Sr. Teacher</strong> <?/*<a href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>*/?></li>
                <li><span>Organization:</span> La Martiniere</li>
                <li><span>Duration:</span> August 2011 - Present (1 year 6 months)</li>
                <li class="nodevider"><span>Job location:</span> Rabindra Sadan, Kolkata</li>
            </ul>
            <?/*
            <ul class="name_list">
                <li><span>By Profession:</span> <strong>School Teacher</strong> <a href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a></li>
                <li><span>Organization:</span> St. Xaviers</li>
                <li><span>Duration:</span> August 2010 - July 2011 (1 year)</li>
                <li class="nodevider"><span>Job location:</span> Rabindra Sadan, Kolkata</li>
            </ul>
            */?>
            </div>
            <div id="frm_professional">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">    
                            
                <div id="add_more_profession_wrapper">
                <ul class="name_list edit_panel botmar30">
                    <li class="nodevider"><span>By Profession:</span> 
                    <div class="edit_section">
                    <input id="s_token" name="s_token" type="hidden" value="">
                    <input type="text" id="s_title" name="s_title" class="botmar" value="" size="40" />
                    </div>
                    </li>
                    <li class="nodevider"><span>Organization:</span> 
                    <div class="edit_section">
                    <input type="text" id="s_company" name="s_company" class="botmar" value="" size="40" /> 
                    </div>
                    </li>
                    <li class="nodevider"><span>Duration:</span> 
                    <div class="edit_section">
                    <input type="text" id="dt_from" name="dt_from" class="calender alignleft" value="" /> 
                    <p id="p_from" class="alignleft">
                        <span id="span_currently_working"> -  present</span> 
                        <input type="text" id="dt_to" name="dt_to" class="calender alignleft" value="" />
                    </p>
                    <p class="short alignright"><input id="i_currently_working" name="i_currently_working" value="1" type="checkbox" /><span id="showValInEdit" style="display: none;"> -  present</span> I am currently working here</p>
                    </div>
                    </li>
                    <li class="nodevider"><span>Job location:</span> 
                    <div class="edit_section">
                    <input type="text" id="s_location" name="s_location" value="" size="40" />
                    <?/*<input type="submit" value="Save" class="top_mar short" /> 
                    <input type="reset" value="Cancel" class="short" />*/?>
                    </div>
                    </li>
                </ul>
                </div>  
                <a id="add_more_profession" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
            </div>
        </div>  
        <h2 class="alignleft">Education</h2>
        <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
        <a id="edit-education" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
        <div class="info">
            <div id="lbl_education">
              <ul class="name_list">
                  <li><span>School:</span> La Martiniere <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Field of Study:</span> Science</li>
                  <li><span>Duration:</span> Aug 2010 - Sep 2011</li>
                  <li><span>Degree:</span> Rabindra Sadan, Kolkata</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper.</p>
          
          <?/*<ul class="name_list">
              <li><span>School:</span> La Martiniere <a href="#" class="right-top edit" title="Edit">Edit</a></li>
              <li><span>Field of Study:</span> Science</li>
              <li><span>Duration:</span> Aug 2010 - Sep 2011</li>
              <li><span>Degree:</span> Rabindra Sadan, Kolkata</li>
          </ul>
          <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper.</p>*/?>
          </div>
          <div id="frm_education">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">    
                            
                <div id="add_more_education_wrapper">
                    
                      <ul class="name_list edit_panel">
                          <li class="nodevider"><span>School:</span> <div class="edit_section">
                          <input id="s_token" name="s_token" type="hidden" value="">
                          <input id="s_instutite" name="s_instutite" type="text" class="botmar" value="La Martiniere" size="40" />
						  <?php echo theme_block_user_suggestion_block($uid,'institute') ?>
                          </div></li>
                          <li class="nodevider"><span>Field of Study:</span> <div class="edit_section">
                <input id="s_specilization" name="s_specilization" type="text" class="botmar" value="Science" size="40"/>
                          </div></li>
                          <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                <input id="dt_from_education" name="dt_from_education" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                           <p class="alignleft rightpad">-</p> 
                <input id="dt_to_education" name="dt_to_education" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                          <li class="nodevider"><span>Degree:</span> <div class="edit_section">
                          <input id="s_degree" name="s_degree" type="text" class="botmar" value="B. Sc." size="40" />
						  <?php echo theme_block_user_suggestion_block($uid,'degree') ?>
                          </div>						  	
						  </li>
                          <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                          <textarea id="s_desc" name="s_desc" rows="3" cols="34" class="clear botmar"></textarea> <?/*<input type="submit" value="Save" class="short" /> 
                            <input type="reset" value="Cancel" class="short" />*/?>
                            </div></li>
                      </ul>                    
                    
                </div>
                <a id="add_more_education" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>
          </div>
          
        </div>
        <h2 class="alignleft">Certification</h2> 
        <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a> */?>
        <a id="edit-certificate" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> 
        <div class="info">
            <div id="lbl_certificate">
              <ul class="name_list">
                  <li><span>Certification on:</span> ISO 9001 <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Number:</span> 346345243</li>
                  <li><span>Organigation:</span> ABCD Org</li>
                  <li><span>Duration:</span> 01-11-2009  to  05-09-2010</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper. Sed rutrum luctus ipsum nec ultricies. Nam turpis felis, dictum nec facilisis nec, mattis eu urna. </p>
              <?/*
              <ul class="name_list">
                  <li><span>Certification on:</span> ISO 9001 <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Number:</span> 346345243</li>
                  <li><span>Organigation:</span> ABCD Org</li>
                  <li><span>Duration:</span> 01-11-2009  to  05-09-2010</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper. Sed rutrum luctus ipsum nec ultricies. Nam turpis felis, dictum nec facilisis nec, mattis eu urna. </p>
              */?>
            </div>
          <div id="frm_certificate">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">   
                
                <div id="add_more_certificate_wrapper">
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
                          <textarea id="s_desc" name="s_desc" rows="3" cols="34" class="clear botmar"></textarea> 
                            <?/*<input type="submit" value="Save" class="short" /> 
                            <input type="reset" value="Cancel" class="short" /></div></li>*/?>
                      </ul>                                
                </div>
                <a id="add_more_certificate" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>                
                    
          </div>
        </div>
        <h2 class="alignleft">License</h2>
        <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a>*/?>
        <a id="edit-license" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>  
        <div class="info">
            <div id="lbl_license">
              <ul class="name_list">
                  <li><span>Certification on:</span> License name  <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Number:</span> 346345243</li>
                  <li><span>Organigation:</span> ABCD Org</li>
                  <li><span>Duration:</span> 01-11-2009  to  05-09-2010</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper. Sed rutrum luctus ipsum nec ultricies. Nam turpis felis, dictum nec facilisis nec, mattis eu urna. </p>
              <?/*
              <ul class="name_list">
                  <li><span>Certification on:</span> License name  <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Number:</span> 346345243</li>
                  <li><span>Organigation:</span> ABCD Org</li>
                  <li><span>Duration:</span> 01-11-2009  to  05-09-2010</li>
              </ul>
              <p>DESCRIPTION: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque mattis est vitae sapien vulputate ullamcorper. Sed rutrum luctus ipsum nec ultricies. Nam turpis felis, dictum nec facilisis nec, mattis eu urna. </p>
              */?>
            </div>
            <div id="frm_license">
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value=""> 
                
                <div id="add_more_license_wrapper">
                      <ul class="name_list edit_panel">
                          <li class="nodevider"><span>Certification on:</span> <div class="edit_section">
                          <input id="s_token" name="s_token" type="hidden" value="">
                          <input id="s_license_name" name="s_license_name" type="text" class="botmar" value="License name" size="40" />
                          </div></li>
                          <li class="nodevider"><span>Number:</span> <div class="edit_section">
                          <input id="s_license_number" name="s_license_number" type="text" class="botmar" value="346345243" size="40" />
                          </div></li>
                          <li class="nodevider"><span>Organigation:</span> <div class="edit_section">
                          <input id="s_licensed_from" name="s_licensed_from" type="text" class="botmar" value="ABCD Org" size="40" />
                          </div></li>
                          <li class="nodevider"><span>Duration:</span> <div class="edit_section">
                          <input id="dt_from_license" name="dt_from_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" /> 
                          <p class="alignleft rightpad">-</p> 
                          <input id="dt_to_license" name="dt_to_license" type="text" class="calender alignleft" value="01 / 07 / 1980" size="15" />
                          </div></li>
                          <li class="nodevider"><span>DESCRIPTION:</span> <div class="edit_section">
                          <textarea id="s_desc" name="s_desc" rows="3" cols="34" class="clear botmar"></textarea> 
                          <?/*<input type="submit" value="Save" class="short" /> 
                          <input type="reset" value="Cancel" class="short" />*/?>
                          </div></li>
                      </ul>                    
                </div>
                <a id="add_more_license" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>                
            </div>             
          
        </div>
        <h2 class="alignleft">Skills &amp; Services offer's</h2> 
        <?/*<a class="short_grey_button alignright rightmar20" href="#">+ Add</a>*/?>
        <a id="edit-skill" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a>  
        <div class="info nomar">
            <div id="lbl_skill"> 
            
                <?php 
                    $count = count($user_skill);
                    $nomar='';
                    foreach($user_skill as $k=>$v){
                        if($count==$k+1)
                            $nomar = 'nomar';
                ?>
                <div class="skills <?=$nomar;?>">
                   <p class="with162 alignleft no-bot-pad" style="width:auto !important;">
                    <a href="javascript:void(0);" class="alignleft skili_count"><span id="endorse_count"><?=intval($v->i_endorse_count);?></span>
                    <?
                    if(get_userLoggedIn("id")!=$v->uid)
                    {
                    ?>
                        <span class="plus" id="<?=$v->id;?>">+</span>
                    <? }///end if?>
                    <?=$v->s_skill_name;?></a>
                    </p>
                    <ul id="endorse_ulist_<?=$v->id;?>" class="alignright">
                        <?php 
                        if(!empty($v->s_endorses_unserialized))
                        {
                            foreach($v->s_endorses_unserialized as $endorsed){
                                ?>
                                <li><?=theme_user_thumb_picture($endorsed["endorsed_by"]); ?> </li>
                            <?php }///end for
                        }///end if?>
                    </ul>
                </div>
                <?php }///end for?>            
                 
            </div>
            <div id="frm_skill">
                <div id="skill_edit" class="skil_edit">
                <?php foreach($user_skill as $k=>$v){?> 				
                    <a href="javascript:void(0);"><?=$v->s_skill_name;?><img src="<?=get_theme_path('guru_frontend/images')?>cross1.png" width="7" height="8" alt="cross" class='delete' rel="<?=$v->id;?>" /></a>					
                <?php }///end for ?> 
                
                <input id="form_token" name="form_token" type="hidden" value="">
                <input id="action" name="action" type="hidden" value="">                
                <input type="text" name="s_skill_name" id="s_skill_name" value="" size="26" placeholder="Type new skill ..."  />
                </div>
                                
            </div>
      </div>
    </div>
<!--LEFT PANEL END -->      
<!--RIGHT PANEL START -->                                  
    <div class="right_panel">
        <!--rank-->
        <?=theme_block_user_profile_rank($uid);?>
        <!-- rank -->
       
        <!-- connection -->
        <?=  theme_block_user_profile_connection($uid);?>
        <!-- connection -->
        
    </div>
<!--RIGHT PANEL END -->                 
</div>          
<!--MAIN PANEL END -->                       
</div>
<!--PANEL WITH LEFT SIDEBAR END -->    