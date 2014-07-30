<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
       $("#start").click(function(){
           var html='<label for="name">Company Name</label>&nbsp;&nbsp;<input type="text" name="s_company" id="s_company" class="text ui-widget-content ui-corner-all" cols="35"/>';
           $("#dialog-common").find('#dialog_msg').html(html);
           $("#dialog-common").dialog('open')  ;
       });
       
    /** 
    * dialoge box for company name
    */
    $( "#dialog-common" ).dialog({
        autoOpen: false,
        resizable: false,
        height:200,
        width:350,
        modal: true,
        buttons: {
            "Save": function() {
                $.post("<?=site_url("start_company/ajaxSaveCompany")?>",{'s_company': $('#s_company').attr('value')}, 
                                function(data)
                                {
									if(data!='')
									{
										window.location.href = data;
									}
                                    <?php /*?>if(data=='success')
                                    {
                                        window.location.href='<?=site_url('company_profile');?>';
                                    }
                                    else
                                    {
                                        $( "#dialog-common").find("#dialog_msg").find('#s_company').before(data);
                                    }<?php */?>
                                }
                     );
                //$( this ).dialog( "close" );
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
      
    });
});

</script>



<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?=theme_user_navigation();?>
            <?php 
                if(is_not_company_owner())
                {
            ?>
                    <div class="main_panel">
                        <h1>Start Company (if not a company)</h1>
                        <?=format_text($not_company->s_content,"decode");?> 
                        <p class="aligncenter"><a href="javascript:void(0);" class="orange_button_big" id="start">Start Company</a></p>             
                    </div>
            <?php 
                }
                else
                {
          
            ?>
            
                    <div class="main_panel">
                        <h1>SRM GROUP (if a company)</h1>
                        <?=format_text($company->s_content,"decode");?>
                        <p class="aligncenter"><a href="<?=site_url('service_profile/add_service_once');?>" class="orange_button_big">Add a new Service Provider</a></p> 
                    </div>
            <?php
                }
            ?>
            
            
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 