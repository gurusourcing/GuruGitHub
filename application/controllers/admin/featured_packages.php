<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Featured_packages extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_feature_package_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer featured packages");//access check  
        
        $table=array();
        
        $table["header"]=array(
         /*array("title"=>'<img src="'.base_url( get_theme_path().'images/elements/other/tableArrows.png' ).'" alt="" />'),   */
        
         array("title"=>"<div>Package Name<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"<div>Price<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Validity<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"Actions",
            "attributes"=>array("width"=>"150")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_feature_package_model->pager["base_url"]=admin_base_url("featured_packages/index");
        $this->user_feature_package_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_package_name"]))
		   		$filter="s_package_name LIKE '%".$this->data["posted"]["s_package_name"]."%'";
			
			/*if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_feature_package_model->user_feature_package_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
       // pr($rec,1);
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                
                $action.='<a id="edit_action" href="'.admin_base_url("featured_packages/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                $action.='<a id="delete_action" href="'.admin_base_url("featured_packages/delete/".encrypt($r->id) ).'" >Delete</a>';  
                $action.='<div id="active_action" class="floatR mr10 on_off">'.form_checkbox('i_active',$r->id,(bool)$r->i_active,'id=i_active').'</div>';
                    
                $table["rows"][]=array(
                      /*'<input type="checkbox" value="'.encrypt($r->id).'" name="checkRow" />',  
                      '<input type="checkbox" value="1" name="checkRow" id="checkRow" />',*/
                      $r->s_package_name,
					  $r->i_price,
                      $r->i_months_validity,
                      $action
                );   
                //pr($table);             
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_feature_package_model->get_pager();
        
        $this->data["page_title"]="Manage Feature Package";
        $this->data["page_title"]="Manage Feature Package";
        $this->data["add_link"]= anchor(admin_base_url('featured_packages/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
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
        user_access("administer featured packages");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->user_feature_package_model->user_feature_package_load(intval($id));        
        //pr($modify_data);
        $default_value[0]=json_encode(array(
                            
                            "s_package_name"    => trim(@$modify_data->s_package_name),
							"s_desc"            => trim(@$modify_data->s_desc),
                            "i_months_validity" => trim(@$modify_data->i_months_validity),
                            "i_price"           => trim(@$modify_data->i_price),
                            "i_active"          => intval(@$modify_data->i_active),
                            "action"            => $action,
                            "form_token"        =>$form_token
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Featured Packages ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer featured packages");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST))
        {
            
            $posted["form_token"]           = decrypt($this->input->post("form_token")); 
            $posted["action"] 		        = trim($this->input->post("action")); 
            $posted["s_package_name"]       = trim($this->input->post("s_package_name"));
			$posted["s_desc"] 		        = trim($this->input->post("s_desc"));
            $posted["i_months_validity"]    = intval($this->input->post("i_months_validity"));
            $posted["i_price"]              = intval($this->input->post("i_price"));
            $posted["i_active"]             = intval($this->input->post("i_active"));
            
            ////rules 
			$this->form_validation->set_rules('s_package_name', 'package name', 'required');
            $this->form_validation->set_rules('i_months_validity', 'validity', 'integer');
            $this->form_validation->set_rules('i_price', 'price', 'integer');
			
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
                    $ret=$this->user_feature_package_model->update_user_feature_package(array(
                                            "s_package_name"        =>$posted["s_package_name"],
                                            "s_desc"                =>$posted["s_desc"],
                                            "i_months_validity"     =>$posted['i_months_validity'],
                                            "i_price"               =>$posted['i_price'],
                                            "i_active"              =>$posted['i_active'],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    
                    $ret=$this->user_feature_package_model->add_user_feature_package(array(
                                            "s_package_name"        =>$posted["s_package_name"],
											"s_desc"	            =>$posted["s_desc"],
                                            "i_months_validity"     =>$posted['i_months_validity'],
                                            "i_price"               =>$posted['i_price'],
                                            "i_active"              =>$posted['i_active'],
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
        user_access("administer featured packages");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_feature_package_model->delete_user_feature_package(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("featured_packages"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("featured_packages"));
        }         
    }
    
    /**
    * changin the value of i_active
    */
    public function ajax_changeStatus()
    {
        $id=$_POST['id'][0];
        $ret=$this->user_feature_package_model->update_user_feature_package_status($id);
        
        if($ret)
            set_success_msg(message_line("status update success"));
        else
            set_error_msg(message_line("status update error"));
    }
    /**
    * Assigning permisions available 
    */
    public function featured_packages_permission()
    {
        return array(
            "administer featured packages"=>array(
                "title"=>"Administer featured packages",
                "description"=>"Can view, add, delete featured packages.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
   
}

