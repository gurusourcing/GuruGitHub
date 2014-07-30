<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe user signin, signup
* 
*/

  

class Account extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        $this->data['hideSocialConnect'] = '';
        $this->load->model('user_model');
        //$this->load->helper('string');//already loaded in autoload
    }
    
    public function signin()
    {
        if( is_userLoggedIn() )
        {
            redirect(base_url()."dashboard");
        }
          
        if($this->input->post()){              
            $posted=array();
            $posted["userdata"]= trim($this->input->post("userdata"));
            $posted["password"]= trim($this->input->post("password"));

            $this->form_validation->set_rules('userdata', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');           
            
            if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
            }
            else
            {   
                /*$condition = '(u.s_user_name ="'.$posted["userdata"].'" OR d.s_email ="'.$posted["userdata"].'") AND u.s_password="'.md5($posted["password"]).'" AND u.e_status="active"';*/
                /*$condition ='u.e_status ="active" AND (u.s_user_name ="'.$posted["userdata"].'" OR d.s_email ="'.$posted["userdata"].'") AND u.s_password="'.md5($posted["password"]).'"';*/
                
                $condition ="u.e_status ='active' AND (u.s_user_name ='".$posted["userdata"]."' OR d.s_email ='".$posted["userdata"]."') AND u.s_password='".md5($posted["password"])."'";
                
                $user=$this->user_model->user_load($condition);
                
                if(!empty($user))
                {
					
                    // calculating profile completion  (%)
                    $profile_complete = user_profile_prc_calculation($user[0]->id);
                    $user[0]->i_profile_complete_percent = $profile_complete;
  
                    $this->set_userLoginInfo($user[0]);
                    
                    /* this portion is for fb fetch friend cron */
                    $s_extra_fetch_friend=unserialize($user[0]->s_extra_fetch_friend);
                    
                    if(empty($s_extra_fetch_friend)) // if s_extra_fetch_friend is empty
                        $s_extra_fetch_friend=array('count_visits'=>0,'count_login'=>1); // initialize the array
                    else
                        $s_extra_fetch_friend['count_login']+=1;  // increase the count_login 
                   
                   // update the database                        
                   $this->user_model->update_user(array("s_extra_fetch_friend"=>serialize($s_extra_fetch_friend)),
                                                    array('id'=>intval($user[0]->id)));
                    
                    /* end of fb fetch friend cron */
					if($user[0]->country_id>0)
                    	$this->session->set_userdata(array("global_country_id"=>$user[0]->country_id));
						
                    redirect(base_url()."dashboard");
                }
                else
                {
                    set_error_msg(message_line("invalid user"));
                    $this->data["posted"]=$posted;
                }
              }
        }
        ////login form starts from here////        
        $this->data['page_title'] = 'Sign in';
        $this->render();
    }
    
    public function forget_password()
    {
         session_start();    
         $posted["s_email"]         = trim($this->input->post("s_email"));
         $posted["captcha"]         = trim($this->input->post("captcha"));
         
         $this->form_validation->set_rules('captcha','captcha', 'required|trim|callback__captcha_valid');
         if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
            }
            else
            {   
                $condition=array('s_email'=>$posted["s_email"]);
                $user=$this->user_model->user_load($condition);
                
                if(count($user)){
                    
                    $user=$user[0];
                    $new_password = random_string();
                    //updating the last login field///
                    $ret=$this->user_model->update_user(array("s_password"=>md5($new_password)),
                                                array("id"=>$user->id)
                                                );  
                    if($ret)
                    {
                        // sending Password to the user via email
                        $mailData['from']   ="Admin <".site_mail().">";
                        $mailData['to']     = $posted["s_email"];
                        $mailData['subject']= 'Request New Password for '.$user->s_user_name.' at '.site_name();
                        $user_data['s_user_name'] = $user->s_user_name; 
                        $user_data['s_name'] = $user->s_name;
                        $user_data['s_password'] = $new_password;
                        $mailData['message']= theme_forget_password_mail($user_data);        
                        sendMail($mailData);    
                        //updating the last login field///   
                        set_success_msg(message_line("forget password mail sent"));                        
                                    
                    }
                    else {
                        set_error_msg(message_line("server error"));
                    } 
                } else {
                    set_error_msg(message_line("email not exist"));
                }
            }
        $this->data['page_title'] = 'Forget Password';
        $this->render();
    }
	
	
    public function signup()
    {
        //pr($this->input->ip_address(),1);
        
        if($_POST)
        {
           session_start();       
           $posted=array();
           $posted["s_name"]         = trim($this->input->post("s_user_name"));
           $posted["s_email"]        = trim($this->input->post("s_email"));
           $posted["s_display_name"] = trim($this->input->post("s_display_name"));
           $posted["dt_dob"]         = $this->input->post("dt_dob");
           $posted["e_gender"]       = trim($this->input->post("e_gender"));
           $posted["s_password"]     = trim($this->input->post("s_password"));
           $posted["s_cnf_password"] = trim($this->input->post("s_cnf_password"));
           $posted['txt_captcha']     = $this->input->post('txt_captcha');
           
           //pr($posted,1);

           $this->form_validation->set_rules('s_user_name', 'User Name', 'required|trim');
           $this->form_validation->set_rules('s_email', 'Email', 'valid_email|is_unique[user_details.s_email]');
           $this->form_validation->set_rules('dt_dob', 'Date of Birth', 'required');
           $this->form_validation->set_rules('s_password', 'Password', 'required|matches[s_cnf_password]');
           $this->form_validation->set_rules('s_cnf_password', 'Confirm Password', 'required');
           $this->form_validation->set_rules('txt_captcha','captcha', 'required|trim|callback__captcha_valid');          

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
                set_error_msg(validation_errors());
            }
            else
            {   
                $posted['dt_dob']=format_date($posted['dt_dob'],"Y-m-d");
                $posted['s_ip']=$this->input->ip_address();
                $posted['dt_registration']=date("Y-m-d H:i:s");
                $posted['dt_last_login']=date("Y-m-d H:i:s");
                $posted['s_user_name']=$posted["s_email"];
                $posted['s_verification_code']=random_string('alnum', 8);   
               
                $ret=$this->user_model->add_user($posted);
                
                if($ret)
                {
                    $s_short_url=generate_unique_shortUrl();
                        $this->user_model->update_user(array("s_short_url"=> $s_short_url),
                            array("id"=>$ret)
                        );  
                    
                    // sending email verification code to the user via email
                    $mailData['from']   ="Admin <".site_mail().">";
                    $mailData['to']     = $posted["s_email"];
                    $mailData['subject']= 'Email Verification for '.$posted['s_user_name'].' at '.site_name();
                    $mailData['message']=  theme_signup_confirmation_mail($posted);
                    $e_ret=sendMail($mailData); //return TRUE on success;
                    
                    //$condition = 'd.s_email ="'.$posted["s_email"].'"  AND u.s_password="'.md5($posted["s_password"]).'" AND u.e_status="active"';
                    //$user=$this->user_model->user_load($condition);
                    //pr($this->db->last_query());
                    //$this->set_userLoginInfo($user[0]); \
                    $msg=sprintf(message_line('success registration'),$posted["s_email"]);
                    set_success_msg($msg);
                    
                    redirect(base_url()."account/signin");
                }
                else
                {
                    set_error_msg(message_line("invalid user"));
                    $this->data["posted"]=$posted;
                }     
            }
        }
           
        $this->data['page_title'] = 'Sign Up';
        $this->data['benefits']=get_cms(11);
        $this->render();
    }
    
    public function index() 
    {
        //pr(get_userLoggedIn());
        
    }
    
    public function signout()
    {
        //updating the last login field///
        $this->user_model->update_user(array("dt_last_login"=>date("Y-m-d H:i:s")),
                                array("id"=>get_userLoggedIn("id"))
                                );      
       //updating the last login field///   
        
        $this->reset_userLoginInfo();
        redirect(base_url()."home");
    }
    
    /**
    * Validating captcha
    */
        
    public function _captcha_valid($s_captcha)
    {
        if($s_captcha!=$_SESSION["captcha"])
        {
            // $this->form_validation->set_message('_captcha_valid', 'Please provide correct %s.');
             set_error_msg(message_line("captcha missmatch"));  
             unset($s_captcha);
             return false;
        }
        else
        {
            return true;
        }
    }
	
	function _alpha_dash_space($str)
	{
		if ( ! preg_match("/^([-a-z_ ])+$/i", $str))
		{
            // $this->form_validation->set_message('_captcha_valid', 'Please provide correct %s.');
             set_error_msg('proper name');  
             unset($str);
             return false;
        }
        else
        {
            return true;
        }
		
	} 
    
    
    /**
    * confirm email
    */
    public function verify_email($code)
    {
        $condition=array('s_verification_code'=>$code,'i_email_verified'=>0);
        $user=$this->user_model->user_load($condition); //checking if any user exist in table having the code.
        
        if(!empty($user)) //user exists
        {
            $ret=$this->user_model->update_user(array('i_email_verified'=>'1'),array('id'=>$user[0]->id));
            if($ret)
            {
                
                $posted["s_email"]=$user[0]->s_email;
                $posted["s_user_name"]=$user[0]->s_user_name;
                $posted["s_name"]=$user[0]->s_name; 
                
                //sending email welcome email to the user via email
                $mailData['from']   ="Admin <".site_mail().">";
                $mailData['to']     = $posted["s_email"];
                /*$mailData['subject']= 'Account details for '.$posted['s_user_name'].' at '.site_name();
                $mailData['message']=  theme_signup_welcome_mail($posted);*/
				$mailData['subject']= 'Email Verification for '.$posted['s_user_name'].' at '.site_name();
                $mailData['message']=  theme_email_verify_mail($posted);
                $e_ret=sendMail($mailData); //return TRUE on success;                
                
                /**
                * show success message
                */
                $this->data['msg'] = message_line('email verification success'); 
           }
           else
           {
                /**
                * show error message 
                */
                $this->data['msg'] = message_line('email verification error'); 
           }
        }
        else //invalid user
        {
                $this->data['msg'] = message_line('email verification code error');
        }
            
        
        
        $this->data['page_title'] = 'Verify Email'; 
        $this->render(); 
          
    }
    
    
    
    /**
    * mobile verification
    */
    public function verify_mobile()
    {
        is_userLoggedIn(TRUE);
        
        $data = $this->user_model->user_load(intval( get_userLoggedIn('id')));
       // pr($data);
        //mobile already verified , then go back
        if($data->i_mobile_verified)
            redirect('dashboard');
      // verification code is generated but mobile num is not verified
        else if($data->s_mobile_verify_code!='' && intval($data->i_mobile_verified)==0)
            $this->enter_verification_code();
      // verification code is not generated
        else
            $this->enter_mobile_num();
        
    }
    
    /**
    *  updates i_mobile_verified=1 for the logged in user
    */
    public function enter_verification_code()
    {
        if($_POST)
        {
            $code = trim($this->input->post('mob_verification_codes'));
            
            $data= $this->user_model->user_load(array('u.s_mobile_verify_code'=>$code, 'u.id'=>get_userLoggedIn('id')));
           
            //pr($data);
            if(empty($data))/////invalid
            {
                $this->data["verification_code"]=$code;
                set_error_msg(message_line('mobile_verification_error'));
            }
            else
            {   
                 $this->user_model->update_user(
                            array('i_mobile_verified'=>1),array('id'=>get_userLoggedIn('id')));
                            
                 redirect('dashboard');   
            }   
            
        }
        $this->render('verify_mobile/enter_verification_code');
    }

    /**
    * sends the verification code to the mobile num entered and  set the value into 's_mobile_verify_code'
    */
    public function enter_mobile_num()
    {
        if($_POST)
        {
           
            $mobile_num = $this->input->post('mobile_num');
            
            $this->form_validation->set_rules('mobile_num', 'Phone number','exact_length[10]');
           
            
            if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["mobile_num"]=$mobile_num;
                set_error_msg(validation_errors());
            }
            else
            {   
                 $mobile_verify_code=random_string('alnum', 8);
                 
                 $this->user_model->update_user(
                            array('s_mobile_verify_code'=>$mobile_verify_code,'s_mobile'=>$mobile_num),
                            array('id'=>get_userLoggedIn('id'))
                            );
                            
                 
                $posted["s_email"]=get_userLoggedIn('s_email');
                $posted["s_user_name"]=get_userLoggedIn('s_user_name');
                $posted["s_name"]=get_userLoggedIn('s_name');
                $posted["s_mobile_verify_code"]=$mobile_verify_code;
                
                //sending email welcome email to the user via email
                $mailData['from']   ="Admin <".site_mail().">";
                $mailData['to']     = $posted["s_email"];
                $mailData['subject']= 'Mobile Vrification for '.$posted['s_user_name'].' at '.site_name();
                $mailData['message']=  theme_mobile_verify_mail($posted);
                $e_ret=sendMail($mailData); //return TRUE on success;                
                
                /**
                * show success message
                */
                if($e_ret)
                {
                    redirect('account/verify_mobile');
                }
                else
                {
                    set_success_msg(message_line('mobile verification send mail failed'));
                }
            }
        }
        $this->render('verify_mobile/enter_mobile_num');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
