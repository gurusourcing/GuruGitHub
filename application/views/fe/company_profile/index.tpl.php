<?php
  /**
  * Company  Profile,
  * Company Profile Edit
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
    /*$("#company_profile, #contact").inedit({*/
    $("#inline_edit").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "globalPrivacySettings": {
            "Public" : {"icon":"<?=trim( get_theme_path().'images/icon19.jpg');?>","css":""},
            "Private" : {"icon":"<?=trim( get_theme_path().'images/icon20.jpg');?>","css":""},
        },       
       "sections" : {
            0 : {
                "fieldContainer" : "#frm_company_name",
                "contentContainer" : "#lbl_company_name",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
                    contentContainer.text(values["s_company"]);
                }
            },
            1 : {
                "fieldContainer" : "#frm_about_company",
                "contentContainer" : "#lbl_about_company",
                "showButton" : "#edit-about_company",
                "defaultValues" : $.parseJSON('<?=$default_value[1];?>'),
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_operation");?>",  
                "beforeHideCallback" : function(contentContainer,values){
    
                    contentContainer.find("#longtxt_about").html(values["s_about_company"]);
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
            2 : {
                "fieldContainer" : "#frm_company_certificate",
                "contentContainer" : "#lbl_company_certificate",
                "showButton" : "#edit-company_certificate",
                "defaultValues" : $.parseJSON('<?=$default_value[2];?>'),
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_certificate_operation");?>",  
                
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
            3 : {
                "fieldContainer" : "#frm_company_license",
                "contentContainer" : "#lbl_company_license",
                "showButton" : "#edit-company_license",
                "defaultValues" : $.parseJSON('<?=$default_value[3];?>'),
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_license_operation");?>",  
                
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
            4 : {
                "fieldContainer" : "#frm_company_link",
                "contentContainer" : "#lbl_company_link",
                "showButton" : "#edit-company_link",
                "defaultValues" : $.parseJSON('<?=$default_value[4];?>'),
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
            },
            5 : {
                "fieldContainer" : "#frm_location",
                "contentContainer" : "#lbl_location",
                "defaultValues" : $.parseJSON('<?=$default_value[5];?>'),
                "showButton" : "#edit-location",
                "ajaxSaveUrl"   : "<?=base_url("company_profile/ajax_operation");?>",  
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
					
					 /*to hide contact view 19 feb 2014*/
					<?php if(!$contact_view){ ?>				
					$("#lbl_location").parent('div').css('display','none');
					<?php } ?>
					
                }
            },                                        
       }
    });
    

	
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
    
    
    // setup ul.tabs to work as tabs for each div directly under div.panes
    //$("#twitter_feed").tabs();        
    //$("ul.tabs").tabs("div.panes > div");    
    /**
    * Blog and Twitter tabs
    */
    $("#twitter_feed ul.tabs li").each(function(k){
       
       ////default display settings///
       /*if(k>0)
         $("#twitter_feed div.panes > div").get(k).hide();*/
        
       $(this).on("click",function(){
          ////hide all tabs//
          $("#twitter_feed div.panes > div").each(function(m){
             $(this).stop(true,true).fadeOut("slow"); 
			 
			 /* added on 30 Nov 2013 */
			 if(k==0)
			 {
			 	$("#twitter_feed ul.tabs li a:last").attr('class','');
				$("#twitter_feed ul.tabs li a:first").attr('class','current');
			 }
			 else if(k==1)
			 {
				$("#twitter_feed ul.tabs li a:first").attr('class','');
				$("#twitter_feed ul.tabs li a:last").attr('class','current');
			 }
			 /* added on 30 Nov 2013 end */
			 
             if(k==m)
             {
                 $(this).stop(true,true).fadeIn("slow");
             }              
          });            
       }); 
    });
    
    
    $('#blog_txt').more('destroy');
                    $('#blog_txt').more({
                        length: 300,
                        ellipsisText: '', 
                        moreText: '+ Show more',
                        lessText: '- Show less',
                    });
	 $('#twitt_txt').more('destroy');
                    $('#twitt_txt').more({
                        length: 300,
                        ellipsisText: '', 
                        moreText: '+ Show more',
                        lessText: '- Show less',
                    });
	
					
    /**
    * unique skills 
    * if skill name is repeated we need to
    * merge it into one
    */
    var t_skill=new Array();
    $("div[id='comp_skills']").each(function(i){
       var title=$(this).attr("rel");
       
       if($.inArray( title, t_skill )<0)
       {
           t_skill.push(title);
       }
       else
       {
           $(this).attr("rel",title+"_");
           var endorses=$(this).find("ul[id^='endorse_ulist_']").html();
           
           $("div[id='comp_skills'][rel='"+title+"'] ul[id^='endorse_ulist_'] li:last")
           .after( endorses );
                      
           ///count endorse_count
           var endorseCount=$("div[id='comp_skills'][rel='"+title+"'] ul[id^='endorse_ulist_'] li").length;
           $("div[id='comp_skills'][rel='"+title+"'] #endorse_count").text( endorseCount );           
           $(this).hide();
       }
        
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
               
                $("#state_name").attr("value",ui.item.state_name);
                $("#zip_code").attr("value",ui.item.zip_code);
              
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
    
    
    
});    
});

    
function certificateFldSettings()
{
jQuery(function($){
   $(document).ready(function(){
           
    ////date pickers///
    $("div[class='edit_section'] input[id^='dt_from_certificate'],div[class='edit_section'] input[id^='dt_to_certificate']").each(function(){
        
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
                    $( "div[class='edit_section'] input[id^='dt_from_certificate']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "div[class='edit_section'] input[id^='dt_to_certificate']" ).datepicker( "option", "maxDate", selectedDate );
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
    $("div[class='edit_section'] input[id^='dt_from_license'],div[class='edit_section'] input[id^='dt_to_license']").each(function(){
        
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
                    $( "div[class='edit_section'] input[id^='dt_from_license']" ).datepicker( "option", "minDate", selectedDate );
                else
                    $( "div[class='edit_section'] input[id^='dt_to_license']" ).datepicker( "option", "maxDate", selectedDate );
            }            
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
           
            <?=theme_block_company_profile_pic($comp_id);?>
            <!--profile pic end-->
            <!--short url -->
            <?=theme_block_company_profile_short_url($comp_id);?>
            <!--short url -->
                <div class="panel_info facebook_fans nopad" id="contact">
                    <p class="name">Contact Info<a id="edit-location" rel="edit-location" href="javascript:void(0);" class="alignright edit" title="Edit">Edit</a></p>
                    <div id="lbl_location"></div>
                    <div id="frm_location" class="edit_section">  
                        <input id="form_token" name="form_token" type="hidden" value="">
                        <input id="action" name="action" type="hidden" value="">
                        <input id="country_id" name="country_id" type="hidden" value="">
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

                <? /* <div class="panel_info">
                    <h3><a href="#"><img src="images/icon1.jpg" width="18" height="12" alt="icon" /> Share with Friend</a></h3>
                </div>
                <div class="panel_info">
                    <h3><a href="#"><img src="images/icon2.jpg" width="18" height="18" alt="icon" /> Share via Facebook</a></h3>
                </div>
                <div class="panel_info">
                    <h3><a href="#"><img src="images/icon3.jpg" width="18" height="18" alt="icon" /> Share via Twitter</a></h3>
                </div>
                <div class="panel_info">
                    <h3><a href="report-abouse.html"><img src="images/icon4.jpg" width="18" height="18" alt="icon" /> Report  abuse</a></h3>
                </div>
                <div class="panel_info facebook_fans">
                    <p class="name">Facebooks fans</p>
                    <div class="facebook">
                        <p class="botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
                           <img src="images/test2.jpg" width="173" height="174" alt="test" />
                    </div>
                    <a href="#" class="orange_button">View All</a>
                </div> */ ?>
               
                    <!--Share with Friend -->
                    <?= theme_block_user_profile_share_with_friend($comp_id,"company");?>
                     <!--Share with Friend -->
                     
                     <!--Share via Facebook -->
                     <?=theme_block_user_profile_share_via_facebook($comp_id,"company");?>
                    <!--Share via Facebook -->
                    
                    <!--Share via Twitter -->
                    <?=theme_block_user_profile_share_via_twitter($comp_id,"company")?>
                    <!--Share via Twitter -->
                  
                    <!--Report Abuse -->
                    <?=theme_block_company_profile_report_abuse($comp_id)?>
                    <!--Report Abuse -->
					
					
					
					<!-- Company User Apr 2014 -->   
				   <?
					if( !empty($service_provider))
				   { 
				   ?> 
					<?php /*?><div class="panel_info facebook_fans">
						<p class="name">Company Users</p>
						<div class="facebook">
							<p class="botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. </p>
							<?php 
								if(!empty($service_provider))
								{
									foreach($service_provider as $k=>$pic)
									{
										echo theme_user_thumb_picture($pic->uid,'','style="margin: 5px;"');
									}
								}
							?>
						</div>
						<a href="<?=site_url('company_employee/other_company_employee/'.encrypt(get_userCompany($pic->comp_id)));?>" class="orange_button" >View All</a>
					</div><?php */?>
				   <?
					}
				   ?>
				
					<!-- Company User Apr 2014 -->
                    
                    <!--Facebook Fan -->
                    <?=theme_block_user_profile_facebook_fan()?>
                    <!--Facebook Fan -->
              </div>
<!--LEFT SIDEBAR END -->   
<!--MAIN PANEL START -->
            <div class="main_panel">
<!--LEFT PANEL START -->            
        <div id="company_profile" class="left_panel">
            <!--<h1 class="alignleft botmar30">-->
          
        <? /* Company Name */ ?>
            <h1 class="alignleft relative right40">
                <strong id="lbl_company_name" class="alignleft"></strong>
                 <div id="frm_company_name">
                    <input id="form_token" name="form_token" type="hidden" value="">
                    <input id="action" name="action" type="hidden" value="">                
                    <input id="s_company" name="s_company" type="text" value="" />
               </div> 
               <a id="edit-company_name" rel="edit-company_name" href="javascript:void(0);" class="right-top edit" title="Edit">Edit</a>
        </h1> 
        <? /* Company Name end*/ ?> 
        
                  
         <? /* SERVICE OFFERED */ ?>         
                  
            <h2 class="clear_left">Services offered</h2>  
               <div class="info">
                  <ul class="name_list">
                 
                  <?php if(!empty($service_offered))
                  {
                    $cnt=count($service_offered);
                    foreach($service_offered as $i=>$so)
                    {  
                          $style=($cnt==$i+1)?"class='nodevider'":"";  
                  ?>
                     <li <?=$style;?>>
                         <?= $so["s_service_name"];?>
                         <span class="grey">(<?=$so["i_service_provider"];?> service provider(s))</span> 
                         <a class="short_grey_button alignright" href="<?=site_url($so['s_short_url']);?>">View Detail</a>
                     </li>
                     <? /* <li>Web Design &amp; Development <span class="grey">(1 service provider)</span> 
                      <a class="short_grey_button alignright" href="#">View Detail</a>  </li> */ ?>
                      
                  <?php
                    } 
                  }
                  ?>
                   </ul>
                 </div>  
         <? /* SERVICE OFFERED end*/ ?>  
                           
           
        <? /* ABOUT COMPANY */ ?>
             
           <h2 class="clear_left">About <? /*<span id="lbl_company_name"></span> */ ?><a href="javascript:void(0);" id="edit-about_company" class="alignright rightmar20 edit" title="Edit">Edit</a></h2>
           <div class="info">
                        <div id="lbl_about_company">
                            <p id="longtxt_about"></p>
                        </div>
                        <div id="frm_about_company">
                            <input id="form_token" name="form_token" type="hidden" value="">
                            <input id="action" name="action" type="hidden" value="">     
                            <textarea id="s_about_company" name="s_about_company" rows="3" cols="55" class="clear botmar"></textarea>
                        </div>
                    </div>
        <? /* ABOUT COMPANY  end*/ ?>
           
                    
           <? /* SERVICE PROVIDER */ ?>

             <h2>Service Providers</h2>
                 <div class="info">
                 <?php $cn=count($service_provider);
                        $style="";
                 ?>
                     <ul class="name_list">
                 <?php foreach($service_provider as $k=>$s):
                        $style=($cn==$k+1)?"class='nodevider'":"";
                 ?>          
                        <li <?=$style;?>>
                            <?=theme_user_thumb_picture($s->uid,"","class='alignleft'");?>
                            <a href="<?=site_url(short_url_code($s->uid));?>"><strong><?=get_user_display_name($s->uid,'');?></strong></a>
                            <p class="short"><span><?=$s->s_title;?></span></p>
                        </li>
                 <?php endforeach; ?>
                        </ul>
                      
                </div>
              <? /* SERVICE PROVIDER  end*/ ?> 
              
              
              <? /* COMPANY CERTIFICATE */ ?> 
                <h2>Company Certification<a id="edit-company_certificate" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a> </h2>
                <div class="info">
                    <div id="lbl_company_certificate">
                        <ul class="name_list">
                            <li><span>Certification on:</span> ISO 9001</li>
                            <li><span>Number:</span></li>
                            <li><span>Organigation:</span></li>
                            <li><span>Duration:</span></li>
                        </ul>
                        <p>DESCRIPTION: </p>
                    </div>
                    <div id="frm_company_certificate">
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
                                    <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                      </ul>                                
                </div>
                        <a id="add_more_certificate" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>                
                   </div>
                </div>
               <? /* COMPANY CERTIFICATE end */ ?> 
               
               <? /* COMPANY LICENSE */  ?>
                    <h2>Company License <a id="edit-company_license" href="javascript:void(0);" class="alignright rightmar20 edit" title="Edit">Edit</a></h2> 
                    <div class="info">
            <div id="lbl_company_license">
              <ul class="name_list">
                  <li><span>Certification on:</span> License name  <a href="#" class="right-top edit" title="Edit">Edit</a></li>
                  <li><span>Number:</span></li>
                  <li><span>Organigation:</span></li>
                  <li><span>Duration:</span></li>
              </ul>
              <p>DESCRIPTION: </p>
             
            </div>
            <div id="frm_company_license">
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
                          <textarea id="s_desc" name="s_desc" rows="3" cols="45" class="clear botmar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis placerat commodo libero, eget porta urna congue vitae. Donec venenatis vulputate massa.</textarea> 
                          
                          </div></li>
                      </ul>                    
                </div>
                <a id="add_more_license" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>                
            </div>             
          
        </div>   
       <? /* COMPANY LICENSE end*/ ?> 
               
                    
               <? /* LINK */?>
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
                <a id="add_more_link" class="short_grey_button alignright rightmar20" href="javascript:void(0);">+ Add</a>                
            </div>
       </div>
                    
                    
               <? /* LINK end*/?> 
                    
               <? /* Skills */?>                          
                  <h2>Skills</h2> 
                  <div class="info">
                   <?php 
                        $count = count($skill);
                        $nomar='';
                        
                        foreach($skill as $k=>$v){
                            if($count==$k+1)
                                $nomar = 'nomar';
                    ?>
                        <div id="comp_skills" class="skills <?=$nomar;?>" rel="<?=trim($v->s_skill_name);?>" >
                           <p class="with162 alignleft no-bot-pad">
                            <a href="javascript:void(0);" class="alignleft skili_count"><span id="endorse_count"><?=intval($v->i_endorse_count);?></span>
                           
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
               <? /* Skills end*/?>
               
                         
               
               <? /*  twitter feed */?>
                        <div id="twitter_feed">
                            <ul class="tabs">
                                <li><a class="current" href="javascript:void(0);">Blog</a></li>
                                <li><a href="javascript:void(0);">Twitter Feed</a></li>
                            </ul>
                            <div class="panes">
                                <div style="display: block;">
                                    <?php /*?><h3 class="border_bottom">Lorem ipsum dolor sit amet, consectetur adipiscing elit, Fusce ac 
    molestie elit. Maecenas dictum justo nec tortor porttitor volutpat.</h3>
                                    <p class="grey">Jan. 5, 2012</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris ut sem tortor. Etiam aliquet pharetra ullamcorper. Nunc tristique lectus id turpis  ligula commodo. Morbi aliquam tincidunt arcu, sit amet porttitor eros lacinia vitae. Cras tristique ultrices nulla at rutrum. Phasellus tempus viverra nisi, eget laoreet est faucibus non. Donec vel tincidunt diam. Sed augue mauris, venenatis a ultrices non, rutrum sed risus. Nunc vel leo pharetra justo dictum ultrices. </p>
                                    <p>Etiam cursus metus nec nulla luctus rhoncus eu sed augue. Donec eget velit non elit volutpat imperdiet quis non metus. [...]   <a href="#">More</a></p><?php */?>
									
									<h3 class="border_bottom"><?php echo $blog_title; ?></h3>
									<span id="blog_txt"><?php echo $blog_description; ?></span>
									
                                </div>
                                <div style="display: none; height:370px; overflow:hidden;" id="tw" >
                                  <?php /*?><h3 class="border_bottom">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                                    <p class="grey">Mar. 5, 2012</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris ut sem tortor. Etiam aliquet pharetra ullamcorper. Nunc tristique lectus id turpis  ligula commodo. Morbi aliquam tincidunt arcu, sit amet porttitor eros lacinia vitae. Cras tristique ultrices nulla at rutrum. Phasellus tempus viverra nisi, eget laoreet est faucibus non. Donec vel tincidunt diam. Sed augue mauris, venenatis a ultrices non, rutrum sed risus. Nunc vel leo pharetra justo dictum  <a href="#">More</a></p><?php */?>
									
									<?php if($twitt_feeds) {
											foreach($twitt_feeds as $key=>$val)
												{
									 ?>
									
                                    <p class="grey"><?php echo $val->text; ?><br />
									<span style="font-size:12px;"><?php echo date("M d,Y",strtotime($val->created_at)); ?></span> </p>
									<?php 
											} 
										}
									?>
									
                                </div>                              
                                
                            </div>    
                        </div>
    
               
				<script type="text/javascript">
				/* for autscroll the twitter div */
				function autoScroll() {
					var itemHeight = $('#tw p').outerHeight();
					/* calculte how much to move the scroller */
					var moveFactor = parseInt($('#tw').css('top')) + itemHeight;
					/* animate the carousel */
					$('#tw').animate({
						'top' : moveFactor
					}, 'fast', 'linear', function(){
						/* put the last item before the first item */
						$("#tw p:first").before($("#tw p:last"));
						/* reset top position */             
						$('#tw').css({'top' : '-6em'});
					});
				};
				/* make the carousel scroll automatically when the page loads */
				var moveScroll = setInterval(autoScroll, 6000);
				
				</script>
				
				 <? /*  twitter feed end */?>
				

 <!-- This JavaScript snippet activates those tabs -->
 <?/*
<script>

// perform JavaScript after the document is scriptable.
jQuery(function($){
$(document).ready(function(){
    
    
    // setup ul.tabs to work as tabs for each div directly under div.panes
    $("ul.tabs").tabs("div.panes > div");    
    
}); 
});
</script>
*/?>

                </div>
<!--LEFT PANEL END -->      
<!--RIGHT PANEL START -->                                  
                <div class="right_panel">
                 <? /* <div class="info">
                        <ul class="rank">
                            <li><img src="images/icon6.jpg" width="11" height="10" alt="icon" /> Rank <span>97</span></li>
                            <li><img src="images/icon7.jpg" width="16" height="10" alt="icon" /> Views <span>43</span></li>
                            <li><img src="images/icon8.jpg" width="14" height="12" alt="icon" /> Recommendation <span>32</span></li>
                        </ul>
                    </div> */ ?>
                     <!--rank-->
                    <?=theme_block_user_profile_rank($comp_id,'company');?>
                    <!-- rank -->
                    
                   <? /*  <div class="info aligncenter">
                        <p>To see the connection</p>
                        <a href="#" class="grey_button">Login in to your FB</a>
                    </div>
                    <div class="info aligncenter">
                        <p>To see the connection</p>
                        <a href="#" class="grey_button">Add your FB account</a>
                    </div>
                    <div class="yellow_massage">You are not connected !</div>
                    <div class="info">
                        <p>You are connected to Atanu Samanta as below</p>
                        <div class="connect">
                            <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="top" />
                            <ul>
                                <li><span>Mr. Abir (You)</span></li>
                                <li><span>Manika</span></li>
                                <li><span><a href="#">Atanu Samanta</a></span></li>                                
                          </ul>
                            <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="bottom" />
                        </div>
                        <span class="skills aligncenter"><strong>and</strong></span>
                        <div class="connect">
                            <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="top" />
                            <ul>
                                <li><span>Mr. Abir (You)</span></li>
                                <li><span>Manika</span></li>
                                <li><span><a href="#">Sanhita Sinha</a></span></li>                                
                          </ul>
                            <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="bottom" />
                        </div>
                        <p>Want to know about &nbsp; Atanu Samanta / Sanhita Sinha ?</p>
                        <a href="#" class="grey_button">Ask</a>
                    </div>
                    */ ?>
                    
                     <!-- connection -->
                    <?=  theme_block_user_profile_connection(intval($owner_id));?>
                    <!-- connection -->
                </div>
<!--RIGHT PANEL END -->                 
            </div>          
<!--MAIN PANEL END -->                       
        </div>
<!--PANEL WITH LEFT SIDEBAR END -->          
    </div>
<!--INNER BODY END -->