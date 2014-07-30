<?php
/**
* Admin User add edit form
* Admin can modify the administrative users from here.
* 
* 
* @see, controllers/admin/admin_user.php  
*/
?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

   var inE= $("#admin_user").inedit({
        //no need to put the save and cancel buttons//
       "globalSaveResetButton": true,
       "sections" : {
            0 : {
                "fieldContainer" : "#form_cont",
                "contentContainer" : "#form_lbl",
                "defaultValues" : $.parseJSON('<?=$default_value[0];?>'),
                "ajaxSaveUrl"   : "<?=admin_base_url("admin_user/ajax_operation");?>",
                "saveSuccessRedirectUrl" : "<?=$listing_path;?>", ///rediect after success
                "cancelRedirectUrl" : "<?=$listing_path;?>", //redirect when cancel 
                "beforeHideCallback" : function(contentContainer,values){
                    $.each(values,function(i,v){
                      var lbl='#'+i+'_lbl';
                      $(lbl).text(v);  
                    });
                }
            }            
                             
       }
        
    });
    
    //inE.data("inE").showMessage("error","There is an server error. Please try again.")
    //console.log();
    
    
    
});    
});


</script>


<?/*
<div id="right_panel">
    <h2><?php echo $page_title;?></h2>
    <div id="accountlist">
        <div id="admin_user">
            <ul class="name_list edit_form">
                <li>
                    <div id="form_lbl" class="alignleft">
                        <div style="margin: 5px 0;" >
                            User Role : <label id="admin_type_id_lbl"></label>
                        </div>   
                        <div style="margin: 5px 0;" >
                            User name : <label id="s_admin_name_lbl"></label>
                        </div>                          
                        <div style="margin: 5px 0;" >
                            User belongs to domain : <label id="s_domain_name_lbl"></label>
                        </div>                        
                    </div>
                    <div id="form_cont" class="edit_section">
                        
                        <ul>
                            <li>
                                <input id="form_token" name="form_token" type="hidden" value="">
                                <input id="action" name="action" type="hidden" value="">
                                <span class="alignleft">
                                    User Role : <?=form_dropdown('admin_type_id', dd_admin_type());?>
                                </span>
                                <span class="alignleft">
                                    User name : <input type="text" name="s_admin_name" id="s_admin_name" value="">
                                </span>   
                  <span class="alignleft">
                   New Password : <input id="s_password" name="s_password" type="password" value="" />
                   Confirm Password : <input id="s_confirm_password" name="s_confirm_password" type="password" value="" />
                  </span> 
                                <?
                                if(
                                    (check_multiPermAccess(array("administer admin user","add any admin")) 
                                        && $action=="add")
                                    || (check_multiPermAccess(array("administer admin user","edit any admin")) 
                                        && $action=="edit")
                                )
                                {
                                ?>                                   
                                <span class="alignleft">
                                    User belongs to domain : <?=form_dropdown('s_domain_name', dd_domain());?>
                                </span>                                                              
                                <?
                                }
                                else
                                {
                                    ?>
                                    <input id="s_domain_name" name="s_domain_name" type="hidden" value="">
                                    <?
                                }
                                ?>
                            </li>
                        </ul>
                        
                        
                    </div>
                    <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a> 
                </li>               
            </ul>
            
        </div>
    </div>
</div>
*/?>



<div id="admin_user" class="widget fluid">
    <div class="formRow" style="padding-top: 0px;">
        <div id="form_lbl" class="grid3">
            <div class="formRow" >
                <label>User Role :&nbsp;</label><label id="admin_type_id_lbl" ></label>
            </div>   
            <div class="formRow" >
                <label>User name :&nbsp;</label><label id="s_admin_name_lbl"></label>
            </div>                          
            <div class="formRow" >
                <label>User belongs to domain :&nbsp;</label><label id="s_domain_name_lbl"></label>
            </div>           
        </div>
        <div id="form_cont" class="grid9">
              <input id="form_token" name="form_token" type="hidden" value="">
              <input id="action" name="action" type="hidden" value="">  
                  <div class="formRow fluid">
                  <div class="grid4"><label>User role :</label></div> 
                  <div class="grid4"><?=form_dropdown('admin_type_id', dd_admin_type(),"",'id="admin_type_id"');?></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>User name :</label></div>
                  <div class="grid4"><input type="text" name="s_admin_name" id="s_admin_name" value=""></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>New password :</label></div>
                  <div class="grid4"><input id="s_password" name="s_password" type="password" value="" /></div>
                  </div>
                  <div class="formRow">
                  <div class="grid4"><label>Confirm password :</label></div>
                  <div class="grid4"><input id="s_confirm_password" name="s_confirm_password" type="password" value="" /></div>
                  </div>              
                <?
                if(
                    (check_multiPermAccess(array("administer admin user","add any admin")) 
                        && $action=="add")
                    || (check_multiPermAccess(array("administer admin user","edit any admin")) 
                        && $action=="edit")
                )
                {
                ?>    
                    <div class="formRow">
                    <div class="grid4"><label>User belongs to domain :</label></div>
                    <div class="grid4"><?=form_dropdown('s_domain_name', dd_domain(),"",'id="s_domain_name"');?></div>
                    </div>                
                <?
                }///end if
                else
                {
                    ?>
                    <input id="s_domain_name" name="s_domain_name" type="hidden" value="">
                    <?
                }
                ?>                        
        </div>
        <a href="javascript:void(0);"  class="right-top edit" title="Edit">Edit</a>         
    </div>
</div>