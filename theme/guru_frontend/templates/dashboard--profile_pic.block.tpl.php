<?php 
$img_thumb = 'resources/company/thumb_'.str_replace('/resources/company/','',$view_data);
if (file_exists(FCPATH.'/'.$img_thumb)) {		
}
else{
	$img_thumb = $view_data;
}
?>
<div class="profile_pic">
    <a href="<?=$view_profile_link;?>"><img src="<?=base_url()."/";?><?= $view_data?$img_thumb:'resources/no_image.jpg'?>" width="210" height="198" alt="pic" /></a>
 </div>