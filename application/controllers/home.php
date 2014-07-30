<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Dashboard
* Admin Login 
* 
* TODO :: Franchisee Section shifted into Phase2. 
*/


class Home extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
    }
	    
    public function index()
    {
        $this->data['page_title'] = 'Home';
        
        /* getting random active user */
        $this->load->model('user_model');
        $condition=array("e_status"=>"active");
        $home_page_user_data=$this->user_model->user_load($condition,10, 0,"RAND()");
        
        $this->data['home_page_user_data']=$home_page_user_data;		
		$this->data['pop_txt']=get_cms(15); // on 20 Nov 2013
			
        $this->render("",TRUE);
    }
    
    public function cms($s_url){
        $this->load->model('cms_model');
        $condition = array('s_url'=>$s_url);
        $cms_data = $this->cms_model->cms_load($condition);
        $this->data['page_title'] = $cms_data[0]->s_menu;
        $this->data['page_content'] = $cms_data[0]->s_content;

        $this->render();
    }
    
//    public function short_desc($uid){
//        if(empty($uid))
//            $uid=get_userLoggedIn("id");
//        else
//            $uid=decrypt($uid);
//         $this->load->model('user_model');
//        $data = 'no uid';
//        if($uid){
//            $data = short_desc($uid);
//        }
//        pr($data);
//    }
    
    public function contact() {
        session_start(); 
        $posted = $this->input->post();
        if(!empty($posted)){
            $this->form_validation->set_rules('captcha','Captcha', 'required|trim|callback__captcha_valid');
            $this->form_validation->set_rules('name','Name', 'required|trim');
            $this->form_validation->set_rules('email','Email', 'required|trim');
         if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
            }
            else
            {   
                
                // sending contact email to the admin via email
                $mailData['from']   ="Admin <".site_mail().">";
                $mailData['to']     = site_mail();
                $mailData['subject']= 'Contact Request';
                $mailData['from']   = $posted["email"];
                $mailData['message']= theme_contact_mail($posted);
                
                if(sendMail($mailData)){
                     
                    set_success_msg(message_line("contact us success"));
                } else {
                    set_error_msg(message_line("server error"));
                } 
                
            }
        }
        
        $this->load->model('cms_model');
        $condition = array('s_url'=>'contact');
        $cms_data = $this->cms_model->cms_load($condition);
        $this->data['page_title'] = $cms_data[0]->s_menu;
        $this->data['page_content'] = $cms_data[0]->s_content;
       
        $this->render();
    }
    
    /**
    * Validating captcha
    */
        
    function _captcha_valid($s_captcha)
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
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */