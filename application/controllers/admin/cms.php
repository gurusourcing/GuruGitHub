<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Cms
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Cms extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("cms_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer cms");//access check  
        
        $table=array();
        $table["header"]=array(
          array("title"=>"<div>Menu<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Content<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          /*array("title"=>"<div>Url<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),*/
          array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->cms_model->pager["base_url"]=admin_base_url("cms/index");
        $this->cms_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="s_menu LIKE '%".$this->input->post("s_cms")."%' 
                        OR s_content LIKE '%".$this->input->post("s_cms")."%' 
                        OR s_url LIKE '%".$this->input->post("s_cms")."%'";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////
        
        $rec=$this->cms_model->cms_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("cms/operation/edit/".encrypt($r->id) ).'">Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("cms/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $table["rows"][]=array(
                    $r->s_menu,
                    word_limiter(strip_tags(format_text($r->s_content)),20),
                    /*$r->s_url,*/
                      $action
                );                
            }
        }
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->cms_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="Cms";
        $this->data["add_link"]= anchor(admin_base_url('cms/operation/add'), '<span class="icos-add"></span>', 'title="Add Cms" class="tipS"');
        $this->data["table"]=theme_table($table);
        //$this->data["pager"]=$this->cms_model->get_pager();
        
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
        user_access("administer cms");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->cms_model->cms_load(intval($id));        
        
        /*$default_value[0]=json_encode(array(
                            "s_menu"=>trim(@$modify_data->s_menu),
                            "s_content"=>format_text(@$modify_data->s_content,"decode"),
                            //"s_url"=>trim(@$modify_data->s_url),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            )); */
                            
        $default_value = array(
                            "s_menu"=>trim(@$modify_data->s_menu),
                            "s_content"=>format_text(@$modify_data->s_content,"decode"),
                            //"s_url"=>trim(@$modify_data->s_url),
                            "form_token"=>$form_token,
                            "action"=>$action,);
        $this->data["default_value"]= $default_value;
        //pr($default_value);
        $this->data["page_title"]="Cms ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer cms");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_menu"]))
        {
         //  pr($_POST,1);
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_menu"] = trim($this->input->post("s_menu"));
            $posted["s_content"] = trim($this->input->post("s_content"));
            /*$posted["s_url"] = trim($this->input->post("s_url"));*/
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            

            $this->form_validation->set_rules('s_menu', 'Menu', 'required');
            $this->form_validation->set_rules('s_content', 'Content', 'required');
            /*$this->form_validation->set_rules('s_url', 'Url', 'required');*/
            if($posted["action"]=="edit") //rules for edit
            {
                
                $this->form_validation->set_rules('form_token', 'form token', 'required');
            }
                
            if($this->form_validation->run() == FALSE)/////invalid
            {
                /*$ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;*/

                set_error_msg(validation_errors());
                redirect(admin_base_url("cms/operation/".$posted["action"]."/".trim($this->input->post("form_token"))));
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                if($posted["action"]=="edit")
                {
                    $ret=$this->cms_model->update_cms(array(
                                            "s_menu"=>$posted["s_menu"],
                                            "s_content"=>format_text($posted["s_content"],'encode'),
                                            /*"s_content"=>trim($posted["s_content"]),*/
                                            /*"s_url"=>$posted["s_url"],*/
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->cms_model->add_cms(array(
                                            "s_menu"=>$posted["s_menu"],
                                            "s_content"=>format_text($posted["s_content"],'encode'),
                                            /*"s_url"=>$posted["s_url"],*/
                                        ));
                }
 
                  
                if($ret)//success
                {
                    /*
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    if($posted["action"]=="add")
                        $ajx_ret["form_token"]=encrypt($ret); 
                          
                    echo json_encode($ajx_ret);
                    return TRUE;  */
                    set_success_msg(message_line("saved success"));
                    redirect(admin_base_url("cms"));                                         
                }
                else//error
                {
                    /*$ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;  */
                    set_error_msg(message_line("saved error"));
                    redirect(admin_base_url("cms"));                  
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
        user_access("administer cms");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->cms_model->delete_cms(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("cms"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("cms"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function cms_permission()
    {
        return array(
            "administer cms"=>array(
                "title"=>"Administer cms",
                "description"=>"Can view, add, edit, delete cms.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

