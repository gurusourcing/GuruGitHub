<script type="text/javascript" language="javascipt">

$(document).ready(function(){
    $(".view_all_endorsed_by").each(function(){
       $(this).toggle(function(){
                $(this).text("Hide all");
                $("#extra_endorsed_by").show('slow');
            }, function(){
                $(this).text("View all");
                $("#extra_endorsed_by").hide('slow');
            }); 
    });
});
</script>


<!-- FULL WIDTH NO SIDEBAR START  -->
        <div class="full_no_sidebar">
             <?=theme_user_navigation();?>
             
            <div class="main_panel">
                <h1>Endorsement</h1>
                <p class="botpad20">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a erat quis erat molestie gravida a sodales ipsum. Aliquam sed tortor sit amet metus euismod tincidunt sed vel libero. </p>
                <div class="top_select">
                    <h3 class="alignleft with162"><strong>Skills</strong></h3>
                    <h3 class="alignleft"><strong>Person Endorsed</strong></h3>
                </div>
              <div class="info">
              
              <?php 
            if(!empty($user_skill))
            {              
              foreach($user_skill as $k=>$v):?>
              <div class="border_bottom relative">
                    <p class="with162 alignleft no-bot-pad" style="width:auto; padding-right:5px;"><a class="skili_count" href="javascript:void(0);"><span><?=$v->i_endorse_count;?></span><?php /* ?><span class="plus">+</span><?php */?> <?=$v->s_skill_name;?></a></p>
                    <ul id="default_endorsed_by" class="alignleft ulleft">
                    <?php
                    $lis="";
                    $i=0;
                    if(!empty($v->s_endorses))
                    {
                        /**
                        * We are spliting into 2 ul the entire results.
                        * Default ul will contain 18 li. 
                        * The rest in other li
                        *  
                        */
                        foreach($v->s_endorses as $i=>$uid){ 
                            
                            if($i==18)
                            {
                                $lis.='</ul><ul id="extra_endorsed_by" style="display:none;width: 684px; padding-left: 161px;" class="alignleft ulleft">';
                            }                            
                        
                            $lis.='<li>'.theme_user_thumb_picture($uid["endorsed_by"]).'</li>';
                       }
                    }
                        echo $lis;
                      ?>
                    </ul>
                    <?php if($i > 17){
                    ?>
                        <a  href="javascript:void(0);" class="short_grey_button top_mar alignright view_all_endorsed_by">View all</a>    
                   <?php  
                    }?>
                    
                </div>
                <?php 
                endforeach; 
            }//
            else
                echo '<div class="border_bottom relative">'.message_line("no_information_found").'</div>';
                ?>
                
              </div>
            </div>
            
 
 
 
<?php /* ?>section for other user view of my skills and endorse my skill<?php * /?>
<script type="text/javascript">
$(document).ready(function(){
   $(".plus").click(function(){
        var skill_id=$(this).attr('id');
        $.post('<?=site_url('endorsement/ajaxEndorseSkill')?>',{"id": skill_id}, 
                function(data){
                    if(data='success')
                    {
                        $('ul#endorse_ulist_'+skill_id).append('<li><?= theme_user_thumb_picture(get_userLoggedIn('id')); ?> </li>');
                    }
                        
                }
            );  
   }); 
});

</script>
            
            <div class="info nomar">
            <?php 
                $count = count($user_skill);
                $nomar='';
                foreach($user_skill as $k=>$v) :
                    if($count==$k+1)
                        $nomar = 'nomar';
            ?>
            <div class="skills <?=$nomar;?>">
               <p class="with162 alignleft no-bot-pad"><a href="javascript:void(0);" class="alignleft skili_count"><span><?=$v->i_endorse_count;?></span><span class="plus" id="<?=$v->id;?>">+</span><?=$v->s_skill_name;?></a></p>
                <ul id="endorse_ulist_<?=$v->id;?>" class="alignright">
                    <?php 
                    if(!empty($v->s_endorses))
                    {
                        foreach($v->s_endorses as $uid):?>
                            <li><?=theme_user_thumb_picture($uid["endorsed_by"]); ?> </li>
                        <?php endforeach;
                    }?>
                </ul>
            </div>
            <?php endforeach;?>
            </div>
      
      
    
<?php /* ?> Section for me to add the skills<?php * / ?> 
<script type="text/javascript">
$(document).ready(function(){
   /**
   * adding skills
   * /
   
    $("#btn_submit").click(function(){
        var uid=$('#form_token').attr('value');
        var s_skill_name=$.trim($("#s_skill_name").attr('value'));
        $.post('<?=site_url('endorsement/addSkills')?>',{"uid": uid, "s_skill_name": s_skill_name}, 
                function(data){
                    
                    if(data=='success')
                    {
                        $("#s_skill_name").attr('value','');
                        var str='<a href="javascript:void(0);">'+s_skill_name+'<img src="<?=get_theme_path('guru_frontend/images')?>cross1.png" width="7" height="8" alt="cross" /></a>';
                        $("#skill_edit a:last").after(str);   
                        
                        //console.log($("#skill_edit a:last") );                     
                    }
                }
            );  
   });
   
   /**
   * deleting skills
   * /
   
   $(".delete").click(function(){
       var id=$(this).attr('rel');
       var element=$(this);
        $.post('<?=site_url('endorsement/ajaxDeleteSkill')?>',{"id": id}, 
                function(data){
                    if(data=='success')
                    {
                       element.parent().remove(); 
                    }
                    
                });
       
   });
   
    
});

</script>


<div class="info nomar">
                        <div id="skill_edit" class="skil_edit">
                           <?php foreach($user_skill as $k=>$v):?> 
                            <a href="javascript:void(0);"><?=$v->s_skill_name;?> <img src="<?=get_theme_path('guru_frontend/images')?>cross1.png" width="7" height="8" alt="cross" class='delete' rel="<?=$v->id;?>" /></a>
                          <?php endforeach; ?> 
                      <input type="hidden" name="form_token" id="form_token" value="<?=$form_token?>">
                      <input type="text" name="s_skill_name" id="s_skill_name" value="" size="26" placeholder="Type nothe area of skill ..."  />
                        </div>
                        <input type="submit" name="btn_submit" id="btn_submit" value="Save" class="short" /> 
                        <input type="reset" value="Cancel" class="short" />
                      
                        
                  </div>     
*/ ?>

<!-- FULL WIDTH NO SIDEBAR END  -->