<?php
/*
* Created By : Sahinul Haque
* Created On : 17 April 2013
* Modified By: 
* Modified date: 
*
* Purpose: Main template
* Package : Admin Default Theme
* 
* 
*/

//pr($main_content);
//echo $main_content;
//echo "I AM @ default Theme"
?>

<?/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
*/?>



<!DOCTYPE HTML>
<html lang="en">
<? /**/?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url(); ?>" />
<title>:: <?=site_name();?> Administration ::</title>

<?
////CSS ///
/*
?>
<link href="css/admin/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" media="all" type="text/css" href="css/admin/menu.css" />
<link  type="text/css" rel="stylesheet" media="screen" href="js/jquery/themes/prettyPhoto/prettyPhoto.css" />
<link  type="text/css" rel="stylesheet" media="screen" href="js/jquery/themes/vader/ui.all.css" />
<link  type="text/css" rel="stylesheet" media="screen" href="js/jquery/themes/jquery.ui.tooltip.css" />

<?
///JS//
?>
<script language="javascript" type="text/javascript" src="js/jquery/jquery-1.7.2.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.ui.core.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery-ui-1.8.4.custom.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.ui.tooltip.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.blockUI.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.ui.dialog.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.ui.tabs.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/jquery.prettyPhoto.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery/ui/alphanumeric/jquery.alphanumeric.pack.js"></script>
*/
 
 print $header;
?>
<!--[if IE]> <link href="<?=base_url(get_theme_path())."/";?>css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

<script type="text/javascript" language="javascript" >
var base_url = '<?php echo base_url()?>';

