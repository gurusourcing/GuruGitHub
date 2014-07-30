<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin User Role
* Admin 
*  View list, 
*  Add, Edit , Delete Role. 
* 
*/

class User_role extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_type_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user role");//access check  
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>Role<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"Description",
          ),
          array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->admin_type_model->pager["base_url"]=admin_base_url("user_role/index");
        $this->admin_type_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="s_type LIKE '%".$this->input->post("s_type")."%'";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////        
        
        $roles=$this->admin_type_model->admin_type_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($roles))
        {
            foreach($roles as $r)
            {
                $action="";
                if( !$r->i_not_deletable )
                {
                    $action='<a id="edit_action" href="'.admin_base_url("user_role/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                    $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("user_role/delete/".encrypt($r->id) ).'" >Delete</a>';                    
                }
                else
                    $action='No action available. Because this system default roles.';   
                    
                $table["rows"][]=array(
                      $r->s_type,
                      $r->s_desc,
                      $action
                );                
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->admin_type_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="User Role";
        $this->data["add_link"]= anchor(admin_base_url('user_role/operation/add'), '<span class="icos-add"></span>', 'title="Add Role" class="tipS"');
        $this->data["table_roles"]=theme_table($table);
        
        
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
        user_access("administer user role");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->admin_type_model->admin_type_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_type"=>trim(@$modify_data->s_type),
                            "s_desc"=>trim(@$modify_data->s_desc),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="User Role ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer user role");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_type"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_type"] = trim($this->input->post("s_type"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add
            if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_type', 'user role', 'required|is_unique[admin_type.s_type]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_type', 'user role', 'required');
                $this->form_validation->set_rules('form_token', 'form token', 'required');
            }
                
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
                if($posted["action"]=="edit")
                {
                    $ret=$this->admin_type_model->update_admin_type(array(
                                            "s_type"=>$posted["s_type"],
                                            "s_desc"=>$posted["s_desc"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->admin_type_model->add_admin_type(array(
                                            "s_type"=>$posted["s_type"],
                                            "s_desc"=>$posted["s_desc"],
                                        ));                     
                }
 
                  
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
        user_access("administer user role");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->admin_type_model->delete_admin_type(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_role"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_role"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function user_role_permission()
    {
        return array(
            "administer user role"=>array(
                "title"=>"Administer user role",
                "description"=>"Can view, add, edit, delete user roles.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

