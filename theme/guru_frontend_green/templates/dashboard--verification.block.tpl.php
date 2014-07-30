
<div class="panel_info facebook_fans nopad">
           <p class="name">Verification</p>
            <div class="facebook nodevider nopad no-bot-mar">
                <ul class="name_list">
                    <li class="li5 short"><a href="javascript:void(0);">Email <?= ((bool)$view_data['i_email_verified'])?'':' not '?> verified</a> <img src="<?=base_url(get_theme_path())."/";?>images/<?= ((bool)$view_data['i_email_verified'])?'green-right.png':'cross2.png'?>" width="16" height="16" alt="arrow" class="alignright" /></li>
                    <li class="li6 short"><a href="<?=site_url('account/verify_mobile')?>">Phone <?= ((bool)$view_data['i_mobile_verified'])?'':' not '?> verified</a> <img src="<?=base_url(get_theme_path())."/";?>images/<?= ((bool)$view_data['i_mobile_verified'])?'green-right.png':'cross2.png'?>" width="16" height="16" alt="cross" class="alignright" /></li>
                    <li class="li7 short"><a href="javascript:void(0);">Facebook <?= ((bool)$view_data['i_fb_verified'])?'':' not '?> verified</a> <img src="<?=base_url(get_theme_path())."/";?>images/<?= ((bool)$view_data['i_fb_verified'])?'green-right.png':'cross2.png'?>" width="16" height="16" alt="cross" class="alignright" /></li>
                </ul>
                <a href="javascript:void(0);" class="short_grey_button">Add more</a>
            </div>
        </div>