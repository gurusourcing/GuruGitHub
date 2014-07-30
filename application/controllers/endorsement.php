<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Endorsement
*  
* ////Messages are thereaded inside the db column like below.////
* $s_message=array(array("endorsed_by"=> $user_name,"On"=>date("Y-m-d"));
*/

class Endorsement extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_skill_model');
        
    }
    
    public function index()
    {
        is_userLoggedIn(TRUE);
        
        $this->data['page_title'] = 'Endorsement';
        
        
        $uid=get_userLoggedIn('id');
        $user_skill=$this->user_skill_model->user_skill_load(array("uid"=>$uid));
        
        /*foreach($user_skill as $key=>$value){
                $user_skill[$key]->s_endorses = unserialize($user_skill[$key]->s_endorses);
        }*/
        //this function acts same as above but very faster than for loop///
        array_walk_recursive($user_skill,"modifyUnSerialCallback","s_endorses");
        
        $this->data['user_skill']=$user_skill;
        $this->data['form_token']=encrypt($uid);
        $this->render();    
        
        
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
