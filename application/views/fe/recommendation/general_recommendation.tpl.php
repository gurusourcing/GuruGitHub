<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?=theme_user_navigation();?>
            <div class="main_panel">
                <h1><?=get_service_name(decrypt($form_token),'');?>'s Recommendation</h1>
                <?php //pr($user_recommendation);?>
                <div class="info">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
                    <?php foreach($user_recommendation as $k=>$v):?>
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
                        
                        <? /* <td width="50" align="left" valign="middle">&nbsp;</td> */?>

                      </tr>
                      <?php endforeach;?>
                      
                    
                  </table>
                  <?=$pagination;?>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 