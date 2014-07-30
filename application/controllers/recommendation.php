<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Endorsement
*  
* ////Messages are thereaded inside the db column like below.////
* $s_message=array(array("endorsed_by"=> $user_name,"On"=>date("Y-m-d"));
*/

class Recommendation extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_service_recommendation_model');
        
    }
    
    public function index()
    {
        is_userLoggedIn(TRUE);
        
        $this->data['page_title'] = 'Recommendation';
        
        
        $uid=get_userLoggedIn('id');
        
        ////Auto Pagination
        $this->user_service_recommendation_model->pager["base_url"]=base_url("recommendation");
        $this->user_service_recommendation_model->pager["uri_segment"]=2;
        
        $user_recommendation=$this->user_service_recommendation_model->user_service_recommendation_load(array("uid"=>$uid),$this->noRecFe,$this->uri->segment(2,0));
        $this->data['pagination']=$this->user_service_recommendation_model->get_pager();
        
        //this function acts same as above but very faster than for loop///
        array_walk_recursive($user_recommendation,"modifyUnSerialCallback","s_message");
        
        $this->data['user_recommendation']=$user_recommendation;
        $this->data['form_token']=encrypt($uid);
        $this->render();    
        
        
    }
    /**
    * General recommendation
    * used when to see others recommendation
    * @param $service_id encrypted
    */
    public function general_recommendation($service_id='')
    {
        if($service_id=="")
            return FALSE;
        
       
        $this->data['page_title'] = 'General Recommendation';    
        $service_id=decrypt($service_id);
        
        ////Auto Pagination
        $this->user_service_recommendation_model->pager["base_url"]=base_url("recommendation");
        $this->user_service_recommendation_model->pager["uri_segment"]=2;
        
        $user_recommendation=$this->user_service_recommendation_model->user_service_recommendation_load(array("service_id"=>$service_id),$this->noRecFe,$this->uri->segment(2,0));
        $this->data['pagination']=$this->user_service_recommendation_model->get_pager();
        
        //this function acts same as above but very faster than for loop///
        array_walk_recursive($user_recommendation,"modifyUnSerialCallback","s_message");
        
        $this->data['user_recommendation']=$user_recommendation;
        $this->data['form_token']=encrypt($service_id);
        $this->render();    
    }
    
    
    
   /**
   * recommend service of an user
   *  
   * @param encrypted $uid
   */
    public function addRecommendation($uid=null)
    {
        $this->data['page_title'] = 'Recommend';
        $form_token=decrypt($uid);
        //whose service is recommended
        $this->load->model('user_model');
        $user_data=$this->user_model->user_load(intval($form_token));
        $Uname=set_user_profile_name($user_data);
        $this->data['name']=$Uname->name;
        
        $this->data['form_token']=$form_token;
        //getting cms content
        $this->data['cms']=get_cms(6);
        
        if($_POST)
        {
           session_start();       
           $posted=array();
           $posted["service_id"]     = trim($this->input->post("service_id"));
           $posted["s_message"]      = trim($this->input->post("s_message"));
           $posted["accept"]        = trim($this->input->post("accept"));
           $posted['txt_captcha']    = $this->input->post('txt_captcha');
           
           //validation
           //$this->form_validation->set_rules('txt_captcha','captcha', 'required|trim|callback__captcha_valid');
           $this->form_validation->set_rules('accept','accept', 'required');
           $this->form_validation->set_rules('service_id','service', 'required');
           
           if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
                set_error_msg(validation_errors());
                
            }
            else
            {
                $posted['uid_recommended_by']=get_userLoggedIn('id');
                //making serialize array
                $s_messge=array();
                $s_messge[]=array("uid_posted_by"=>get_userLoggedIn('id'),"s_msg"=>format_text($posted["s_message"],'encode') ,"dt_posted"=>date("Y-m-d"));
                
                //inserting into database
                $ret=$this->user_service_recommendation_model->add_user_service_recommendation(
                    array(  'uid'=>$form_token,
                            's_message'=>serialize($s_messge),
                            'uid_recommended_by'=>$posted['uid_recommended_by'],
                            'service_id'=>$posted["service_id"],
                    )
                );
                if($ret)
                    set_success_msg(message_line("saved success"));  //seccess
                else
                    set_error_msg(message_line("saved error"));  //error
                    
            }
        }
        $this->render('recommendation/recommend');
    }
    
    /**
    * Validating captcha
    */
        
    function _captcha_valid($s_captcha)
    {
        if($s_captcha!=$_SESSION["captcha"])
        {
            // $this->form_validation->set_message('_captcha_valid', 'Please provide correct %s.');
             set_error_msg(message_line("captcha missmatch"));  
             unset($s_captcha);
             return false;
        }
        else
            return true;
       
    }
    
    /**
    * approving recommandation
    */
    public function approveRecommendation($id='')
    {
        $ret=$this->user_service_recommendation_model->update_user_service_recommendation(array('e_status'=>'approved'),array('id'=>decrypt($id)));
        if($ret)
            set_success_msg(message_line("status update success"));
        else
            set_error_msg(message_line("status update error"));
        redirect(get_destination());
    }
    
    /**
    * Delete Recomandation
    */
    public function deleteRecommendation($id='')
    {
        $ret=$this->user_service_recommendation_model->delete_user_service_recommendation(array('id'=>decrypt($id)));
		
        if($ret)
            set_success_msg(message_line("delete success"));
        else
            set_error_msg(message_line("delete error"));
        redirect(get_destination());
    }
    /**
    * add skills
    */
    /*public function addSkills()
    {
      $data=$this->input->post();
      
      $ret=$this->user_skill_model->add_user_skill(
                                            array('uid'=>intval(decrypt($data['uid'])),
                                                  's_skill_name'=>$data['s_skill_name'],
                                                  "i_endorse_count"=>0
                                                  )
                                            );
      if($ret)
      {
          echo 'success';
      }
      
      
       
    }*/
   
    /**
    * endorse skills
    */
    /*
    public function ajaxEndorseSkill()
    {
        $id=$this->input->post('id');
        //echo $id;
        $data=$this->user_skill_model->user_skill_load(intval($id));
        
        $endorses=unserialize(@$data->s_endorses); 
        /*pr(count($endorses),1);
        if(!empty($endorses))
        {
            foreach($endorses as $key=>$val)
                $cnt=$key+1;
        }* /
        $cnt=count($endorses);
        
        if(empty($endorses))
            $endorses=array();
            
       $endorses[]=array("endorsed_by"=>get_userLoggedIn('id'),"On"=>date("Y-m-d"));
         
        $ret=$this->user_skill_model->update_user_skill(
                          array("s_endorses"=>serialize($endorses),"i_endorse_count"=>1+$cnt),
                          array("id"=>intval($id))
                       );
       if($ret)
        echo 'seccess';
       else
        echo 'failed';     
    }
    */
    
    /**
    * delete search tag
    */
    /*public function ajaxDeleteSkill()
    {
       $data=$this->input->post();
       
       $condition="id =".$data['id'];
      
       $ret=$this->user_skill_model->delete_user_skill($condition);
       
       if($ret)
            echo 'success';
       
    }*/

}


/* End of file Endorsement.php */
/* Location: ./application/controllers/endorsement.php */
