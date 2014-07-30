<? $user_data = short_desc(get_userLoggedIn('id'));?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
            
            var html='<label for="name">Message</label><textarea name="s_message_common" id="s_message_common" class="text ui-widget-content ui-corner-all" cols="35"/></textarea>';
            $( "#dialog-common" ).find("#dialog_msg").html(html);
            $( "#dialog-common" ).dialog( "open" );     
            
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
                        $(this).dialog("close");
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
     $("#search_type_value" ).autocomplete({
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
        //console.log($(this).attr('rel'));
        $("select[id='search_type']").find("option[value='user']").attr("selected",true);
        $("#search_type_value").val($(this).attr('rel'));
        $("#top_search_form").submit();
        
    });
          
    
    
    
});///end document       
    
    
$("#search_type").selectbox();
$("#location_type").selectbox();
$("#distance").selectbox(); 
    
});


/*$(function () {

});*/
</script>
      <script type="text/javascript">
var jap = jQuery.noConflict();
  jap(document).ready(function() {
  if(1==0)
  {
  var check_cookie = jap.cookie('the_cookie459');
  if (jap.cookie('the_cookie459')) {
        // it hasn't been twenty days yet
    }
	else{
	jap.cookie('the_cookie459', 'true' , { expires: 1});
	jap('.fancybox-effects-a').fancybox({
		modal: false,
		helpers: {overlay: {opacity: 0.7}}
	}).trigger('click');	
	}    
  }
  else{
  jap('.fancybox-effects-a').fancybox({
		modal: false,
		helpers: {overlay: {opacity: 0.7}}
	}).trigger('click');
  }
     });
</script>
<?php include_once(APPPATH."views/fe/common/facebook_js.php"); ?> 
</head>

<body>

<!--TOP PANEL START -->
<div class="top_panel">
	<ul class="alignleft">
    	<li><a href="<?=site_url("cms/".get_cms(4,"s_url"))?>">How it Works?</a></li>
        <li><a href="<?=site_url("cms/".get_cms(14,"s_url"));?>">List your Service</a></li>
    </ul>
    <div class="language">
        <?=form_dropdown("global_country_id",dd_country(),get_globalCountry(),'id="global_country_id"');?>
    </div>
    <ul class="alignright">
    	<li>Welcome  <span class="blue"><?=is_userLoggedIn()?$user_data->name:'<a class="blue" href="'.site_url("account/signin").'">Guest</a>'?></span>
        <?if(is_userLoggedIn()):?>
       
        <div class="link_panel">
            <h3><img src="<?=base_url();?><?=isset($user_data->s_profile_photo)?$user_data->s_profile_photo:'images/no-pic.jpg'?>" width="23" height="23" alt="people" /> <?=$user_data->name?></h3>
            <ul>
                <li><a href="<?=site_url("dashboard")?>">Dashboard</a></li>
                <li><a href="<?=site_url("user_profile")?>">Profile  <?=intval(get_user_profile_complete(get_userLoggedIn('id')))?>% completed</a></li>

                <li><a href="javascript:void(0);">Service(s) <strong class="blue"><?=intval($user_data->total_services)?></strong></a></li>
                <li><a href="<?=site_url("account/signout")?>">Sign out</a></li>
            </ul>
    	</div>
        <? endif;?>    
        </li>
    </ul>

</div>
<!--TOP PANEL END -->
<!--LOGO PANEL START -->
<div class="logo_panel">
    <div class="contaner">
    	<p><a href="<?=site_url();?>" class="logo"><img src="<?=base_url(get_theme_path())."/";?>images/logo-big.png" width="335" height="78" alt="logo" /></a></p>
    	<div class="search_panel">
            <!--<form name="scholarship_form" id="scholarship_form" action="listing.html" onSubmit="listing.html">
              <select class="alignleft blue-bg" name="service" id="service"><option>Service</option><option>Service 1</option><option>Service 2</option></select>
              <input type="text" class="textbox alignleft rightmar" value="Webdesign" onblur="if(this.value=='')this.value=this.defaultValue;" onfocus="if(this.value==this.defaultValue)this.value='';" />
              <select class="alignleft" id="city"><option>Zip</option><option>City</option><option>Select 2</option></select>
              <input type="text" class="textbox alignleft rightmar" value="City/ Zip / Online" onblur="if(this.value=='')this.value=this.defaultValue;" onfocus="if(this.value==this.defaultValue)this.value='';" />
              <select class="alignleft rightmar select_topbg" name="distance"  id="distance"><option>Distance</option><option>1-10 Km</option><option>10-50 Km</option><option>50-100 Km</option></select>
             <input class="alignleft leftmar rightmar" type="image" title="Search"  src="<?=base_url(get_theme_path())."/";?>images/search-button.png">
          </form>-->
          
            <form name="top_search_form" id="top_search_form"  action="<?=site_url("search_engine");?>" method="post">
                <input type="hidden" id="search_cat_id" name="search_cat_id" value="">
                <input type="hidden" id="search_uid" name="search_uid" value="">
                <input type="hidden" id="search_zip_id" name="search_zip_id" value="">
                <input type="hidden" id="search_city_id" name="search_city_id" value="">
                
              <?/*<select class="alignleft blue-bg" id="search_type" name="search_type">
                <option value="service">Service</option>
                <option value="user">People</option>
              </select>*/?>
              <?=form_dropdown("search_type",
                    dd_search_type(),
                    @$posted['search_type'],
                    'id="search_type" class="alignleft blue-bg"'
                    );?>
              
              <input id="search_type_value" name="search_type_value" type="text" class="textbox alignleft rightmar" value="Keyword" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';" />
              <?/*<select class="alignleft" id="location_type" name="location_type">
                <option value="zip">Zip</option>
                <option value="city">City</option>
              </select>*/?>
              <?=form_dropdown("location_type",
                    dd_location_type(),
                    @$posted['location_type'],
                    'id="location_type" class="alignleft"'
                    );?>
              <input id="location_type_value" name="location_type_value" type="text" class="textbox alignleft rightmar" value="City/ Zip" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';" />
              <?/*<select class="alignleft rightmar select_topbg" id="distance" name="distance">
                <option value="">Distance</option>
                <option value="10">1-10 Km</option>
                <option value="50">10-50 Km</option>
                <option value="100">50-100 Km</option>
              </select>*/?>
              <?=form_dropdown("distance",
                    dd_distance(),
                    @$posted['distance'],
                    'id="distance" class="alignleft rightmar select_topbg"'
                    );?>
             <input id="submit_search" class="alignleft leftmar rightmar" type="image" title="Search"  src="<?=base_url(get_theme_path())."/";?>images/search-button.png">
          </form>          
          
          
          
       </div>
    </div>
 </div>
