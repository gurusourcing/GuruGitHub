<div class="panel_info facebook_fans">
   <p class="name">Your Services</p>
    <div class="facebook nodevider nopad no-bot-mar">
            <ul class="name_list short">
        <?if($view_data):?>
                <? foreach ($view_data as $key=>$value):?>
                <li><a href="<?= site_url($value->s_short_url) ?>"><?=$value->s_service_name?></a></li>
                <?endforeach;?>            
        <? else:?>
                <li>No Service <?=(get_userLoggedIn('i_is_company_emp')?"Available":"Added!!");?></li>
         <? endif;?>
            </ul>
            
         <?php
         /**
         * IF is employee then 
         * hide the add service button
         */
         if(!get_userLoggedIn('i_is_company_emp'))
         {
         ?>
            <a href="<?=site_url("service_profile/add_service_once");?>" class="short_grey_button">Add more Service</a>
         <?php
         }
         ?>
        
        <?/*if($view_data):?>
            <ul class="name_list short">
                <? foreach ($view_data as $key=>$value):?>
                <li><a href="<?= site_url($value->s_short_url) ?>"><?=$value->s_service_name?></a></li>
                <?endforeach;?>
            </ul>
            <div class="clear"><br/></div>
            <a href="<?=site_url("service_profile/add_service_once");?>" class="short_grey_button">Add more Service</a>
        <? else:?>
            <ul class="name_list short">No Service Added!!</ul>
            <div class="clear"><br/></div>
            <a href="<?=site_url("service_profile/add_service_once");?>" class="short_grey_button">Add a Service</a>
        <? endif;*/?>
        
    </div>
</div>