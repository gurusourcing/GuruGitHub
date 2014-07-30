<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
/*        $("#dt_dob").datepicker({
            "dateFormat": "dd-mm-yy",
            "showButtonPanel": true,
            "closeText": "Close",
            "changeYear": true, 
        });*/ 
    });    
});

</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?= theme_user_navigation();?>
            <div class="main_panel">
                <h1><?=$page_title;?></h1>
                <p><?=get_cms(13,'s_content');?></p>
                <form id="frm_service" action="" method="post">
                <input id="form_token" name="form_token" type="hidden" value="<?=@$form_token;?>">
                <input id="action" name="action" type="hidden" value="<?=$action;?>">
                
                <div class="category_panel newservice">
                    <p><label class="alignleft">Employee name</label> 
                        <input id="s_name" name="s_name" required="true" type="text" size="38" value="<?=@$posted["s_name"];?>" /></p>
                    <p><label class="alignleft">Employee email</label> 
                        <input id="s_email" name="s_email" required="true" type="text" size="38" value="<?=@$posted["s_email"];?>"/></p>
                    <p><label class="alignleft">Gender</label> 
                        <input type="radio" name="e_gender" value="Male" required="true" class="top_mar" <?php if(@$posted["e_gender"]=="Male") echo "checked='checked'";?>>Male
                        <input type="radio" name="e_gender" value="Female" required="true" class="leftmar" <?php if(@$posted["e_gender"]=="Female") echo "checked='checked'";?>>Female
                    </p>
                   <? /*label class="alignleft">Date of birth</label> 
                        <input id="dt_dob" name="dt_dob" required="true" type="text" size="38" value="<?=@$posted["dt_dob"];?>"/></p>                */?>
                    <p><label class="alignleft"></label> 
                    <input type="submit" value="Proceed" /></p>
                </div>
               </form>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  -->
