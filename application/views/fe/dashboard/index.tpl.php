<?=theme_user_navigation();?>    

<!--PANEL WITH LEFT SIDEBAR START -->    
        <div class="col_left_sidebar">
<!--LEFT SIDEBAR START -->        
            <div class="left_sidebar">
                <!--profile_pic start-->
                <?=theme_block_dashboard_profile_pic();?>
                <!--profile_pic end-->
                 <!--verification start-->
                <?=theme_block_dashboard_verification();?>
                <!--verification end-->
                <!--service start-->
                <?=theme_block_dashboard_services();?>
                <!--service end-->
              </div>
<!--LEFT SIDEBAR END -->   
<!--MAIN PANEL START -->
            <div class="main_panel">
          
            <?php //find_connected_chain_within_friends(23,73); ?>
            <?php // pr(service_profile_completion('2'));?>
			<?php //pr(find_all_friend_and_their_friend('23'));?>
            <?php //pr(get_category_name('1'));?>
                <h1 class="alignleft botmar20"><strong class="alignleft"><?=get_dashboard_profile_name(get_userLoggedIn('id'));?></strong> <a href="<?=site_url(get_editProfileLink());?>" class="short_grey_button grey_hover_orange leftmar">Edit Profile</a></h1>                
                <h2 class="heading-orange botmar">Service Description</h2>
                <div class="info yellow_info">
                  <ul class="name_list">
                      <li>Last logged in on <?=  date(' F jS,Y',  strtotime(get_userLoggedIn('dt_last_login')))?></li>
                      <li>You have to endorsements. <a href="<?=site_url("endorsement");?>" class="short_grey_button">View Now</a></li>
                      <li class="nodevider">You have <?=count_recommendation(intval(get_userLoggedIn('id')));?> Testimonials / Recommendations for approval. <a href="<?=site_url("recommendation");?>" class="short_grey_button">View Now</a></li>
                  </ul>
               </div>
            </div>          
<!--MAIN PANEL END -->                       
        </div>
<!--PANEL WITH LEFT SIDEBAR END -->  