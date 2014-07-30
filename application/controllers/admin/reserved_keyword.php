<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Reserved_keyword
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Reserved_keyword extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("reserved_keyword_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer reserved keyword");//access check  
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>Keyword <span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->reserved_keyword_model->pager["base_url"]=admin_base_url("reserved_keyword/index");
        $this->reserved_keyword_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="s_keyword LIKE '%".$this->input->post("s_keyword")."%'";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////
        
        $rec=$this->reserved_keyword_model->reserved_keyword_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("reserved_keyword/operation/edit/".encrypt($r->id) ).'">Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("reserved_keyword/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $table["rows"][]=array(
                      $r->s_keyword,
                      $action
                );                
            }
        }
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->reserved_keyword_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="Reserved Keyword ";
        $this->data["add_link"]= anchor(admin_base_url('reserved_keyword/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
        $this->data["table"]=theme_table($table);
        //$this->data["pager"]=$this->reserved_keyword_model->get_pager();
        
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
        user_access("administer reserved_keyword");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->reserved_keyword_model->reserved_keyword_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_keyword"=>trim(@$modify_data->s_keyword),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Reserved Keyword ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer reserved_keyword");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_keyword"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_keyword"] = trim($this->input->post("s_keyword"));

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add
            if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_keyword', 'reserved_keyword', 'required|is_unique[reserved_keyword.s_keyword]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_keyword', 'reserved_keyword', 'required');
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
                    $ret=$this->reserved_keyword_model->update_reserved_keyword(array(
                                            "s_keyword"=>$posted["s_keyword"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->reserved_keyword_model->add_reserved_keyword(array(
                                            "s_keyword"=>$posted["s_keyword"],
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
        user_access("administer reserved_keyword");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->reserved_keyword_model->delete_reserved_keyword(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("reserved_keyword"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("reserved_keyword"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function reserved_keyword_permission()
    {
        return array(
            "administer reserve keyword"=>array(
                "title"=>"Administer Reserved Keyword",
                "description"=>"Can view, add, edit, delete Reserved Keyword.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