jQuery(function($){
    
$(document).ready(function(){
        
    <? /*$('#right_panel').css({width:'96.0%'});
        $('#show_hide').toggle(
            function()
            {
                $('#left_panel').stop(true, true).hide(1000);

                //$('#right_panel').css({width:'96.8%'});

                $('#right_panel').stop(true, true).animate({width:'96.0%'},
                                          {
                                            duration: 2000, 
                                            specialEasing: {
                                            width: 'linear',
                                            height: 'easeOutBounce'
                                            }
                                          });
                $('#show_hide a img').attr('src','images/admin/show.gif').stop(true, true).show("slow");
            },
            function()
            {
                //$('#right_panel').css({width:'81.8%'});
                $('#right_panel').stop(true, true).animate({width:'81.7%'},
                                          {
                                            duration: 1000, 
                                            specialEasing: {
                                            width: 'linear',
                                            height: 'easeInBounce'
                                            }
                                          });            
                $('#left_panel').stop(true, true).show(3000);
                $('#show_hide a img').attr('src','images/admin/hide.gif').stop(true, true).show("slow");
            }
        );
        /////////Page Transaction Animation////////
        $("#content").fadeIn(3000);
        /////////end Page Transaction Animation////////   

        //////When ajax starts Blocks the Page///////

        //$(document).ajaxStart($.blockUI({ message: 'Just a moment please...' }));

        //////When ajax stops Unblocks the Page///////
        $(document).ajaxStop($.unblockUI);      
        $.unblockUI();////unblock any opened blocking 
        /////Css for .info_massage added here////          
         var css_blockUI={
             "margin":"10px 0",
             "padding":"10px 10px 10px 50px",
             "background":"#d1e4f3 url(<?php echo base_url();?>images/admin/icon-info.png) no-repeat left",
             "border":"1px solid #4d8fcb",
             "font":"normal 12px Arial, Helvetica, sans-serif",
             "color":"#565656",
             "clear":"both",
             "width":'30%',
             "top":'40%',
             "left":'35%',
             "textAlign":'left',             
             "cursor":'wait'
         };
         //$.blockUI.defaults.themedCSS=css_blockUI;
         $.blockUI.defaults.css=css_blockUI;       
         */?>
        <?php /*?> $.growlUI("","Landing <?php echo $title;?>...",3000);    <?php */?>
         

        /////////end useful sample JQ///*/
        <?/*///////////PrettyPhoto Configuration for popup windows//////
        $("a[rel^='prettyPhoto']").prettyPhoto({
            animation_speed: 'fast',
            show_title: true,
            allow_resize: true,
            default_width: 500,
            default_height: 344,
            theme: 'facebook', /* light_rounded / dark_rounded / light_square / dark_square / facebook * /
            keyboard_shortcuts: false/* Set to false if you open forms inside prettyPhoto * /
        });
        
        ///////////end PrettyPhoto Configuration for popup windows//////
        */?> 
        /*********

        * ex- $.prettyPhoto.open('images/fullscreen/image.jpg','Title','Description');

        */

       /**********Menu and shortcut Related,loggedin My_account selecting menus Jquery*************/
       <?php /*
       $("ul[class='select'] a,ul[class='link'] a,ul[id='login_info'] a,ul[class=sub2] a").each(function(i){
           //alert(i+" "+$(this).attr("id"));
          ///clicked in the menu or sub menu////////
          $(this).click(function(e){
              var menu= $(this).attr("id").split("_");   
              var s_loc=$(this).attr("href");   
              /////Ajax call for storing the menu id into session////
              $.post("<?php echo admin_base_url('home/ajax_menu_track')?>",
                    {"h_menu":"mnu_"+menu[1]},
                    function(data)
                    {
                        if(data && s_loc)
                        {
                            $.blockUI({ message: 'Just a moment please...' });
                            window.location.href=s_loc;
                        }
                        //alert(data,s_loc);
                    }
                    );
              /////end Ajax call for storing the menu id into session////
              return false;
          }); 
          
          /////selectin the menu clicked last///
          if($(this).attr("id")=="<?php echo $h_menu;?>")
          {
              $(this).attr("class","active");
          }
          else
          {
              $(this).attr("class","");
          }
       });
       /**********end Menu Related Jquery************* /  
       
       /***********Access Controls for the Shortcut Menus********* /
       $("#left_panel .link li").each(function(i){
           var controller= $(this).find("a").attr("controller");
           var controllers_access=JSON.parse('<?php echo makeArrayJs($controllers_access);?>');
           //console.log(controllers_access);
           
           /**
           * If any controller doesnot exists 
           * OR 
           * No add,edit or delete is set
           * /
           if(!controllers_access[controller]
                || (
                    controllers_access[controller]['action_add']==0 
                    && controllers_access[controller]['action_edit']==0 
                    && controllers_access[controller]['action_delete']==0 
                    )
             )
           {
               $(this).remove();
           }
           
       });
       /***********end Access Controls for the Shortcut Menus********* /  
       */?>
             
      
      /*** Global admin delete confirmation ****/
      
////Click Delete ////
$("[id='delete_action']").each(function(i){
    
   ///Css related///
    $(this).addClass("tablectrl_small bDefault tipS");
    $(this).attr("title","Delete").html('<span class="iconb" data-icon="&#xe136;"></span>');    
    
    $(this).click(function(){

       ////////Confirm///// 
       /*$("#dialog-confirm #dialog_msg").html('<strong>The item will be permanently removed and cannot be recovered.</strong> Are you sure?');       
       $("#dialog-confirm").dialog({
            resizable: false,
            height:240,
            width:350,
            modal: true,
            title: "Removal Confirmation?",
            buttons: {
                'Delete': function() {
                    $(this).dialog('close');
                    $.blockUI({ message: 'Just a moment please...' });
                    $("#h_list").attr("value",$("#dd_list_"+i).find("option:selected").attr("value"));
                    $("#frm_list").attr("action",g_controller+'remove_information/');
                    $("#frm_list").submit();
                },
                Cancel: function() {
                    $("#h_list").attr("value","");
                    $(this).dialog('close');
                }
            }
        });*/
       ////////end Confirm/////
       
       return window.confirm("Are you sure to remove this item? Once item is deleted, cannot be recovered.");

   }); 
});    
////end Click Delete ////      

/*******Global Delete Edit Approve buttons formatting**********/
$("[id='edit_action']").each(function(i){
    $(this).addClass("tablectrl_small bDefault tipS");
    $(this).attr("title","Edit").html('<span class="iconb" data-icon="&#xe1db;"></span>');
    $(this).parent("td").addClass("tableActs");
});

$("[id='approve_action']").each(function(i){
    $(this).addClass("tablectrl_small bDefault tipS");
    $(this).attr("title","Approve").html('<span class="iconb" data-icon="&#xe134;"></span>');
});
/*******end Global Delete Edit buttons formatting**********/

/***Reset Button in Listing pages*****/
/*$("input[name='reset']").each(function(){
    $(this).click(function(){
       $(this).parents("form").each(function(){
           $(this).reset();
       });
    });
});*/         
/***end Reset Button in Listing pages*****/

/***
* on, 30July2013
* Listing page search Problem 
* Solution. 
* In anything is searched from page 5, then 
* the result was not showing.
*/
if($.trim($("form[id='filter']").attr("action"))=="")
{
    $("form[id='filter']").attr("action","<?=$listing_path;?>");
}
/**
* TODO :: Main Nav Using jquery             
*/

/**
* Pagination with searched value
*/
$(".pages a:not([class~='active'])").each(function(k){
    //console.log($(this));
    var act=$(this).attr("href");
    $(this).off("click").on("click",function(event){
        event.preventDefault();
        
        $("form[id='filter']").attr("action",act);
        //$("form[id='filter']").submit();
        $("form[id='filter'] input[name='submit']").click();
        //console.log($("form[id='filter']"));
    });
});
 
 
 
             
});   


//not in document ready
$(window).ready(function(){
    
 $('#admin_menu').superfish({
    onInit: function(){
        //console.log($(this));
        $(this).find("ul").each(function(){
           $(this).hide(); 
        });
    }
 });
 
    //===== Tooltips =====//
    $('.tipN').each(function(){
       $(this).tipsy({gravity: 'n',fade: true, html:true});
    });
    $('.tipS').each(function(){
       $(this).tipsy({gravity: 's',fade: true, html:true});
    });
    $('.tipW').each(function(){
       $(this).tipsy({gravity: 'w',fade: true, html:true});
    });
    $('.tipE').each(function(){
       $(this).tipsy({gravity: 'e',fade: true, html:true});
    }); 
    
    
/**
* On, 30July2013
* As per Mr. Ashim's Request 
* Cancel buttons in add/edit forms  
* should redirect to the listing page.
*/
/*$("input[type='reset'][value='Cancel']").on("click",function(){
   console.log("Dfff");  
   window.location.href="<?=$listing_path;?>"; 
   return false;
});*/
   
    
});


});
</script>

