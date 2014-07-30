<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* user favourite services
*  
* ////Messages are thereaded inside the db column like below.////
* $s_message=array(array("posted_by"=> $user_name." On ".date("Y-m-d"),"message"=>trim($s_msg))); 
*/



class Favourites extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        $this->load->model('user_favourite_service_model');
        $this->load->model('user_service_model');
		
		//pr(encrypt('3'));
        
    }
    
    public function index()
    {
        is_userLoggedIn(TRUE);
        
        $this->data['page_title'] = 'Favourites';
        $data=$this->user_favourite_service_model->user_favourite_service_load(array("f.uid"=>get_userLoggedIn("id")));
        
      
        foreach($data as $key=>$value){
                $data[$key]->s_message = unserialize($data[$key]->s_message);
        }
        $this->data['value']=$data;    
        $this->render();
    }
    
    /**
    * delete favourite from table
    
    public function ajaxdeleteFavourite()
    {
       $id=$this->input->post('id');
       $condition="id=".$id." AND uid=".get_userLoggedIn("id");
       $ret=$this->user_favourite_service_model->delete_user_favourite_service($condition); 
       
       if($ret)
        set_success_msg(message_line('delete success'));
       else  
        set_error_msg(message_line('delete error'));
    }
    */
    /**
    * delete multiple values
    */
    public function ajaxdeleteFavouriteMulti()
    {
        $data=$this->input->post();
        $ids=is_array($data['id'])?implode(',',$data['id']):$data['id'];
        $condition="id IN(".$ids.") AND uid=".get_userLoggedIn("id");
        $ret=$this->user_favourite_service_model->delete_user_favourite_service($condition);
        if($ret)
            set_success_msg(message_line('delete success'));
        else  
            set_error_msg(message_line('delete error'));
    }
    /**
    * adding messeage
    */
    public function add_message()
    {
        if($_POST)
        {
            $id=$this->input->post('id');
            $s_message=$this->input->post('s_message');
            
            $data=$this->user_favourite_service_model->user_favourite_service_load(intval($id));
            $msg=unserialize($data->s_message); 

            if(empty($msg))
                $msg=array();
            
            $user_name=get_dashboard_profile_name(get_userLoggedIn("id"));
            
            $msg[]=array("posted_by"=> $user_name." On ".date("Y-m-d"),"message"=>trim($s_message)); 
            
            $this->user_favourite_service_model->update_user_favourite_service(
                            array("s_message"=>serialize($msg)),
                            array("id"=>intval($id)/*,"uid"=>get_userLoggedIn("id")*/)
                        );
            redirect(site_url('favourites'));
                    
        }
    }
    
    /**
    * this function adds service to user_favourite_service table. 
    * 
    */
    public function ajaxAddFavouriteService()
    {
       // getting service id and message
       $service_id=$this->input->post("service_id");//favourite service
       $s_msg= $this->input->post("s_msg");
       
       // getting the service provider id from service table
       $data=$this->user_service_model->user_service_load(intval($service_id));
       
       // setting data to be inserted into user_favourite_service
       $uid_favourite   = $data->uid;///who is favouite 
       $uid             = get_userLoggedIn("id");//favourite of the user. 
       //$s_message       = serialize($s_msg);
       
       $user_name=get_dashboard_profile_name(get_userLoggedIn("id"));
       $s_message=array(array("posted_by"=> $user_name." On ".date("Y-m-d"),"message"=>trim($s_msg))); 
	   
	   // below code is commented for not to show message after successfully add
	   /*$this->user_favourite_service_model->add_user_favourite_service(
                            array(  "s_message"=>serialize($s_message),
                                    "uid"=>intval($uid),
                                    "uid_favourite"=>intval($uid_favourite),
                                    "service_id"=>intval($service_id)
                                    )
                        );*/
						
       // below code is for to show message after successfully add
	   if($uid>0)
	   {
		   if(
		   
				$this->user_favourite_service_model->add_user_favourite_service(
								array(  "s_message"=>serialize($s_message),
										"uid"=>intval($uid),
										"uid_favourite"=>intval($uid_favourite),
										"service_id"=>intval($service_id)
										)
							)
			)
				echo "success";
			else
				echo "error";
		}
		else
		{
			echo "login_error";
		}
				
    }
    
    
}

/* End of file favourites.php */
/* Location: ./application/controllers/favourites.php */
