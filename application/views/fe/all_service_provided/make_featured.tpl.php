<script type="text/javascript">
jQuery(function($){
    $(document).ready (function(){
		$(".chk_price").each(function(){
			$(this).click(function(){
				var id_ = $(this).attr('rel');
				$(".feature_id").val(id_);
			});
		});
	});
});

</script>
<!-- FULL WIDTH NO SIDEBAR START  -->
<div class="full_no_sidebar">	
	<?= theme_user_navigation();?>
	<form name="frm_service_feture" id="frm_service_feture" action="" method="post">
	<input id="form_token" name="form_token" type="hidden" value="<?php echo $form_token?>">
	<div class="main_panel">
		<h1>Make Service Featured</h1>
		<p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
		<div class="category_panel">
		<p class="no-bot-mar">
		<label class="alignleft no-bot-pad">Name</label> 
		<!--<select>
		<option>Private tutor for Sc. Subject</option>
		</select>-->	
		<?php //pr($s_condition); ?>
		<select name="service_id" id="service_id">
			<option value="">Select</option>
			<?php echo makeOptionServices($s_condition,$sid) ?>
		</select>
		</p>
		<!--<p class="nomar"><label class="alignleft height_auto"></label> 
		<input type="checkbox" class="alignleft rightmar" /> Online (If person is registered as online)</p>
		<p><label class="alignleft nomar height_auto"></label> 
		<input type="checkbox" class="alignleft rightmar" /> Location only</p>
		 <p class="nomar"><label class="alignleft nomar height_auto"></label> <strong>Our Charges (select one)</strong></p>
		 <p><label class="alignleft nomar height_auto"></label> 
		 <input type="checkbox" class="rightmar alignleft" /> <span class="alignleft">Rs.1000  / month</span> 
		 <input type="checkbox" class="leftmar rightmar alignleft" /> Rs.5500  / month
		 </p>-->
		<p class="nomar"><label class="alignleft height_auto"></label> 
		<input type="radio" name="i_type" class="alignleft rightmar" checked="checked" value="1" /> Online (If person is registered as online)</p>
		<p><label class="alignleft nomar height_auto"></label> 
		<input type="radio" name="i_type" class="alignleft rightmar" value="2" /> Location only</p>
		 <p class="nomar"><label class="alignleft nomar height_auto"></label> <strong>Our Charges (select one)</strong></p>
		 <p>
		 <?php /*?><label class="alignleft nomar height_auto"></label> 
		 <input type="radio" name="i_price" class="rightmar alignleft" checked="checked" value="1000"/> 
		 <span class="alignleft">Rs.1000  / month</span> 
		 <input type="radio" name="i_price" class="leftmar rightmar alignleft" value="5500" /> Rs.5500  / month<?php */?>
		 <label class="alignleft nomar height_auto"></label> 
		 <?php if($packages) { 
				$i=1;
		 		foreach($packages as $val){
				$chkd = ($i==1)?'checked="checked"':'';
		 ?>
		 <input type="hidden" name="feature_id" class="feature_id" value="<?php echo $val->id ?>" />
		 <input type="radio" name="i_price" class="rightmar alignleft chk_price" <?=$chkd?> rel="<?php echo $val->id ?>" value="<?php echo $val->i_price ?>"/> 
		 <span class="alignleft" style="margin-right:10px;"><?php echo $val->s_package_name ?> (Rs. <?php echo $val->i_price ?>)</span>
		 
		 <?php $i++; } } ?>
		 </p>
		 <p><label class="alignleft nomar"></label> 
		 <input type="submit" value="Submit" />
		 </p>
		 <h2>How to pay us</h2>
		 <div class="info skyborder">
			<ul class="arrow_list featured">
				<li><a href="javascript:void(0);">Debit card <span>Visa / Master Card / Maestro Card</span></a></li>
				<li><a href="javascript:void(0);">Credit card <span>Visa / Master Card / American Express</span></a></li>
				<li><a href="javascript:void(0);">Internet Banking</a></li>
				<li><a href="javascript:void(0);">Cheque / DD</a></li>
				<li><a href="javascript:void(0);">Cash before Delivery (CBD)</a></li>
				<li><a href="javascript:void(0);">EMIs</a></li>
				<li><a href="javascript:void(0);">Gift Certificate</a></li>
			</ul>
		 </div>
		</div>				
	</div>
	</form>
</div>
<!-- FULL WIDTH NO SIDEBAR END  --> 