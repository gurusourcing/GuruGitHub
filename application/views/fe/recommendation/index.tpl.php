<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?=theme_user_navigation();?>
            <div class="main_panel">
                <h1>Manage Recommendation</h1>
                <?php //pr($user_recommendation);?>
                <div class="info">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
                    <?php 
                    if(!empty($user_recommendation))
                    {                      
                      foreach($user_recommendation as $k=>$v):?>
                      <tr>
                        <input type="hidden" name="recommendation_id" id="recommendation_id" value="<?=$v->id?>"/>
                        <td width="43" align="left" valign="top">
                        <?=theme_user_thumb_picture($v->uid_recommended_by);?>
                        </td>
                        <td width="224" align="left" valign="top"><?=get_user_display_name($v->uid_recommended_by)?></td>
                        <?php 
                            if(!empty($v->s_message))
                                foreach($v->s_message as $val)
                        ?>
                                  <td width="400" align="left" valign="middle"><?=$val['s_msg'];?></td>    
                        
                        <td width="50" align="left" valign="middle">&nbsp;</td>
                        <td align="left" valign="top">
                        <?php if($v->e_status=="pending")
                            {
                        ?>
                            <a href="<?=site_url('recommendation/approveRecommendation').'/'.encrypt($v->id);?>" name="approve" id="approve"  class="short_grey_button approve">Approve</a>
                        <?php } ?>
                        <a href="<?=site_url('recommendation/deleteRecommendation').'/'.encrypt($v->id);?>" name="delete" id="delete" class="short_grey_button grey_to_orange delete">Delete</a></td>
                      </tr>
                      <?php 
                      endforeach;
                    }
                    else
                        echo '<tr><td>'.message_line("no_information_found").'</td></tr>'; 
                    ?>                    
                  </table>
                  <?=$pagination;?>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 