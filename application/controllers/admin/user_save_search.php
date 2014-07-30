<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin User_save_search
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_save_search extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_save_search_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user save search");//access check  
        
        $table=array();
        $table["header"]=array(
         
          array("title"=>"<div>User<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Search Tag<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ), 
//          array("title"=>"<div>Search field value<span></span></div>",
//                "attributes"=>array("class"=>"sortCol")
//          ), 
            
           array("title"=>"<div>Search Url<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),  
            
          array("title"=>"<div>Locked<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),   
            
            array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_save_search_model->pager["base_url"]=admin_base_url("user_save_search/index");
        $this->user_save_search_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="u.s_user_name 
                        LIKE '%".$this->input->post("s_user_save_search")."%' 
                    OR uss.s_search_tag 
                        LIKE '%".$this->input->post("s_user_save_search")."%' 
                    OR uss.s_url 
                        LIKE '%".$this->input->post("s_user_save_search")."%' 
                   ";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////
        
        $rec=$this->user_save_search_model->user_save_search_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("user_save_search/operation/edit/".encrypt($r->id) ).'">Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("user_save_search/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $table["rows"][]=array(
                    $r->s_user_name,
                    $r->s_search_tag,
//                    $r->s_search_field_value,
                    $r->s_url,
                    $r->i_lock?'Yes':'No',
                      $action
                );                
            }
        }
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_save_search_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="User save search";
        $this->data["add_link"]= anchor(admin_base_url('user_save_search/operation/add'), '<span class="icos-add"></span>', 'title="Add User save search" class="tipS"');
        $this->data["table"]=theme_table($table);
        //$this->data["pager"]=$this->user_save_search_model->get_pager();
        
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
        user_access("administer user save search");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->user_save_search_model->user_save_search_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                    "s_user_name"=>trim(@$modify_data->s_user_name),
                    /*"s_user_save_search"=>trim(@$modify_data->s_user_save_search),*/
                    "s_search_tag"=>trim(@$modify_data->s_search_tag),
                    /*"s_search_field_value"=>trim(@$modify_data->s_search_field_value),*/
                    "s_url"=>trim(@$modify_data->s_url),
                    "i_lock"=>trim(@$modify_data->i_lock),
                    "form_token"=>$form_token,
                    "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        $this->data["page_title"]="User save search ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer user save search");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();

        if(isset($_POST["form_token"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            //$posted["uid"] = trim($this->input->post("uid"));
            $posted["s_search_tag"] = trim($this->input->post("s_search_tag"));
            //$posted["s_search_field_value"] = trim($this->input->post("s_search_field_value"));
            $posted["s_url"] = trim($this->input->post("s_url"));
            $posted["i_lock"] = intval(trim($this->input->post("i_lock")))?intval(trim($this->input->post("i_lock"))):0;
            //encrypted id(PK) is the form_token 
            
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            ////rules 
            
            //$this->form_validation->set_rules('uid', 'user_save_search', 'required');
            $this->form_validation->set_rules('s_search_tag', 's_search_tag', 'required');
            //$this->form_validation->set_rules('s_search_field_value', 's_search_field_value', 'required');
            $this->form_validation->set_rules('s_url', 's_url', 'required');
            $this->form_validation->set_rules('form_token', 'form token', 'required');

                
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
                    $ret=$this->user_save_search_model->update_user_save_search(array(
                    /*"uid"=>$posted["uid"],*/
                    "s_search_tag"=>$posted["s_search_tag"],
                    /*"s_search_field_value"=>$posted["s_search_field_value"],*/
                    "s_url"=>$posted["s_url"],
                    "i_lock"=>$posted["i_lock"],
                                        ),array("id"=>$posted["form_token"])); 

                }
                /*elseif($posted["action"]=="add")
                {
                    $ret=$this->user_save_search_model->add_user_save_search(array(
                    "uid"=>$posted["uid"],
                    "s_search_tag"=>$posted["s_search_tag"],
                    "s_search_field_value"=>$posted["s_search_field_value"],
                    "s_url"=>$posted["s_url"],
                    "i_lock"=>$posted["i_lock"],                                        ));
                }*/
 
                  
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
        user_access("administer user save search");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_save_search_model->delete_user_save_search(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_save_search"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_save_search"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function user_save_search_permission()
    {
        return array(
            "administer user save search"=>array(
                "title"=>"Administer user save search",
                "description"=>"Can view, add, edit, delete user_save_search.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

