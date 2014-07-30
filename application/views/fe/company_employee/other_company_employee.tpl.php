<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
            <?= theme_user_navigation();?>
            <div class="main_panel">
               <h1>All Employee (Other Company)</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero.</p>
                <div class="info">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="employee">
                    <?php 
                        if(!empty($values))
                        { //pr($values);
                            $len=count($values);
                            foreach($values as $k=>$v)
                            {
                             
                                $last=(($len==$k+1)?"class='last'":"");
                                
                   ?>             
                        <tr <?=$last?> >
                            <td width="53" align="left" valign="middle">
                                <?=theme_user_thumb_picture(intval($v->uid));?>
                            </td>
                            <td width="505" align="left" valign="middle">
                                <a href="<?=site_url(short_url_code(intval($v->uid)));?>" class="black">
                                    <?=get_user_display_name(intval($v->uid),'');?>
                                </a>
                            </td>
                            <td width="366" align="right" valign="middle">
                                <span class="grey"><?=isset($v->designation['s_title'])? $v->designation['s_title'] : 'N/A';?></span>
                            </td>
                            
                        </tr>   
                   <?php           
                            }//end foreach
                        }//end if
                    ?>
                    
                  </table>
                  <?=$pagination;?>
              </div>
            </div>
        </div>
<!-- FULL WIDTH NO SIDEBAR END  --> 



