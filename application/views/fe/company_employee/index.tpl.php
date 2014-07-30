<script type="text/javascript">
jQuery(function($){
    $(document).ready (function(){
        var company_employee={"uid":0};//global var
        
       /**
       * changing status
       */
       $(".active_inactive").click(function(){
          var uid=$(this).attr("rel");
           $.post('<?=site_url('company_employee/ajax_change_status');?>',{'uid': uid}, 
                        function(data){
                            location.reload(true);
                        }
               );
       });
       
       /**
       * change employee role
       */
       $('.e_employee_role').change(function(){
          var role=$(this).val();
          var id=$(this).attr('rel');
          $.post('<?=site_url('company_employee/ajaxChangerole')?>',{role : role, id: id}, 
                            function(){
                                location.reload(true);
                            });
       });
       
       /**
       * change employee service
       */
       $('.service_ids').change(function(){
          if($.trim($(this).val())!="")
          {
              var emp_id = $(this).parent().parent().find('.chk_').attr('id');
              var service=$(this).val();
              var id=$(this).attr('rel');
              var prev_service_id = $(this).prev().val();
              $.post('<?=site_url('company_employee/ajaxChangeservice')?>',{'service' : service, 'id': id,'emp_id' : emp_id, "prev_service_id":prev_service_id}, 
                                function(){
                                    location.reload(true);
                                });
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
    * delete single
    */
     $('.remove').each(function(){
        $(this).click(function(){
            company_employee.uid=$(this).attr('id');
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
               id[i]=$(this).attr('id');
           });
            company_employee.uid=id;
            $( "#dialog-confirm-delete" ).dialog( "open" );     
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
                $.post("<?=site_url("company_employee/ajaxDeleteEmployee")?>",company_employee, 
                                function()
                                {
                                    location.reload(true);
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
      
        
    });
});

</script>


<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?= theme_user_navigation();?>
            <div class="main_panel">
                <h1>All Employee</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
                <div class="top_select">
                    <p class="alignleft"><input type="checkbox" class="alignleft" id="all_chk" /> Select all</p>
                    <a href="javascript:void(0);" class="short_grey_button remove_all">Delete Selected</a>
                </div>
                <div class="info">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
                    <?php 
                        if(!empty($values))
                        {
                            $len=count($values);
                            foreach($values as $k=>$v)
                            {
                                $last=(($len==$k+1)?"class='last'":"");
                                
                   ?>             
                        <tr <?=$last?> >
                            <td width="25" align="left" valign="middle"><input type="checkbox" class="chk_" id="<?=$v->uid;?>" /></td>
                            <td width="43" align="left" valign="middle"><?=theme_user_thumb_picture(intval($v->uid));?></td>
                            <td width="263" align="left" valign="middle"><?=get_dashboard_profile_name(intval($v->uid));?><span class="grey">(<?=(($v->i_active==1)?"Active":"Inactive");?>)</span></td>
                            <td width="127" align="left" valign="middle"><span class="grey"><?=(isset($v->s_title)?$v->s_title:"N/A")?></span></td>
                            <td width="250" align="left" valign="middle">
                                <?=form_dropdown("e_employee_role",dd_employeeRole(),$v->e_employee_role,'id="e_employee_role" class="alignleft e_employee_role" rel="'.$v->id.'"');?>
                                <input type="hidden" name="h_service_name" id="h_service_name" value="<?=$v->service_ids[0]?>">
                                <?=form_dropdown("service_ids",dd_service(array("s.comp_id"=>$v->comp_id,"s.i_active"=>1)),$v->service_ids[0],'id="service_ids" class="alignleft service_ids" rel="'.$v->id.'"');?>
                            </td>
                            <td align="left" valign="middle">
                                <a href="<?=site_url($v->s_short_url);?>" class="short_grey_button">View</a>
                                <a href="<?=site_url("company_employee/edit_company_employee/".encrypt($v->uid));?>" class="short_grey_button">Edit</a>
                                <a href="javascript:void(0);" class="short_grey_button active_inactive" rel="<?=$v->uid;?>"><?=(($v->i_active==1)?"Inactive":"Active");?></a>
                                <a href="javascript:void(0);" class="short_grey_button grey_to_orange remove" id="<?=$v->uid;?>">Delete</a>
                            </td>
                        </tr>   
                   <?php           
                            }//end foreach
                        }//end if
                        else
                            echo '<tr><td>'.message_line("no_information_found").'</td></tr>';                         
                    ?>
                    
                  </table>
                  <?=$pagination;?>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 


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
///common delete confirm box////
?>
<div id="dialog-confirm-delete" title="Delete this saved search?">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Item(s) will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<?php 
////common delete confirm box////
?>