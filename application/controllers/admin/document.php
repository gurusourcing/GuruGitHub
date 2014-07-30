<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Document extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("doc_verification_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer document");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>Document<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		 array("title"=>"<div>Type<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->doc_verification_model->pager["base_url"]=admin_base_url("document/index");
        $this->doc_verification_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_document_required"]))
		   		$filter="s_document_required LIKE '%".$this->data["posted"]["s_document_required"]."%'";
			
			if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->doc_verification_model->doc_verification_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("document/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("document/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $table["rows"][]=array(
                      $r->s_document_required,
					  humanize($r->e_doc_type),
                      $action
                );                
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->doc_verification_model->get_pager();
        
        $this->data["page_title"]="Document";
        $this->data["add_link"]= anchor(admin_base_url('document/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
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
    public function operation($action="add",$form_token="")
    {
        user_access("administer document");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->doc_verification_model->doc_verification_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_document_required"   => trim(@$modify_data->s_document_required),
							"e_doc_type"            => trim(@$modify_data->e_doc_type),
                            "cat_id"                => intval(@$modify_data->cat_id),
                            "sub_cat_id"            => intval(@$modify_data->sub_cat_id),
                            "form_token"            => $form_token,
                            "action"                => $action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Document ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer document");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_document_required"]))
        {
            $posted["action"] 		        = trim($this->input->post("action")); 
            $posted["s_document_required"]  = trim($this->input->post("s_document_required"));
			$posted["e_doc_type"] 		    = trim($this->input->post("e_doc_type"));
            $posted["cat_id"]               = intval($this->input->post("cat_id"));
            $posted["sub_cat_id"]           = intval($this->input->post("sub_cat_id"));
            
            
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules 
			$this->form_validation->set_rules('s_document_required', 'document', 'required');
			$this->form_validation->set_rules('e_doc_type', 'type', 'required');
            
            if($posted["action"]=="edit") //rules for edit
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
                    $ret=$this->doc_verification_model->update_doc_verification(array(
                                            "s_document_required"	=>$posted["s_document_required"],
											"e_doc_type"		    =>$posted["e_doc_type"],
                                            "cat_id"                =>$posted['cat_id'],
                                            "sub_cat_id"            =>$posted['sub_cat_id'],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    
                    $ret=$this->doc_verification_model->add_doc_verification(array(
                                            "s_document_required"       =>$posted["s_document_required"],
											"e_doc_type"		        =>$posted["e_doc_type"],
                                            "cat_id"                   =>$posted['cat_id'],
                                            "sub_cat_id"                =>$posted['sub_cat_id'],
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
        user_access("administer document");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->doc_verification_model->delete_doc_verification(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("document"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("document"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function document_permission()
    {
        return array(
            "administer document"=>array(
                "title"=>"Administer document",
                "description"=>"Can view, add, edit, delete Document.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
    public function ajax_sub_cat_list()
    {
        $id=intval($this->input->get('cat_id'));
        $ret="";
        if($id)
        {
            $ret=dd_sub_category(array("cat_id"=>$id));
            unset($ret[""]);
        }

        echo json_encode($ret);
    }
}

