<? $user_data = short_desc(get_userLoggedIn('id'));?>
<?/*<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">-->*/?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<base href="<?php echo base_url(); ?>" />
<title><? print $page_title;?> :: <?=site_name();?> </title>

<? print $header; ?>
<script language="javascript">
jQuery(function($){
    
$(document).ready(function(){
    /**
    *  "add_to_favourite" is the class which need to be added with the element 
    *   on which the click event will tirgger 
    *   and add the service to 'user_favourite_service' table 
    *   by calling ajaxAddFavouriteService() function. 
    *   this function excepts one parameter, 
    *   the 'service_id' which is a attribute of that element.
    */
    var favourite={"service_id":0,"s_msg":""};//global var
    
    $('.add_to_favourite').each(function(i){
    
        $(this).click(function(){
            
            favourite.service_id=$(this).attr('service_id');
            
            <?php /*?>var html='<label for="name">Message</label><textarea name="s_message_common" id="s_message_common" class="text ui-widget-content ui-corner-all" cols="35"/></textarea>';
            $( "#dialog-common" ).find("#dialog_msg").html(html);
            $( "#dialog-common" ).dialog( "open" ); <?php */?>   
			
			$.post("<?=site_url("favourites/ajaxAddFavouriteService")?>",
                    favourite,
                    function(data)
                    {
                        $( "#dialog-common" ).find("#dialog_msg").html("");
						$( "#dialog-common" ).dialog( "open" );
						$("#dialog-common").css('height','36px');
						if(data=="success")
                        {							
                            $( "#dialog-common" ).find("#dialog_msg").html("Successfully added to your favourite.");
                        }
						else if(data=="login_error")
						{
							$( "#dialog-common" ).find("#dialog_msg").html("Please login to your favourite.");
						}
						else
						{
                            $( "#dialog-common" ).find("#dialog_msg").html("Add to favourite failed to save.");							
						}
						
						$(".ui-dialog-buttonpane").hide();
                        //$(this).dialog("close");
                        //$( "#dialog-common" ).dialog("close");
                    }
                    ); 
            
        });
        
    });
    

       
       $( "#dialog-common" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            modal: true,
            draggable: true,
            buttons: {
            save: function() {
                
                favourite.s_msg=$("#s_message_common").attr("value");
                
                $.post("<?=site_url("favourites/ajaxAddFavouriteService")?>",
                    favourite,
                    function(data)
                    {
                        if(data)
                        {
                            $( "#dialog-common" ).find("#dialog_msg").html(data);
                        }
                        //$(this).dialog("close");
                        $( "#dialog-common" ).dialog("close");
                    }
                    );
            },
            Cancel: function() {
                $('.ui-dialog-buttonset').find('button:first').show();
                $('.ui-dialog-buttonset').find('button:last').find('span').html('Cancel'); 
                $(this).dialog("close");
            }
            }
     });

   
    //===== Notification boxes =====//
    
    $(".nNote").click(function() {
        $(this).fadeTo(200, 0.00, function(){ //fade
            $(this).slideUp(200, function() { //slide up
                $(this).remove(); //then remove from the DOM
            });
        });
    });       
   
   
    /**
    * global_country_id change
    */
    $("#global_country_id").on("change",function(){
       var global_country_id=$(this).find("option:selected").attr("value");
       $.post("autocomplete/ajaxSetGlobalCountry",
            {"global_country_id":global_country_id},
            function(data){
                if(data=="success")
                {
                    window.location.href='<?=current_url();?>';
                }
            }
       ); 
    });
    
    
    /**
    * Service category OR People AutoComplete
    */
     /*$( "#search_type_value" ).on("click",function(){
            $("#search_cat_id").attr("value","");
            $("#search_uid").attr("value","");    
     });*/
     $( "#search_type_value" ).autocomplete({
        source: "<?=site_url("autocomplete/ajax_searchTypeValue")?>",
        minLength: 2,
        select: function( event, ui ) {
            
            if(ui.item)
            {
                if($("#search_type").attr("value")=="service")
                {
                    $("#search_cat_id").attr("value",ui.item.id);
                    $("#search_uid").attr("value","");
                }
                else
                {
                    $("#search_uid").attr("value",ui.item.id);
                    $("#search_cat_id").attr("value","");
                }
            }
        },
        search: function( event, ui ) {
            $("#search_cat_id").attr("value","");
            $("#search_uid").attr("value","");
        }
    });
    
    
    
    /**
    * Location AutoComplete
    */
     $( "#location_type_value" ).autocomplete({
        source: "<?=site_url("autocomplete/ajax_locationTypeValue")?>",
        minLength: 2,
        select: function( event, ui ) {
          
            if(ui.item)
            {
                $("#search_zip_id").attr("value",ui.item.zip_id);
                $("#search_city_id").attr("value",ui.item.city_id);                     
            }
            
            /*console.log( ui.item ?
            "Selected: " + ui.item.value + " aka " + ui.item.id :
            "Nothing selected, input was " + this.value );*/
        },
        search: function( event, ui ) {
            $("#search_zip_id").attr("value","");
            $("#search_city_id").attr("value","");
        }
		
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
         
			   var re = new RegExp(item.label, "ig") ;
			   
			   if(item.header_row=='Y')
			   {
			   	   var t = item.label.replace(re,"<span style='background-color:#229BCA; color:white;display: block;padding: 3px 0;text-align: center; font-weight:bold;'>" + item.label + "</span>");			   
				   // for result
				   span_result_html = '<span>'+ t +'</span>';			   
				   li_html = span_result_html;
				   a_href_html = ''+ li_html +'';
				   
				   
				   return $( '<li></li>' )
					.data( "item.autocomplete", item )
					.append( a_href_html )
					.appendTo( ul );
				}
				else 
				{
					var t = item.label.replace(re,item.label);			   
					// for result
				   span_result_html = '<span>'+ t +'</span>';			   
				   li_html = span_result_html;
				   a_href_html = '<a class="partition_item ui-corner-all" tabindex="-1">'+ li_html +'</a>';
				   
				   
				   return $( '<li></li>' )
					.data( "item.autocomplete", item )
					.append( a_href_html )
					.appendTo( ul );
				}
				
  	 };
    
    
    //////Autocomplete Mandatory Fields////
    $(document).ajaxSend(function(e, xhr, settings) {
        //console.log(settings);
        //passing countryid in zip auto complete//
        var u='<?=site_url("autocomplete/ajax_searchTypeValue?term=");?>'+$("#search_type_value").attr("value");
        var u2='<?=site_url("autocomplete/ajax_locationTypeValue?term=");?>'+$("#location_type_value").attr("value");
        
        ////for search_type_value
        if (settings.url == u) {
            settings.url=settings.url+'&search_type='+$("#search_type").attr("value");
        }        
        else if(settings.url == u2) ////for location_type
        {
            settings.url=settings.url+'&location_type='+$("#location_type").attr("value");
        }         
        
    });       
    
    
    
    //FOOTER MEMBER LINK
    $("ul#footer_member_link li a").click(function(){
        /*console.log(
            $("select[id='search_type']").next()/*.find("li a[rel='user']")* /
        );*/
        
        $("select[id='search_type']").find("option[value='user']").attr("selected",true);
        $("#search_type_value").val($(this).attr('rel'));
        $("#top_search_form").submit();
    });
    
    
    
});///end document    


$("#search_type").selectbox();
$("#location_type").selectbox();
$("#distance").selectbox();
    
});





