<?php /*?><script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
<script src="<?=site_url("theme/aquincum/js/inedit/moment.min.js");?>"></script>
<script src="<?=site_url("theme/aquincum/js/inedit/inedit.js");?>"></script><?php */?>
<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
          $('.top_bar').remove();
          $('.add_image').remove();
          $('#show_edit_url').remove();
          $('script#short_url_script').remove();
     ///handling inEdit
     /*     
     $('a.edit').remove();     
     $('a.short_grey_button.alignright.rightmar20').remove();
     $('a.short_grey_button a.botmar a.grey a.leftmar').remove();
     */
     
 });
 
$(window).ready(function(){
     
     ///handling inEdit
     $('a.edit').remove();   
     $('a.short_grey_button.alignright.rightmar20').remove();  
     $('a.short_grey_button a.botmar a.grey a.leftmar').remove();     
     
 });    
    
});

 
</script>
<?/*include (APPPATH.'views/fe/user_profile/index.tpl.php');*/?>

<?
/**
* $profile_type = user,company,service [it is defined within the controller itself]
* @see, controllers/user_profile.php , __construct()
* @see, controllers/company_profile.php , __construct()
* @see, controllers/service_profile.php , __construct()
*/

include (APPPATH.'views/fe/'.$profile_type.'_profile/index.tpl.php');?>
<script type="text/javascript">
    $(document).ready(function(){
         $('#short_url_display').unbind();  
         $('script#short_url_script').remove();
         
         $('#show_full_url').bind('click',function(){
            $("#short_url_display").select();    
         });         
         
    });
</script>
