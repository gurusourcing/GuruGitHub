<div class="top_select clear">
  <p class="alignleft">
  <input type="checkbox" class="alignleft" id="all_chk_outbox"/> Select all</p>
  <a href="javascript:void(0);" class="short_grey_button" id="all_delete_outbox">Delete Selected</a>
  <ul class="leftul alignright rightpad20">
	  <li class="top_pad">
	  <?php if($total_rows>0) { ?> <?php echo ($start+1) ?> to <?php echo $total_rows>($limit+$start)?($limit+$start):$total_rows ?> out of <?php echo $total_rows ?> <?php } else { ?> 0 out of 0 <?php } ?>
	  </li>
	  <!--<li class="nopad leftmar">
	  <a href="#">
	  <img src="<?php echo base_url()?>theme/guru_frontend/images/arrow2.jpg" width="24" height="24" alt="arrow" /></a>
	  </li>
	  <li class="nopad">
	  <a href="#"><img src="<?php echo base_url()?>theme/guru_frontend/images/arrow3.jpg" width="24" height="24" alt="arrow" />
	  </a>
	  </li>-->
	  <li class="nopad leftmar">
	  <?php echo $page_links_outbox ?>
	  </li>
  </ul>
</div>
<div class="info border">
<table width="698" border="0" align="center" cellpadding="0" cellspacing="0" class="name">
<?php 
if(!empty($outbox)) {			
	foreach($outbox as $ok=>$ov)
	{
		//$cls = ($ov["e_read_status"]=='read')?' class="grey" ':"";
		$cls = 'class="grey"';
 ?>

  <tr <?php echo $cls;?>>
	<td width="30" align="left" valign="middle">
	<input type="checkbox" name="outbox[]" id="chk_outbox_<?=$ok?>" class="chk_outbox_<?=$ov["i_id"];?>" value="<?=$ov["i_message_index_id"];?>" />
	</td>
	<td width="190" align="left" valign="middle"><?php echo $ov["receiver_name"]; ?></td>
	<td width="360" align="left" valign="middle">
	<span class="msg_open" rel="<?php echo $ov["i_message_index_id"];?>"><?php echo $ov["s_subject"]; ?></span>
	</td>
	<td align="right" valign="middle">
	<?php echo time_ago(strtotime($ov["dt_created_on"]),'');
	 ?></td>
  </tr>
  <tr id="full_msg_<?php echo $ov["i_message_index_id"]; ?>" style="display:none;" class="msg_info">
	<td colspan="4">
	<div style="background:#E9E9E9; clear:both;" >
		<p>Message :<b><?php echo $ov["s_body"]; ?></b></p>
	</div>
	</td>
 </tr>
  
<?php } 
} ?> 
</table>
</div>  
<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
		$(".del").remove();
	});
});
</script>