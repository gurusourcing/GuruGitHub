<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    ///show hide company
    $("input[name='i_is_company_service']").each(function(){
       $(this).on("click",function(){
          
           console.log($(this).is(":checked") );
           
           if($(this).is(":checked") 
            && $(this).attr("value")==1
          )///company is selected
          {
              $("#s_company").attr("required","true");
              $("#spn_company").fadeIn("slow");
          }
          else if($(this).is(":checked") 
            && $(this).attr("value")==0
          )///Individual is selected
          {
              $("#s_company").removeAttr("required");
              $("#spn_company").fadeOut("slow");
          }
           
       }); 
    });  
    ///end show hide company///
    
});    
});

</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
		<?=theme_user_navigation();?>
            <div class="main_panel">
                <h1><?=$page_title;?></h1>
                <p><?=get_cms(9,'s_content');?></p>
                <form id="frm_service_once" action="" method="post">
                <input id="form_token" name="form_token" type="hidden" value="<?=$default_value["form_token"]?>">
                <input id="action" name="action" type="hidden" value="<?=$default_value["action"];?>">
                              
                  <div class="category_panel newservice signup_formsection">
                            <p class="no-bot-pad no-bot-mar"><label class="alignleft height_auto">&nbsp;</label> 
                            <span class="alignleft rightpad" >
                            <?
                            $rdo=array(
                                'name'        => 'i_is_company_service',
                                'id'          => 'i_is_company_service',
                                'value'       => '0',
                                'checked'     => (bool)@$default_value["i_is_company_service"],
                                'class'       => 'rightmar', 
                                'required'    => 'true',                             
                            );
                            print form_radio($rdo);
                            ?> Individual
                            </span> 
                            <span class="alignleft">
                            <?
                            $rdo["value"]=1;
                            print form_radio($rdo);
                            ?> Company</span> 
                            </p>
                            <p>
                            <div id="spn_company" style="display: none;">
                                <p>
                                <label class="alignleft">Company name</label> 
                                    <input id="s_company" name="s_company" value="<?=@$default_value["s_company"]?>" type="text" size="40" />
                                </p>
                                <p><label class="alignleft">&nbsp;</label>
                                <?
                                $chk=array(
                                    'name'        => 'i_is_registered',
                                    'id'          => 'i_is_registered',
                                    'value'       => '1',
                                    'checked'     => (bool)@$default_value["i_is_registered"],
                                    'class'       => 'rightmar',                            
                                );
                                print form_checkbox($chk);
                                ?>  Registered Company<br />
                                </p>
                            </div>
                            
                            <input type="submit" value="Submit" class="top_mar" />
                            </p>
                            
                </div> 
                <div class="facebook_login toppad20">
                <h2>Help or supporting information</h2>
                    <?=get_cms(9,'s_content');?>
                </div>
               </form>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  -->
