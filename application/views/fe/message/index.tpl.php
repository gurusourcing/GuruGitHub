<script type="text/javascript">
jQuery(function($){
    $(document).ready(function(){
		$(".s2").CarouSlide({
		  showSlideNav:true,
		});
		
		
		<?php if($to_outbox) { ?>
		$(".li2 a").click();
		<?php } else if($to_draft) { ?>
		$(".li4 a").click();
		<?php } else if($to_inbox) { ?>
		$(".li1 a").click();		
		<?php } else { ?>
		$(".li4 a").click();
		$(".li1 a").click();
		<?php } ?>
		
		 $( "#to_user" ).autocomplete({
			source: "<?=site_url("autocomplete/ajax_searchUserList")?>",
			minLength: 2,
			select: function( event, ui ) {				
				if(ui.item)
				{					
					$("#srch_uid").attr("value",ui.item.id);
				}
			},
			search: function( event, ui ) {
				$("#srch_uid").attr("value","");
			}
		});
		
		//////Autocomplete Mandatory Fields////
		$(document).ajaxSend(function(e, xhr, settings) {
			var u='<?=site_url("autocomplete/ajax_searchUserList?term=");?>'+$("#to_user").attr("value");
			
			if (settings.url == u) {
				settings.url=settings.url;
			}    
			
		});   
		
		//// submit the form  ////
		$("#btn_msg_send").bind('click',function(){
		
			var b_valid = true;
			var msg =new Array();
			if($("#srch_uid").val()=="")
			{
				b_valid = false;				
				msg.push('Please select user from autosuggest.\n');
			}
			if($.trim($("#s_subject").val())=="" ||($("#s_subject").val()=="Subject of the message"))
			{
				b_valid = false;				
				msg.push('Please provide subject.\n');
			}
			if($.trim($("#s_message").val())=="")
			{
				b_valid = false;
				msg.push('Please provide message.\n');
			}
			
			if(b_valid)
			{
				$("#post_msg").submit();
			}
			else
			{
				var show_msg = '';
				for(var i=0; i<msg.length;i++)
				{
					show_msg+=msg[i];
				}
				//alert(show_msg);
				$( "#dialog-alert" ).find('#dialog_msg').html( show_msg);
				$( "#dialog-alert" ).dialog( "open" );
			}
		});
		
		//// save to draft the message ////
		$("#btn_draft").bind('click',function(){			
			
			var DRAFT={'userId':0,'subject':'','message':''};
			var b_valid = true;	
			var err_msg = '';
			if(($.trim($("#s_subject").val())=="" ||($("#s_subject").val()=="Subject of the message")) && $.trim($("#s_message").val())=="")
			{
				b_valid = false;				
				err_msg = 'Please provide subject or message.';
			}
					
			if(b_valid)
			{
				DRAFT.userId 	= $("#srch_uid").val();
				DRAFT.subject 	= $("#s_subject").val();
				DRAFT.message 	= $("#s_message").val();
				$.post("<?= site_url('message/ajaxSaveToDraft')?>",DRAFT, function(data){
							window.location.href='<?=site_url('message')?>';
							//$("#outbox_msg").html(data);
						});
			}
			else
			{
				$( "#dialog-alert" ).find('#dialog_msg').html( err_msg);
				$( "#dialog-alert" ).dialog( "open" );
			}
			
		});
		
		
		/******************* OUTBOX CODE START ******************/
		var ID_OUTBOX={'id':0,'folder':'outbox'};
		 /**
		* delete multile entry
		*/
		 $("#all_delete_outbox").click(function(){
			if (!$('input[class^=chk_outbox_]').is(':checked'))
				{
				   $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
					$( "#dialog-alert" ).dialog( "open" ); 
				}
			else
			{
			   var chk=[];
				$("input[class^=chk_outbox_]:checked").each(function(i){
					chk[i]=$(this).attr('value');
			   });
			   ID_OUTBOX.id=chk;
			   $( "#dialog-confirm-delete-outbox" ).dialog( "open" ); 
			}
	
		 });
		 
		 
		/**
		* alert box
		*/
		 $( "#dialog-alert" ).dialog({
			autoOpen: false,
			modal: true,
			buttons: {
			"Ok": function() {
				$( this ).dialog( "close" );
				}
			},
			hide: {
					/*effect: "explode",*/
					duration: 1000
		 }
		 });
		 
		  /**
		 * delete confirm modal box
		 */
		  $( "#dialog-confirm-delete-outbox" ).dialog({
			autoOpen: false,
			resizable: false,
			height:200,
			width:350,
			modal: true,
			buttons: {
				"Delete": function() {
					 
					  $.post("<?= site_url('message/ajaxdeleteMessageMulti')?>",ID_OUTBOX, function(data){
							window.location.href='<?=site_url('message')?>';
							//$("#outbox_msg").html(data);
						});
					$( this ).dialog( "close" );
				},
				"Cancel": function() {
					$( this ).dialog( "close" );
				}
				
			},
			 hide: {
					/*effect: "explode",*/
					duration: 1000
			 }
		  });
		
		/**
		 * check all check boxes
		 */
		$("#all_chk_outbox").click(function(){
			if ($("#all_chk_outbox").is(":checked")){
				$('input[id^=chk_outbox_]').prop('checked', true);
			}
			else
				$('input[id^=chk_outbox_]').prop('checked', false); 
		});
		
		 /**
		* uncheck the select all check box if any chkbox is unchecked
		* and the select all check box if all select box is selected
		*/
		$('input[id^=chk_outbox_]').click(function(){
			if ($('input[type=checkbox]:not(:checked)').length)
			   $("input[id=all_chk_outbox]").prop('checked',false);
		   if (!$('input[id^=chk_outbox_]:not(:checked)').length)
				$("input[id=all_chk_outbox]").prop('checked',true);
		});
		
		/******************* OUTBOX CODE END ******************/
		
		/******************* INBOX CODE START ******************/
		var ID_INBOX={'id':0,'folder':'inbox'};
		 /**
		* delete multile entry
		*/
		 $("#all_delete_inbox").click(function(){
			if (!$('input[class^=chk_inbox_]').is(':checked'))
				{
				   $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
					$( "#dialog-alert" ).dialog( "open" ); 
				}
			else
			{
			   var chk=[];
				$("input[class^=chk_inbox_]:checked").each(function(i){
					chk[i]=$(this).attr('value');
			   });
			   ID_INBOX.id=chk;
			   $( "#dialog-confirm-delete-inbox" ).dialog( "open" ); 
			}
	
		 });
		 
		 
		/**
		* alert box
		*/
		 $( "#dialog-alert" ).dialog({
			autoOpen: false,
			modal: true,
			buttons: {
			"Ok": function() {
				$( this ).dialog( "close" );
				}
			},
			hide: {
					/*effect: "explode",*/
					duration: 1000
		 }
		 });
		 
		  /**
		 * delete confirm modal box
		 */
		  $( "#dialog-confirm-delete-inbox" ).dialog({
			autoOpen: false,
			resizable: false,
			height:200,
			width:350,
			modal: true,
			buttons: {
				"Delete": function() {
					 
					  $.post("<?= site_url('message/ajaxdeleteMessageMulti')?>",ID_INBOX, function(data){
							window.location.href='<?=site_url('message')?>';
							//$("#inbox_msg").html(data);
						});
					$( this ).dialog( "close" );
				},
				"Cancel": function() {
					$( this ).dialog( "close" );
				}
				
			},
			 hide: {
					/*effect: "explode",*/
					duration: 1000
			 }
		  });
		
		/**
		 * check all check boxes
		 */
		$("#all_chk_inbox").click(function(){
			if ($("#all_chk_inbox").is(":checked")){
				$('input[id^=chk_inbox_]').prop('checked', true);
			}
			else
				$('input[id^=chk_inbox_]').prop('checked', false); 
		});
		
		 /**
		* uncheck the select all check box if any chkbox is unchecked
		* and the select all check box if all select box is selected
		*/
		$('input[id^=chk_inbox_]').click(function(){
			if ($('input[type=checkbox]:not(:checked)').length)
			   $("input[id=all_chk_inbox]").prop('checked',false);
		   if (!$('input[id^=chk_inbox_]:not(:checked)').length)
				$("input[id=all_chk_inbox]").prop('checked',true);
		});
		
		/******************* INBOX CODE END ******************/
		
		/******************* DRAFT CODE START ******************/
		var ID_DRAFT={'id':0,'folder':'draft'};
		 /**
		* delete multile entry
		*/
		 $("#all_delete_draft").click(function(){
			if (!$('input[class^=chk_draft_]').is(':checked'))
				{
				   $( "#dialog-alert" ).find('#dialog_msg').html( "Please select atleast one item." );
					$( "#dialog-alert" ).dialog( "open" ); 
				}
			else
			{
			   var chk=[];
				$("input[class^=chk_draft_]:checked").each(function(i){
					chk[i]=$(this).attr('value');
			   });
			   ID_DRAFT.id=chk;
			   $( "#dialog-confirm-delete-draft" ).dialog( "open" ); 
			}
	
		 });
		 
		 
		/**
		* alert box
		*/
		 $( "#dialog-alert" ).dialog({
			autoOpen: false,
			modal: true,
			buttons: {
			"Ok": function() {
				$( this ).dialog( "close" );
				}
			},
			hide: {
					/*effect: "explode",*/
					duration: 1000
		 }
		 });
		 
		  /**
		 * delete confirm modal box
		 */
		  $( "#dialog-confirm-delete-draft" ).dialog({
			autoOpen: false,
			resizable: false,
			height:200,
			width:350,
			modal: true,
			buttons: {
				"Delete": function() {
					 
					  $.post("<?= site_url('message/ajaxdeleteMessageMulti')?>",ID_DRAFT, function(data){
							window.location.href='<?=site_url('message')?>';
							//$("#draft_msg").html(data);
						});
					$( this ).dialog( "close" );
				},
				"Cancel": function() {
					$( this ).dialog( "close" );
				}
				
			},
			 hide: {
					/*effect: "explode",*/
					duration: 1000
			 }
		  });
		
		/**
		 * check all check boxes
		 */
		$("#all_chk_draft").click(function(){
			if ($("#all_chk_draft").is(":checked")){
				$('input[id^=chk_draft_]').prop('checked', true);
			}
			else
				$('input[id^=chk_draft_]').prop('checked', false); 
		});
		
		 /**
		* uncheck the select all check box if any chkbox is unchecked
		* and the select all check box if all select box is selected
		*/
		$('input[id^=chk_draft_]').click(function(){
			if ($('input[type=checkbox]:not(:checked)').length)
			   $("input[id=all_chk_draft]").prop('checked',false);
		   if (!$('input[id^=chk_draft_]:not(:checked)').length)
				$("input[id=all_chk_draft]").prop('checked',true);
		});
		
		/******************* DRAFt CODE END ******************/
		
		/************** OPEN MESSAGE  ******************/
		$(".msg_open").click(function(){
			var id_ = $(this).attr('rel');
			$('#full_msg_'+id_).toggle('slow');
		});
		
		var IDX_MSG={'id':0};
		$(".msg_open_inbox").click(function(){
			var id_ = $(this).attr('rel');
			$(this).parent().parent('tr').addClass('grey');
			$('#full_msg_inbox_'+id_).toggle('slow',function(){ 
					IDX_MSG.id=id_;
				 	$.post("<?= site_url('message/ajaxreadMessage')?>",IDX_MSG, function(data){
						
							$(".new_msg_no").text(data);
					});
			  });
		});
		
	});
});

