  <script type="text/javascript">
    $(document).ready(function(){
             $( "#dialog-common" ).attr('title','Change Profile Picture');
     });
     
     function open_uploader(){
        $( "#dialog-common" ).find("#dialog_msg").html('<iframe id="picture_upload" style="border: medium none; min-height: 330px;"  width="100%" height="90%" src="<?=  site_url('shorturl/company_profile_picture_upload')?>"></iframe>');
        $( "#dialog-common" ).dialog({
                  autoOpen: false,
                  height: 480,
                  width: 640,
                  modal: true,
                  draggable: true,
                  buttons: {
                      Close: function() {

                          $(this).dialog("close");
                          window.location.reload();

                      }
                  }
         });
        $( "#dialog-common" ).dialog( "open" );
  }
   
</script>

    <div class="profile_pic">
        <img src="<?=site_url($view_data?$view_data:'resources/no_image.jpg');?>" width="210" height="198" alt="pic" />
        <p class="add_image"><a href="javascript:void(0);" onclick='open_uploader();'>+ Add profile image</a></p>
    </div>
    

 



