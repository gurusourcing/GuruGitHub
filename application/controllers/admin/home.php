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
        $this->load->model("admin_model");
    }
    
    /**
    * Login page
    */
	public function index()
	{
        //pr(is_userLoggedIn());
        
        /*$admin=$this->admin_model->admin_load(array(
                            "a.id"=>1));
        pr($admin,1);*/
        
        if( is_adminLoggedIn() )
        {
            redirect(admin_base_url("home/dashboard"));
        }
                      
        $posted=array();
        $posted["txt_user_name"]= trim($this->input->post("txt_user_name"));
        $posted["txt_password"]= trim($this->input->post("txt_password"));
        
        $this->form_validation->set_rules('txt_user_name', 'user name', 'required');
        $this->form_validation->set_rules('txt_password', 'password', 'required');           
        if($this->form_validation->run() == FALSE)/////invalid
        {
            $this->data["posted"]=$posted;
        }
        else
        {
            
            /**
            * TODO :: Franchisee Section shifted into Phase2. 
            * So we will escape the domain checking
            */            
            $admin=$this->admin_model->admin_load(array(
                            "s_admin_name"=>$posted["txt_user_name"],
                            "s_password"=>md5($posted["txt_password"]),
                            /*"s_domain_name"=>current_domain()*/
                            ));
            if(!empty($admin))
            {
                $this->set_adminLoginInfo($admin[0]);
                //pr(get_adminLoggedIn());
                //pr(is_adminLoggedIn());
                //print_r($_SESSION);die();
                redirect(admin_base_url("home/dashboard"));
            }
            else
            {
                set_error_msg(message_line("invalid user"));
                $this->data["posted"]=$posted;
            }
            
        }   
        ////login form starts from here////
        
        $this->data["page_title"]="Admin Login";
        $this->hide_menus=TRUE;
        $this->render("",TRUE);
        ////end login form starts from here////        
	}
    
    /**
    * Logout 
    */
    public function logout()
    {
        //updating the last login field///
        $this->admin_model->update_admin(array("dt_last_login"=>date("Y-m-d H:i:s")),
                                array("id"=>get_adminLoggedIn("id"))
                                );      
       //updating the last login field///   
        
        $this->reset_adminLoginInfo();
        redirect(admin_base_url("home"));
    }
    
    
    /**
    * Admin Dashboard
    */
    public function dashboard()
    {
        ///dashboard must be used by all admin users
        //user_access("view admin dashboard");//access check
        if(!is_adminLoggedIn())
            goto_accessDeny();
        
		clear_allCache();
        //pr(is_adminLoggedIn());pr(user_access("view admin dashboard"));
        ////dashboard////
        $this->data["page_title"]="Admin Dashboard";
        $this->render();
        ////end dashboard////
    }    

    /**
    * Admin Clear cache
    */
    public function clear_cache()
    {
        user_access("clear cache");//access check
        
        clear_allCache();
        
        ///Re-scan the theme folder and update the db
        get_allThemes();
        
        //reassign the theme settings, if user is loggedin
        $this->reset_themes();
        
        set_success_msg("All cache are cleared");
        /*pr(get_destination());
        pr(current_url());*/
        redirect(get_destination());
    }    
    
    /**
    * Assigning permisions available 
    */
    public function home_permission()
    {
        return array(
            //because every admin must land into dashboard after successful login
            /*"view admin dashboard"=>array(
                "title"=>"Can View Admin Dashboard",
                "description"=>"If checked, then users under that role can view the dashboard page.",
            ),*/
            "clear cache"=>array(
                "title"=>"Can Clear Cache",
                "description"=>"If checked, then users under that role can clear all cache."
                                .message_line("security concern") ,
            ),            
        );
    }//end welcome_permission
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */