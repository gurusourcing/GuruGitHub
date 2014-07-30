<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_company extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_company_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user company");//access check  
        
        $table=array();
        
        $table["header"]=array(
         /*array("title"=>'<img src="'.base_url( get_theme_path().'images/elements/other/tableArrows.png' ).'" alt="" />'),   */
        
         array("title"=>"<div>User Name<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Company Name<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"<div>Logo<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Registered<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Email ID<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Contact No.<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"<div>Active<span></span></div>",
               "attributes"=>array("class"=>"sortCol")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_company_model->pager["base_url"]=admin_base_url("user_company/index");
        $this->user_company_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	
            if(!empty($this->data["posted"]["s_company"]))
		   		$filter="uc.s_company LIKE '%".$this->data["posted"]["s_company"]."%'";
			
            /*if(!empty($this->data["posted"]["s_package_name"]))
		   		$filter.=(!empty($filter)? " AND ":"")."s.s_service_name LIKE '%".$this->data["posted"]["s_service_name"]."%'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_company_model->user_company_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
       // pr($rec,1);
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                
                $logo='<a class="lightbox" title="company logo" href=""><img src="'.(!empty($r->s_logo)?site_url($r->s_logo) :'resources/no_image.jpg').'" height="33" width="33"></a><br/>';
               $active='<div id="active_action" class="floatL mr10 on_off">'.form_checkbox('i_active',$r->id,(bool)$r->i_active,'id=i_active').'</div>';    
                $con_no=$r->s_phone.'<br/>'.$r->s_mobile;
                $table["rows"][]=array(
                      /*'<input type="checkbox" value="'.encrypt($r->id).'" name="checkRow" />',  
                      '<input type="checkbox" value="1" name="checkRow" id="checkRow" />',*/
                      $r->s_user_name,
                      $r->s_company,
                      $logo,
					  $r->i_is_registered?'Registered':'Not Registered',
                      $r->s_email,
                      $con_no,
                      $active
                      //$action
                );   
                //pr($table);             
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_company_model->get_pager();
        
        $this->data["page_title"]="Manage User Company";
        $this->data["page_title"]="Manage User Company";
       /* $this->data["add_link"]= anchor('admin/featured_services/operation/add', '<span class="icos-add"></span>', 'title="Add User" class="tipS"');*/
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
   /* public function operation($action="add",$form_token="")
    {
        user_access("administer featured services");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->featured_services_model->featured_services_load(intval($id));        
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
        
        $this->data["page_title"]="Featured services ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }*/
    
    
    /**
    * Ajax add edit post
    */
    /*public function ajax_operation()
    {
        user_access("administer featured services");//access check
        
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
                    $ret=$this->featured_services_model->update_featured_services(array(
                                            "s_package_name"        =>$posted["s_package_name"],
                                            "s_desc"                =>$posted["s_desc"],
                                            "i_months_validity"     =>$posted['i_months_validity'],
                                            "i_price"               =>$posted['i_price'],
                                            "i_active"              =>$posted['i_active'],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    
                    $ret=$this->featured_services_model->add_featured_services(array(
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
    }    */
    
    
    /**
    * Delete 
    * 
    * @param mixed $form_token
    */
   /* public function delete($form_token)
    {
        user_access("administer featured services");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->featured_services_model->delete_featured_services(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect("admin/featured_services");                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect("admin/featured_services");
        }         
    }*/
    
    /**
    * changing the value of i_active
    */
   public function ajax_changeStatus()
    {
        $id=$_POST['id'][0];
        
        $ret=$this->user_company_model->update_user_company_status($id);
        
        // fetching company current status to change its services//
        $curr_status = $this->user_company_model->fetch_company_status(intval($id));
        //pr($curr_status); exit;
        if(intval($curr_status->i_active)) // if current status is active the activate all company services
        {
           $default_service = array("i_is_company_service"=>1); 
           $other_service = array("i_active"=>1);
        }
        else     // if company status is inactive then 
                //  make only default service ('i_is_company_default') active 
                //  and change it to individual service ('i_is_company_service=0') , 
                //  and make all other company services inactive
        {
            $default_service = array("i_is_company_service"=>0); 
            $other_service = array("i_active"=>0);
        }
        
        $this->load->model("user_service_model");
        
        // updating the company default service///
        $this->user_service_model->update_user_service($default_service,array("i_is_company_default"=>1 ,"comp_id"=>intval($id)));
        /// updating company other services ///
        $this->user_service_model->update_user_service($other_service,array("i_is_company_default"=>0 ,"comp_id"=>intval($id)));
        if($ret)
            set_success_msg(message_line("status update success"));
        else
            set_error_msg(message_line("status update error"));
    }
    /**
    * Assigning permisions available 
    */
    public function user_company_permission()
    {
        return array(
            "administer user company"=>array(
                "title"=>"Administer user company",
                "description"=>"Can view user company.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
   
}

