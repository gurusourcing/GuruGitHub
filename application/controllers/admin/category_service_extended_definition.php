<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Category_service_extended_definition
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
* ***Sub category is inactivated right now
* 
* ON 7Oct2013, 
*  A "country_id" field/column is added into "category_service_extended_defination" table. 
* So that USA and India have different categories. 
* @see, controllers/admin/category_service_extended_definition.php 
* @see, controllers/search_engine.php
* @see, controllers/service_profile.php
* 
* purpose country wise category selection. Default country is 2=>USA
* 
* new columns added in table "user_service_extended" , 
* "s_availability_ids" , "s_tools_ids", "s_designation_ids"
* 
* Also new enum value "available" , "tool" , "tution_mode" 
* added at "option" and "user_suggestion" tables
* 
*/

class Category_service_extended_definition extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
                
        $this->load->model("category_service_extended_defination_model");
        $this->load->model("user_service_extended_model");
    }
    
    public function index() {
        user_access("administer extended service");//access check    
        
        if($posted = $this->input->post()){
            $s_cat = $this->ajax_cat_list($this->input->post('cat_id'),FALSE);
            $s_sub_cat = $this->ajax_sub_cat_list($this->input->post('cat_id'),FALSE);

            $this->data["s_cat"] = $s_cat[$this->input->post('cat_id')];
            $this->data["cat_id"] = $this->input->post('cat_id');

            /*$this->data["sub_cat_id"] = $this->input->post('sub_cat_id');
            $this->data["s_sub_cat"] = @$s_sub_cat[$this->input->post('sub_cat_id')];*/
            
            /**
            * on 7Oct2013, country wise extended defination
            */
            $this->data["country_id"] = $this->input->post('country_id');
            $this->data["s_country"] = get_countryName(intval($this->data["country_id"]));

            $this->form_validation->set_rules('cat_id', 'category', 'required');
            $this->form_validation->set_rules('country_id', 'country', 'required');
                
            if($this->form_validation->run() == FALSE)/////invalid  
            {
                $this->data["stage"] = 'step 1';
                set_error_msg(validation_errors());
            }
            else{
                $this->data["stage"] = 'step 2';          
                
                //generate table starts here
                $table=array();
                $table["header"]=array(
                 array("title"=>"<div>Search Page Label</div>",
                        "attributes"=>array("width"=>"70")
                  ),	
                 /* array("title"=>"<div>Search Page Default Value</div>",
                        "attributes"=>array("width"=>"70")
                  ),
                   array("title"=>"<div>Search Page Order</div>",
                        "attributes"=>array("width"=>"70")
                  ),*/
                  array("title"=>"<div>Service Page Label</div>",
                        "attributes"=>array("width"=>"70")
                  ),
			       array("title"=>"<div>Enabled</div>",
                        "attributes"=>array("width"=>"70")
                  ),
                  /*array("title"=>"<div>Service Page Default Value</div>",
                        "attributes"=>array("width"=>"70")
                  ),
                   array("title"=>"<div>Service Page Order</div>",
                        "attributes"=>array("width"=>"70")
                  ),  */
                );
            $table["no result text"]="No information found.";

            ////Auto Pagination
            $this->category_service_extended_defination_model->pager["base_url"]=admin_base_url("category_service_extended_definition/index");
            $this->category_service_extended_defination_model->pager["uri_segment"]=4;
            
            //////Filter/////
            $filter=array();
            $filter="country_id ='".$this->input->post("country_id")."'  AND cat_id = '".$this->input->post("cat_id")."' ";
            if(!empty($_POST["sub_cat_id"]))
            {
                $filter.=" AND sub_cat_id = '".$this->input->post("sub_cat_id")."'";
            }
            $this->data["posted"]=$this->input->post();
            //////end Filter/////
		    
		    /** first add all the column which are not exist in category_service_extended_defination table
		    *  for this category 
		    *  @see service_extended_column_operation(), defined in common_helper
		    * @param category id 
		    */
		    service_extended_column_operation($this->input->post("cat_id"));
		    
		    $this->noRecAdmin = 20; // for this page we need to show all the fields
            
            $rec=$this->category_service_extended_defination_model->category_service_extended_defination_load(
                                      $filter,
                                      $this->noRecAdmin,
                                      $this->uri->segment(4,0)
                                    );
								    
								    
							    
            if(!empty($rec))
            {
                foreach($rec as $r)
                {
				    
				    $checked = ($r->i_active==1)?'checked="checked"':'';

                    $table["rows"][]=array(
                        //search page
                        '<input value="'.$r->s_search_page_label.'" type="text" name="s_search_page_label['.$r->id.']">',
                       /*
					    '<input value="'.$r->s_search_page_default_value.'" type="text" name="s_search_page_default_value['.$r->id.']">',
                        form_dropdown("s_search_page_order[".$r->id."]",dd_order(),$r->s_search_page_order,'class="s_search_page_order" style="margin:10px"'),
					    */
					    
                        //service page
                        '<input value="'.$r->s_service_page_label.'" type="text" name="s_service_page_label['.$r->id.']">',
                        '<input class="extend_cat_chk" value="1" '.$checked.' type="checkbox" id="a_active_'.$r->id.'" name="s_active['.$r->id.']">'
				       /* 
				       '<input value="'.$r->s_service_page_default_value.'" type="text" name="s_service_page_default_value['.$r->id.']">',
                        form_dropdown("s_service_page_order[".$r->id."]",dd_order(),$r->s_service_page_order,'class="s_service_page_order" style="margin:10px"'), 
					    */                   
                    );                
                }
            }
            /**
            * Pager goes to the footer of the table
            */
            $table["footer"]='<input type="submit" value="Submit" name="submit">';
            
            /*pr($this->uri->segment_array());
            pr($this->admin_type_model->get_pager());*/
            
            $this->data["table"]=theme_table($table);
            //$this->data["pager"]=$this->country_model->get_pager();
            //generate table stops here
            }///end validation success
        } else {
            $this->data["stage"] = 'step 1';
        }
        

        $this->data["page_title"]="Service Extended Definition";
        $this->render();
    }    
    /**
    * Add edit form
    * 
    * @param mixed $action
    * @param mixed $form_token
    */
    public function operation()
    {
        user_access("administer extended service");//access check    
        ////Inedit configuration////
        $id=0;
       
        $posted = $this->input->post();
		
         foreach ($posted['s_search_page_label'] as $key => $neverUsed) {
            //$modify_data[$key]['s_service_page_order'] =  $posted['s_service_page_order'][$key];
            $modify_data[$key]['s_service_page_label'] =  $posted['s_service_page_label'][$key];
           // $modify_data[$key]['s_service_page_default_value'] =  $posted['s_service_page_default_value'][$key];
        
            //$modify_data[$key]['s_search_page_default_value'] =  $posted['s_search_page_default_value'][$key];
            $modify_data[$key]['s_search_page_label'] =  $posted['s_search_page_label'][$key];
           // $modify_data[$key]['s_search_page_order'] =  $posted['s_search_page_order'][$key];
			
			$modify_data[$key]['i_active'] =  ($posted['s_active'][$key]!="")?$posted['s_active'][$key]:0;
         			
			$ret=$this->category_service_extended_defination_model->update_category_service_extended_definition($modify_data[$key],array("id"=>$key));       
         }
         
         set_success_msg( message_line("saved success") );
         redirect(admin_base_url('category_service_extended_definition'));
        ////end login form starts from here////         
    }
    
    
    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer extended service");//access check
        
        $ajx_ret=array(
            "mode" => "", //success|error
            "message"=>"",//html string  
        );
        
        $posted=array();
        
        if(isset($_POST["s_category_service_extended_definition"]))
        {
            $posted["action"] = trim($this->input->post("action")); 
            $posted["s_category_service_extended_definition"] = trim($this->input->post("s_category_service_extended_definition"));

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  
            
            ////rules for add
            if($posted["action"]=="add")
            {
                $this->form_validation->set_rules('s_category_service_extended_definition', 'category_service_extended_definition', 'required|is_unique[category_service_extended_definition.s_category_service_extended_definition]');
            }
            elseif($posted["action"]=="edit") //rules for edit
            {
                $this->form_validation->set_rules('s_category_service_extended_definition', 'category_service_extended_definition', 'required');
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
                    $ret=$this->category_service_extended_definition_model->update_category_service_extended_definition(array(
                                            "s_category_service_extended_definition"=>$posted["s_category_service_extended_definition"],
                                        ),array("id"=>$posted["form_token"]));                    
                }
                elseif($posted["action"]=="add")
                {
                    $ret=$this->category_service_extended_definition_model->add_category_service_extended_definition(array(
                                            "s_category_service_extended_definition"=>$posted["s_category_service_extended_definition"],
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
        user_access("administer extended service");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->category_service_extended_definition_model->delete_category_service_extended_definition(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("category_service_extended_definition"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("category_service_extended_definition"));
        }         
    }
    
    /*ajax sub cat populate
     */
     public function ajax_sub_cat_list($id = NULL,$echo_on = true)
    {
        if($id==NULL)
            $id=intval($this->input->get('cat_id'));
        $ret="";
        if($id)
        {
            $ret=dd_sub_category(array("cat_id"=>$id));
            unset($ret[""]);
        }
        if($echo_on)
        echo json_encode($ret);
        return $ret;
    }
    
     /*ajax sub cat populate
     */
     public function ajax_cat_list($id = NULL,$echo_on = true)
    {   
        if($id==NULL)
            $id=intval($this->input->get('id'));
        $ret="";
        if($id)
        {
            $ret=dd_category(array("id"=>$id));
            unset($ret[""]);
        }
        if($echo_on)
        echo json_encode($ret);
        return $ret;
    }
    
    
     /*ajax country populate
     */
    public function ajax_country_list($id = NULL,$echo_on = true)
    {           
        if($id==NULL)
            $id=intval($this->input->get('id'));
        $ret="";
        if($id)
        {
            $ret=dd_country(array("id"=>$id));
            unset($ret[""]);
        }
        if($echo_on)
        echo json_encode($ret);
        return $ret;
    }    
    
    /**
    * Assigning permisions available 
    */
    public function category_service_extended_definition_permission()
    {
        return array(
            "administer extended service"=>array(
                "title"=>"Administer extended service",
                "description"=>"Can view, add, edit, delete category service extended definition.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

