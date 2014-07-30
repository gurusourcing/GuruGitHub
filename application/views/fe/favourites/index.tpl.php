<script type="text/javascript">
$(document).ready(function(){
    var ID={'id':0};
    
    /**
    * delete single entry
    */
    $('.delete').click(function(){
        var di=$(this).attr('id');
        var temp=di.split('_');
        ID.id=$('#h_id_'+temp[1]).attr("value");
        $( "#dialog-confirm-delete" ).dialog( "open" ); 
/*        
        $.blockUI({ message: 'Just a moment...' });
        $.post("<?= site_url('favourites/ajaxdeleteFavourite')?>",{id:id}, function(data){
            window.location.href='<?=site_url('favourites')?>';
         }) */   
    });
    
    /**
    * delete multile entry
    */
     $("#all_delete").click(function(){
        if (!$('input[class^=chk]').is(':checked'))
            {
               $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
                $( "#dialog-alert" ).dialog( "open" ); 
            }
        else
        {
           var chk=[];
            $(".chk:checked").each(function(i){
                chk[i]=$(this).attr('value');
           });
           ID.id=chk;
           $( "#dialog-confirm-delete" ).dialog( "open" ); 
        }

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
                 
                  $.post("<?= site_url('favourites/ajaxdeleteFavouriteMulti')?>",ID, function(data){
                        window.location.href='<?=site_url('favourites')?>';
                    });
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
        if ($("#all_chk").is(":checked")){
            $('input[type=checkbox]').prop('checked', true);
        }
        else
            $('input[type=checkbox]').prop('checked', false); 
    });
    
     /**
    * uncheck the select all check box if any chkbox is unchecked
    * and the select all check box if all select box is selected
    */
    $('input[id^=chk_]').click(function(){
        if ($('input[type=checkbox]:not(:checked)').length)
           $("input[id=all_chk]").prop('checked',false);
       if (!$('input[id^=chk_]:not(:checked)').length)
            $("input[id=all_chk]").prop('checked',true);
    });
    
    
    /**
    * displaying the message box
    */
    $('.add').click(function(){
        var di=$(this).attr('id');
        var temp=di.split('_');
        $('#message_'+temp[1]).css("display","block");
    });
    /**
    * hiding the message box on clicking cancel button
    */
     $(".cancel").click(function(){
         var di=$(this).attr('id');
         var temp=di.split('_');
         $('#message_'+temp[1]).css("display","none");
     });
    
});
</script>
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

<!-- FULL WIDTH NO SIDEBAR START  -->
            <?=theme_user_navigation();?> 
            <div class="main_panel">
                <h1>Favourites</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
                <div class="top_select">
                    <p class="alignleft"><input type="checkbox" class="alignleft" id="all_chk"/> Select all</p>
                    <a href="javascript:void(0);" class="short_grey_button" id="all_delete">Delete Selected</a>
                </div>
                <div class="info">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
                      
                <?php
                    if(!empty($value))
                    {                  
                    //pr($value); 
                    $cnt=count($value);
                    foreach($value as $k=>$v)
                    { 
                        $style=$k==($cnt-1)?'class="last"':'';
                 ?>
                    <tr <?= $style;?>>
                        <td><input type="hidden" id="h_id_<?=$k;?>" name="h_id" value="<?= $v->id;?>"/></td>
                        <td width="25" align="left" valign="middle"><input type="checkbox" id="chk_<?=$k?>" class="chk" name="chk[]" value="<?= $v->id;?>"/></td>
                        <td width="43" align="left" valign="middle"><img src="<?= get_dashboard_profile_pic(get_userLoggedIn('id'))?get_dashboard_profile_pic(get_userLoggedIn('id')):site_url('resources/no_image.jpg')?>" width="33" height="33" alt="pic" /></td>
                        <td width="228" align="left" valign="middle"><?= $v->s_service_name;?> <span class="greytext clear"><?= $v->s_city.', '.$v->s_country?></span></td>
                        <td width="450" align="left" valign="middle">
                            <?php foreach($v->s_message as $key=>$msg):?>
                            <p class="greytext"><?=$msg['message'];?></p>
                            <?endforeach;?>
                            <div id="message_<?=$k?>" style="display: none;">
                            <form method="post" action="<?=site_url('favourites/add_message')?>">
                                <input id="id" name="id" type="hidden" value="<?= $v->id;?>">                
                                <input id="s_message" name="s_message" type="text" value="" size="38" />
                                <input type="submit" value="Save" class="leftmar short" /><input cancel="cancel" type="reset" value="Cancel" class="short cancel" id="cancel_<?=$k;?>" />
                            </form>
                            </div>
                        </td>
                        <td width="50" align="left" valign="middle"></td>
                        <td align="left" valign="middle"><a href="javascript:void(0);" class="short_grey_button add" id='add_<?=$k?>'>Add note</a><a href="javascript:void(0);" class="short_grey_button delete" id="delete_<?=$k?>">Delete</a></td>
                        
                        </tr>   
                <?php   
                    }
                    }
                    else
                        echo '<tr><td>'.message_line("no_information_found").'</td></tr>'; 
                ?>
                  </table>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 