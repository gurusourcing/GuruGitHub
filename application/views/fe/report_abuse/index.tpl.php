<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){

  $("#change_image").click(function(){
        $("#captcha").attr('src','<?=site_url("captcha/index");?>/'+Math.random());
    });
    
    
});    
});

</script>
<?=theme_user_navigation();?>
<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <div class="main_panel">
                <h1><?=$page_title;?></h1>
                <form id="frm_report_abuse" action="" method="post">
                <input id="form_token" name="form_token" type="hidden" value="<?=$default_value["form_token"]?>">
                <input id="action" name="action" type="hidden" value="<?=$default_value["action"];?>">
                <input id="e_absue_for" name="e_absue_for" type="hidden" value="<?=$default_value["e_absue_for"];?>" >
                <input id="s_absue_for_id" name="s_absue_for_id" type="hidden" value="<?=$default_value["s_absue_for_id"];?>" >
                
                <?php
                /**
                * It is not possiable to put the options as checkbox here.
                * Because, If a user wants to report abuse for 
                * 1> userProfile Then we need uid 
                * 2> ServiceProfile then we need service id
                * 3> Company profile then we need company id
                * 
                * User can only come to this page from either 
                * profile page or service page or company page.
                * 
                * What will happen if a user is abusing for a service and 
                * the company has three services. So it is not possiable to determine. 
                * 
                */
                /*
                $abuse_for=dd_abuse_for();
                $c=0;
                foreach($abuse_for as $k=>$v)
                {
                    $temp = array(
                        'name'        => 'e_absue_for[]',
                        'id'          => 'e_absue_for_'.$k,
                        'value'       => $k,
                        'checked'     => in_array($k,$default_value["e_absue_for"]),
                        'class'       => 'rightmar',
                        );
                    
                    $str='<p class="padbot5 left200">
                            '.form_checkbox($temp).' '.$v.'
                    </p>';  
                    echo $str;  
                }*/
                
                ///printing the abuse for
                $abuse_for=dd_abuse_for();
                echo '<p class="padbot5 left200">'.@$abuse_for[ $default_value["e_absue_for"] ].'</p>';
                
                
                /*
                ?>
                <p class="padbot5 left200"><input type="checkbox" class="rightmar"  /> Report the profile as fake</p>
                <p class="padbot5 left200"><input type="checkbox" class="rightmar"  /> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> 
                <p class="padbot5 left200"><input type="checkbox" class="rightmar"  /> Donec laoreet sapien eget odio molestie bibendum.</p>
                */?>
               <div class="category_panel newservice signup_formsection">
                    <p><label class="alignleft">Write a description</label> 
                    <textarea id="s_report" name="s_report"  required='true' rows="5" cols="32" class="with240"><?=@$default_value["s_report"];?></textarea>
                    </p>
                    <p><label class="alignleft">&nbsp;</label>
                        <span class="alignleft top_pad">Type the code below
                        <img src="<?=site_url("captcha");?>" alt="Captcha" id="captcha" width="165" height="59" class="clear top_mar"/>
                        <a href="javascript:void(0);" id="change_image" >
                    <img src="<?=site_url( get_theme_path()."images/reload.png");?>" title="Reload" align="Reload"/>
                         </a><br />
                        <input class="top_mar botmar"  required='true' name="txt_captcha" id="txt_captcha" type="text"  value=""  />
                        <br />
                        <input type="submit" value="Send" class="top_mar" />
                        </span>
                    </p>                            
               </div>
               </form>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  -->