</script>

</head>

<body>
<!-- <a href="javascript:void(0);" class="add_to_favourite" service_id="1" >click</a> -->
<?php
///common Dialog Box///
?>
<div id="dialog-common" style="display: block;" title="Message">
    <p><!-- <span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span> -->
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end common Dialog Box///
?>




<!--TOP PANEL START -->
<div class="top_panel">
<ul class="alignleft">
    	<li><a href="<?=site_url("cms/".get_cms(4,"s_url"));?>">How it Works?</a></li>
        <li><a href="<?=site_url("cms/".get_cms(14,"s_url"));?>">List your Service</a></li>
    </ul>
    <div class="language">
        <?=form_dropdown("global_country_id",dd_country(),get_globalCountry(),'id="global_country_id"');?>        
    </div>
    <ul class="alignright">
    	<li>Welcome  <span class="blue"><?=is_userLoggedIn()?$user_data->name:'<a class="blue" href="'.site_url("account/signin").'">Guest</a>'?></span>
        <?if(is_userLoggedIn()):?>
        <div class="link_panel">
            <h3><img src="<?=site_url($user_data->s_profile_photo?$user_data->s_profile_photo:get_theme_path().'images/no-pic.jpg')?>" width="23" height="23" alt="people" /> <?=$user_data->name?></h3>
            <ul>
                <li><a href="<?=site_url("dashboard")?>">Dashboard</a></li>
                <li><a href="<?=site_url("user_profile")?>">Profile  <?=intval(get_user_profile_complete($user_data->id));?>% completed</a></li>
                
                <li><a href="javascript:void(0);">Service(s) <strong class="blue"><?=intval($user_data->total_services)?></strong></a></li>
                <li><a href="<?=site_url("account/signout")?>">Sign out</a></li>
            </ul>
    	</div>
        <? endif;?>    
        </li>
    </ul>
    <a href="<?=site_url();?>" class="logo aligncenter"><img  src="<?=base_url(get_theme_path())."/";?>images/logo.png" width="142" height="33" alt="logo" /></a>
