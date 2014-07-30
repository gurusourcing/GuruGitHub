<script type="text/javascript">
    $(document).ready(function(){	
				
			//$( "#dialog-common" ).attr('title','Change Profile Picture');
     });
    
     function open_suggestion_block(type){
		
		var relVal = type;
		
        $( "#dialog-common" ).find("#dialog_msg").html('<iframe id="suggestion_block" style="border: medium none; min-height: 330px;"  width="100%" height="98%" src="<?php echo site_url() ?>shorturl/suggestion_block/'+relVal+'"></iframe>');
		
		
        $( "#dialog-common" ).dialog({
                  autoOpen: false,
                  height: 480,
                  width: 640,
                  modal: true,
                  draggable: true,
				  title:'Suggestion Box',
                  buttons: {
/*                     Save: function() {
                        $('#picture_upload').contents().find('#frm_profilepic_upload').submit();
                          $.post('<?=  site_url('shorturl/profile_picture_upload')?>',{file=}, function(data) {
      //                        $( "#dialog-common" ).find("#dialog_msg").html(data);
      //                    });

                      },*/
                      Close: function() {

                          $(this).dialog("close");
                          /*window.location.reload();*/

                      }
                  }
         });
        $( "#dialog-common" ).dialog( "open" );
  }
   
</script>

	
	<p style="padding-bottom: 5px;">	
	<a href="javascript:void(0);" class="field_description" id="suggestion_link" rel="<?php echo $enum_type ?>" onclick="open_suggestion_block('<?php echo $enum_type ?>');">Suggest new</a>	
	</p>
    

 