</script>

<!-- FULL WIDTH NO SIDEBAR START  -->
<div class="full_no_sidebar">
	<?=theme_user_navigation();?> 
<!--PANEL WITH LEFT SIDEBAR START -->    
   <div class="col_left_sidebar CarouSlide s2">
<!--LEFT SIDEBAR START -->        
	  <div class="left_sidebar">
		<ul class="tab_link slider-nav">
			<li class="li1"><a href="#no1">Message Inbox <?php if($new_msg){?><b class="red_bg"><span class="new_msg_no"><?php echo $new_msg; ?></span></b><?php } ?></a></li>
			<li class="li2"><a href="#no2">Message Outbox</a></li>
			<li class="li3"><a href="#no3">Create new message</a></li>
			<li class="li4"><a href="#no4">Draft of message</a></li>
		</ul>
	  </div>
<!--LEFT SIDEBAR END -->   
<!--MAIN PANEL START -->
	<div class="main_panel">            
		<div class="category-info text-info slider-holder">
			<!-- INBOX START -->
			<div class="info" id="no1">
				<h1 class="alignleft">Inbox <span class="short greytext"><?php if($new_msg){ ?>(<span class="new_msg_no"><?php echo $new_msg; ?></span> new)<?php } ?></span></h1>		  
			   	<div id="inbox_msg">
				<?php echo $inbox_msg; ?>
				</div> 
	   		</div> 
	   		<!-- INBOX END -->
	   		<!-- OUTBOX START -->
			<div class="info" id="no2">
				<h1 class="alignleft">Outbox <span class="short greytext"><?php if($new_msg){ ?>(<span class="new_msg_no"><?php echo $new_msg; ?></span> new)<?php } ?></span></h1>
		  		<div id="outbox_msg">
				<?php echo $outbox_msg; ?>
				</div> 
	   		</div> 
			<!-- OUTBOX END -->
	   		<!-- POST MESSAGE START -->
	   		<div class="info" id="no3">
			<h1 class="alignleft">Create New Message <span class="short greytext"><?php if($new_msg){ ?>(<span class="new_msg_no"><?php echo $new_msg; ?></span> new)<?php } ?></span></h1>
			<div class="clear">			
				<form name="post_msg" id="post_msg" action="<?php echo site_url('message/add_message')?>" method="post">
					<input type="hidden" name="srch_type" id="srch_type" value="user" />
					<input type="hidden" id="srch_uid" name="srch_uid" value="<?=@$posted["srch_uid"];?>">
					<input type="text" required="true" class="textbox clear" name="to_user" id="to_user" value="To whom" size="45" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';"/>
					<span id="user_err"></span>
					<input type="text" required="true" name="s_subject" id="s_subject" class="textbox clear" value="Subject of the message" size="115" onBlur="if(this.value=='')this.value=this.defaultValue;" onFocus="if(this.value==this.defaultValue)this.value='';"/>
					<span id="sub_err"></span>
					<textarea rows="4" cols="109" required="true" name="s_message" id="s_message"></textarea>
					<span id="msg_err"></span>
					<input type="button" name="btn_msg_send" id="btn_msg_send" value="Send" />
					<input type="button" name="btn_draft" id="btn_draft" value="Add To Draft" />
				</form>
			</div>
		   </div> 
		   <!-- POST MESSAGE END -->
	   		<!-- DRAFT START -->
			<div class="info" id="no4">
				<h1 class="alignleft">Draft Of Message <span class="short greytext"><?php if($new_msg){ ?>(<?php echo $new_msg;?> new)<?php } ?></span></h1>
		  		<div id="draft_msg">
				<?php echo $draft_msg; ?>
				</div>
	   		</div> 
	  	 	<!-- DRAFT END -->
		</div>
  </div>		  
<!--MAIN PANEL END -->                       
</div>
<!--PANEL WITH LEFT SIDEBAR END -->
</div>
<!-- FULL WIDTH NO SIDEBAR END  --> 

<?php 
///common delete confirm box////
?>
<div id="dialog-confirm-delete-inbox" title="Delete this message?">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Item(s) will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<?php 
////common delete confirm box////
?>
<?php 
///common delete confirm box////
?>
<div id="dialog-confirm-delete-outbox" title="Delete this message?">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Outbox message(s) will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<?php 
////common delete confirm box////
?>
<?php 
///common delete confirm box////
?>
<div id="dialog-confirm-delete-draft" title="Delete this message?">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Draft message(s) will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<?php 
////common delete confirm box////
?>
<?php
///common alert Box///
?>
<div id="dialog-alert" style="display: block;" title="Attention">
    <p><span id="alert_icon" class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
      <span id="dialog_msg"></span>      
    </p>
</div>
<?php
///end common alert Box///
?>   
