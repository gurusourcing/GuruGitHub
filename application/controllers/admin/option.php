<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Option
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
* on 13Dec13, as per client request
* If user chooses a particular category then that category 
* specific advanced filters are displayed below common filters. 
* These filters are related to the service that user provide as 
* expert/guru under a particular category. Example : When search 
* with ( dentist ) In the Specialization boxes  auto suggest  will 
* show the specialization under that particular category. And will 
* check all other right panel boxes with proper options.
* >>a new column "cat_id" added in db "option","user_suggestion" table. 
* 
* We are not storing the optionid in any foreign table. 
* throughout the site.
*/

class Option extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("option_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer option");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>Option<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         /*array("title"=>"<div>Category<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),*/ 
		 array("title"=>"<div>Type<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->option_model->pager["base_url"]=admin_base_url("option/index");
        $this->option_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["cat_id"]))
		   		$filter.="cat_id ='".$this->data["posted"]["cat_id"]."'";
			
			if(!empty($this->data["posted"]["e_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_type ='".$this->data["posted"]["e_type"]."'";
			
            if(!empty($this->data["posted"]["s_suggestion"]))
                   $filter.=(!empty($filter)? " AND ":"")."s_suggestion LIKE '%".$this->data["posted"]["s_suggestion"]."%'";
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->option_model->option_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action.=form_dropdown("action_cat_id[]",dd_category(),$r->cat_id,
                        'id="action_cat_id_'.$r->id.'" rel="'.encrypt($r->id).'" class="show_cat tipS" title="Change Category"');
                
                $action.='<a id="copy_action" href="'.admin_base_url("option/copyToAllCategory/".encrypt($r->id) ).'" class="tablectrl_small bDefault tipS" title="Copy To All Category"><span class="iconb" data-icon="&#xe015"></span></a>';
                $action.='<a id="edit_action" href="'.admin_base_url("option/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("option/delete/".encrypt($r->id) ).'" >Delete</a>';
                    
                $table["rows"][]=array(
                      $r->s_suggestion,
                      /*humanize($r->s_category),*/
					  humanize($r->e_type),
                      $action
                );                
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->option_model->get_pager();
        
        $this->data["page_title"]="Option";
        $this->data["add_link"]= anchor(admin_base_url('option/operation/add'), '<span class="icos-add"></span>', 'title="Add" class="tipS"');
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
        user_access("administer option");//access check    
        
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);
        
        $modify_data=$this->option_model->option_load(intval($id));        
        
        $default_value[0]=json_encode(array(
                            "s_suggestion"=>format_text(@$modify_data->s_suggestion),
							"e_type"=>trim(@$modify_data->e_type),
                            "cat_id"=>intval(@$modify_data->cat_id),
                            "form_token"=>$form_token,
                            "action"=>$action,
                            ));
        $this->data["default_value"]= $default_value;
        
        $this->data["page_title"]="Option ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer Option");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_suggestion"]))
        {
            $posted["action"] 		= trim($this->input->post("action")); 
            $posted["s_suggestion"] = trim($this->input->post("s_suggestion"));
			$posted["e_type"] 		= trim($this->input->post("e_type"));
            $posted["cat_id"]         = intval($this->input->post("cat_id"));

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules 
			$this->form_validation->set_rules('s_suggestion', 'option', 'required');
			$this->form_validation->set_rules('e_type', 'type', 'required');
            
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
                    $ret=$this->option_model->update_option(array(
                                            "s_suggestion"	=>$posted["s_suggestion"],
											"e_type"		=>$posted["e_type"],
                                            "cat_id"        =>$posted["cat_id"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->option_model->add_option(array(
                                            "s_suggestion"=>$posted["s_suggestion"],
											"e_type"		=>$posted["e_type"],
                                            "cat_id"        =>$posted["cat_id"],
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
        user_access("administer option");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->option_model->delete_option(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("option"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("option"));
        }         
    }
    
    /**
    * ajax change category
    * 
    */
    public function ajax_changeCategory()
    {
        user_access("administer Option");//access check
        
        $ajx_ret="false";//false/true as string
        
        $posted=array();
        
        if(isset($_POST["form_token"]))
        {
            $posted["cat_id"]         = intval($this->input->post("cat_id"));

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));              
                
            if(empty($posted["form_token"]))/////invalid
            {
                echo $ajx_ret;
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $ret=$this->option_model->update_option(array(
                                            "cat_id"    =>$posted["cat_id"],
                                        ),array("id"=>$posted["form_token"]));                    
                if($ret)//success
                {
                    $ajx_ret="true";
                    echo $ajx_ret;
                    return TRUE;                    
                }
                else//error
                {
                    echo $ajx_ret;
                    return TRUE;                    
                }                
            }     
           
        }//end if         
    }        
    
    /**
    * Copy option to all category
    * 
    * @param mixed $form_token
    */
    public function copyToAllCategory($form_token)
    {
        user_access("administer option");//access check
        $id=decrypt($form_token);    
        if(empty($id))
        {
            set_error_msg(message_line("saved error"));
            redirect(admin_base_url("option"));
        }
        
        $ret=FALSE;
        $modify_data=$this->option_model->option_load(intval($id)); 
        /**
        * Now we can remove all option data for the same suggestion, 
        * As we are not storing the optionid in any foreign table.
        * throughout the site.
        */
        $this->option_model->delete_option(array("s_suggestion"=>$modify_data->s_suggestion));
        
        if(!empty($modify_data))
        {
            $all_category=dd_category();
            foreach($all_category as $cat_id=>$s_category)
            {
                if(!empty($cat_id))
                {
                    $ret=$this->option_model->add_option(array(
                            "s_suggestion"=>$modify_data->s_suggestion,
                            "e_type"        =>$modify_data->e_type,
                            "cat_id"        =>intval($cat_id),
                        ));                    
                }
            }
        }
        
        //pr(array($all_category,$modify_data),1);
        
        /*if($ret)
            set_error_msg(message_line("saved success"));
        else
            set_error_msg(message_line("saved error"));*/
            
        set_error_msg(message_line("saved success"));   
        redirect(admin_base_url("option"));
                 
    }    
    
    /**
    * Assigning permisions available 
    */
    public function option_permission()
    {
        return array(
            "administer option"=>array(
                "title"=>"Administer option",
                "description"=>"Can view, add, edit, delete Option.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

