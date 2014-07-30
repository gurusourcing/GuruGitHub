<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin User Services
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_services extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_service_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user services");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>User<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"<div>Category<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         /*array("title"=>"<div>Sub Category<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),*/
         array("title"=>"<div>Service<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_service_model->pager["base_url"]=admin_base_url("user_services/index");
        $this->user_service_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_user_name"]))
		   		$filter="u.s_user_name LIKE '%".$this->data["posted"]["s_user_name"]."%'";
			
			/*if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_service_model->user_service_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                
                $data = array(
                'name'        => 'i_active',
                'class'       => 'i_active',
                'value'       => $r->id,
                'checked'     => (bool)$r->i_active,
                );


                 $action.='<div id="active_action" class="floatL mr10 on_off tipS" title="Active / Inactive">'.form_checkbox($data).'</div>';

                
                
                
                //$action.='<div id="active_action" class="floatL mr10 on_off"><input type="checkbox" id="check20" checked="checked" name="chbox" /></div>'; 
              
                //$action.='<div id="active_action" class="floatL mr10 on_off">'.form_checkbox('i_active['.$r->id.']',intval($r->i_active) , (bool)$r->i_active,'class=i_active').'</div>';
               
                $action.='<a id="delete_action" href="'.admin_base_url("user_services/delete/".encrypt($r->id) ).'" >Delete</a>';  
               
                $table["rows"][]=array(
                      $r->s_user_name,
					  $r->s_category,
                      $r->s_service_name,
                      $action
                );
               
            }
        }
         //pr($table["rows"]);                
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_service_model->get_pager();
        
        $this->data["page_title"]="Manage User Services";
       
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
        user_access("administer user services");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->user_service_model->user_service_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_user_name"        => trim(@$modify_data->s_user_name),
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
        user_access("administer user services");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_user_name"]))
        {
            $posted["action"] 		        = trim($this->input->post("action")); 
            $posted["s_user_name"]          = trim($this->input->post("s_user_name"));
			$posted["s_email"] 		        = trim($this->input->post("s_email"));
            $posted["s_password"]           = trim($this->input->post("s_password"));
            $posted["s_confirm_password"]   = trim($this->input->post("s_confirm_password"));
            ////rules 
			$this->form_validation->set_rules('s_user_name', 'user name', 'required');
			$this->form_validation->set_rules('s_email', 'email', 'required');
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
                    
                    $ret=$this->user_service_model->add_user_service(array(
                                            "s_email"       =>$posted["s_email"],
											"s_user_name"	=>$posted["s_user_name"],
                                            "s_password"    =>md5($posted['s_password']),
                                        ));
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
        user_access("administer user services");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_service_model->delete_user_service(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_services"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_services")); 
        }         
    }
    
  
    /**
    * change the service status 
    */
    public function changeStatus()
    {
        //pr($_POST);
        if(!empty($_POST)) 
        {
            foreach($_POST["i_active"] as $id=>$v )
            {
               $where="id=".intval($id);
               $ret=$this->user_service_model->update_user_service_status("",$where);
            }
            //redirect("admin/user_services");
        }
    }
    
     public function ajax_status_update(){
            
            $posted   = $this->input->post(); 
            foreach($posted as $id=>$value){
                $val=$value=='1'?array("i_active"=>'1'):array("i_active"=>'0');
                $condition=array('id'=>$id);
                $res=$this->user_service_model->update_user_service($val,$condition); 
                
                if($res)
                    set_success_msg(message_line("status update success"));
                else
                    set_error_msg(message_line("status update error"));
                
            }
            

    }
    
    
    
    
    
     /**
    * Assigning permisions available 
    */
    public function user_services_permission()
    {
        return array(
            "administer user services"=>array(
                "title"=>"Administer user services",
                "description"=>"Can view, add, delete User services.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
}

