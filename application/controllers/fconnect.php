<?php
/*********
* Author: Kallol Basu
* Date  : 10 Dec 2012
* Modified By: 
* Modified Date: 
* 
* @includes My_Controller.php
*/


class Fconnect extends My_Controller
{

    public $cls_msg;//////All defined error messages. 
    public $pathtoclass;
    public function __construct()
    {
        try
        { 
          parent::__construct(); 
          $this->data['page_title'] = 'Fconnect';
          $this->load->model('social_connect_model');	
          $this->load->model('user_model');	
		
        }

        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
    public function index() {
        try
        {
            $this->render();            
	    }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 	    
    }
	
	/**
    * Authenticate code by fb access token
    * @param string $access_token_val The access token returned by fb login 
    * either popup or general login
    *
    */
    public function lgoin_direct_from_facebook($access_token_val='')
    {   
        /**********make the access token Extended by extend_access_token() and get extended token********/
        $extended_access_token_val = $this->extend_access_token($access_token_val);
        if($extended_access_token_val==''){
            $access_token_val = $extended_access_token_val;
        } 

        
        /***running FQL to fetch data from facebook ****/
        // $fql = urlencode("SELECT post_id,viewer_id,source_id,updated_time,created_time,actor_id,message,attachment,permalink ,type FROM stream WHERE source_id = me() AND actor_id = me() order by created_time desc LIMIT 5");
        $fql = urlencode("SELECT uid,about_me, birthday, current_location, first_name, has_added_app, hometown_location, last_name, locale,  birthday_date, pic, pic_with_logo, pic_big, pic_big_with_logo, pic_small, pic_small_with_logo, pic_square, pic_square_with_logo, profile_url, proxied_email, email, contact_email, sex, meeting_sex, status, timezone, website, education_history, work_history, work, education, hs_info, religion, relationship_status, political, activities, interests, family, music, tv, movies, books, username, quotes, sports, favorite_teams, favorite_athletes, inspirational_people, languages FROM user WHERE uid = me()");
        $content = $this->process_fql($fql,$access_token_val);
        
        //pr($content['data'][0],1);
        
        
                
            if(isset($content->error))
                    echo 'A user of access token '.$access_token_val. ' got following error while fetching user details'.$temp_ret_graph;
            else
            {
                
               
                    if(!is_userLoggedIn()) {                         
                      
                        
                       if($this->login_by_facebook($content['data'][0],$access_token_val)){
                            redirect(base_url().'dashboard');                                
                            
                       } else {
                            echo '<script type="text/javascript">';
							echo ' window.location.href="window.location.reload()"; ';
							echo '</script>';
                        }

                    } else {
                        if(get_userLoggedIn('s_email') == $content['data'][0]['email'] ){
                            $content['data'][0]['access_token'] = $access_token_val;
                            $this->user_model->update_user(array("s_facebook_credential"=>serialize($content['data'][0])),
                                    array("id"=>  get_userLoggedIn('id'))
                         );  
                            set_success_msg(message_line('facebook account add success'));
                        } else {
                            set_error_msg(message_line('facebook account email not match'));
                        }
                        
                        redirect(base_url()."user_profile");
                    }
            }   

		} 	
	
    /**
    * Authenticate code by fb access token
    * @param string $access_token_val The access token returned by fb login 
    * either popup or general login
    *
    */
    public function authenticate($access_token_val='')
    {   
        /**********make the access token Extended by extend_access_token() and get extended token********/
        $extended_access_token_val = $this->extend_access_token($access_token_val);
        if($extended_access_token_val==''){
            $access_token_val = $extended_access_token_val;
        } 

        
        /***running FQL to fetch data from facebook ****/
        // $fql = urlencode("SELECT post_id,viewer_id,source_id,updated_time,created_time,actor_id,message,attachment,permalink ,type FROM stream WHERE source_id = me() AND actor_id = me() order by created_time desc LIMIT 5");
        $fql = urlencode("SELECT uid,about_me, birthday, current_location, first_name, has_added_app, hometown_location, last_name, locale,  birthday_date, pic, pic_with_logo, pic_big, pic_big_with_logo, pic_small, pic_small_with_logo, pic_square, pic_square_with_logo, profile_url, proxied_email, email, contact_email, sex, meeting_sex, status, timezone, website, education_history, work_history, work, education, hs_info, religion, relationship_status, political, activities, interests, family, music, tv, movies, books, username, quotes, sports, favorite_teams, favorite_athletes, inspirational_people, languages FROM user WHERE uid = me()");
        $content = $this->process_fql($fql,$access_token_val);
        
        //pr($content['data'][0],1);
        
        
                
            if(isset($content->error))
                    echo 'A user of access token '.$access_token_val. ' got following error while fetching user details'.$temp_ret_graph;
            else
            {
                
               
                    if(!is_userLoggedIn()) {                         
                      
                        
                       if($this->login_by_facebook($content['data'][0],$access_token_val)){
                            redirect(base_url().'dashboard');                                
                            
                       } else {
                            if($this->register_by_facebook($content['data'][0],$access_token_val)){
                                if($this->login_by_facebook($content['data'][0],$access_token_val)){
                                        redirect(base_url().'dashboard');
                                } else {
                                        echo 'login failed!';
                                }
                            }
                            //echo 'registration failed!';
                           set_error_msg(message_line('fb_reg_fail'));  // either user email is not verified in fb 
                                                                        // or kept private, so goto signup page
                           redirect(base_url('account/signup'));
                        }

                    } else {
                        if(get_userLoggedIn('s_email') == $content['data'][0]['email'] ){
                            $content['data'][0]['access_token'] = $access_token_val;
                            $this->user_model->update_user(array("s_facebook_credential"=>serialize($content['data'][0])),
                                    array("id"=>  get_userLoggedIn('id'))
                         );  
                            set_success_msg(message_line('facebook account add success'));
                        } else {
                            set_error_msg(message_line('facebook account email not match'));
                        }
                        
                        redirect(base_url()."user_profile");
                    }
            }   

		} 			
    /**
     * Generate extended fb token from general small short term access token
     * @param string $access_token_val short term access token to extend
     * @return string offline extended access token value
     * incase of faliour return empty string.
     */

    private function extend_access_token($access_token_val = NULL){
        $fb_access_token_detail = $this->get_url_data('https://graph.facebook.com/oauth/access_token?client_id='.$this->data['fb_app_id'].'&client_secret='.$this->data['fb_app_secret'].'&grant_type=fb_exchange_token&fb_exchange_token=' . $access_token_val); 
        $offline_access_token_val = '';
        $matches = explode('access_token=',$fb_access_token_detail);
        if(!empty($matches)){  
            $match = explode('&expires=',$matches[1]);
        }
        if(!empty($match)){
            $offline_access_token_val=$match[0];
        }
        return $offline_access_token_val;   
    }
    
    /**
     * Login By Facebook
     * @param string $fb_email Email Address from facebook
     * @return bool false on faliour Else return true
     */
   // public function login_by_facebook($facebookData,$access_token_val,$facebookData) {           
    
      public function login_by_facebook($facebookData,$access_token_val) {  
    
        if(empty($facebookData['email']))
        return false;  
        else   
        $fb_email = $facebookData['email'];
        
        $condition = 'd.s_email ="'.$fb_email.'"';
        $user=$this->user_model->user_load($condition);
        
        //pr($facebookData,1);        
        //pr($user,1);
        
        if(!empty($user))
        {
            //pr($facebookData,1);
            $facebookData['access_token'] = $access_token_val;
            //$facebookData['fb_friends'] = $this->get_friend_list($access_token_val);            
            $this->user_model->update_user(array("s_facebook_credential"=>serialize($facebookData)),
                        array("id"=>$user[0]->id)
             );
			 
			 
			//$user[0]['login_by_facebook'] = 'true';
			$user[0]->login_by_facebook = 'true';
			$this->set_userLoginInfo($user[0]);
			//$this->session->set_userdata(array("global_country_id"=>$user[0]->country_id));
			if($user[0]->country_id>0)
				$this->session->set_userdata(array("global_country_id"=>$user[0]->country_id));
			return true;
        }

        return false;     
    }
     
     
    /**
     * Registration By Facebook
     * @param mix $facebookData Data from facebok
     * @param string $access_token_val Access token
     * @return bool false on faliour Else return true
     */
     public function register_by_facebook($facebookData = NULL,$access_token_val = NULL){
         
    //    pr($facebookData,1);
        if(empty($facebookData['email']))
        return false;
        
        
        $userData=array();
        $userData["s_name"]          = $facebookData['first_name'].' '.$facebookData['last_name'];
        $userData["s_email"]         = $facebookData['email'];
        $userData["s_display_name"]  = $facebookData['first_name'].' '.$facebookData['last_name'];
        //$userData["dt_dob"]          = date('Y-m-d H:i:s',strtotime($facebookData["birthday_date"]));
        $userData["e_gender"]        = ucfirst($facebookData['sex']);;
        $userData["s_password"]      = random_string();
        $userData["s_cnf_password"]  = $userData["s_password"]; 
        
        $userData['dt_dob']=format_date($facebookData["birthday_date"],"Y-m-d");
        
        $userData['s_ip']=$this->input->ip_address();
        $userData['dt_registration']=date("Y-m-d H:i:s");
        $userData['dt_last_login']=date("Y-m-d H:i:s");
        $userData['s_user_name']=$facebookData['email'];
        $facebookData['access_token'] = $access_token_val;
        $userData['s_facebook_credential'] = serialize($facebookData);
        $userData['s_verification_code']  = '';
        $userData['i_email_verified']  = 1;            
        $ret=$this->user_model->add_user($userData);
                        
       if($ret)
       {
         
            $this->user_model->update_user(array("s_short_url"=> encrypt($ret)),
                array("id"=>$ret)
            );  
            
           /*$condition = 'd.s_email ="'.$userData["s_email"].'"  AND u.s_password="'.md5($userData["s_password"]).'" AND u.e_status="active"';*/
           $condition = 'u.e_status="active" AND d.s_email ="'.$userData["s_email"].'"  AND u.s_password="'.md5($userData["s_password"]).'"';
           
           $user=$this->user_model->user_load($condition);
           $this->set_userLoginInfo($user[0]);
           
            // sending email verification code to the user via email
            $mailData['from']   ="Admin <".site_mail().">";
            $mailData['to']     = $userData["s_email"];
            $mailData['subject']= 'Email Verification for '.$userData['s_user_name'].' at '.site_name();
            $mailData['message']=  theme_fbsignup_success_mail($userData);
            $e_ret=sendMail($mailData); //return TRUE on success;
           
            $msg=sprintf(message_line('success registration'),$userData["s_email"]);       
          
            return true;
       }
       else
       {
           set_error_msg(message_line("invalid user"));
           return false;

       }     
                  
        

        return false;
}

/////////////sh ajax Json for any array to use into JS//////////
    /**
     * Perform Curl Operation on specific url
     * @param url string $url Url to perform operation
     * @return string Responce String
     */
    private function get_url_data($url,    $internal_call_count=0){
        //$url = str_replace('access_token=','access_token2=',$url); // for force error testing
        log_message('info', basename(__FILE__).' : '.'get_url_data fetching: '.$url. ' no. of try: '.($internal_call_count+1));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600); // originally 5 secs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Connection: close'));
        $tmp = curl_exec($ch);
        $http_ret_code=curl_getinfo($ch, CURLINFO_HTTP_CODE).'';
        curl_close($ch);    

        $tmp_data=@json_decode($tmp);             

        if(
            ($http_ret_code!='200') ||
            ($tmp=='') ||
            isset($tmp_data->error)
        )
        {
            log_message('debug', basename(__FILE__).' : '.'get_url_data fetching error: '.$tmp.' return status code: '.$http_ret_code.' for url: '.$url);

            $internal_call_count++;
            if($internal_call_count<3)
            {
                sleep(3);
                return $this->get_url_data($url,$internal_call_count);
            }    
        }

        return $tmp;     
    }
     
    /**
     * Perform FQL Operation on specific accesstoken
     * @param String $fql FQL to perform operation by
     * @param String $access_token Access Token to perform operation on
     * @return array Responce
     */
    private function process_fql($fql=NULL,$access_token =NULL) {
        $content = array();
        $content = @json_decode($temp_ret_graph=($this->get_url_data('https://graph.facebook.com/fql?q='.$fql.'&access_token='.$access_token)),true);
        return $content;
    }
    
    /**
     * @access private
     * Get Friends' list in the facebook
     * @param string $access_token Access Token
     * @return array Facebook Friends Array [uid/name/pic_square/sex]
     */
    private function get_friend_list($access_token =NULL) {
      $list = array(); 
      $list['data'] = array();
      $fql = urlencode('SELECT uid, name, pic_square, sex, email FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())');
      $list = $this->process_fql($fql,$access_token);
      if(isset($list['error'])){
          return $list;
      } else {
          return $list['data'];
      }
    }
    
     /**
     * Share on facebook
     * @param string $access_token Access Token
     * @todo Need to test
      * 
     */
    public function share_on_facebook($access_token =NULL) {
//      $list = array(); 
//      $list['data'] = array();
//      $fql = urlencode('SELECT uid, name, pic_square, sex FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = me())');
//      $list = $this->process_fql($fql,$access_token);
//      
//      
    }    
     /**
     * Get Facebook Priend List 
     * @access public
     * @see user_profile|Facebook fan
     * @author Kallol Basu <mail@kallol.net>
     * @param int $usrid user id|it also accept post data $_POST['uid']|default=current login user id
     * @param bool $isJson set true to get json else return array|default:true
     * @return friend list array[uid/name/pic_square/sex/email]||error array 
     */
    
    public function get_user_fb_friends($usrid = null,$isJson = true) {
        
        if($this->input->post('uid')){
           $uid =  $this->input->post('uid');
        } else {
            $uid = $usrid;
        }
        if(!$uid)
            $uid = get_userLoggedIn('id');
        if($uid){
           $CI=&get_instance();
           $CI->load->model('user_model');
           $user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
           $s_facebook_credential = unserialize($user_data_obj[0]->s_facebook_credential);
           if($isJson){
               echo json_encode($this->get_friend_list($s_facebook_credential['access_token']));
           } else {
               return $this->get_friend_list($s_facebook_credential['access_token']);
           }
        }
        return false;
       }
      
       
 }
 

/* End of file welcome.php */
/* Location: ./system/application/controllers/home.php */

