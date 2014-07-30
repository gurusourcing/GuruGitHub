<?php
/**
* Show this only to loggedin users
*/
if(!is_userLoggedIn())
 return false;
?>

<div class="top_bar">
    <ul>
        <li><a href="<?=site_url("dashboard");?>">Dashboard</a></li>
        <li><a href="<?=site_url("user_profile");?>">Profile</a></li>
<?php /* ?>If logged-in user is not an employee of a company.. then show following menu <?php */?>
<?php 
    if(is_not_company_employee())
    {
?>
         <li><a href="javascript:void(0);">Service Profile</a>
            <ul>
                <?php
                $servies=get_user_services(get_userLoggedIn('id'));
                if(!empty($servies))
                {
                    foreach($servies as $s)
                    {
                        echo '<li><a href="'.site_url( "service_profile/".encrypt($s->id) ).'">'.$s->s_service_name.'</a></li>';
                    }
                }
                ?>
                <li><a href="<?=site_url("all_service_provided/");?>">All Service Provided</a></li>
                <li class="nodevider"><a href="<?=site_url("service_profile/add_service_once");?>">Add New Service</a></li>
            </ul>                    
         </li>
        <li><a href="<?=site_url('message')?>">Message</a></li>
        <li><a href="<?=site_url('favourites')?>">Favourites</a></li>
        <li><a href="<?=site_url('save_search')?>">Save Search</a></li>
        <li><a href="<?=site_url('recommendation');?>">Recommendation</a></li>
        <li><a href="<?=site_url('endorsement');?>">Endorsement</a></li>
        <li><a href="javascript:void(0);">Company</a>
            <ul>
                <? 
                if(! get_userCompany() )
                { 
                ?>
                <li><a href="<?=site_url('start_company');?>">Be a Company</a></li>
                <? 
                }
                else{
                ?>
                <li><a href="<?=site_url(get_editProfileLink("company"));?>"><?=get_dashboard_profile_name(get_userLoggedIn('id'));?></a></li>
                <?
                }
                 ?>
                <li><a href="<?=site_url('company_employee')?>">List Of Service Providers</a></li>
                <li class="nodevider"><a href="<?=site_url('company_employee/add_company_employee')?>">Add More Service Providers</a></li>
            </ul>
        </li>
        <li><a href="javascript:void(0);">Be Franchisee</a></li>   
<?php
    }
?>
        
    </ul>
</div>