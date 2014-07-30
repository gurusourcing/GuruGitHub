<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Payment_history extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("payment_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("view payment history");//access check  
        
        $table=array();
        
        $table["header"]=array(
         /*array("title"=>'<img src="'.base_url( get_theme_path().'images/elements/other/tableArrows.png' ).'" alt="" />'),   */
        
         array("title"=>"<div>User Name<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Category<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"<div>Payment For<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Payment Mode<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"Status",
            "attributes"=>array("width"=>"100")
          ),
         
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->payment_model->pager["base_url"]=admin_base_url("payment_history/index");
        $this->payment_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	
            if(!empty($this->data["posted"]["s_user_name"]))
		   		$filter="u.s_user_name LIKE '%".$this->data["posted"]["s_user_name"]."%'";
			
            /*if(!empty($this->data["posted"]["s_package_name"]))
		   		$filter.=(!empty($filter)? " AND ":"")."s.s_service_name LIKE '%".$this->data["posted"]["s_service_name"]."%'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->payment_model->payment_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
       // pr($rec,1);
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
               $status="";
               $status= $r->e_status!="completed"?form_dropdown($r->id,dd_status(),$r->e_status,'id="e_status" class="e_status"'):'<img src="'.site_url(get_theme_path()."images/icons/usual/icon-check.png").'" title="Completed" class="tipS">';
               
               $table["rows"][]=array(
                      /*'<input type="checkbox" value="'.encrypt($r->id).'" name="checkRow" />',  
                      '<input type="checkbox" value="1" name="checkRow" id="checkRow" />',*/
                      $r->s_user_name,
                      $r->e_type,
                      auto_link($r->s_paid_for),
					  $r->s_payment_mode,
                      $status
                      //$action
                );   
                //pr($table);             
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->payment_model->get_pager();
        
        $this->data["page_title"]="Manage Payment History";
        $this->data["page_title"]="Manage Payment History";
       /* $this->data["add_link"]= anchor(admin_base_url('featured_services/operation/add'), '<span class="icos-add"></span>', 'title="Add User" class="tipS"');*/
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
            redirect(admin_base_url("featured_services"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("featured_services"));
        }         
    }*/
    
    /**
    * changin the value of i_active
    */
   public function ajax_changeStatus()
    {
       $id=$this->input->post('id');
       $val=$this->input->post('val');
       
       $ret=$this->payment_model->update_payment(array("e_status"=>trim($val)),
                                array("id"=>intval($id))
                                );
       if($ret)
        set_success_msg(message_line("status update success"));
       else
         set_error_msg(message_line("status update error"));
    }
    /**
    * Assigning permisions available 
    */
    public function payment_permission()
    {
        return array(
            "view payment history"=>array(
                "title"=>"view payment history",
                "description"=>"Can view user payment history.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
   
}