</div>
<!--TOP PANEL END -->
<!--HEADER PANEL START -->
<div class="header_panel">
	<div class="inner_header">&nbsp;</div>
</div>
<!--HEADER PANEL END -->
<!--INNER SEARCH PANEL START -->
    <div class="inner_search">
        <div class="search_panel search_big_panel">
            <form name="top_search_form" id="top_search_form"  action="<?=site_url("search_engine");?>" method="post">
                <input type="hidden" id="search_cat_id" name="search_cat_id" value="<?=@$posted["search_cat_id"];?>">
                <input type="hidden" id="search_uid" name="search_uid" value="<?=@$posted["search_uid"];?>">
                <input type="hidden" id="search_zip_id" name="search_zip_id" value="<?=@$posted["search_zip_id"];?>">
                <input type="hidden" id="search_city_id" name="search_city_id" value="<?=@$posted["search_city_id"];?>">                
                
              <?/*<select class="alignleft blue-bg" id="search_type" name="search_type">
                <option value="service" <?php if(@$posted['search_type']=="service") echo "selected='selected'";?>>Service</option>
                <option value="user" <?php if(@$posted['search_type']=="user") echo "selected='selected'";?>>People</option>
              </select>*/?>
              <?=form_dropdown("search_type",
                    dd_search_type(),
                    @$posted['search_type'],
                    'id="search_type" class="alignleft blue-bg"'
                    );?>
              <input id="search_type_value" name="search_type_value" type="text" class="textbox alignleft rightmar" value="Keyword" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';" />
              <?/*<select class="alignleft" id="location_type" name="location_type">
                <option value="zip" <?php if(@$posted['location_type']=="zip") echo "selected='selected'";?>>Zip</option>
                <option value="city" <?php if(@$posted['location_type']=="city") echo "selected='selected'";?>>City</option>
              </select>*/?>
              <?=form_dropdown("location_type",
                    dd_location_type(),
                    @$posted['location_type'],
                    'id="location_type" class="alignleft"'
                    );?>
              <input id="location_type_value" name="location_type_value" type="text" class="textbox alignleft rightmar" value="City/ Zip" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';" />
              <?/*<select class="alignleft select_topbg" id="distance" name="distance">
                <option value="">Distance</option>
                <option value="1-10" <?php if(@$posted['distance']=="10") echo "selected='selected'";?>>1-10 Km</option>
                <option value="10-50" <?php if(@$posted['distance']=="50") echo "selected='selected'";?>>10-50 Km</option>
                <option value="50-100" <?php if(@$posted['distance']=="100") echo "selected='selected'";?>>50-100 Km</option>
              </select>*/?>
              <?=form_dropdown("distance",
                    dd_distance(),
                    @$posted['distance'],
                    'id="distance" class="alignleft select_topbg"'
                    );?>
             <input id="submit_search" class="alignleft leftmar rightmar" type="image" title="Search"  src="<?=base_url(get_theme_path())."/";?>images/search-button.png">
              <input id="keep_filter" name="keep_filter" type="checkbox" class="checkbox alignleft" value="1" <?php if($posted["keep_filter"]==1){?> checked="checked" <?php } ?> /> Keep filter
          </form>
       </div>
    </div>