</head>
<body>
<?php
///common Dialog Box///
?>
<div id="dialog-confirm" style="display: none;">
    <p><span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <strong id="dialog_msg"></strong>      
    </p>
</div>
<?php
///end common Dialog Box///
?>



<!-- Top line begins -->
<div id="top">
    <div class="wrapper">
        <a href="<?=base_url();?>" title="" class="logo"><img src="<?=base_url(get_theme_path())."/";?>images/logo.png" alt="" /></a>
        
        <!-- Right top nav -->
        <div class="topNav">
            <ul class="userNav">
                <?/*<li><a title="" class="search"></a></li>
                <li><a href="javascript:void(0);" title="" class="screen"></a></li>*/?>
                <li><a href="<?=admin_base_url('home/dashboard');?>" title="Dashboard" class="settings tipN"></a></li>
                <li><a href="<?=admin_base_url('home/logout');?>" title="Logout" class="logout tipN"></a></li>
                <li class="showTabletP"><a href="javascript:void(0);" title="" class="sidebar"></a></li>
            </ul>
            <a title="" class="iButton"></a>
            <a title="" class="iTop"></a>
            <?/*
            <div class="topSearch">
                <div class="topDropArrow"></div>
                <form action="">
                    <input type="text" placeholder="search..." name="topSearch" />
                    <input type="submit" value="" />
                </form>
            </div>
            */?>
        </div>
        
        <!-- Responsive nav -->
        <? /*
        <ul class="altMenu">
            <li><a href="index.html" title="">Dashboard</a></li>
            <li><a href="ui.html" title="" class="exp" id="current">UI elements</a>
                <ul>
                    <li><a href="ui.html">General elements</a></li>
                    <li><a href="ui_icons.html">Icons</a></li>
                    <li><a href="ui_buttons.html">Button sets</a></li>
                    <li><a href="ui_grid.html" class="active">Grid</a></li>
                    <li><a href="ui_custom.html">Custom elements</a></li>
                </ul>
            </li>
            <li><a href="forms.html" title="" class="exp">Forms stuff</a>
                <ul>
                    <li><a href="forms.html">Inputs &amp; elements</a></li>
                    <li><a href="form_validation.html">Validation</a></li>
                    <li><a href="form_editor.html">File uploads &amp; editor</a></li>
                    <li><a href="form_wizards.html">Form wizards</a></li>
                </ul>
            </li>
            <li><a href="messages.html" title="">Messages</a></li>
            <li><a href="statistics.html" title="">Statistics</a></li>
            <li><a href="tables.html" title="" class="exp">Tables</a>
                <ul>
                    <li><a href="tables.html">Standard tables</a></li>
                    <li><a href="tables_dynamic.html">Dynamic tables</a></li>
                    <li><a href="tables_control.html">Tables with control</a></li>
                    <li><a href="tables_sortable.html">Sortable &amp; resizable</a></li>
                </ul>
            </li>
            <li><a href="other_calendar.html" title="" class="exp">Other pages</a>
                <ul>
                    <li><a href="other_calendar.html">Calendar</a></li>
                    <li><a href="other_gallery.html">Images gallery</a></li>
                    <li><a href="other_file_manager.html">File manager</a></li>
                    <li><a href="other_404.html">Sample error page</a></li>
                    <li><a href="other_typography.html">Typography</a></li>
                </ul>
            </li>
        </ul>
        */       
    ?>
    </div>