<!--LOGO PANEL END -->
<!--HEADER PANEL START -->
<div class="header_panel">
	<div class="home_header">&nbsp;</div>
</div>
<!--HEADER PANEL END -->
<!--PEOPLE PANEL START -->
<div class="people_panel">

	<ul>
        <?php 
        
            foreach($home_page_user_data as $k=>$v)
            {
                
        ?>
                <li><a class="various1" href="<?=site_url($v->s_short_url);?>"><img src="<?php if($v->s_profile_photo!="") echo site_url($v->s_profile_photo); else echo site_url('resources/no_image.jpg')?>" width="67" height="66" alt="pic" /></a></li>
        
         <?php      
            }
        ?>
    </ul>
</div>
<!--PEOPLE PANEL END -->
<!--FOOTER PANEL START -->
<div class="footer_panel">
    <? if(!is_userLoggedIn()):?>
    <ul class="center">
    	<li><a href="<?=site_url('account/signup')?>"><img src="<?=base_url(get_theme_path())."/";?>images/social-icon1.png" width="149" height="37" alt="social" /></a></li>
        <li><a href="<?=site_url('account/signin')?>"><img src="<?=base_url(get_theme_path())."/";?>images/social-icon2.png" width="129" height="37" alt="social" /></a></li>
        <li><a href="javascript:void(0)"><img onclick='facebook_connect_init()' src="<?=base_url(get_theme_path())."/";?>images/social-icon3.png" width="202" height="37" alt="social" /></a></li>
    </ul>
    <? endif;?>
    <div class="footer_bottom top_pad">
    	<ul class="link alignleft" id="footer_member_link"> 
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
<div id="popup">
	<a class="fancybox-effects-a" href="#inline" title=""></a>
		<div style="display:none;">
			
		</div>
</div>


<div id="fancybox-wrap">
    <div class="fancybox-outer">
      <div class="fancybox-inner" style="width:620px; height: auto; overflow: auto;">
        <div style="width:620px; height:200px; overflow: auto;" id="inline">
            <div class="lightbox_content">
            <?php 
                if(is_userLoggedIn()){
					
					$friends = find_all_friend_and_their_friend(get_userLoggedIn('id'));
					
            ?>
            <h1>Hello <span class="blue"><?php echo get_userLoggedIn("s_name");?> !</span></h1>
            <p>There are <?php echo count($friends) ?> friends using the GURU.IN</p>
              <ul>
			  	<?php if(!empty($friends)) {
					
						foreach($friends as $val)
							{
				 ?>
                  <li>
					  <a href="javascript:void(0);">
					<!--  <img width="66" height="65" alt="Friends" src="<?php if($val->s_profile_photo!="") echo site_url($val->s_profile_photo); else echo site_url('resources/no_image.jpg')?>">-->
					  <?php echo theme_user_thumb_picture($val->id,'','style="width:66px;height:65px;"'); ?>
					  </a>
				  </li>
				  
				  <?php
				  }
				  			}
				  ?>
                  <!--<li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends2.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends3.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends4.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends1.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends2.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends3.jpg"></a></li>
                  <li><a href="javascript:void(0);"><img width="66" height="65" alt="Friends" src="<?=base_url(get_theme_path())."/";?>images/friends4.jpg"></a></li>-->
              </ul>
            <?php 
            }else{
            ?>
            <h1>Hello <span class="blue">Guest !</span></h1>
            <?php /*?><p>Guru is a great place to find verified and trusted Service Provider who are local to you, connected to you</p><?php */?>
			<?=$pop_txt->s_content?>
            <?php
            }
            ?>
            </div>
        </div>
      </div>
    </div>
	<div class="fancybox-item fancybox-close" title="Close"></div>
</div>



</body>
</html>
