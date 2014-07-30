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
            <form method="post" name="frm_mobile_num">
                <p><label class="alignleft">Your phone number</label> <input type="text" class="onlyNumber" required='true' value="<?=$mobile_num;?>" name="mobile_num" id="mobile_num" size="40" maxlength="10"></p>

                <p><label class="alignleft"></label> <input type="submit" id="submit_verify" value="Verify"></p>
            </form>

        </div>

    </div>          
    <!--MAIN PANEL END -->                       
</div>
<!--PANEL WITH LEFT SIDEBAR END -->  
<script type="text/javascript" language="javascript">
    jQuery(function($){
        $(document).ready(function(){
           $(".onlyNumber").keydown(function(event) {
                // Allow: backspace, delete, tab, escape, and enter
                if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
                // Allow: Ctrl+A
                (event.keyCode == 65 && event.ctrlKey === true) || 
                // Allow: home, end, left, right
                (event.keyCode >= 35 && event.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                else {
                    // Ensure that it is a number and stop the keypress
                    if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                        event.preventDefault(); 
                    }   
                }
            });
        });

    });

</script>