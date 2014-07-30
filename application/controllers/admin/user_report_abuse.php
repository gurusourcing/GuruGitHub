<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Report Abuse
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_report_abuse extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_report_abuse_model");
    }
    
    /**
    * View Listing
    */
    public function index()
    {
        user_access("administer user report abuse");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>User Abused<span></span></div>",
                "attributes"=>array("class"=>"sortCol","width"=>"100")
          ),
         array("title"=>"<div>Abused By<span></span></div>",
                "attributes"=>array("class"=>"sortCol","width"=>"100")
          ),          
         array("title"=>"<div>Report<span></span></div>",
                "attributes"=>array("class"=>"sortCol","width"=>"100")
          ),
         
         /*array("title"=>"<div>Abused Type<span></span></div>",
                "attributes"=>array("class"=>"sortCol","width"=>"100")
          ),*/
            
         array("title"=>"<div>Abused For<span></span></div>",
                "attributes"=>array("class"=>"sortCol","width"=>"100")
          ),
         
	 array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->user_report_abuse_model->pager["base_url"]=admin_base_url("user_report_abuse/index");
        $this->user_report_abuse_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_user_report_abuse"]))
		   	$filter="usr.s_user_name LIKE '%".$this->data["posted"]["s_user_report_abuse"]."%' OR ura.e_action_taken LIKE '%".$this->data["posted"]["s_user_report_abuse"]."%'";
			
			/*if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_report_abuse_model->user_report_abuse_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                          );
           
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action.='<div id="active_action" class="floatL mr10 on_off">'.form_dropdown("e_action_taken",dd_abuse_action(),$r->e_action_taken,'class="e_action_taken show_cat" rel="'.$r->id.'" style="margin:10px"').'</div>';
                
                $status=(trim($r->e_user_status)=="blocked"?"active":"blocked");
                $status_text=(trim($r->e_user_status)=="blocked"?"Activate The Abused user":"Block The Abused user");
                $action.= '<div id="active_action" class="floatL mr10 on_off">'.anchor(admin_base_url('user_report_abuse/ajax_user_status_update/'.encrypt($r->uid).'/'.$status), 
                            $status_text, 
                            'title="Abused user is '.ucfirst(trim($r->e_user_status)).'. To '.$status_text.' click here."
                             class="tipS"
                            ')
                            .'</div>';
                
                $table["rows"][]=array(
                      $r->s_user_name,
                      $r->s_abuse_by_user_name,
		              word_limiter(format_text($r->s_report),100,"..."),
                      /*$r->e_absue_for,*/
                      $r->s_absue_for,
                      $action
                );                
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_report_abuse_model->get_pager();
        
        $this->data["page_title"]="Manage User";
        $this->data["add_link"]= anchor(admin_base_url('user_report_abuse/operation/add'), '<span class="icos-add"></span>', 'title="Add User" class="tipS"');
        $this->data["table"]=theme_table($table);

        $this->render();
        ////end login form starts from here////        
	}
    
