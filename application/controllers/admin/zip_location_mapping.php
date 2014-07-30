<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Zip_location_mapping
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Zip_location_mapping extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("zip_location_mapping_model");
    }
    
    /**
    * View Listing
    */
    public function index()
    {
    user_access("administer zip location mapping");//access check  

    $table=array();
    $table["header"]=array(
        array("title"=>"<div>Popular Location<span></span></div>",
            "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>Zip<span></span></div>",
            "attributes"=>array("class"=>"sortCol")), 
        array(
            "title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
    );
    $table["no result text"]="No information found.";

    ////Auto Pagination
    $this->zip_location_mapping_model->pager["base_url"]=admin_base_url("zip_location_mapping/index");
    $this->zip_location_mapping_model->pager["uri_segment"]=4;

    //////Filter/////
    $filter='';

    if($this->input->post("submit"))
    {
        $filter="z.s_zip LIKE '%".$this->input->post("s_zip_location_mapping")."%' OR pl.s_location LIKE '%".$this->input->post("s_zip_location_mapping")."%'";
        $this->data["posted"]=$this->input->post();

    }
    //////end Filter/////

    $rec=$this->zip_location_mapping_model->zip_location_mapping_load(
                              $filter,
                              $this->noRecAdmin,
                              $this->uri->segment(4,0)
                            );
    
    if(!empty($rec))
    {
        foreach($rec as $r)
        {
            $action="";
            $action='<a id="edit_action" href="'.admin_base_url("zip_location_mapping/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
            $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("zip_location_mapping/delete/".encrypt($r->id) ).'" >Delete</a>';  

            $table["rows"][]=array(
                  $r->s_location,
                  $r->s_zip,
                  $action
            );                
        }
    }

    /*pr($this->uri->segment_array());
    pr($this->admin_type_model->get_pager());*/

    /**
    * Pager goes to the footer of the table
    */
    $table["footer"]=$this->zip_location_mapping_model->get_pager();
    
    $this->data["page_title"]="Zip Location Mapping";
    $this->data["add_link"]= anchor(admin_base_url('zip_location_mapping/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
    $this->data["table"]=theme_table($table);
    //$this->data["pager"]=$this->zip_location_mapping_model->get_pager();
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
        user_access("administer zip_location_mapping");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->zip_location_mapping_model->zip_location_mapping_load(intval($id)); 
       // pr($modify_data);
        $default_value[0]=json_encode(array(
                            "popular_location_id"=>trim(@$modify_data->popular_location_id),
                            "zip_id"=>trim(@$modify_data->zip_id),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Zip Location Mapping ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer zip_location_mapping");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["popular_location_id"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["zip_id"] = trim($this->input->post("zip_id"));
            $posted["popular_location_id"] = trim($this->input->post("popular_location_id"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add 
            
            $this->form_validation->set_rules('zip_id', 'zip', 'required');
            $this->form_validation->set_rules('popular_location_id', 'location', 'required');

            
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
                    $ret=$this->zip_location_mapping_model->update_zip_location_mapping(array(
                                            "popular_location_id"=>$posted["popular_location_id"],
                                            "zip_id"=>$posted["zip_id"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->zip_location_mapping_model->add_zip_location_mapping(array(
                                           "popular_location_id"=>$posted["popular_location_id"],
                                            "zip_id"=>$posted["zip_id"]
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
        user_access("administer zip_location_mapping");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->zip_location_mapping_model->delete_zip_location_mapping(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("zip_location_mapping"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("zip_location_mapping"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function zip_location_mapping_permission()
    {
        return array(
            "administer zip location mapping"=>array(
                "title"=>"Administer zip location mapping",
                "description"=>"Can view, add, edit, delete zip location mapping.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