</div>
<!-- Top line ends -->


<!-- Sidebar begins -->
<div id="sidebar">
    <div class="mainNav">
        <div class="user">
            <a title="" class="leftUserDrop"><img src="<?=base_url(get_theme_path())."/";?>images/userLogin2.png" width="72px" height="70px" alt="" />
            <?/*<span><strong>3</strong></span>*/?></a>
            <span><?=@$admin_loggedin->s_admin_name;?></span>
            <ul class="leftUser">
                <? if(user_access("edit own profile")) { ?>
                <li><a href="<?=admin_base_url("my_profile");?>" title="" class="sProfile">My profile</a></li>
                <? } ?>
                <li><a href="<?=admin_base_url('home/logout');?>" title="" class="sLogout">Logout</a></li>
                <?/*<li><a href="#" title="" class="sMessages">Messages</a></li>
                <li><a href="#" title="" class="sSettings">Settings</a></li>*/?>
            </ul>
        </div>
        
        <!-- Responsive nav -->
        <?/*
        <div class="altNav">
            <div class="userSearch">
                <form action="">
                    <input type="text" placeholder="search..." name="userSearch" />
                    <input type="submit" value="" />
                </form>
            </div>
            
            <!-- User nav -->
            <ul class="userNav">
                <li><a href="#" title="" class="profile"></a></li>
                <li><a href="#" title="" class="messages"></a></li>
                <li><a href="#" title="" class="settings"></a></li>
                <li><a href="#" title="" class="logout"></a></li>
            </ul>
        </div>
        */?>
        <!-- Main nav -->
        <? 
        /*
        <ul class="nav">
            <li><a href="index.html" title="" class="active"><img src="images/icons/mainnav/dashboard.png" alt="" /><span>Dashboard</span></a></li>
            <li><a href="ui.html" title=""><img src="images/icons/mainnav/ui.png" alt="" /><span>UI elements</span></a>
                <ul>
                    <li><a href="ui.html" title=""><span class="icol-fullscreen"></span>General elements</a></li>
                    <li><a href="ui_icons.html" title=""><span class="icol-images2"></span>Icons</a></li>
                    <li><a href="ui_buttons.html" title=""><span class="icol-coverflow"></span>Button sets</a></li>
                    <li><a href="ui_grid.html" title=""><span class="icol-view"></span>Grid</a></li>
                    <li><a href="ui_custom.html" title=""><span class="icol-cog2"></span>Custom elements</a></li>
                </ul>
            </li>
            <li><a href="forms.html" title=""><img src="images/icons/mainnav/forms.png" alt="" /><span>Forms stuff</span></a>
                <ul>
                    <li><a href="forms.html" title=""><span class="icol-list"></span>Inputs &amp; elements</a></li>
                    <li><a href="form_validation.html" title=""><span class="icol-alert"></span>Validation</a></li>
                    <li><a href="form_editor.html" title=""><span class="icol-pencil"></span>File uploader &amp; WYSIWYG</a></li>
                    <li><a href="form_wizards.html" title=""><span class="icol-signpost"></span>Form wizards</a></li>
                </ul>
            </li>
            <li><a href="messages.html" title=""><img src="images/icons/mainnav/messages.png" alt="" /><span>Messages</span></a></li>
            <li><a href="statistics.html" title=""><img src="images/icons/mainnav/statistics.png" alt="" /><span>Statistics</span></a></li>
            <li><a href="tables.html" title=""><img src="images/icons/mainnav/tables.png" alt="" /><span>Tables</span></a>
                <ul>
                    <li><a href="tables.html" title=""><span class="icol-frames"></span>Standard tables</a></li>
                    <li><a href="tables_dynamic.html" title=""><span class="icol-refresh"></span>Dynamic table</a></li>
                    <li><a href="tables_control.html" title=""><span class="icol-bullseye"></span>Tables with control</a></li>
                    <li><a href="tables_sortable.html" title=""><span class="icol-transfer"></span>Sortable and resizable</a></li>
                </ul>
            </li>
            <li><a href="other_calendar.html" title=""><img src="images/icons/mainnav/other.png" alt="" /><span>Other pages</span></a>
                <ul>
                    <li><a href="other_calendar.html" title=""><span class="icol-dcalendar"></span>Calendar</a></li>
                    <li><a href="other_gallery.html" title=""><span class="icol-images2"></span>Images gallery</a></li>
                    <li><a href="other_file_manager.html" title=""><span class="icol-files"></span>File manager</a></li>
                    <li><a href="#" title="" class="exp"><span class="icol-alert"></span>Error pages <span class="dataNumRed">6</span></a>
                        <ul>
                            <li><a href="other_403.html" title="">403 error</a></li>
                            <li><a href="other_404.html" title="">404 error</a></li>
                            <li><a href="other_405.html" title="">405 error</a></li>
                            <li><a href="other_500.html" title="">500 error</a></li>
                            <li><a href="other_503.html" title="">503 error</a></li>
                            <li><a href="other_offline.html" title="">Website is offline error</a></li>
                        </ul>
                    </li>
                    <li><a href="other_typography.html" title=""><span class="icol-create"></span>Typography</a></li>
                    <li><a href="other_invoice.html" title=""><span class="icol-money2"></span>Invoice template</a></li>
                </ul>
            </li>
        </ul>
        */
        
