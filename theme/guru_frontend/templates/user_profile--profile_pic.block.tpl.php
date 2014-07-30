<script type="text/javascript">
    $(document).ready(function(){
             //$( "#dialog-common" ).attr('title','Change Profile Picture');
     });
     
     function open_uploader(){
	 	
        $( "#dialog-common" ).find("#dialog_msg").html('<iframe id="picture_upload" style="border: medium none; min-height: 330px;"  width="100%" height="90%" src="<?=  site_url('shorturl/profile_picture_upload')?>"></iframe>');
        $( "#dialog-common" ).dialog({
                  autoOpen: false,
                  height: 480,
                  width: 640,
                  modal: true,
                  draggable: true,
				  title:'Change Profile Picture',
                  buttons: {
//                     Save: function() {
//                        $('#picture_upload').contents().find('#frm_profilepic_upload').submit();
      //                    $.post('<?=  site_url('shorturl/profile_picture_upload')?>',{file=}, function(data) {
//      //                        $( "#dialog-common" ).find("#dialog_msg").html(data);
//      //                    });
//
//                      },
                      Close: function() {

                          $(this).dialog("close");
                          window.location.reload();

                      }
                  }
         });
        $( "#dialog-common" ).dialog( "open" );
  }
   
</script>
<?php 
$img_thumb = 'resources/logo/thumb_'.str_replace('/resources/logo/','',$view_data);

if (file_exists(FCPATH.'/'.$img_thumb)) {		
}
else{
	$img_thumb = $view_data;
}
?>
    <div class="profile_pic">
        <img src="<?=base_url()."/";?><?= $view_data?$img_thumb:'resources/no_image.jpg'?>" width="210" height="198" alt="pic" />
        <p class="add_image"><a href="javascript:void(0);" onclick='open_uploader();'>+ Add profile image</a></p>
    </div>
    

 

