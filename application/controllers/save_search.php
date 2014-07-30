<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* save_search
*/

class Save_search extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_save_search_model');
        
    }
    
    public function index()
    {
        $this->data['page_title'] = 'Save search';
        
        if( ! is_userLoggedIn() )
        {
            redirect(site_url('account/signin'));
        }
        
        $data=$this->user_save_search_model->user_save_search_load(array("uid"=>get_userLoggedIn('id')));
        $this->data['value']=$data;
        $this->render();    
        
        
    }
    
    /**
    * add search data
    */
    public function addSearchData()
    {
       /*if(!is_userLoggedIn()) 
        return FALSE;*/
		
      $data=$this->input->post();	  
	  
      /*$this->user_save_search_model->add_user_save_search(
                                            array('uid'=>get_userLoggedIn('id'),
                                                  's_search_field_value'=>serialize($data),
                                                  's_url'=>get_destination(),
                                                  )
      
	                                        );*/
	
	  /* 27 Nov 2013 save srch data */
	  if(!is_userLoggedIn()) 
	  {
	  	echo "login_error";
	  }
	  else
	  {
		  if($data["s_search_tag"])
		  {
			  if($this->user_save_search_model->add_user_save_search(
													array('uid'=>get_userLoggedIn('id'),
														  's_search_field_value'=>serialize($data),
														  's_search_tag'=>$data["s_search_tag"],
														  's_url'=>get_destination(),
														  )
													))
					echo "success";
				else
					echo "error";
		 }
		 else
		 {
			echo "error";
		 }
	 }
	   
    }
   
    /**
    * add search tag
    */
    public function ajaxAddSearchTag()
    {
        $data=$this->input->post();
        $ret=$this->user_save_search_model->update_user_save_search(
                                            array('s_search_tag'=> $data['s_search_tag']
                                                  ),
                                            array('id'=>intval($data['id']))
                                            );
        if($ret)
        {
            set_success_msg(message_line('saved success'));
        }
        else
        {
            set_error_msg(message_line('saved error'));
        }
    }
    
    /**
    * delete search tag
    */
    public function ajaxDeleteSearchTag()
    {
       $data=$this->input->post();
       
       $id=is_array($data['id'])?implode(',',$data['id']):$data['id'];
       $condition='id IN ('.$id.')';
       
       $ret=$this->user_save_search_model->delete_user_save_search($condition);
       
       if($ret)
            set_success_msg(message_line('delete success'));
       else
            set_error_msg(message_line('delete error'));
         
    }
    
    /**
    * send email
    */
    public function ajaxSendEmail()
    {
        $data=$this->input->post();
       //pr($data,1);
        
        foreach($data as $k=>$v)
            $this->form_validation->set_rules('email[]','email','valid_email');
        
        if($this->form_validation->run() == FALSE)
        {
            echo validation_errors();
        }
        else
        {
            $from=get_userLoggedIn('s_email');
            $subject=site_name().', sharing link';
            $u=set_user_profile_name(get_userLoggedIn());
            //$msg=sprintf(message_line('email share message'),$data['link'],$data['link'],$u->s_name);
            
           foreach($data['email'] as $k=>$v)
            {   
                $msg='';
                if(is_array($data['link']))
                {
                   foreach($data['link'] as $url)
                    {
                        $msg=sprintf(message_line('email share message'),$url,$url,$u->s_name);
                        sendBulkMail($v,$from,$subject,$msg);
                    } 
                }
                else
                {
                    $msg=sprintf(message_line('email share message'),$data['link'],$data['link'],$u->s_name);
                    sendBulkMail($v,$from,$subject,$msg);
                }
                
            }
            echo 'success'; 
            
        }
        
    }
    
    
    /**
    * Goto search page 
    * using seach parameter. 
    * Form post using herder
    */
    public function gotosearchresult($id)
    {
        $search=$this->user_save_search_model->user_save_search_load(intval($id));
        
        $html='<form id="frm_search" method="post" action="'.site_url("search_engine").'"/>';
        if(!empty($search))
        {
           $temp=unserialize($search->s_search_field_value);
           foreach($temp as $k=>$v)
           {
               $html.='<input type="hidden" id="'.$k.'" name="'.$k.'" value="'.trim($v).'"/>';
           }            
        }
        $html.='</form>';
        $html.='<script type="text/javascript">
                  document.getElementById("frm_search").submit();
                </script>';
        echo $html;
    }
    
}


/* End of file save_search.php */
/* Location: ./application/controllers/save_search.php */
