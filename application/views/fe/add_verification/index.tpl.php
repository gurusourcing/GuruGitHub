<script type="text/javascript">
$(document).ready(function(){
 	
});
function get_doc_type(profile_type)
{
	var profile={"profile_type":""};//global var
	if(profile_type)
	{
		profile.profile_type=profile_type;
		$.post("<?=site_url("add_verification/ajaxGetDocuments")?>",
                    profile,
                    function(data)
                    {
                        $("#doc_files").html(data);
                    }
              ); 
	}
}
<!--//
function validateFileExtension(fld) {
    if(!/(\.png|\.doc|\.docx|\.bmp|\.gif|\.jpg|\.jpeg)$/i.test(fld.value)) {
        alert("This file type not allowed.");
        fld.form.reset();
        fld.focus();
        return false;
    }
    return true;
}
//-->
</script>
<div class="full_no_sidebar">	
	<?=theme_user_navigation();?> 
	
	<div class="main_panel">
		<h1>Add more verification</h1>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero. </p>
	   <div class="category_panel newservice signup_formsection">
				<form name="add_verify_frm" id="add_verify_frm" method="post" enctype="multipart/form-data" action="">
					<p><label class="alignleft">Select Profile</label> 
					
						<select name="choose_profile" id="choose_profile" onchange="get_doc_type(this.value)">
							<option value="">Select</option>
							<option value="user profile">User Profile</option>
							<option value="service">Service Profile</option>
							<option value="company profile">Company Profile</option>
						</select>
					
					</p>
				
					<div id="doc_files">
					</div>
					<!--<p><label class="alignleft">Upload files</label> 
					<span class="like_input"><input type="file" size="38"></span>
					</p>-->
					
					                           
			</form>
	   </div> 
  </div>
</div>