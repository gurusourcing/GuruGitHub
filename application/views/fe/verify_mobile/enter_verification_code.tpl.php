<?=theme_user_navigation();?>    

<!--PANEL WITH LEFT SIDEBAR START -->    
<div class="col_left_sidebar">
    <!--LEFT SIDEBAR START -->        
    <div class="left_sidebar">
        <!--verification start-->
        <?=theme_block_dashboard_verification();?>
        <!--verification end-->

    </div>
    <!--LEFT SIDEBAR END -->   
    <!--MAIN PANEL START -->
    <div class="main_panel">
        <h1 class="alignleft"><strong class="alignleft">Phone Verification</strong></h1>
        <div class="category_panel">
            <form name="frm_mob_verification_code" method="post" >
                <p><label class="alignleft">Enter security code</label> <input type="text" name="mob_verification_codes" value="<?=$verification_code;?>" required='true' size="40"></p>
                <p><label class="alignleft"></label> <input type="submit" value="Submit"></p>
            </form>

        </div>
    </div>          
    <!--MAIN PANEL END -->                       
        </div>
<!--PANEL WITH LEFT SIDEBAR END -->  