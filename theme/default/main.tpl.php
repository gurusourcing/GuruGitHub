<?php
/*
* Created By : Sahinul Haque
* Created On : 28 March 2013
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

<script type="text/javascript" language="javascript" >
var base_url = '<?php echo base_url()?>';

jQuery(function($){
    
$(document).ready(function(){
        $('#right_panel').css({width:'96.0%'});
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
        <?php /*?> $.growlUI("","Landing <?php echo $title;?>...",3000);    <?php */?>
         

        /////////end useful sample JQ///*/
        ///////////PrettyPhoto Configuration for popup windows//////
        $("a[rel^='prettyPhoto']").prettyPhoto({
            animation_speed: 'fast',
            show_title: true,
            allow_resize: true,
            default_width: 500,
            default_height: 344,
            theme: 'facebook', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
            keyboard_shortcuts: false/* Set to false if you open forms inside prettyPhoto */
        });

        ///////////end PrettyPhoto Configuration for popup windows//////

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

<div id="header">
  <div id="logo"><img src="<?php echo base_url().get_theme_path().'images/admin/logo.jpg';?>" alt="Quality Assurance" title="Quality Assurance" /></div>
  <div id="toplink">
  <?php
    if(!empty($admin_loggedin))
    {
        $s_str='<ul>';
        $s_str.='<li><strong>'.$admin_loggedin->s_admin_name.'</strong></li>';
        $s_str.='<li>|</li>';
        $s_str.='<li><a id="mnu_0"  href="'.admin_base_url('home/dashboard').'">My Account</a></li>';
        $s_str.='<li>|</li>';        
        $s_str.='<li><a href="'.admin_base_url('home/logout').'">Logout</a></li>';
        $s_str.='</ul>';
        echo $s_str;
        unset($s_str);
    }
  ?>
  </div>
  <div class="clr"></div>
</div>
<div class="clr"></div>


<?php
//////////navigation////////

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
    ?>
<div id="navigation">
  <div id="pro_linedrop1">
    <?php
    //////////generating the menus/////////
        //create_menus();   
        print $navigation;
    //////////end generating the menus/////////    
    ?>   
  </div>
</div>    
    <?php
}//end navigation
?>



<div class="clr"></div>
<div id="content" style="display: none;">
 <?php show_msg();?>   
 <?php print $main_content; ?>
  <div class="clr"></div>
</div>
<div class="clr"></div>
<div id="footer">
  <p>&copy; <?=date("Y");?> Copyright <?=site_name();?></p>
  <p>&nbsp;</p>
</div>
</body>
</html>