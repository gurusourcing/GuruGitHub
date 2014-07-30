<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Popular_location
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Popular_location extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("popular_location_model");
    }
    
    /**
    * View Listing
    */
    public function index()
    {
    user_access("administer popular location");//access check  

    $table=array();
    $table["header"]=array(
        array(
            "title"=>"<div>Popular Location<span></span></div>",
            "attributes"=>array("class"=>"sortCol")
            ),
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
        array(
            "title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
    );
    $table["no result text"]="No information found.";

    ////Auto Pagination
    $this->popular_location_model->pager["base_url"]=admin_base_url("popular_location/index");
    $this->popular_location_model->pager["uri_segment"]=4;

    //////Filter/////
    $filter='';

    if($this->input->post("submit"))
    {
        $filter="s_location LIKE '%".$this->input->post("s_location")."%'";
        $this->data["posted"]=$this->input->post();

    }
    //////end Filter/////

    $rec=$this->popular_location_model->popular_location_load(
                              $filter,
                              $this->noRecAdmin,
                              $this->uri->segment(4,0)
                            );
    if(!empty($rec))
    {
        foreach($rec as $r)
        {
            $action="";
            $action='<a id="edit_action" href="'.admin_base_url("popular_location/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
            $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("popular_location/delete/".encrypt($r->id) ).'" >Delete</a>';  

            $table["rows"][]=array(
                  $r->s_location,
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
    $table["footer"]=$this->popular_location_model->get_pager();
    /*pr($this->uri->segment_array());
    pr($this->admin_type_model->get_pager());*/

    $this->data["page_title"]="Popular Location";
    $this->data["add_link"]= anchor(admin_base_url('popular_location/operation/add'),'<span class="icos-add"></span>', 'title="Add" class="tipS"');
    $this->data["table"]=theme_table($table);
    //$this->data["pager"]=$this->popular_location_model->get_pager();

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
        user_access("administer popular location");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->popular_location_model->popular_location_load(intval($id)); 
       //pr($modify_data);
        $default_value[0]=json_encode(array(
                            "s_location"=>trim(@$modify_data->s_location),
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
        
        $this->data["page_title"]="Popular Location ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer popular location");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_location"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_location"] = trim($this->input->post("s_location"));
            $posted["city_id"] = trim($this->input->post("city_id"));
            $posted["state_id"] = trim($this->input->post("state_id"));
            $posted["s_latitude"] = trim($this->input->post("s_latitude"));
            $posted["s_longitude"] = trim($this->input->post("s_longitude"));
            $posted["country_id"] = trim($this->input->post("country_id"));
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add 
            
            $this->form_validation->set_rules('s_location', 'popular location', 'required');
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
                    $ret=$this->popular_location_model->update_popular_location(array(
                                            "s_location"=>$posted["s_location"],
                                            "s_latitude"=>$posted["s_latitude"],
                                            "s_longitude"=>$posted["s_longitude"],
                                            "state_id"=>$posted["state_id"],
                                            "city_id"=>$posted["city_id"],
                                            "country_id"=>$posted["country_id"]
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->popular_location_model->add_popular_location(array(
                                            "s_location"=>$posted["s_location"],
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
        user_access("administer popular location");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->popular_location_model->delete_popular_location(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("popular_location"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("popular_location"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function popular_location_permission()
    {
        return array(
            "administer popular location"=>array(
                "title"=>"Administer Popular Location",
                "description"=>"Can view, add, edit, delete popular location.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

