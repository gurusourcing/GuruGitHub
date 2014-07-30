<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin State
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class State extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        //pr(sendBulkMail('eve.aldiniz@gmail.com','kallol.b@acumensoft.com','this is a test'));
        $this->load->model("state_model");
    }
    
    /**
    * View Listing
    */
    public function index()
    {
    user_access("administer state");//access check  

    $table=array();
    $table["header"]=array(
      array("title"=>"<div>State<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
      array("title"=>"<div>Country<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
      array("title"=>"Actions","attributes"=>array("width"=>"100")
          ),
    );
    $table["no result text"]="No information found.";

    ////Auto Pagination
    $this->state_model->pager["base_url"]=admin_base_url("state/index");
    $this->state_model->pager["uri_segment"]=4;

    //////Filter/////
    $filter='';

    if($this->input->post("submit"))
    {
        $filter="s_state LIKE '%".$this->input->post("s_state")."%'";
        $this->data["posted"]=$this->input->post();

    }
    //////end Filter/////

    $rec=$this->state_model->state_load(
                              $filter,
                              $this->noRecAdmin,
                              $this->uri->segment(4,0)
                            );
    if(!empty($rec))
    {
        foreach($rec as $r)
        {
            $action="";
            $action='<a id="edit_action" href="'.admin_base_url("state/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
            $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("state/delete/".encrypt($r->id) ).'" >Delete</a>';  

            $table["rows"][]=array(
                  $r->s_state,
                  $r->s_country,
                  $action
            );                
        }
    }
    /**
    * Pager goes to the footer of the table
    */
    $table["footer"]=$this->state_model->get_pager();
    /*pr($this->uri->segment_array());
    pr($this->admin_type_model->get_pager());*/

    $this->data["page_title"]="State";
    $this->data["add_link"]= anchor(admin_base_url('state/operation/add'), '<span class="icos-add"></span>', 'title="Add State" class="tipS"');
    $this->data["table"]=theme_table($table);
    //$this->data["pager"]=$this->state_model->get_pager();

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
        user_access("administer state");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->state_model->state_load(intval($id)); 
       // pr($modify_data);
        $default_value[0]=json_encode(array(
                            "s_state"=>trim(@$modify_data->s_state),
                            "country_id"=>trim(@$modify_data->country_id),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="State ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer state");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_state"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_state"] = trim($this->input->post("s_state"));
            $posted["country_id"] = trim($this->input->post("country_id"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add 
            
            $this->form_validation->set_rules('s_state', 'state', 'required');
            $this->form_validation->set_rules('country_id', 'country', 'required');
            
            if($posted["action"]=="edit") //rules for edit
            {
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
                    $ret=$this->state_model->update_state(array(
                                            "s_state"=>$posted["s_state"],
                                            "country_id"=>$posted["country_id"]
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->state_model->add_state(array(
                                            "s_state"=>$posted["s_state"],
                                            "country_id"=>$posted["country_id"],
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
        user_access("administer state");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->state_model->delete_state(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("state"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("state"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function state_permission()
    {
        return array(
            "administer state"=>array(
                "title"=>"Administer state",
                "description"=>"Can view, add, edit, delete state.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

