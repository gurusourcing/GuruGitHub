<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* This is the systemwise cron controller.
* 
*/

require_once APPPATH."controllers/fconnect.php";

class Cron extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        //$this->clear_temp_jqUploader();//testing
        //$this->process_rank(23);//debug
    }
    
    /**
    * View Listing
    */
    public function index()
    {}
    
    
    public function clear_temp_jqUploader()
    {
        delete_files( get_theme_path("aquincum")."js/inedit/jquery-fileupload/server/php/files/thumbnail/");
        delete_files( get_theme_path("aquincum")."js/inedit/jquery-fileupload/server/php/files/");
    }
    
    /**
    * Process and send bulkmail in bulkmail table
     * @param int $mails_per_trigger No of Mail will be trigger per try 
     * Default = 15
     * 
     * @param int $bulk_timeout default timeout of each try
     * Default = 15
     * @return void
    */
    public function process_bulk_mail($mails_per_trigger = 15,$bulk_timeout =15) {
        $start_time = time(); 
        $escaped_time = 0;
        $this->load->model("bulk_mail_model");         
        //for bulk mail
        $this->load->library('email');
        $rs =$this->bulk_mail_model->bulk_mail_load('',$limit=$mails_per_trigger,$offset=NULL,$order_by=NULL);
        foreach ($rs as $e_key => $rs_value) {
            
            $mailData['to'] =  $rs_value->s_mail_to;
            $mailData['from'] =  $rs_value->s_mail_from;
            $mailData['subject'] =  $rs_value->s_mail_subject;
            $mailData['message'] =  $rs_value->s_mail_body;

            if(sendMail($mailData)){
               $this->bulk_mail_model->delete_bulk_mail(array("id"=>$rs_value->id));
            }
            $escaped_time = time() - $start_time ;
            if($escaped_time > $bulk_timeout){
               echo 'task preempted after sending '.(intval($e_key)+1).' mail';
               break;
            }

         }
    }
    
    /**
    * This function is fetching user freind list from facebook.
    * Enter data in the user_fb_list table.
    * This cron work once in a day.
    * 
    * The friends list will be inseted if and only if the friend 
    * exists within the guru user table. Because fb returns only one lebel 
    * of friends, so if the user is not registered in guru then we cannot get 
    * friends upto 3rd lebel. As we need the access token to fetch friends.
    * 
    */
     public function insert_user_friend_list() 
     {
       $this->load->model("user_model"); 
       $this->load->model("user_fb_list_model"); 
       //$condition="s_facebook_credential !='' and  "; 
       $condition=array("e_status"=>"active","s_facebook_credential !="=>'');
       
       $rs =$this->user_model->user_load($condition);
       $fconnect= new Fconnect();
       foreach ($rs as $e_key => $rs_value) {
           
           /// calculating the extra days////
           $extra_day=intval($rs_value->s_extra_fetch_friend['count_visits']/10)+intval($rs_value->s_extra_fetch_friend['count_login']/5);
           
           $timestamp=strtotime('-'.$extra_day.' days',strtotime($rs_value->dt_next_fetch_friend));
           $timestamp=date('Y-m-d',$timestamp);
           
           if(intval(calculate_days_gap($rs_value->dt_next_fetch_friend,$timestamp))==0) // today is update day
           {
                /**
               * FB Cron logic for fetching friends
               */
               $facebookdetails=$fconnect->get_user_fb_friends($rs_value->uid,false);         
               if(empty($facebookdetails["error"]))//fb error occurred
               {
                   foreach ($facebookdetails as $fb_key => $fb_value) 
                   {
                       $facebookId=str_replace(",","",number_format($fb_value["uid"]));                 
                       $conditions="s_facebook_credential like '%s:3:\"uid\";d:".$facebookId.";%'";//fb id may be "d" 
                       $conditions.=" OR s_facebook_credential like '%s:3:\"uid\";i:".$facebookId.";%'";//fb id may be "i"                       
                       $chkrs =$this->user_model->user_load($conditions);
                       if(!empty($chkrs))
                       {
                 
                         $fbcondition=array("uid"=>$rs_value->uid,"uid_friend"=>$chkrs[0]->uid);
                                         
                         $valueexsist=$this->user_fb_list_model->user_fb_list_load($fbcondition);                    
                         if(empty($valueexsist))
                         {
                             $values=array("uid"=>$rs_value->uid,"uid_friend"=>$chkrs[0]->uid,"fb_friend_id"=>$facebookId,"fb_friend_note"=>"","fb_friend_label"=>1);
                             $this->user_fb_list_model->add_user_fb_list($values);
                             
                             /*///inserting alternating fb list
                             $values["uid"]=$chkrs[0]->uid;
                             $values["uid_friend"]=$rs_value->uid;
                             $ufbC= unserialize($rs_value->s_facebook_credential);
                             $values["fb_friend_id"]=$ufbC["uid"];
                             $this->user_fb_list_model->add_user_fb_list($values);*/
                         }
                       }               
                   }
                   
                   // updating the next fb cron date and empty the s_extra_fetch_friend in user table//
                   $nextDate=strtotime('+15 days',strtotime(date('Y-m-d H:i:s')) );
                   $nextDate=date('Y-m-d H:i:s',$nextDate);
                   $this->user_model->update_user(array("dt_next_fetch_friend"=>$nextDate,"s_extra_fetch_friend"=>''),
                                                    array("id"=>intval($rs_value->id)));      
                                                    
                   /**
                   * Fb process of a user is finished. 
                   * We can now calculate Rank
                   */
                   $this->process_rank($rs_value->uid);                     
               }//no fb error                
           }//if validated days gap
       }//end users          
          
     }

     /**
     * as per chat conversion with Mr.Ashim on 17Aug2013,
     * > we target that user id to check to see if there is any chnage in our DB with the FB DB...
     * >  in the rank table there is one entry/row per service for which visitor_user_id is 0 .... 
     *    this will be inserted during service creation ... all others have valid visitor_user_id 
     *    and fb_level ... that means it has to be updated when we identify that a persons fb 
     *    connection has chnaged
     * > suppose p1 is the id of a person whose company provide service s1... 
     *    now p1 is connected to p2 and p2 is connected to p3 ... then this will be the entry 
     *    in sevice table for service s1
     * 
     * @param mixed $uid
     */
     public function process_rank($uid)
     { 
         /**
         * No idea why $this not worked 
         * for loading models.
         * So $CI is used here.
         */
         $CI=&get_instance();
         //$this->load->model("user_model");
         $CI->load->model("user_service_model");
         //$this->load->model("user_rank_model");
         //pr($CI->user_service_model->user_service_load(),1);

         ///fetching all services, for rank updation
        $condition=array("s.i_active"=>"1","s.uid"=>$uid);
        $rs =$CI->user_service_model->user_service_load($condition);
        $ret=array();
        if(!empty($rs))
        {
            foreach($rs as $k=>$service)
            {
                ///fetch all friends and their friends////
                $all_friends_and_their_friends=find_all_friend_and_their_friend($uid);
                if(!empty($all_friends_and_their_friends))
                {
                    //@see, rankCallback();
                    array_walk($all_friends_and_their_friends,
                        "rankCallback",
                        $service
                        );
                }                
            }//end for
        }//end if
        
        return FALSE;
     }


     
    /**
    * 
    * The friends list will be inseted if and only if the friend 
    * exists within the guru user table.
    * 
    * on 4Oct 2013, 
    * by clicking a button user can add their fb connections.
    */
     public function insert_user_friend_list_instant($tkn) 
     {
       if(empty($tkn))
          redirect(get_destination());
          
         
       $this->load->model("user_model"); 
       $this->load->model("user_fb_list_model"); 
       //$condition="s_facebook_credential !='' and  "; 
       $condition=array("e_status"=>"active","s_facebook_credential !="=>'');
       /**
       * Instant fetch connection list of a user
       */
       $t_uid=decrypt($tkn);
       $condition["u.id"]=intval($t_uid);
       
       $rs =$this->user_model->user_load($condition);
       
       $fconnect= new Fconnect();
       foreach ($rs as $e_key => $rs_value) {
           
           
                /**
               * FB Cron logic for fetching friends
               */
               $facebookdetails=$fconnect->get_user_fb_friends($rs_value->uid,false);  
               
               if(!empty($facebookdetails["error"]))//fb error occurred
               {
                    set_error_msg(message_line("facebook session timeout")); 
                    redirect(get_destination());   
               }       
               
               foreach ($facebookdetails as $fb_key => $fb_value) 
               {
                   $facebookId=str_replace(",","",number_format($fb_value["uid"]));                 
                   $conditions="s_facebook_credential like '%s:3:\"uid\";d:".$facebookId.";%'";//fb id may be "d" 
                   $conditions.=" OR s_facebook_credential like '%s:3:\"uid\";i:".$facebookId.";%'";//fb id may be "i"
                   $chkrs =$this->user_model->user_load($conditions);
                   
                   if(!empty($chkrs))
                   {
             
                     $fbcondition=array("uid"=>$rs_value->uid,"uid_friend"=>$chkrs[0]->uid);
                                     
                     $valueexsist=$this->user_fb_list_model->user_fb_list_load($fbcondition);                    
                     if(empty($valueexsist))
                     {
                         $values=array("uid"=>$rs_value->uid,"uid_friend"=>$chkrs[0]->uid,"fb_friend_id"=>$facebookId,"fb_friend_note"=>"","fb_friend_label"=>1);
                         $this->user_fb_list_model->add_user_fb_list($values);
                         
                         /*///inserting alternating fb list
                         $values["uid"]=$chkrs[0]->uid;
                         $values["uid_friend"]=$rs_value->uid;
                         $ufbC= unserialize($rs_value->s_facebook_credential);
                         $values["fb_friend_id"]=$ufbC["uid"];
                         $this->user_fb_list_model->add_user_fb_list($values);*/
                     }
                   }               
               }
               
               // updating the next fb cron date and empty the s_extra_fetch_friend in user table//
               $nextDate=strtotime('+15 days',strtotime(date('Y-m-d H:i:s')) );
               $nextDate=date('Y-m-d H:i:s',$nextDate);
               $this->user_model->update_user(array("dt_next_fetch_friend"=>$nextDate,"s_extra_fetch_friend"=>''),
                                                array("id"=>intval($rs_value->id)));      
               
               
               /**
               * Fb process of a user is finished. 
               * We can now calculate Rank
               */
               $this->process_rank($rs_value->uid);                                            
       }    
              
       /**
       * Instant fetch connection list of a user
       */
       set_success_msg(message_line("facebook friend add success")); 
       redirect(get_destination());
          
     }     
     
     
    /**
    * Assigning permisions available 
    */
    /*public function theme_permission()
    {
        return array(
            "administer theme"=>array(
                "title"=>"Administer admin users",
                "description"=>"Can enabled/disable theme. Select default theme for admin and frontend.
                                ".message_line("security concern"),
            ),
            "select own domain theme"=>array(
                "title"=>"View own domain admin users",
                "description"=>"Can select a theme among enabled thems for the domain user registered.
                                <br/> Applicable for \"Franchisee admins\"."
            ),                         
        );
    }//end welcome_permission*/
    
   
    
}

