<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_suggestion extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_suggestion_model");
    }
    
    /**
    * View Listing
	* new category coulumn added on mar 2014
    */
	public function index()
	{
        user_access("administer user suggestion");//access check  
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>Suggestion<span></span></div>",
            "attributes"=>array("class"=>"sortCol")
          ),
		  array("title"=>"<div>Type<span></span></div>",
            "attributes"=>array("class"=>"sortCol")
          ),
		  /*
		  array("title"=>"<div>Category<span></span></div>",
            "attributes"=>array("class"=>"sortCol")
          ),
		  */
          array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),          
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_suggestion_model->pager["base_url"]=admin_base_url("user_suggestion/index");
        $this->user_suggestion_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_suggestion"]))
		   		$filter="s_suggestion LIKE '%".$this->data["posted"]["s_suggestion"]."%'";
			
			if(!empty($this->data["posted"]["e_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_type ='".$this->data["posted"]["e_type"]."'";
			
            
        }
        //pr($filter);
        //////end Filter/////
        $rec=$this->user_suggestion_model->user_suggestion_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                
                $action="";
                /**
                * category and location enum is not allowed in option table
                */
                if($r->e_type != 'category'
                    && $r->e_type != 'location'
                )
                    $action='<a id="approve_action" href="'.admin_base_url("user_suggestion/approve/".encrypt($r->id) ).'" >Approve</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("user_suggestion/delete/".encrypt($r->id) ).'" >Delete</a>';  
                
                $table["rows"][]=array(
                      $r->s_suggestion,
                      humanize($r->e_type),
					  /*get_category_name($r->cat_id),*/
                      $action
                );
                
            }
        }
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_suggestion_model->get_pager();        
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="User suggestion";
        /*$this->data["add_link"]= anchor('admin/user_suggestion/operation/add', 'Add user suggestion', 'title="Add user suggestion"');*/
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
    
    
    /**
    * Delete 
    * 
    * @param mixed $form_token
    */
    public function delete($form_token)
    {
        user_access("administer user suggestion");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_suggestion_model->delete_user_suggestion(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_suggestion"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_suggestion")); 
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function user_suggestion_permission()
    {
        return array(
            "administer user suggestion"=>array(
                "title"=>"Administer user suggestion",
                "title"=>"Administer user suggestion",
                "description"=>"Can view, add, edit, delete User suggestion.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
    public function approve($form_token)
    {
        $res=$this->user_suggestion_model->approve_user_suggestion(decrypt($form_token));
      	
        if($res)
        {
            set_success_msg(message_line("approve success"));
            redirect(admin_base_url("user_suggestion"));
        }
        else//error
        {
            set_error_msg(message_line("already exist"));
            redirect(admin_base_url("user_suggestion"));
        }         
    }
}

