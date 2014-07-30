<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Zip
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Zip extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("zip_model");
    }
    
    /**
    * View Listing
    */
    public function index()
    {
    user_access("administer zip");//access check  

    $table=array();
    $table["header"]=array(
        array("title"=>"<div>Zip<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>City<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>State<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>Country<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>Latitude<span></span></div>",
                "attributes"=>array("class"=>"sortCol")),
        array("title"=>"<div>Longitude<span></span></div>",
                "attributes"=>array("class"=>"sortCol")), 
        array("title"=>"Actions","attributes"=>array("width"=>"100")
          ),
    );
    $table["no result text"]="No information found.";

    ////Auto Pagination
    $this->zip_model->pager["base_url"]=admin_base_url("zip/index");
    $this->zip_model->pager["uri_segment"]=4;

    //////Filter/////
    $filter='';

    if($this->input->post("submit"))
    {
        $filter="s_zip LIKE '%".$this->input->post("s_zip")."%'";
        $this->data["posted"]=$this->input->post();

    }
    //////end Filter/////

    $rec=$this->zip_model->zip_load(
                              $filter,
                              $this->noRecAdmin,
                              $this->uri->segment(4,0)
                            );
    if(!empty($rec))
    {
        foreach($rec as $r)
        {
            $action="";
            $action='<a id="edit_action" href="'.admin_base_url("zip/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
            $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("zip/delete/".encrypt($r->id) ).'" >Delete</a>';  

            $table["rows"][]=array(
                  $r->s_zip,
                  $r->s_city,
                  $r->s_state,
                  $r->s_country,
                  $r->s_latitude,
                  $r->s_longitude,
                  $action
            );                
        }
    }
    
    /**
    * Pager goes to the footer of the table
    */
    $table["footer"]=$this->zip_model->get_pager();
        
    /*pr($this->uri->segment_array());
    pr($this->admin_type_model->get_pager());*/
    
    $this->data["page_title"]="Zip";
    $this->data["add_link"]= anchor(admin_base_url('zip/operation/add'), '<span class="icos-add"></span>', 'title="Add Zip" class="tipS"');
    $this->data["table"]=theme_table($table);
    //$this->data["pager"]=$this->zip_model->get_pager();

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
        user_access("administer zip");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->zip_model->zip_load(intval($id)); 
       // pr($modify_data);
        $default_value[0]=json_encode(array(
                            "s_zip"=>trim(@$modify_data->s_zip),
                            "state_id"=>trim(@$modify_data->state_id),
                            "city_id"=>trim(@$modify_data->city_id),
                            "country_id"=>trim(@$modify_data->country_id),
                            "s_longitude"=>trim(@$modify_data->s_longitude),
                            "s_latitude"=>trim(@$modify_data->s_latitude),
                            "state_id"=>trim(@$modify_data->state_id),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Zip ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer zip");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_zip"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_zip"] = trim($this->input->post("s_zip"));
            $posted["city_id"] = trim($this->input->post("city_id"));
            $posted["state_id"] = trim($this->input->post("state_id"));
            $posted["s_latitude"] = trim($this->input->post("s_latitude"));
            $posted["s_longitude"] = trim($this->input->post("s_longitude"));

            $posted["country_id"] = trim($this->input->post("country_id"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add 
            
            $this->form_validation->set_rules('s_zip', 'zip', 'required');
            $this->form_validation->set_rules('state_id', 'state', 'required');
            $this->form_validation->set_rules('country_id', 'country', 'required');
            $this->form_validation->set_rules('city_id', 'city', 'required');
                        
            $this->form_validation->set_rules('s_latitude', 'Latitude', 'decimal');
            $this->form_validation->set_rules('s_longitude', 'Longitude', 'decimal');
            
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
                    $ret=$this->zip_model->update_zip(array(
                                            "s_zip"=>$posted["s_zip"],
                                            "s_latitude"=>$posted["s_latitude"],
                                            "s_longitude"=>$posted["s_longitude"],
                                            "state_id"=>$posted["state_id"],
                                            "city_id"=>$posted["city_id"],
                                            "country_id"=>$posted["country_id"]
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->zip_model->add_zip(array(
                                            "s_zip"=>$posted["s_zip"],
                                            "s_latitude"=>$posted["s_latitude"],
                                            "s_longitude"=>$posted["s_longitude"],
                                            "state_id"=>$posted["state_id"],
                                            "city_id"=>$posted["city_id"],
                                            "country_id"=>$posted["country_id"]
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
        user_access("administer zip");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->zip_model->delete_zip(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("zip"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("zip"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function zip_permission()
    {
        return array(
            "administer zip"=>array(
                "title"=>"Administer zip",
                "description"=>"Can view, add, edit, delete zip.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

