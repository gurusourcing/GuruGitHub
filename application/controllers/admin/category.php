<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Category
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Category extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("category_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer category");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>Category<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
          array("title"=>"<div>Alias<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
         
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->category_model->pager["base_url"]=admin_base_url("category/index");
        $this->category_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter=array();
        if($this->input->post("submit"))
        {
            $filter="s_category LIKE '%".$this->input->post("s_category")."%'";
            $this->data["posted"]=$this->input->post();
        }
        //////end Filter/////
        
        $rec=$this->category_model->category_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action='<a id="edit_action" href="'.admin_base_url("category/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("category/delete/".encrypt($r->id) ).'" >Delete</a>';  
                    
                $alias=dbHashSeperateDec($r->s_alias_names);
                $s_alias_names="";
                if(!empty($alias))
                {
                    foreach($alias as $a)
                    {
                        $li=explode("|",$a);
                        $s_alias_names.=$li[0]."(".get_countryName($li[1]).')<br/>';
                    }
                }
                
                
                $table["rows"][]=array(
                      $r->s_category,
                      $s_alias_names,
                      $action
                );                
            }
        }
        $table["footer"]=$this->category_model->get_pager();
        
        /*pr($this->uri->segment_array());
        pr($this->admin_type_model->get_pager());*/
        
        $this->data["page_title"]="Category";
        $this->data["add_link"]= anchor(admin_base_url('category/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
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
        user_access("administer category");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->category_model->category_load(intval($id));        
        ///default values for add more//
        $add_more_alias=array();
        //$temp=explode("###",trim(@$modify_data->s_alias_names));
        $temp=dbHashSeperateDec(trim(@$modify_data->s_alias_names));
        if(!empty($temp))
        {
            foreach($temp as $k=>$alias)
            {
                if(!empty($alias))
                {
                    $li=explode("|",$alias);
                    $add_more_alias[]=array(
                        "s_alias_name"=>trim($li[0]),
                        "s_alias_country"=>intval($li[1]),
                        );
                }
            }
        }
        ///end default values for add more//
        
        
        $default_value[0]=json_encode(array(
                            "s_category"=>trim(@$modify_data->s_category),
                            "s_desc"=>trim(@$modify_data->s_desc),
                            "add_more_alias"=>$add_more_alias,
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Category ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer category");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        //pr($_POST,1);
        if(isset($_POST["s_category"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_category"] = trim($this->input->post("s_category"));
            $posted["s_desc"]     = trim($this->input->post("s_desc"));

            ///add more, s_alias_names//
            $add_more_alias=$this->input->post("add_more_alias");
            $posted["s_alias_names"] = "";
            $temp=array();
            if(!empty($add_more_alias))
            {
                foreach($add_more_alias as $k=>$alias)
                {
                    if(!empty($alias["s_alias_name"]))
                    {
                        $temp[]=$alias["s_alias_name"]."|".$alias["s_alias_country"];
                    }
                }
                $posted["s_alias_names"] = dbHashSeperateEnc($temp);
            }
            ///end add more, s_alias_names//
            
            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add
            if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_category', 'category', 'required|is_unique[category.s_category]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_category', 'category', 'required');
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
                    $ret=$this->category_model->update_category(array(
                                            "s_category"=>$posted["s_category"],
                                            "s_desc"=>$posted["s_desc"],
                                            "s_alias_names"=>$posted["s_alias_names"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->category_model->add_category(array(
                                            "s_category"=>$posted["s_category"],
                                            "s_desc"=>$posted["s_desc"],
                                            "s_alias_names"=>$posted["s_alias_names"],
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
        user_access("administer category");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->category_model->delete_category(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("category"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("category"));
        }         
    }
    
    /**
    * Assigning permisions available 
    */
    public function category_permission()
    {
        return array(
            "administer category"=>array(
                "title"=>"Administer category",
                "description"=>"Can view, add, edit, delete category.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

