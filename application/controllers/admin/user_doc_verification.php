<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class User_doc_verification extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("user_doc_verification_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        user_access("administer user doc verification");//access check  
        
        $table=array();
        $table["header"]=array(
         array("title"=>"<div>User<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         array("title"=>"<div>Verified Document<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
		  array("title"=>"<div>Download Document<span></span></div>",
                "attributes"=>array("class"=>"sortCol")
          ),
         
	 array("title"=>"Actions",
            "attributes"=>array("width"=>"100")
          ),
        );
        $table["no result text"]="No information found.";
        
        ////Auto Pagination
        $this->manage_user_model->pager["base_url"]=admin_base_url("user_doc_verification/index");
        $this->manage_user_model->pager["uri_segment"]=4;
        
        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
			$this->data["posted"]=$this->input->post();
           	if(!empty($this->data["posted"]["s_user_doc_verification"]))
		   	$filter="u.s_user_name LIKE '%".$this->data["posted"]["s_user_doc_verification"]."%' OR dv.s_document_required LIKE '%".$this->data["posted"]["s_user_doc_verification"]."%'";
			
			/*if(!empty($this->data["posted"]["e_doc_type"]))
		   		$filter.=(!empty($filter)? " AND ":"")."e_doc_type ='".$this->data["posted"]["e_doc_type"]."'";*/
			
            
        }
        //pr($filter);
        //////end Filter/////
        
        $rec=$this->user_doc_verification_model->user_doc_verification_load(
                                  $filter,
                                  $this->noRecAdmin,
                                  $this->uri->segment(4,0)
                                );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                
                $data = array(
                'name'        => 'i_verified',
                'class'       => 'i_verified',
                'value'       => $r->id,
                'checked'     => (bool)$r->i_verified,
                );


                 $action.='<div id="active_action" class="floatL mr10 on_off">'.form_checkbox($data).'</div>';
                $action.='<a id="delete_action" href="'.admin_base_url("user_doc_verification/delete/".encrypt($r->id) ).'" >Delete</a>';  
               
                
                    
                $table["rows"][]=array(
                      $r->s_user_name,
		      $r->s_document_required,
			  '<a href="'.admin_base_url("user_doc_verification/download_document/".encrypt($r->id) ).'" class="dwnld">'.$r->s_file.'</a>',
                      //'<div id="active_action" class="floatL mr10 on_off"><input type="checkbox" id="check20" checked="checked" name="chbox" /></div>',
                     // $r->i_verified,
                      $action
                );                
            }
        }
        
        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->user_doc_verification_model->get_pager();
        
        $this->data["page_title"]="Manage User";
        $this->data["add_link"]= anchor(admin_base_url('user_doc_verification/operation/add'), '<span class="icos-add"></span>', 'title="Add User" class="tipS"');
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
//        user_access("administer user doc verification");//access check    
//        
//        ////Inedit configuration////
//        $id=0;
//        if($action=="edit")
//            $id=decrypt($form_token);
//        
//        $modify_data=$this->user_doc_verification_model->user_doc_verification_load(intval($id));        
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
//        user_access("administer user doc verification");//access check
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
//                    $ret=$this->user_doc_verification_model->add_user_doc_verification(array(
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
        user_access("administer user doc verification");//access check
        $id=decrypt($form_token);    
        
        $ret=$this->user_doc_verification_model->delete_user_doc_verification(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("user_doc_verification"));                       
                                    
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("user_doc_verification"));                       
        }         
    }
    
   
    public function ajax_status_update(){
            
            $posted   = $this->input->post(); 
            foreach($posted as $id=>$value){
                $val=$value=='1'?array("i_verified"=>'1'):array("i_verified"=>'0');
                $condition=array('id'=>$id);
                $res=$this->user_doc_verification_model->update_user_doc_verification($val,$condition); 
                
            }
            

    }
    /**
    * Assigning permisions available 
    */
    public function user_doc_verification_permission()
    {
        return array(
            "administer user doc verification"=>array(
                "title"=>"Administer user doc verification",
                "description"=>"Can view, add, delete User Doc Verification.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
	
	public function download_document($i_id)
	{
		$i_id = decrypt($i_id);
		if($i_id>0)
		{
			$cond = array('udv.id'=>$i_id);
			$rec=$this->user_doc_verification_model->user_doc_verification_load($cond);
			
			$saved_file_name = $rec[0]->s_file;
			
			$this->load->helper('download'); 
			$file_path = FCPATH.'resources/verification/';
			if(file_exists($file_path.$saved_file_name))
			{
				$content = file_get_contents($file_path.$saved_file_name); 
				force_download($saved_file_name, $content); 				
			}
			else
			{
				echo '<script>history.back();</script>';
			}
		}
	}
    
}