////Caching the menus HTML////
/**
* Menu caching depends upon user role of the admin 
* who is logged id.
*/
$navigation=cache_var("admin_menu-".@$admin_loggedin->admin_type_id);
if(empty($navigation))///found from cache, do not execute below codes.
    $navigation=cache_var("admin_menu-".@$admin_loggedin->admin_type_id,
                    get_adminMenusHtml()); 
////Caching the menus HTML////
if(!empty($navigation))
{
    //////////generating the menus/////////
        //create_menus();   
        print $navigation;
    //////////end generating the menus/////////  
} 
?>        
        
        
    </div>
    
    <!-- Secondary nav -->
    <?/*
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo">Balance:<span>Apr 21 2012</span></div>
                    <div class="balAmount"><span class="balBars"><!--5,10,15,20,18,16,14,20,15,16,12,10--></span><span>$58,990</span></div>
                </div>
                <a href="#" class="triangle-red"></a>
            </div>
            
            <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
                <ul class="iconsLine ic3 etabs">
                    <li><a href="#general" title=""><span class="icos-fullscreen"></span></a></li>
                    <li><a href="#alt1" title=""><span class="icos-user"></span></a></li>
                    <li><a href="#alt2" title=""><span class="icos-archive"></span></a></li>
                </ul>
                
                <div class="divider"><span></span></div>
                
                <div id="general">
                
                    <!-- Sidebar big buttons -->
                    <div class="sidePad">
                        <a href="#" title="" class="sideB bLightBlue">Add new session</a>
                    </div>
                    
                    <div class="divider"><span></span></div>
                
                    <!-- Sidebar file uploads widget -->
                    <div class="sideUpload">
                        <div class="dropFiles"></div>
                        <ul class="filesDown">
                            <li class="currentFile">
                                <div class="fileProcess">
                                    <img src="images/elements/loaders/10s.gif" alt="" class="loader" />
                                    <strong>Homepage_widgets_102.psd</strong>
                                    <div class="fileProgress">
                                        <span>9.1 of 17MB</span> - <span>243KB/sec</span> - <span>1 min</span>
                                    </div>
                                    
                                    <div class="contentProgress"><div class="barG tipN" title="61%" id="bar10"></div></div>
                                </div>
                            </li>
                            <li><span class="fileSuccess"></span>About_Us_08956.psd<span class="remove"></span></li>
                            <li><span class="fileSuccess"></span>Our_services_02811.psd<span class="remove"></span></li>
                            <li><span class="fileError"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                            <li><span class="fileQueue"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                            <li><span class="fileQueue"></span>Homepage_Alt_032811.psd<span class="remove"></span></li>
                        </ul>
                    </div>
                    
                    <div class="divider"><span></span></div>
                    
                    <!-- Sidebar chart -->
                    <div class="sideChart">
                        <div class="barsS" id="placeholder1_hS"></div>
                    </div>
                </div>
                
                <div id="alt1">
                    
                    <!-- Sidebar chart -->
                    <div class="numStats">
                        <ul>
                            <li><a href="#" title="">4248</a><span>visitors</span></li>
                            <li><a href="#" title="">748</a><span>orders</span></li>
                            <li class="last"><a href="#" title="">357</a><span>reviews</span></li>
                        </ul>
                    </div>
                    
                    <div class="divider"><span></span></div>
                
                    <!-- Sidebar user list -->
                    <ul class="userList">
                        <li>
                            <a href="#" title="">
                                <img src="images/live/face1.png" alt="" />
                                <span class="contactName">
                                    <strong>Eugene Kopyov <span>(5)</span></strong>
                                    <i>web &amp; ui designer</i>
                                </span>
                                <span class="status_away"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" title="">
                                <img src="images/live/face2.png" alt="" />
                                <span class="contactName">
                                    <strong>Lucy Wilkinson <span>(12)</span></strong>
                                    <i>Team leader</i>
                                </span>
                                <span class="status_off"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" title="">
                                <img src="images/live/face3.png" alt="" />
                                <span class="contactName">
                                    <strong>John Dow</strong>
                                    <i>PHP developer</i>
                                </span>
                                <span class="status_available"></span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="divider"><span></span></div>
                
                    <!-- Sidebar progress bars -->
                    <div class="sideWidget">
                        <div class="contentProgress"><div class="barGr tipS" id="bar1" title="15%"></div></div>   
                        <div class="contentProgress mt8"><div class="barB tipS" id="bar2" title="30%"></div></div>
                        <div class="contentProgress mt8"><div class="barO tipS" id="bar3" title="45%"></div></div>
                        <div class="contentProgress mt8"><div class="barBl tipS" id="bar4" title="60%"></div></div>
                        <div class="contentProgress mt8"><div class="barR tipS" id="bar5" title="75%"></div></div>  
                    </div>       
                    
                </div>
                
                
                <div id="alt2">
                
                    <!-- Sidebar forms -->
                    <div class="sideWidget">
                        <div class="formRow">
                            <label>Usual input field:</label>
                            <input type="text" name="regular" placeholder="Your name" />
                        </div>
                        <div class="formRow">
                           <label>Usual password field:</label>
                            <input type="password" name="regular" placeholder="Your password" /> 
                        </div>
                        <div class="formRow">
                            <label>Single file uploader:</label>
                            <input type="file" class="styled" id="fileInput" />
                        </div>
                        <div class="formRow">
                            <label>Dropdown menu:</label>
                            <select name="select2" class="styled" >
                                <option value="opt1">Usual select box</option>
                                <option value="opt2">Option 2</option>
                                <option value="opt3">Option 3</option>
                                <option value="opt4">Option 4</option>
                                <option value="opt5">Option 5</option>
                                <option value="opt6">Option 6</option>
                                <option value="opt7">Option 7</option>
                                <option value="opt8">Option 8</option>
                            </select>
                        </div>
                        
                        <div class="formRow searchDrop">
                            <label>Dropdown with search:</label>
                            <select data-placeholder="Choose a Country..." class="select" tabindex="2">
                                <option value=""></option> 
                                <option value="Cambodia">Cambodia</option> 
                                <option value="Cameroon">Cameroon</option> 
                                <option value="Canada">Canada</option> 
                                <option value="Cape Verde">Cape Verde</option> 
                                <option value="Cayman Islands">Cayman Islands</option> 
                                <option value="Central African Republic">Central African Republic</option> 
                                <option value="Chad">Chad</option> 
                                <option value="Chile">Chile</option> 
                                <option value="China">China</option> 
                                <option value="Christmas Island">Christmas Island</option> 
                                <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
                                <option value="Colombia">Colombia</option> 
                                <option value="Comoros">Comoros</option> 
                                <option value="Congo">Congo</option> 
                                <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
                                <option value="Cook Islands">Cook Islands</option> 
                                <option value="Costa Rica">Costa Rica</option> 
                                <option value="Cote D'ivoire">Cote D'ivoire</option> 
                                <option value="Croatia">Croatia</option> 
                                <option value="Cuba">Cuba</option> 
                                <option value="Cyprus">Cyprus</option> 
                                <option value="Czech Republic">Czech Republic</option> 
                                <option value="Denmark">Denmark</option> 
                                <option value="Djibouti">Djibouti</option> 
                                <option value="Dominica">Dominica</option> 
                                <option value="Dominican Republic">Dominican Republic</option> 
                                <option value="Ecuador">Ecuador</option> 
                                <option value="Egypt">Egypt</option> 
                                <option value="El Salvador">El Salvador</option> 
                                <option value="Equatorial Guinea">Equatorial Guinea</option> 
                                <option value="Eritrea">Eritrea</option> 
                                <option value="Estonia">Estonia</option> 
                                <option value="Ethiopia">Ethiopia</option> 
                                <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
                                <option value="Faroe Islands">Faroe Islands</option> 
                                <option value="Fiji">Fiji</option> 
                                <option value="Finland">Finland</option> 
                                <option value="France">France</option> 
                                <option value="French Guiana">French Guiana</option> 
                                <option value="French Polynesia">French Polynesia</option> 
                                <option value="French Southern Territories">French Southern Territories</option> 
                                <option value="Gabon">Gabon</option> 
                                <option value="Gambia">Gambia</option> 
                                <option value="Georgia">Georgia</option> 
                                <option value="Germany">Germany</option> 
                                <option value="Ghana">Ghana</option> 
                                <option value="Gibraltar">Gibraltar</option> 
                                <option value="Greece">Greece</option> 
                            </select>
                        </div>
                    
                        <div class="formRow">
                            <input type="checkbox" id="check2" name="chbox1" checked="checked" class="check" />
                            <label for="check2"  class="nopadding">Checkbox checked</label>
                        </div>
                        <div class="formRow">
                            <input type="radio" id="radio1" name="question1" checked="checked" />
                            <label for="radio1"  class="nopadding">Usual radio button</label>
                        </div>
                        <div class="formRow">
                            <label>Usual textarea:</label>
                            <textarea rows="8" cols="" name="textarea" placeholder="Your message"></textarea>
                        </div>
                        <div class="formRow">
                            <input type="submit" class="buttonS bLightBlue" value="Submit button" />
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="divider"><span></span></div>
            
            <!-- Sidebar datepicker -->
            <div class="sideWidget">
                <div class="inlinedate"></div>
            </div>
            
            <div class="divider"><span></span></div>
            
            <!-- Sidebar tags line -->
            <div class="formRow">
                <input type="text" id="tags" name="tags" class="tags" value="these,are,sample,tags" />
            </div>
            
            <div class="divider"><span></span></div>
            
            <!-- Sidebar buttons -->
            <div class="fluid sideWidget">
                <div class="grid6"><input type="submit" class="buttonS bRed" value="Cancel" /></div>
                <div class="grid6"><input type="submit" class="buttonS bGreen" value="Submit" /></div>
            </div>
            
            <div class="divider"><span></span></div>
            
       </div> 
   </div>
   */?>