//    /**
//    * Add edit form
//    * 
//    * @param mixed $action
//    * @param mixed $form_token
//    */
//    public function operation($action="add",$form_token="")
//    {
//        user_access("administer user report abuse");//access check    
//        
//        ////Inedit configuration////
//        $id=0;
//        if($action=="edit")
//            $id=decrypt($form_token);
//        
//        $modify_data=$this->user_report_abuse_model->user_report_abuse_load(intval($id));        
//        
//        $default_value[0]=json_encode(array(
//                            "s_user_name"        => trim(@$modify_data->s_user_name),
//							"s_email"            => trim(@$modify_data->s_email),
//                            "action"             => $action,
//                            ));
//        $this->data["default_value"]= $default_value;
//        
//        $this->data["page_title"]="User ".ucwords($action);
//        $this->render();
//        ////end login form starts from here////         
//    }
//    
//    
//    /**
//    * Ajax add edit post
//    */
//    public function ajax_operation()
//    {
//        user_access("administer user report abuse");//access check
//        
//        $ajx_ret=array(
//            "mode" => "", //success|error
//            "message"=>"",//html string  
//        );
//        
//        $posted=array();
//        
//        if(isset($_POST["s_user_name"]))
//        {
//            $posted["action"] 		        = trim($this->input->post("action")); 
//            $posted["s_user_name"]          = trim($this->input->post("s_user_name"));
//            $posted["s_email"] 		        = trim($this->input->post("s_email"));
//            $posted["s_password"]           = trim($this->input->post("s_password"));
//            $posted["s_confirm_password"]   = trim($this->input->post("s_confirm_password"));
//            ////rules 
//			$this->form_validation->set_rules('s_user_name', 'user name', 'required');
//			$this->form_validation->set_rules('s_email', 'email', 'required');
//            $this->form_validation->set_rules('s_password', 'new password', 'required|matches[s_confirm_password]');
//            $this->form_validation->set_rules('s_confirm_password', 'confirm password', 'required');
//            
//                
//            if($this->form_validation->run() == FALSE)/////invalid
//            {
//                $ajx_ret["mode"]="error";
//                //$ajx_ret["message"]=form_error('s_admin_name');
//                $ajx_ret["message"]= validation_errors();   
//                echo json_encode($ajx_ret);
//                return FALSE;
//            }
//            else//valid, saving into db
//            {
//                $ret=FALSE;
//                if($posted["action"]=="add")
//                {
//                    
//                    $ret=$this->user_report_abuse_model->add_user_report_abuse(array(
//                                            "s_email"       =>$posted["s_email"],
//											"s_user_name"	=>$posted["s_user_name"],
//                                            "s_password"    =>md5($posted['s_password']),
//                                        ));
//                }
// 
//                  
//                if($ret)//success
//                {
//                    
//                    $ajx_ret["mode"]="success";
//                    $ajx_ret["message"]= message_line("saved success");
//                    if($posted["action"]=="add")
//                        $ajx_ret["form_token"]=encrypt($ret); 
//                          
//                    echo json_encode($ajx_ret);
//                    return TRUE;                    
//                }
//                else//error
//                {
//                    $ajx_ret["mode"]="error";
//                    $ajx_ret["message"]= message_line("saved error");   
//                    echo json_encode($ajx_ret);
//                    return TRUE;                    
//                }                
//            }     
//           
//        }//end if         
//    }    
//    
    
    /**
    * Delete 
    * 
    * @param mixed $form_token
    */
    public function delete($form_token)
    {
        user_access("administer user report abuse");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_report_abuse_model->delete_user_report_abuse(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_report_abuse"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_report_abuse"));
        }         
    }
    
   
    public function ajax_status_update(){
            
            $posted   = $this->input->post(); 
            foreach($posted as $id=>$value){
                $val=array("e_action_taken"=>$value);
                $condition=array('id'=>$id);
                $res=$this->user_report_abuse_model->update_user_report_abuse($val,$condition);    
            }
    }
    
    public function ajax_user_status_update($form_token,$status){
            
        user_access("administer user report abuse");//access check
        $id=decrypt($form_token);    
        
        $val=array("e_status"=>$status);
        $condition=array('id'=>$id);        
        $this->load->model("user_model");
        $ret=$this->user_model->update_user($val,$condition);
        if($ret)//success
        {
            set_success_msg(message_line("status update success"));
            redirect(admin_base_url("user_report_abuse"));                       
            
        }
        else//error
        {
            set_error_msg(message_line("status update error"));
            redirect(admin_base_url("user_report_abuse"));
        }
    }    
    
    /**
    * Assigning permisions available 
    */
    public function user_report_abuse_permission()
    {
        return array(
            "administer user report abuse"=>array(
                "title"=>"Administer user report abuse",
                "description"=>"Can view, add, delete User report abuse.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
    
}

