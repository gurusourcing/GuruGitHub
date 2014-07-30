<script type="text/javascript">
var array_share =  [];
jQuery(function($){
    $(document).ready(function(){
        var save_search={"id":0,"s_search_tag":"", "title":"", "link": "", "email":""};//global var
        
        $('.modify').each(function(i){
            $(this).click(function(){
               save_search.id=$(this).attr('id');
               var $tag=$($(this).siblings("span").find("input[class^='chk_']")[0].nextSibling);               
               
                var html='<label for="name">Search Tag</label><textarea name="s_search_tag" id="s_search_tag" class="text ui-widget-content ui-corner-all" cols="35"/></textarea>';
                $( "#dialog-search-tag" ).find("#dialog_msg").html(html);                
                $( "#dialog-search-tag" ).find("#s_search_tag").text( $.trim($tag.text()) );
                $( "#dialog-search-tag" ).dialog( "open" );     
            });
        });
        
        $('.remove').each(function(){
            $(this).click(function(){
                save_search.id=$(this).attr('id');
                $( "#dialog-confirm-delete" ).dialog( "open" );
            });
        });
        
        /**
        * delete selected 
        */
        $('.remove_all').click(function(){
            if (!$('input[class^=chk_]').is(':checked'))
            {
               $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
                $( "#dialog-alert" ).dialog( "open" ); 
            }
               
            else
            {
                var id=new Array;
                $('input[class^=chk_]:checked').each(function(i){
                   //console.log(i);
                   id[i]=$(this).attr('value');
               });
                save_search.id=id;
                $( "#dialog-confirm-delete" ).dialog( "open" );     
            }            
        });
        
        /**
        * add search tag dialog box
        */
        $( "#dialog-search-tag" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            modal: true,
            draggable: true,
            buttons: {
            save: function() {
                
                save_search.s_search_tag=$("#s_search_tag").attr("value");
                
                $.post("<?=site_url("save_search/ajaxAddSearchTag")?>",save_search,
                    function(data)
                    {
                        /*if(data)
                        {
                            $( "#dialog-search-tag" ).find("#dialog_msg").html(data);
                        }*/
                        //$(this).dialog("close");
                        window.location.href='<?=site_url('save_search')?>';
                    }
                );
                $(this).dialog("close");
                    
            },
            Cancel: function() {
                $(this).dialog("close");
            },
            
            },
            hide: {
                /*effect: "explode",*/
                duration: 1000
         }
     });
     
     /**
     * delete confirm modal box
     */
      $( "#dialog-confirm-delete" ).dialog({
        autoOpen: false,
        resizable: false,
        height:200,
        width:350,
        modal: true,
        buttons: {
            "Delete": function() {
                $.post("<?=site_url("save_search/ajaxDeleteSearchTag")?>",save_search, 
                                function()
                                {
                                    window.location.href='<?=site_url('save_search')?>';
                                }
                     );
                $( this ).dialog( "close" );
            },
            "Cancel": function() {
                $( this ).dialog( "close" );
            }
            
        },
         hide: {
                /*effect: "explode",*/
                duration: 1000
         }
      });
      
       /**
     * check all check boxes
     */
    $("#all_chk").click(function(){
        if ($("#all_chk").is(":checked"))
            $('input[type=checkbox]').prop('checked', true);
        else
            $('input[type=checkbox]').prop('checked', false); 
    });
    /**
    * uncheck the select all check box if any chkbox is unchecked
    *  and the select all check box if all select box is selected
    */
    $('input[class^=chk_]').click(function(){
        if ($('input[type=checkbox]:not(:checked)').length)
           $("input[id=all_chk]").prop('checked',false);
        if(!$('input[class^=chk_]:not(:checked)').length)
            $("input[id=all_chk]").prop('checked',true);
    });
    
    /**
    * alert box
    */
     $( "#dialog-alert" ).dialog({
        autoOpen: false,
        modal: true,
        buttons: {
        "Ok": function() {
            $( this ).dialog( "close" );
            }
        },
        hide: {
                /*effect: "explode",*/
                duration: 1000
     }
     });
   
   /**
   * mulitple sharing via FB
   */
     $("input[type='checkbox']").change(function(){
         array_share = [];
      $('.search_list').children().find("input:checked").each(function(){
          var $t=$(this)[0].nextSibling;
          var url=$(this).parent().next().attr('href');
          //console.log(url);
          array_share.push({title:$t.nodeValue, link: url});
          
      });
    });
    
    /**
    * single share via FB
    */
    $('.share').click(function(){
        array_share = [];
        var title=$(this).siblings("span").find("input[class^='chk_']")[0].nextSibling.nodeValue;
        var url=$(this).parent().find('a.url').attr('href');
        //console.log($(this).siblings("span").find("input[class^='chk_']")[0].nextSibling);
        //$(this).
        array_share.push({title:title, link: url});
        share_of_fb();
    });
    
    
    
    /**
    * shara via mail     
    */
    $('.send_email').click(function(){
       
       var title=$(this).siblings("span").find("input[class^='chk_']")[0].nextSibling.nodeValue;
       var url=$(this).parent().find('a.url').attr('href');
       var html='<label for="name">Receiver Email</label><textarea name="s_email" id="s_email" class="text ui-widget-content ui-corner-all" cols="35"/></textarea><label>For multiple email please insert comma(,) separated email id.</label>';
       save_search.title=title;
       save_search.link=url;
       $( "#dialog-email" ).find("#dialog_msg").html(html);                
       $( "#dialog-email" ).dialog( "open" );     
       
     });
     
    /**
   * mulitple sharing via mail
   */
   $('#send_email').click(function(){
        if (!$('input[class^=chk_]').is(':checked'))
        {
           $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
           $( "#dialog-alert" ).dialog( "open" ); 
        }
        else
        {
            var t=[];
            var url=[];
            $('.search_list').children().find("input:checked").each(function(i){
              var val=$(this)[0].nextSibling;
              t[i]=val.nodeValue;
              url[i]=$(this).parent().next().attr('href');
             // array_share.push({title:$t.nodeValue, link: url});
            });
            save_search.title=t;
            save_search.link=url;
            
            var html='<label for="name">Receiver Email</label><textarea name="s_email" id="s_email" class="text ui-widget-content ui-corner-all" cols="35"/></textarea><label>For multiple email please insert comma(,) separated email id.</label>';
            $( "#dialog-email" ).find("#dialog_msg").html(html);                
            $( "#dialog-email" ).dialog( "open" );     
            
        }
   });
     
    
    
    /** 
    * dialoge box for send email
    */
    $( "#dialog-email" ).dialog({
        autoOpen: false,
        resizable: false,
        height:300,
        width:400,
        modal: true,
        buttons: {
            "Send": function() {
                var emails=$('textarea[name="s_email"]').attr("value").split(',');
                var e=[]
                $.each(emails,function(i,v){
                    if($.trim(v)!="")
                    {
                        e[i]=$.trim(v);
                    }
                });
                save_search.email=e;
                //console.log(save_search);
                $.post("<?=site_url("save_search/ajaxSendEmail")?>",save_search, 
                                function(data)
                                {
                                    if(data=='success')
                                    {
                                        $('.ui-dialog-buttonset').find('button:first').hide();
                                        $('.ui-dialog-buttonset').find('button:last').find('span').html('Ok');
                                        $( "#dialog-email" ).find("#dialog_msg").html('Emails sent successfully.');
                                    }
                                    else
                                    {
                                        $( "#dialog-email").find("#dialog_msg").find('#s_email').before(data);
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
                /*effect: "explode",*/
                duration: 1000
         }
      });
    
    
    
    });
});


    /**
     * share via facebook
     */
     function share_of_fb(){
        //console.log(array_share);
        $.map(array_share, function(data) {
             console.log(data.title + ", " + data.link);
        FB.ui(
            {
              method: 'feed',
              name: data.title,
              /*link: 'https://developers.facebook.com/docs/reference/dialogs/',*/
              link : data.link,
              caption: 'Guru Profile Page',
              //description: 'Please visit my profile page in <?=site_name();?>.'
            },
            function(response) {
                  if (response && response.post_id) {
                    alert('Post was published.');
                  } else {
                    alert('Post was not published.');
                  }
                }
           );
        });
        
    } 
     
</script>

<?php
///common search tag Box///
?>
<div id="dialog-search-tag" style="display: block;" title="Search Tag">
    <p><!-- <span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span> -->
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end common search tag Box///
?>
<?php 
///common delete confirm box////
?>
<div id="dialog-confirm-delete" title="Delete this saved search?">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Item(s) will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<?php 
////common delete confirm box////
?>
<?php
///common alert Box///
?>
<div id="dialog-alert" style="display: block;" title="Attention">
    <p><span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end common alert Box///
?>

<?php
///common email Box///
?>
<div id="dialog-email" style="display: block;" title="Attention">
    <p><span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end common email Box///
?>

<?php //pr($value);?>
<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?=theme_user_navigation();?> 
            <div class="main_panel">
                <h1>Save Search</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
                <div class="top_select">
                    <p class="alignleft"><input type="checkbox" class="alignleft" id="all_chk"/> Select all</p>
                   <?php /*?> <a href="javascript:void(0);" onclick="share_of_fb();" class="short_grey_button">FB Share Selected</a><?php */?>
                    <?php /*?><a href="javascript:void(0);" class="short_grey_button" id="send_email">Mail Selected</a><?php */?>
                    <a href="javascript:void(0);" class="short_grey_button remove_all">Delete Selected</a>
                </div>
              <div class="info">
                   <ul class="search_list">
                     <?php 
                    if(!empty($value))
                    {                      
                     foreach($value as $k=>$v) : ?>
                     <li><span><input type="checkbox" class="chk_<?=$v->id;?>" value="<?=$v->id;?>" /> <?=!empty($v->s_search_tag)?$v->s_search_tag:'[empty]';?></span>
                     <?/*<a href="<?=$v->s_url;?>" class="url"><?=$v->s_url;?></a>*/?>
                     <a href="<?=site_url("save_search/gotosearchresult/".$v->id);?>" class="url"><?=$v->s_url;?></a>
                     <a href="javascript:void(0);" class="short_grey_button grey_to_orange alignright remove" id="<?=$v->id;?>">Delete</a>
                     <a href="javascript:void(0)" class="short_grey_button alignright send_email">Mail</a>
                     <a href="javascript:void(0);"  class="short_grey_button alignright share">FB Share</a>
                     <a href="javascript:void(0);" class="modify short_grey_button alignright" id="<?=$v->id;?>" >Edit</a>
                     </li>
                     <?php 
                     endforeach;
                    }
                    else
                        echo '<li>'.message_line("no_information_found").'</li>'; 
                     ?>
                  </ul>
                </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 