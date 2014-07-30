<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Administer Users/Members
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Manage_user extends MY_Controller {
                           
     public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user");//access check  
        
        $table=array();
        
        $table["header"]=array(
        
         array("title"=>"<div>User<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"<div>Last Log In<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Org. Type<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Emp Role<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_model->pager["base_url"]=admin_base_url("manage_user/index");
        $this->user_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_user_name"]))
            {
                $filter="d.s_name LIKE '%".$this->data["posted"]["s_user_name"]."%'";
                $filter.=" OR d.s_display_name LIKE '%".$this->data["posted"]["s_user_name"]."%'";
                $filter.=" OR d.s_email LIKE '%".$this->data["posted"]["s_user_name"]."%'";
            }
			
			/*if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";*/            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_model->user_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
       // pr($rec,1);
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                //pr($r);
                $action="";
                $action.=anchor(admin_base_url("manage_user/impersonate/".encrypt($r->id)),
                            '<span class="iconb" data-icon="&#xe270;"></span>',
                            array(
                                "class"=>"tablectrl_small bDefault tipS",
                                "title"=>"Impersonate",
                                "target"=>"_blank"
                            )
                          );                
                $action.='<a id="approve_action" href="'.base_url("/".encrypt($r->id) ).'" >Approve</a>';
                $action.='<a id="delete_action" href="'.admin_base_url("manage_user/delete/".encrypt($r->id) ).'" >Delete</a>';
                
                /*$user='<a class="lightbox" title="" href=""><img src="'.site_url($r->s_profile_photo).'"></a><br/>'.$r->s_user_name;*/
                $user= theme_user_thumb_picture($r->id).$r->s_display_name.'['.$r->s_email.']';
                $org_type = (intval($r->comp_id)>0)? "Company" : "Individual";
                $emp_role = (($r->i_is_company_owner==1) ? "Owner" : (($r->i_is_company_emp==1)? "Employee" : "N/A"));
                    
                $table["rows"][]=array(
                      /*'<input type="checkbox" value="'.encrypt($r->id).'" name="checkRow" />',*/  
                      
                      $user,
					  $r->dt_last_login,
                      $org_type,
                      $emp_role,
                      $action
                );   
                //pr($table);             
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_model->get_pager();
        
        $this->data["page_title"]="Manage User";
        $this->data["add_link"]= anchor(admin_base_url('manage_user/operation/add'), '<span class="icos-add"></span>', 'title="Add User" class="tipS"');
        $this->data["table"]=theme_table($table);

        $this->render();
        ////end login form starts from here////        
	}
    
    /**
    * Add edit form
    * 
    * @param mixed $action
    * @param mixed $form_token
    */
    public function operation($action="add",$form_token="")
    {
        user_access("administer user");//access check  
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->user_model->user_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_user_name"        => trim(@$modify_data->s_name),
							"s_email"            => trim(@$modify_data->s_email),
                            "action"             => $action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="User ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer user");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_user_name"]))
        {
            $posted["action"] 		        = trim($this->input->post("action")); 
            $posted["s_name"]               = trim($this->input->post("s_user_name"));
			$posted["s_email"] 		        = trim($this->input->post("s_email"));
            $posted["s_password"]           = trim($this->input->post("s_password"));
            $posted["s_confirm_password"]   = trim($this->input->post("s_confirm_password"));
            ////rules 
			$this->form_validation->set_rules('s_user_name', 'user name', 'required');
			$this->form_validation->set_rules('s_email', 'email', 'valid_emails|required|is_unique[user_details.s_email]');
            $this->form_validation->set_rules('s_password', 'new password', 'required|matches[s_confirm_password]');
            $this->form_validation->set_rules('s_confirm_password', 'confirm password', 'required');
            
                
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
                if($posted["action"]=="add")
                {
                    //$posted['s_password']=md5($posted['s_password']);
					$posted['s_password']=$posted['s_password'];
                    $posted['dt_dob']=date("Y-m-d");
                    $posted['s_ip']=$this->input->ip_address();
                    $posted['dt_registration']=date("Y-m-d H:i:s");
                    $posted['dt_last_login']=date("Y-m-d H:i:s");
                    $posted['i_email_verified']=1;//force email verified
                    $posted['s_verification_code']=random_string('alnum', 8);
                    $posted['s_user_name']=$posted["s_email"];
                    $posted["s_display_name"] = $posted["s_name"];
                    $posted["e_gender"]       = "Male";
                    
                    
                    /*$ret=$this->user_model->add_user(array(
                                            "s_email"       =>$posted["s_email"],
											"s_user_name"	=>$posted["s_user_name"],
                                            "s_password"    =>md5($posted['s_password']),
                                        ));*/
                    $ret=$this->user_model->add_user($posted);
                    
                    /**
                    * By default we will try to get the emails  
                    * first letter to check the short url.
                    * TODO::
                    */
                    
                    $s_short_url=generate_unique_shortUrl();
                        $this->user_model->update_user(array("s_short_url"=> $s_short_url),
                            array("id"=>$ret)
                        );                      
                }
                  
                if($ret)//success
                {
                    
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    if($posted["action"]=="add")
                        $ajx_ret["form_token"]=encrypt($ret); 
                          
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
    }    
    
    
    /**
    * Delete 
    * 
    * @param mixed $form_token
    */
    public function delete($form_token)
    {
        user_access("administer user");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_model->delete_user(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("manage_user"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("manage_user"));
        }         
    }
    
    /**
    * impersonate, 
    * Admin is logging in as a user/member in 
    * frontend.  
    * 
    * @param mixed $form_token
    */
    public function impersonate($form_token)
    {
        user_access("administer user");//access check
        $id=decrypt($form_token);    
        
        if(empty($id))
            redirect($this->data["listing_path"]);
            
        ///Un Registering the previously loaded user from session//
        $this->session->unset_userdata("user");
        /**
        * Registering the admin as user for frontend.
        */
        $user=$this->user_model->user_load(intval($id));
        if(empty($user))
            redirect($this->data["listing_path"]);
            
        $this->set_userLoginInfo($user);
        redirect(base_url()."user_profile");
    }   
    
    /**
    * deimpersonate, 
    * Admin is logging out as a user/member from 
    * frontend.  
    */
    public function deimpersonate()
    {
        user_access("administer user");//access check        
            
        ///Un Registering the previously loaded user from session//
        $this->session->unset_userdata("user");
        redirect(admin_base_url("home/dashboard"));
    }     
    
    
    /**
    * Assigning permisions available 
    */
    public function manage_user_permission()
    {
        return array(
            "administer user"=>array(
                "title"=>"Administer user",
                "description"=>"Can view, add, delete User.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
   
}

