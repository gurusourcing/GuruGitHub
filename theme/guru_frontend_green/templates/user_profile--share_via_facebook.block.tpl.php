<script type="text/javascript">
    function share_of_fb<?=$id;?>(){
        FB.ui(
            {
              method: 'feed',
              name: 'Share my profile',
              /*link: 'https://developers.facebook.com/docs/reference/dialogs/',
              link : 'http://192.168.1.253/guru/php/user_profile',*/
              link:'<?=site_url($link);?>',
              picture: '<?=site_url($view_data?$view_data:'resources/no_image.jpg');?>',
              caption: '<?=ucwords(site_name());?> <?=ucwords($type);?> Profile Page',
              description: 'Please visit my <?=ucwords($type);?> profile page in <?=site_name();?>.'
            },
            function(response) {
              if (response && response.post_id) {
                alert('Post was published.');
              } else {
                alert('Post was not published.');
              }
            }
      );
    }

</script>

<?php
/**
* For profile pages    
*/
if($disp=="button")
{
?>

<div class="panel_info">
    <h3><a href="javascript:void(0);" onclick="share_of_fb<?=$id;?>();"><img src="<?=base_url(get_theme_path())."/";?>images/icon2.jpg" width="18" height="18" alt="icon" /> Share via Facebook</a></h3>
</div>
<?php
}
else//for search pages
{
?>
<a href="javascript:void(0);" onclick="share_of_fb<?=$id;?>();"><img src="<?=get_theme_path('guru_frontend')?>images/icon14.png" width="18" height="12" alt="icon" /></a>
<?php     
}
?>