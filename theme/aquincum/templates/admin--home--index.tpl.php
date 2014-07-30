<?
/**
* Admin Login page 
* 
*/

/*pr(base_url().get_theme_path());
pr(base_url(get_theme_path()));
pr(site_url(get_theme_path()));*/
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php echo base_url(); ?>" />
<title>:: <?=site_name();?> Administration ::</title>
<? print $header;?>
</head>
<body>


<!-- Top line begins -->
<div id="top">
    <div class="wrapper">
        <a href="<?=admin_base_url("home");?>" title="" class="logo"><img src="<?=base_url(get_theme_path())."/";?>images/logo.png" alt="" /></a>
    </div>
</div>
<!-- Top line ends -->

<!-- Login wrapper begins -->
<div class="loginWrapper">

<div class="fluid">
<?php print $main_content; ?>
</div>

</div>
<!-- Login wrapper ends -->  
  
</body>
</html>