<!--INNER SEARCH PANEL END -->
<!--INNER BODY START -->
    <div class="contaner">    	
        <?php show_msg();?>
        <div clas="clear"></div>
            <?php print $main_content; ?>
    </div>
<!--INNER BODY END -->
<!--FOOTER PANEL START -->
<div class="footer_panel">
    <? if(!is_userLoggedIn()):?>
    <ul class="center">
    	<li><a href="<?=site_url('account/signup')?>"><img  src="<?=base_url(get_theme_path())."/";?>images/social-icon1.png" width="149" height="37" alt="social" /></a></li>
        <li><a href="<?=site_url('account/signin')?>"><img  src="<?=base_url(get_theme_path())."/";?>images/social-icon2.png" width="129" height="37" alt="social" /></a></li>
        <li><a href="javascript:void(0)"><img onclick='facebook_connect_init()' src="<?=base_url(get_theme_path())."/";?>images/social-icon3.png" width="202" height="37" alt="social" /></a></li>
    </ul>
    <? endif;?>
    <div class="footer_bottom top_pad">
    	<ul class="link alignleft" id='footer_member_link'> 
        	<li>guru.in member directory: </li>
            <li><a href="javascript:void(0);" rel="A">A</a></li>
            <li><a href="javascript:void(0);" rel="B">B</a></li>
            <li><a href="javascript:void(0);" rel="C">C</a></li>
            <li><a href="javascript:void(0);" rel="D">D</a></li>
            <li><a href="javascript:void(0);" rel="E">E</a></li>
            <li><a href="javascript:void(0);" rel="F">F</a></li>
            <li><a href="javascript:void(0);" rel="G">G</a></li>
            <li><a href="javascript:void(0);" rel="H">H</a></li>
            <li><a href="javascript:void(0);" rel="I">I</a></li>
            <li><a href="javascript:void(0);" rel="J">J</a></li>
            <li><a href="javascript:void(0);" rel="K">K</a></li>
            <li><a href="javascript:void(0);" rel="L">L</a></li>
            <li><a href="javascript:void(0);" rel="M">M</a></li>
            <li><a href="javascript:void(0);" rel="N">N</a></li>
            <li><a href="javascript:void(0);" rel="O">O</a></li>
            <li><a href="javascript:void(0);" rel="P">P</a></li>
            <li><a href="javascript:void(0);" rel="Q">Q</a></li>
            <li><a href="javascript:void(0);" rel="R">R</a></li>
            <li><a href="javascript:void(0);" rel="S">S</a></li>
            <li><a href="javascript:void(0);" rel="T">T</a></li>
            <li><a href="javascript:void(0);" rel="U">U</a></li>
            <li><a href="javascript:void(0);" rel="V">V</a></li>
            <li><a href="javascript:void(0);" rel="W">W</a></li>
            <li><a href="javascript:void(0);" rel="X">X</a></li>
            <li><a href="javascript:void(0);" rel="Y">Y</a></li>
            <li><a href="javascript:void(0);" rel="Z">Z</a></li>
        </ul>
        <p class="alignright">Copyright &copy; <?=date('Y');?>
            <a href="<?=base_url()?>"> guru.in</a>
        </p>
        <ul class="links alignright"> 
            <li><a href="<?=site_url("cms/".get_cms(1,"s_url"))?>">User Agreement</a> </li>
            <li><a href="<?=site_url("cms/".get_cms(2,"s_url"))?>">About GURU.in</a></li>
            <li><a href="<?=site_url("cms/".get_cms(3,"s_url"))?>">Privacy Policy</a></li>
        </ul>
    </div>
</div>
<!--FOOTER PANEL END -->
<?php include_once(APPPATH."views/fe/common/facebook_js.php"); ?> 
</body>
</html>
<script language="javascript">

//var int=setInterval(function(){hide_common_dialog()},2000);
hide_common_dialog();
function hide_common_dialog()
{
    
jQuery(function($){
    
$(window).ready(function(){
    
    $("span[id='dialog-message-icon']").each(function(){
        $(this).hide(); 
        //console.log("@hided");
        //int=window.clearInterval(int);
    });
    
    
    
         

});
});    
      
}
</script>
