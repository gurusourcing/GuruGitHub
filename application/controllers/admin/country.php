<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Country extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("country_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer country");//access check  
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>Country<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->country_model->pager["base_url"]=admin_base_url("country/index");
        $this->country_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="s_country LIKE '%".$this->input->post("s_country")."%'";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////
        
        $rec=$this->country_model->country_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("country/operation/edit/".encrypt($r->id) ).'">Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("country/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $table["rows"][]=array(
                      $r->s_country,
                      $action
                );                
            }
        }
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->country_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="Country";
        $this->data["add_link"]= anchor(admin_base_url('country/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
        $this->data["table"]=theme_table($table);
        //$this->data["pager"]=$this->country_model->get_pager();
        
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
        user_access("administer country");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->country_model->country_load(intval($id));        
        //pr($modify_data);
        $default_value[0]=json_encode(array(
                            "s_country"=>trim(@$modify_data->s_country),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Country ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer country");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_country"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_country"] = trim($this->input->post("s_country"));

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add
            if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_country', 'country', 'required|is_unique[country.s_country]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_country', 'country', 'required');
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
                    $ret=$this->country_model->update_country(array(
                                            "s_country"=>$posted["s_country"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->country_model->add_country(array(
                                            "s_country"=>$posted["s_country"],
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
        user_access("administer country");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->country_model->delete_country(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("country"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("country"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function country_permission()
    {
        return array(
            "administer country"=>array(
                "title"=>"Administer country",
                "description"=>"Can view, add, edit, delete country.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

