<?php
/**
* on 4 Oct 2013, 
* if loggedin user is not facebook 
* verified, then show login via fb button 
* else, if user is already fb verified 
* then show Add you fb connection button. 
*/
?>
<?if(!$view_data){?>
    <?/*if(isset($user[0]['login_by_facebook'])):?>
    <div class="info aligncenter">
        <p>To see the connection</p>
        <a  href="javascript:void(0)" onclick='facebook_connect_init()' class="grey_button">Login in to your FB</a>
    </div>        
        <?endif;?>
    <div class="info aligncenter">
        <p>To see the connection</p>
        <a href="javascript:void(0)" onclick='facebook_connect_init()' class="grey_button">Add your FB account</a>
    </div>
    */?>
    
    <div class="info aligncenter">
        <p>To see the connection</p>
        <a  href="javascript:void(0)" onclick='facebook_connect_init()' class="grey_button">Login in to your FB</a>
    </div>
    <div class="yellow_massage">You are not connected !</div>

    <?
    }//end if
    elseif(!empty($chain_html))
    {    
    
    /**
    * Add your fb connection
    */
    ?>
    <div class="info aligncenter">
        <a href="<?=site_url(admin_base_url("cron/insert_user_friend_list_instant/".encrypt(get_userLoggedIn("id")))); ?>" class="grey_button">Add your FB friends</a>
    </div>    
    
    <div class="info">
        <p>You are connected to <?php echo get_user_display_name($viewing,'name'); ?> as below</p>

        <?php 
            $cnt = count($chain_html)-1;
            foreach($chain_html as $key=>$html)
            {
            ?>

            <div class="connect">
                <img src="<?=site_url(get_theme_path()."images/white-dot.jpg")?>" width="1" height="9" alt="pic" class="top" />
                <ul>
                    <?php
                        echo $html;
                    ?>                              
                </ul>
                <img src="<?=site_url(get_theme_path()."images/white-dot.jpg")?>" width="1" height="9" alt="pic" class="bottom" />
            </div> 
            <?php if($key!=$cnt) { ?>
                <span class="skills aligncenter"><strong>and</strong></span> 
        <?php  
                }   // end if                      
            } // end for
        ?>

        <!--<span class="skills aligncenter"><strong>and</strong></span>
        <div class="connect">
        <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="top" />
        <ul>
        <li><span>Mr. Abir (You)</span></li>
        <li><span>Manika</span></li>
        <li><span><a href="#">Sanhita Sinha</a></span></li>                                
        </ul>
        <img src="images/white-dot.jpg" width="1" height="9" alt="pic" class="bottom" />
        </div>-->
        <p>Want to know about &nbsp;<?php echo get_user_display_name($viewing,'name'); ?> ?</p>
        <?/*<a href="#" class="grey_button">Ask</a>*/?>
    </div>
    <?
    }//end else
    elseif(empty($chain_html))
    {
    ?>
    
        <?// Add your fb connection ?>
        <div class="info aligncenter">
            <p>To see the connection</p>
            <a href="<?=admin_base_url("cron/insert_user_friend_list_instant/".encrypt(get_userLoggedIn("id"))); ?>" class="grey_button">Add your FB friends</a>
        </div>     
        <div class="yellow_massage">You are not connected !</div>
    <?
    }//end else
    ?>
        