</div>
<!-- Sidebar ends -->
    
    
<!-- Content begins -->
<div id="content">
    <div class="contentTop">
        <span class="pageTitle"><span class="icon-screen"></span><? print $page_title;?></span>
        <?/*
        <ul class="quickStats">
            <li>
                <a href="" class="blueImg"><img src="images/icons/quickstats/plus.png" alt="" /></a>
                <div class="floatR"><strong class="blue">5489</strong><span>visits</span></div>
            </li>
            <li>
                <a href="" class="redImg"><img src="images/icons/quickstats/user.png" alt="" /></a>
                <div class="floatR"><strong class="blue">4658</strong><span>users</span></div>
            </li>
            <li>
                <a href="" class="greenImg"><img src="images/icons/quickstats/money.png" alt="" /></a>
                <div class="floatR"><strong class="blue">1289</strong><span>orders</span></div>
            </li>
        </ul>*/?>
    </div>
    
    <!-- Breadcrumbs line -->
    <div class="breadLine">
        <?/*
        <div class="bc">
            <ul id="breadcrumbs" class="breadcrumbs">
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">UI elements</a>
                    <ul>
                        <li><a href="ui.html" title="">General elements</a></li>
                        <li><a href="ui_icons.html" title="">Icons</a></li>
                         <li><a href="ui_buttons.html" title="">Button sets</a></li>
                        <li><a href="ui_custom.html" title="">Custom elements</a></li>
                    </ul>
                </li>
                <li class="current"><a href="ui_grid.html" title="">Grid</a></li>
            </ul>
        </div>
        */?>
        <div class="breadLinks">
            <ul>
                <li><a href="<?=$listing_path;?>" title="Goto listing page" class="tipS"><i class="icos-list"></i><span>Listing</span></a></li>
                <?/*
                <li><a href="#" title=""><i class="icos-check"></i><span>Tasks</span> <strong>(+12)</strong></a></li>
                <li class="has">
                    <a title="">
                        <i class="icos-money3"></i>
                        <span>Invoices</span>
                        <span><img src="<?=base_url(get_theme_path())."/";?>images/elements/control/hasddArrow.png" alt="" /></span>
                    </a>
                    <ul>
                        <li><a href="#" title=""><span class="icos-add"></span>New invoice</a></li>
                        <li><a href="#" title=""><span class="icos-archive"></span>History</a></li>
                        <li><a href="#" title=""><span class="icos-printer"></span>Print invoices</a></li>
                    </ul>
                </li>
                */?>
            </ul>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="wrapper">
        <?php show_msg();?>
        <div class="fluid">
            <?php print $main_content; ?>
        </div>
    </div>
    <!-- Main content ends -->
    
</div>
<!-- Content ends -->
</body>
<script type="text/javascript">

jQuery(function($){
   $(window).ready(function(){
   
   
/***Add and Edit Buttons in operation templates***/
<?php
/**
* For all admin section, the operation link is as follows
* ex- /admin/admin_user/operation/add 
* "add" is the action
*/
$action=$this->uri->segment(4);
?>
$(".widget a").each(function(){
    if( $(this).hasClass("edit") )
    {
        $(this).addClass("tipS");
        <? if($action=="add") { ?>
            $(this).removeClass("edit");
            $(this).addClass("add").attr("title","Add").text("Add");                 
        <?} ?>         
        
        /**
        * On add operation show the form 
        * on load
        */   
        $(this).click();        
        
    }
}); 
/***end Add and Edit Buttons in operation templates***/          
   
   }); 
});

</script>


</html>