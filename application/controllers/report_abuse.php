<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Report_abuse
*  
* Site wide report abuse.
*/

class Report_abuse extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_report_abuse_model');
        
    }
    
    public function index($e_absue_for="",$s_absue_for_id="",$s_uid="")
    {
        is_userLoggedIn(TRUE);
        if(empty($s_uid) || empty($s_absue_for_id))
            show_404();//page not found
        
        $this->data['page_title'] = 'Report Abuse';
        
        $posted=array();
        if($_POST)
        {
            session_start();
            $posted=$this->input->post();
            $uid=decrypt(trim($this->input->post("form_token")));         
            
            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('e_absue_for', 'report for user or service or company', 'required');
            $this->form_validation->set_rules('s_absue_for_id', 'report for user or service or company', 'required');
            $this->form_validation->set_rules('s_report', 'description', 'required');
            $this->form_validation->set_rules('txt_captcha','code', 'required|trim|callback__captcha_valid');
            
            if($this->form_validation->run() == FALSE)/////invalid
            {
                set_error_msg(validation_errors());
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                
                $dml_val=array(
                    "uid"=>$uid,
                    "s_report"=>$posted["s_report"],
                    "uid_abuse_by"=>get_userLoggedIn("id"),
                    "i_absue_for_id"=>decrypt($posted["s_absue_for_id"]),
                    "e_absue_for"=>$posted["e_absue_for"],
                );
                
                $ret=$this->user_report_abuse_model->add_user_report_abuse($dml_val);
                  
                if($ret)//success
                {
                    set_success_msg(message_line("saved success")); 
                    $posted=array();
                }
                else//error
                {
                    set_error_msg(message_line("saved error"));                    
                }                
            }     
        }
        
        $default_value=array(
            "form_token"=>trim($s_uid),
            "action"=>"add",
            "e_absue_for"=>$e_absue_for,
            "s_absue_for_id"=>$s_absue_for_id,
        );
        $default_value=$default_value+$posted;
        //pr($default_value);
        $this->data["default_value"]= $default_value;

        $this->render();    
    }
    
    
    /**
    * Validating captcha
    */
    public function _captcha_valid($s_captcha)
    {
        if($s_captcha!=$_SESSION["captcha"])
        {
            // $this->form_validation->set_message('_captcha_valid', 'Please provide correct %s.');
             set_error_msg(message_line("captcha missmatch"));  
             unset($s_captcha);
             return false;
        }
        else
        {
            return true;
        }
    }    
    
    

}


/* End of file Endorsement.php */
/* Location: ./application/controllers/endorsement.php */
