<script type="text/javascript">
jQuery(function($){
    $(document).ready (function(){
		/*$(".pages li").each(function(){
			$(this).children('a').attr('href','javascript:void(0);');
		});*/
	});
});

</script>
<!-- FULL WIDTH NO SIDEBAR START  -->
<div class="full_no_sidebar">
	<?= theme_user_navigation();?>
	<div class="main_panel">
		<h1>All Service Provided</h1>
		<p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
		<!--<div class="top_select">
			<p class="alignleft"><input type="checkbox" class="alignleft" id="all_chk" /> Select all</p>
			<a href="javascript:void(0);" class="short_grey_button remove_all">Delete Selected</a>
		</div>-->
		<div class="info">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
				<tr>
					<th width="350" align="left">Service Name</th>
					<th width="85">Edit</th>
					<?php /*?><th width="85">Delete</th><?php */?>
					<th width="125">Status</th>
				</tr>
				<?php if(!empty($user_service)) {
						$len=count($user_service);
						foreach($user_service as $key=>$val)
							{
								$last=(($len==$key+1)?"class='last'":"");
				 ?>
				<tr <?php echo $last;?>>
					<td width="350" align="left" valign="middle"><?php echo $val->s_service_name; ?></td>
					<td width="85" align="center" valign="middle">
						<a href="<?php echo site_url( "service_profile/".encrypt($val->id) ) ?>" class="short_grey_button">Edit</a>
					</td>
					<?php /*?><td width="85" align="center" valign="middle">
						<a href="javascript:void(0);" class="short_grey_button">Delete</a>
					</td><?php */?>
					<td width="125" align="center" valign="middle">
						<?php if(!$val->i_featured) { ?>
						<a href="<?php echo site_url( "all_service_provided/make_featured/".encrypt($val->id) ) ?>" class="short_grey_button">Make Feature</a>
						<?php } else { ?>
						<span>Featured</span>
						<?php } ?>
					</td>
				</tr>
				
				<?php		}	 } else { ?>
				
				<?php } ?>
			</table>		 
	  </div>
	  <?php echo $link_pager;?>
	</div>
</div>
<!-- FULL WIDTH NO SIDEBAR END  --> 