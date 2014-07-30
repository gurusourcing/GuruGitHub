<?php
/**
* For profile pages    
*/
if($disp=="button")
{
?>
<div class="panel_info">
    <h3><a href="javascript:void(0);" onclick="Popup=window.open('https://twitter.com/share?url=<?=site_url($link);?>','Popup','toolbar=yes,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=420,height=400,left=430,top=23'); return false;"><img src="<?=base_url(get_theme_path())."/";?>images/icon3.jpg" width="18" height="18" alt="icon" /> Share via Twitter</a></h3>
</div>

<?php
}
else//for search pages
{
?>
<a href="javascript:void(0);" onclick="Popup=window.open('https://twitter.com/share?url=<?=site_url($link);?>','Popup','toolbar=yes,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=420,height=400,left=430,top=23'); return false;"><img src="<?=get_theme_path('guru_frontend')?>images/icon13.png" width="18" height="12" alt="icon" /></a>
<?php     
}
?>
