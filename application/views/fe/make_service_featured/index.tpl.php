<script type="text/javascript">
    $(document).ready(function(){
        $(".payment_mode").click(function(){
            $(".payment_mode").css({'font-weight':'normal'});
            $(this).css({'font-weight':'bold'});
            $("input[name='h_payment_mode']").val($(this).attr('rel'));
        });
        
        $(".payment_mode").each(function(){
            if($(this).attr('rel')==$("input[name='h_payment_mode']").val())
            {
                $(".payment_mode").css({'font-weight':'normal'});
                $(this).css({'font-weight':'bold'});
            }
        });
    });
</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
<div class="full_no_sidebar">
    <?=theme_user_navigation();?>
    <div class="main_panel">
    <form name="make_service_featured" action="" method="post">
        <h1>Make Service Featured</h1>
        <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
        <div class="category_panel">
            <p class="no-bot-mar"><label class="alignleft no-bot-pad">Name</label> 
                <?=form_dropdown("service_id",dd_service(array("s.uid"=>intval(get_userLoggedIn('id')))),$posted['service_id'],'id="service_id"');?>
            </p>

            <p class="nomar"><label class="alignleft height_auto"></label> 
                <input type="checkbox" name="i_featured_online" class="alignleft rightmar" value="1" <?php if($posted['i_featured_online']==1) echo "checked='checked'"; ?>/> Online (If person is registered as online)</p>
            <p><label class="alignleft nomar height_auto"></label>
                <input type="checkbox" name="i_featured_location" class="alignleft rightmar" value="1" <?php if($posted['i_featured_location']==1) echo "checked='checked'"; ?> /> Location only</p>

            <p class="nomar">
                <label class="alignleft nomar height_auto"></label>
                <strong>Our Charges (select one)</strong>
            </p>
            <p>
            <label class="alignleft nomar height_auto"></label>
            <?php if(!empty($rate))
                {
                    foreach($rate as $k=>$vl){                        
                    ?>
                        <span class="alignleft">
                            
                            <input type="radio" name="i_price" class="leftmar rightmar alignleft" value="<?=$vl['pkg_id'];?>"  <?php if($posted['i_price']==$vl['pkg_id']) echo "checked='checked'"; ?> /> 
                            <span class="alignleft">Rs. <?=intval($vl['pkg_val']);?>  / month</span>
                        </span>
                    <?php         
                    }
                }
            ?>
            </p>
            <? /* <input type="radio" name="i_price" class="rightmar alignleft" /> <span class="alignleft">Rs.1000  / month</span>
                <input type="radio" name="i_price" class="leftmar rightmar alignleft" /> Rs.5500  / month</p>
            */ ?>

            <p><label class="alignleft nomar"></label> <input type="submit" value="Submit" /></p>
            <h2>How to pay us</h2>
            <div class="info skyborder">
                <input type="hidden" value="<?=$posted['h_payment_mode']?>" name="h_payment_mode">
                <ul class="arrow_list featured">
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Debit card">Debit card <span>Visa / Master Card / Maestro Card</span></a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Credit card">Credit card <span>Visa / Master Card / American Express</span></a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Internet Banking">Internet Banking</a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Cheque / DD">Cheque / DD</a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Cash before Delivery">Cash before Delivery (CBD)</a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="EMIs">EMIs</a></li>
                    <li><a href="javascript:void(0);" class="payment_mode" rel="Gift Certificate">Gift Certificate</a></li>
                </ul>
            </div>
            </form>
        </div>
    </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 