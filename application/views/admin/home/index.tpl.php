<script type="text/javascript" language="javascript" >
jQuery.noConflict();///$ can be used by other prototype which is not jquery
jQuery(function($) {
$(document).ready(function(){
    
    $("#login").submit(function(){
        $.blockUI({ message: 'Just a moment please...' });
        var b_valid=true;
        var s_err="";
        $("#div_err").hide("slow");  
        
        if($.trim($("#txt_user_name").val())=="") 
        {
            s_err='<div id="err_msg" class="error_massage">Please provide user name.</div>';
            b_valid=false;
        }
        
        if($.trim($("#txt_password").val())=="") 
        {
            s_err+='<div id="err_msg" class="error_massage">Please provide password.</div>';
            b_valid=false;
        }        
        
        /////////validating//////
        if(!b_valid)
        {
            $.unblockUI();  
            $("#div_err").html(s_err).show("slow");
        }
        
        return b_valid;        
    });
})});   
</script>


    <!-- Current user form -->
    <?/*
    <form action="<?=site_url("admin/home/forgot_password");?>" id="forgot">
        <div class="loginPic">
            <a href="javascript:void(0);" title=""><img src="<?=base_url().get_theme_path();?>images/userLogin2.png" alt="" /></a>
            <span>Eugene Kopyov</span>
            <div class="loginActions">
                <div><a href="javascript:void(0);" title="Login user" class="logleft flip"></a></div>
                <div><a href="javascript:void(0);" title="Forgot password?" class="logright"></a></div>
            </div>
        </div>
        
        <div class="fluid">
        <?php
          show_msg("error");  
          echo validation_errors('<div class="nNote nFailure">','</div>');
        ?> 
        </div>        
        
        <input type="text" id="txt_user_name" name="txt_user_name" value="<?=$posted["txt_user_name"];?>" placeholder="Your username" class="loginUsername" />
        
        <div class="logControl">
            <input type="submit" name="submit" value="Login" class="buttonM bBlue" />
        </div>
    </form>        
    */?> 
       
    <!-- user Login form -->
    <div class="fluid">
    <form action="" method="post" id="login">
        <div class="loginPic">
            <a href="javascript:void(0);" title=""><img src="<?=base_url().get_theme_path();?>images/userLogin2.png" alt="" /></a>
            <?/*<div class="loginActions">
                <div><a href="javascript:void(0);" title="Login user" class="logback flip"></a></div>
                <div><a href="javascript:void(0);" title="Forgot password?" class="logright"></a></div>
            </div>*/?>
        </div>
            
            <div class="fluid">
            <?php
              show_msg("error");  
              echo validation_errors('<div class="nNote nFailure">','</div>');
            ?> 
            </div>
            
            
        <input type="text" id="txt_user_name" name="txt_user_name" value="<?=$posted["txt_user_name"];?>" placeholder="Your username" class="loginUsername" />
        <input type="password" id="txt_password" name="txt_password" placeholder="Password" class="loginPassword" />
        
        <div class="logControl">
            <?/*<div class="memory"><input type="checkbox" checked="checked" class="check" id="remember2" /><label for="remember2">Remember me</label></div>*/?>
            <input type="submit" name="submit" value="Login" class="buttonM bBlue" />
        </div>
    </form>
    </div>
