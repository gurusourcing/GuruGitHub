<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin User, 
*  Who can view all users of admin sections.
*  
*  Users permissions for this controller are  
*       "administer admin user",
*       "view admin list", "view own domain admin list", 
*       "add any admin", "edit any admin", "delete any admin",
*       "add own domain admin", "edit own domain admin", "delete own domain admin",
* 
* TODO :: Franchisee Section shifted into Phase2. 
* 
* Admin 
*  View list, 
*  Add, Edit , Delete admin users. 
* 
*/

class Admin_user extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        //user_access("administer user role");//access check  
        /**
        * Checking multiple access.
        * If any of the permission is true then 
        * user is allowed
        */
        if(!check_multiPermAccess(array(
                "administer admin user",
                "view admin list",
                "view own domain admin list"
            ))
        )
        {
            goto_accessDeny();
        }
        
        
        
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>User Name<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Role<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Last Login Since<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Registered Since<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Ip<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Domain<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
        );
        $table["no result text"]="No information found.";
        
        /**
        * Show the action header only is these access are true
        */
        if(check_multiPermAccess(array(
               "edit any admin", "delete any admin",
               "edit own domain admin", "delete own domain admin",
            ))
        )
        {
            $table["header"][]= array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
            );
        }
        
        ////Auto Pagination
        $this->admin_model->pager["base_url"]=admin_base_url("admin_user/index");
        $this->admin_model->pager["uri_segment"]=4;
        
        /**
        * exclude the site super admin from display and from any type of operation.
        * A fixed user must be always there to handle critical situation of the site.
        */
        //$condition=array("a.id !="=>1);
        $condition="a.id !=1 ";
        
        /**
        * Show the own domain's records as per permission.
        */
        if(check_multiPermAccess(array(
               "add own domain admin", "edit own domain admin", "delete own domain admin",
            ))
            && !user_access("administer admin user") 
        )
        {
            //$condition["a.s_domain_name"]=get_adminLoggedIn("s_domain_name");
            $condition.=" AND a.s_domain_name ='".get_adminLoggedIn("s_domain_name")."'";
        }        
        
        //////Filter/////
        if($this->input->post("submit"))
        {
            $condition.="AND s_admin_name LIKE '%".$this->input->post("s_admin_name")."%'";
            //if(!empty($this->input->post("admin_type_id")))
                $condition.="AND admin_type_id LIKE '%".$this->input->post("admin_type_id")."%'";
                
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////        
        
        $rec=$this->admin_model->admin_load(
                                  $condition,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                if(check_multiPermAccess(array(
                       "edit any admin","edit own domain admin",
                    ))
                )
                {
                    $action.='<a id="edit_action" href="'.admin_base_url("admin_user/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                }                
                if(check_multiPermAccess(array(
                       "delete any admin","delete own domain admin",
                    ))
                )
                {
                    $action.='<a id="delete_action" href="'.admin_base_url("admin_user/delete/".encrypt($r->id) ).'" >Delete</a>';
                }                  
                //pr(site_url(admin_base_url()) );
                
                /*$last_login=(strtotime($r->dt_last_login)
                            ? timespan( strtotime($r->dt_last_login),  time() )
                            :"");*/
                $last_login=(!isEmptyDateField($r->dt_last_login)
                            ? timespan( strtotime($r->dt_last_login),  time() )
                            :"");
                    
                $table["rows"][]=array(
                      $r->s_admin_name,
                      $r->s_type,
                      $last_login,
                      timespan( strtotime($r->dt_registration),  time() ),
                      $r->s_ip,
                      $r->s_domain_name,
                      $action
                );  
                
            }
        }
         /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->admin_model->get_pager();
        
        $this->data["page_title"]="Manage Admins";
        if(check_multiPermAccess(array(
               "add any admin","add own domain admin",
            ))
        )
        {
            $this->data["add_link"]= anchor( admin_base_url('admin_user/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
        }
        
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
        //user_access("administer user role");//access check    
        if(!check_multiPermAccess(array(
               "administer admin user","add any admin","add own domain admin", 
            ))
            && $action=="add"
        )
            goto_accessDeny();
        elseif(!check_multiPermAccess(array(
               "administer admin user","edit any admin","edit own domain admin", 
            ))
            && $action=="edit"
        )
            goto_accessDeny();
        //////end access check/////
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        
        $modify_data=$this->admin_model->admin_load(intval($id));
        
        $edit_any=check_multiPermAccess(array("administer admin user","edit any admin"));
        $default_value[0]=json_encode(array(
                            "form_token"=>$form_token,
                            "action"=>$action,
                            "admin_type_id"=>trim(@$modify_data->admin_type_id),
                            "s_admin_name"=>trim(@$modify_data->s_admin_name),
                            "s_domain_name"=>trim($edit_any 
                                                ? @$modify_data->s_domain_name
                                                : get_adminLoggedIn("s_domain_name")),
                            ));
         
                 
        $this->data["default_value"]= $default_value;
        $this->data["action"]=$action;
        
        $this->data["page_title"]="Admin User ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        //user_access("administer user role");//access check
        if(!check_multiPermAccess(array(
               "administer admin user","add any admin","add own domain admin", 
            ))
            && trim($this->input->post("action"))=="add"
        )
            goto_accessDeny();
        elseif(!check_multiPermAccess(array(
               "administer admin user","edit any admin","edit own domain admin", 
            ))
            && trim($this->input->post("action"))=="edit"
        )
            goto_accessDeny();
        //////end access check/////        
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
            "form_token"=>"",
        );
        
        $posted=array(); 
        if(isset($_POST))
        {
            $posted["action"] = trim($this->input->post("action"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
                         
            $posted["admin_type_id"] = trim($this->input->post("admin_type_id"));
            $posted["s_admin_name"] = trim($this->input->post("s_admin_name"));
            $posted["s_password"] = trim($this->input->post("s_password"));
            $posted["s_confirm_password"] = trim($this->input->post("s_confirm_password"));
            $posted["s_domain_name"] = trim($this->input->post("s_domain_name"));
            
            
            ////rules for add
            /*if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_type', 'user role', 'required|is_unique[admin_type.s_type]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_type', 'user role', 'required');
                $this->form_validation->set_rules('form_token', 'form token', 'required');
            }*/
            
            if($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('form_token', 'form token', 'required');
            }
            

            ////Validation Rules as per inedit section////
            $this->form_validation->set_rules('admin_type_id', 'user role', 'required');
            
            if($posted["action"]=="add")
                $this->form_validation->set_rules('s_admin_name', 'user name', 'required|is_unique[admin.s_admin_name]');
            else
                $this->form_validation->set_rules('s_admin_name', 'user name', 'required');    
            /**
            * For add mode password are mandatory.
            * In edit mode if both password fields are empty, 
            * then donot update that field.
            */
            if(!empty($posted["s_password"])||!empty($posted["s_confirm_password"])
                || $posted["action"]=="add" 
            )   
            {
                $this->form_validation->set_rules('s_password', 'new password', 'required|matches[s_confirm_password]');
                $this->form_validation->set_rules('s_confirm_password', 'confirm password', 'required');
            }         
            ///end if both password fields are empty, then donot update that field////
                        
            $this->form_validation->set_rules('s_domain_name', 'domain name', 'required'); 
            ////end Validation Rules as per inedit section////
            
                
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
                $dml_val=array(
                    "admin_type_id"=>$posted["admin_type_id"],
                    "s_admin_name"=>$posted["s_admin_name"],
                    "s_domain_name"=>$posted["s_domain_name"],
                );
                
                /**
                * For add mode password are mandatory.
                * In edit mode if both password fields are empty, 
                * then donot update that field.
                */
                if(!empty($posted["s_password"]) || $posted["action"]=="add")   
                    $dml_val["s_password"]=md5($posted["s_password"]);
                ///end if both password fields are empty, then donot update that field////
                
                
                if($posted["action"]=="edit")
                {
                    $ret=$this->admin_model->update_admin($dml_val,
                                                    array("id"=>$posted["form_token"])
                                                    );                    
                }
                elseif($posted["action"]=="add")
                {
                    $dml_val["dt_registration"]=date("Y-m-d H:i:s");
                    $dml_val["s_ip"]= $this->input->ip_address();
                    
                    $ret=$this->admin_model->add_admin($dml_val);     
                    $ajx_ret["form_token"]=encrypt($ret);//to save callback
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
        //user_access("administer user role");//access check
        if(!check_multiPermAccess(array(
               "administer admin user","delete any admin","delete own domain admin", 
            ))
        )
            goto_accessDeny();        
        //////end access check///
        
        $id=decrypt($form_token);    
        
        $ret=$this->admin_model->delete_admin(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            //redirect(admin_base_url("admin_user"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            //redirect("admin/admin_user");
        }
        
        redirect(admin_base_url("admin_user"));         
    }
    
    /**
    * Assigning permisions available 
    * TODO :: Franchisee Section shifted into Phase2. 
    */
    public function admin_user_permission()
    {
        return array(
            "administer admin user"=>array(
                "title"=>"Administer admin users",
                "description"=>"Can view, add, edit, delete any admin user. Who can login into admin panel.
                                ".message_line("security concern"),
            ),
            "view admin list"=>array(
                "title"=>"View all admin users",
                "description"=>"Can view all admin users in list.
                                ".message_line("security concern"),
            ),              
            "add any admin"=>array(
                "title"=>"Add admin users for any domian.",
                "description"=>"Can add admin users for any domain. The add form will show the domain drop down.
                                ".message_line("security concern"),
            ),                       
            "edit any admin"=>array(
                "title"=>"Edit admin users of any domian.",
                "description"=>"Can edit admin users of any domain. The edit form will show the domain drop down.
                                ".message_line("security concern"),
            ),            
            "delete any admin"=>array(
                "title"=>"Delete admin users of any domian.",
                "description"=>"Can delete admin users of any domain. 
                                ".message_line("security concern"),
            ),  
            
            /*"view own domain admin list"=>array(
                "title"=>"View own domain admin users",
                "description"=>"Can view all admin users in list of the domain user registered.
                                <br/> Applicable for \"Franchisee admins\"."
            ),             
            "add own domain admin"=>array(
                "title"=>"Add admin users for own domian.",
                "description"=>"Can add admin users for own domain. The add form will not show the domain drop down.
                               <br/> And the loggedin user's domain will be asigned to the newly added user.
                               <br/> Applicable for \"Franchisee admins\".
                               ",
            ),
            "edit own domain admin"=>array(
                "title"=>"Edit admin users of own domian.",
                "description"=>"Can edit admin users of own domain. The domain selection dropdown will not be editable.
                               <br/> Applicable for \"Franchisee admins\". ",
            ),
                                                   
            "delete own domain admin"=>array(
                "title"=>"Delete admin users of own domian.",
                "description"=>"Can delete admin users of own domain. 
                                <br/> Applicable for \"Franchisee admins\".",
            ),*/            
        );
    }//end welcome_permission
}

