<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
       $("#share").click(function(){
           var html='<label for="name">Receiver Email</label><textarea name="s_email" id="s_email" rel="<?=$form_token;?>" class="text ui-widget-content ui-corner-all" cols="42"/></textarea><label>For multiple email please insert comma(,) separated email id.</label>';
           
           //save_search.title=title;
           //save_search.link=url;
           $( "#dialog-share[rel='<?=$form_token;?>']" ).find("#dialog_msg").html(html);                
           $( "#dialog-share[rel='<?=$form_token;?>']" ).dialog( "open" );  
       }); 
	   
	   $("a[id^='share_srch_']").each(function(){
		   $(this).click(function(){
		   		var Rel = $(this).attr('id').split('_').pop();
				var html='<label for="name">Receiver Email</label><textarea name="s_email" id="s_email" rel="'+Rel+'" class="text ui-widget-content ui-corner-all" cols="42"/></textarea><label>For multiple email please insert comma(,) separated email id.</label>';
			   
			   //save_search.title=title;
			   //save_search.link=url;
			   $( "#dialog-share[rel='"+Rel+"']" ).find("#dialog_msg").html(html);                
			   $( "#dialog-share[rel='"+Rel+"']" ).dialog( "open" );
		   
		   });
	   });
       
       
    /** 
    * dialoge box for share with friends
    */
    $( "#dialog-share[rel='<?=$form_token;?>']" ).dialog({
        autoOpen: false,
        resizable: false,
        height:300,
        width:400,
        modal: true,
        buttons: {
            "Send": function() {
                //save_search.email=$('textarea[name="s_email"]').attr("value");
                
                var emails= $('textarea[name="s_email"][rel="<?=$form_token;?>"]').attr("value").split(",");
                var parm={"email":{},"form_token":"","type":"","link":""};
                parm["email"]=new Array();
                $.each(emails,function(i,v){
                    if($.trim(v)!="")
                    {
                        parm["email"].push($.trim(v));
                    }
                });
               parm["link"]='<?=site_url($link);?>';
               parm["form_token"]='<?=$form_token;?>';
               parm["type"]='<?=$type?>';
               
                $.post("<?=site_url("autocomplete/ajaxSendEmail")?>",
                        parm, 
                        function(data)
                        {
                            if(data=='success')
                            {
                                $('.ui-dialog-buttonset').find('button:first').hide();
                                $('.ui-dialog-buttonset').find('button:last').find('span').html('Ok');
                                $( "#dialog-share" ).find("#dialog_msg").html('Emails sent successfully.');
                                /*$('.ui-dialog-buttonset').find('button:last').click(function(){
                                     $( "#dialog-share" ).dialog('close');
                                });*/
                            }
                            else
                            {
                                $( "#dialog-share[rel='<?=$form_token;?>']" ).find("#dialog_msg").find('#s_email').before(data);
                            }
                        }
                     );
                //$( this ).dialog( "close" );
            },
            "Cancel": function() {
				//console.log($(this));
                $('.ui-dialog-buttonset').find('button:first').show();
                $('.ui-dialog-buttonset').find('button:last').find('span').html('Cancel');   
				$('.share_box').dialog('close');             
                $( this ).dialog( "close" );
            }
            
        },
         hide: {
                /*effect: "explode",*/
                duration: 1000
         }
      });
       
       
       
    });
});

</script>



<?php
///common share Box///
?>
<div id="dialog-share" class="share_box"  rel="<?=$form_token;?>" style="display: block;" title="Share with Friend">
    <p><!-- <span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span> -->
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end share Box///
?>

<?php
/**
* For profile pages    
*/
if($disp=="button")
{
?>
<div class="panel_info">
    <h3><a href="javascript:void(0);" id="share"  rel="<?=$form_token;?>"><img src="<?=base_url(get_theme_path())."/";?>images/icon1.jpg" width="18" height="12" alt="icon"> Share with Friend</a></h3>
</div>
<?php
}
else//for search pages
{
?>
<?php /*?><a href="javascript:void(0);" id="share"  rel="<?=$form_token;?>"><img src="<?=get_theme_path('guru_frontend')?>images/icon11.png" width="18" height="12" alt="icon" /></a><?php */?>
<a href="javascript:void(0);" id="share_srch_<?=$form_token;?>"  rel="<?=$form_token;?>"><img src="<?=get_theme_path('guru_frontend')?>images/icon11.png" width="18" height="12" alt="icon" /></a>
<?php     
}
?>