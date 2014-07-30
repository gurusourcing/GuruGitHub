<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin My Profile
* Admin can update his profile.
* 
*/

class My_profile extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_model");
    }
    
    /**
    * Profile view / edit form page
    */
	public function index()
	{
        user_access("edit own profile");//access check  
        
        $admin=get_adminLoggedIn();///loggedin user in admin panel
        //pr($admin);
        ////Inedit configuration////
        $enc_id=encrypt($admin->id);
        $default_value[0]=json_encode(array(
                            "s_admin_name"=>$admin->s_admin_name,
                            "form_token"=>$enc_id,
                            ));
        $default_value[1]=json_encode(array(
                            "s_admin_name"=>$admin->s_admin_name,
                            "form_token"=>$enc_id,
                            ));        
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="My Profile";
        $this->render();
        ////end login form starts from here////        
	}
    
    
    /**
    * Form post
    */
    public function ajax_edit_profile()
    {
        user_access("edit own profile");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_admin_name"]))
        {
            $posted["s_admin_name"] = trim($this->input->post("s_admin_name"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            $this->form_validation->set_rules('s_admin_name', 'user name', 'required|min_length[5]|max_length[12]|is_unique[admin.s_admin_name]');  
            $this->form_validation->set_rules('form_token', 'form token', 'required'); 
            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $ret=$this->admin_model->update_admin(array(
                                        "s_admin_name"=>$posted["s_admin_name"]
                                    ),array("id"=>$posted["form_token"])); 
                  
                if($ret)//success
                {
                    /**
                    * update the session for loggedin admin, 
                    * as the profile has been modified
                    */
                    $admin_modif=$this->admin_model->admin_load(intval($posted["form_token"]));
                    $this->set_adminLoginInfo($admin_modif); 
                    
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }     
           
        }//end if
        elseif(isset($_POST["s_current_password"])) ////saving section 1  
        {
            $posted["s_current_password"] = trim($this->input->post("s_current_password"));
            $posted["s_password"] = trim($this->input->post("s_password"));  
            $posted["s_confirm_password"] = trim($this->input->post("s_confirm_password"));  
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            $this->form_validation->set_rules('s_current_password', 'current password', 'required');  
            $this->form_validation->set_rules('s_password', 'new password', 'required|matches[s_confirm_password]');  
            $this->form_validation->set_rules('s_confirm_password', 'confirm password', 'required');    
            
            $this->form_validation->set_rules('form_token', 'form token', 'required'); 
            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                ///e10adc3949ba59abbe56e057f20f883e 
                $ret=FALSE;
                $ret=$this->admin_model->update_admin(array(
                                        "s_password"=>md5($posted["s_password"])
                                    ),array("id"=>$posted["form_token"],
                                            "s_password"=>md5($posted["s_current_password"]))
                                    ); 
                
                if($ret)//success
                {
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }///end else     
           
        }//end elseif          
    }    
    
    
    /**
    * Assigning permisions available 
    */
    public function my_profile_permission()
    {
        return array(
            "edit own profile"=>array(
                "title"=>"Edit own profile",
                "description"=>"If checked, then users under that role can modify his own profile.",
            ),
            
        );
    }//end welcome_permission
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */