<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* message
*/

class Message extends MY_Controller {    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_message_model');
        
    }
    
    public function index()
    {
        $this->data['page_title'] = 'Message';
		$this->data["to_inbox"] = $_SESSION['to_inbox']?$_SESSION['to_inbox']:false;
		$_SESSION['to_inbox'] = "";
		
		$this->data["to_outbox"] = $_SESSION['to_outbox']?$_SESSION['to_outbox']:false;
		$_SESSION['to_outbox'] = "";
		
		$this->data["to_draft"] = $_SESSION['to_draft']?$_SESSION['to_draft']:false;
		$_SESSION['to_draft'] = "";
        
        if( ! is_userLoggedIn() )
        {
            redirect(site_url('account/signin'));
        }
        
        $data=$this->user_message_model->user_message_load(array("umd.i_sender_id"=>get_userLoggedIn('id')));
        $this->data['value']=$data;
		
		// get total new messages
		$s_where=" WHERE umd.i_receiver_id = '".get_userLoggedIn('id')."' AND umi.e_receiver_folder = 'inbox' AND umd.e_read_status='unread' ";
		$this->data["new_msg"] = $this->user_message_model->gettotal_msg($s_where);
		
		
		// get pagination list for inbox messages
		ob_start();
		$this->ajax_inbox_pagination(0,1);
		$inbox = ob_get_contents();
		ob_end_clean();	
		$this->data['inbox_msg'] 	= $inbox;				
		//pr($inbox);
		
		// get pagination list for outbox messages
		ob_start();
		$this->ajax_outbox_pagination(0,1);
		$outbox = ob_get_contents();
		ob_end_clean();
		//$outbox = explode('^',$outbox);		
		$this->data['outbox_msg'] 	= $outbox;			
		//pr($this->data['outbox']);
		
		// get pagination list for draft messages
		ob_start();
		$this->ajax_draft_pagination(0,1);
		$draft = ob_get_contents();
		ob_end_clean();	
		$this->data['draft_msg'] 	= $draft;				
		//pr($draft);	
		
        $this->render();    
        
        
    }
	
	/*
	*  ajax call to get inbox messages
	*/
	function ajax_inbox_pagination($start=0,$param=0) {
		
		$userId = get_userLoggedIn('id');
		$s_where=" WHERE umd.i_receiver_id = '".$userId."' AND umi.e_receiver_folder = 'inbox' ";
		$this->data['start'] 		= $start;
		$limit = 5;
		
		$inbox	= $this->user_message_model->get_all_message($s_where,intval($start),$limit);	
		$total_rows = $this->user_message_model->gettotal_msg($s_where);
		//pr($inbox,1);
		$this->data['inbox'] = $inbox;
		/* pagination start here */
		$ctrl_path 	= base_url().'message/ajax_inbox_pagination/';
		$paging_div = 'inbox_msg';
		$this->data['page_links'] 	= fe_ajax_pagination($ctrl_path, $total_rows, $start, $limit, $paging_div);
		$this->data['total_rows'] 	= $total_rows;		
		$this->data['limit'] 		= $limit;
		
		
		if(empty($param))
			$job_vw = $this->load->view('fe/message/ajax_inbox.tpl.php',$this->data,TRUE);
		else
			$job_vw = $this->load->view('fe/message/ajax_inbox.tpl.php',$this->data,TRUE);
			//$job_vw = $this->load->view('fe/message/ajax_inbox.tpl.php',$this->data,TRUE).'^'.$total_rows;
		echo $job_vw;	
			
	}
	
	/*
	*  ajax call to get outbox messages
	*/
	
	function ajax_outbox_pagination($start=0,$param=0) {
		
		$userId = get_userLoggedIn('id');
		$s_where=" WHERE umd.i_sender_id = '".$userId."' AND umi.e_sender_folder = 'sent_item' ";
		$this->data['start'] 		= $start;
		$limit = 5;
		
		$outbox	= $this->user_message_model->get_all_message($s_where,intval($start),$limit);	
		$total_rows = $this->user_message_model->gettotal_msg($s_where);
		//pr($outbox,1);
		$this->data['outbox'] = $outbox;
		/* pagination start here */
		$ctrl_path 	= base_url().'message/ajax_outbox_pagination/';
		$paging_div = 'outbox_msg';
		$this->data['page_links_outbox'] 	= fe_ajax_pagination($ctrl_path, $total_rows, $start, $limit, $paging_div);
		$this->data['total_rows'] 	= $total_rows;		
		$this->data['limit'] 		= $limit;
		
		
		if(empty($param))
			$job_vw = $this->load->view('fe/message/ajax_outbox.tpl.php',$this->data,TRUE);
		else
			$job_vw = $this->load->view('fe/message/ajax_outbox.tpl.php',$this->data,TRUE);
			//$job_vw = $this->load->view('fe/message/ajax_outbox.tpl.php',$this->data,TRUE).'^'.$total_rows;
		echo $job_vw;	
			
	}
	
	/*
	*  ajax call to get draft messages
	*/
	function ajax_draft_pagination($start=0,$param=0) {
		
		$userId = get_userLoggedIn('id');
		$s_where=" WHERE umd.i_sender_id = '".$userId."' AND umi.e_sender_folder = 'draft' ";
		$this->data['start'] 		= $start;
		$limit = 5;
		
		$draft	= $this->user_message_model->get_all_message($s_where,intval($start),$limit);	
		$total_rows = $this->user_message_model->gettotal_msg($s_where);
		//pr($draft,1);
		$this->data['draft'] = $draft;
		/* pagination start here */
		$ctrl_path 	= base_url().'message/ajax_draft_pagination/';
		$paging_div = 'draft_msg';
		$this->data['page_links_draft'] 	= fe_ajax_pagination($ctrl_path, $total_rows, $start, $limit, $paging_div);
		$this->data['total_rows'] 	= $total_rows;		
		$this->data['limit'] 		= $limit;
		
		
		if(empty($param))
			$job_vw = $this->load->view('fe/message/ajax_draft.tpl.php',$this->data,TRUE);
		else
			$job_vw = $this->load->view('fe/message/ajax_draft.tpl.php',$this->data,TRUE);
			//$job_vw = $this->load->view('fe/message/ajax_inbox.tpl.php',$this->data,TRUE).'^'.$total_rows;
		echo $job_vw;	
			
	}
	
	
	
	public function add_message()
    {
		
        $this->data['page_title'] = 'Add Message';
		if($_POST)
		{
			$i_sender_id	= get_userLoggedIn("id");
			$i_receiver_id 	= $this->input->post('srch_uid');
			$s_subject 		= $this->input->post('s_subject');
			$s_message 		= $this->input->post('s_message');
			
			
			$_SESSION['to_outbox']=true;
			
			$msg_idx = array();
			$msg_idx["i_sender_id"] 		= $i_sender_id;
			$msg_idx["i_receiver_id"] 		= decrypt($i_receiver_id);
			$msg_idx["s_subject"] 			= $s_subject;
			$msg_idx["e_sender_folder"] 	= 'sent_item';
			$msg_idx["e_receiver_folder"] 	= 'inbox';
			$msg_idx["dt_created_on"] 		= date("Y-m-d H:i:s",time());
			
			$i_ins = $this->user_message_model->add_table_data('user_message_index',$msg_idx);
			if($i_ins)
			{
				$msg_data = array();
				$msg_data["i_sender_id"] 			= $i_sender_id;
				$msg_data["i_receiver_id"] 			= decrypt($i_receiver_id);
				$msg_data["s_body"] 				= $s_message;
				$msg_data["i_message_index_id"] 	= $i_ins;
				$msg_data["dt_created_on"] 			= date("Y-m-d H:i:s",time());
				
				$i_ret = $this->user_message_model->add_table_data('user_message_data',$msg_data);
				if($i_ret)
				{
					set_success_msg(message_line("saved success")); 
					redirect(site_url('message'));
				}
				else
				{
					set_error_msg(message_line("saved error")); 
					redirect(site_url('message'));				
				}
				
			}
			else
			{
				set_error_msg(message_line("saved error")); 
				redirect(site_url('message'));
			}
			
		}
		else
		{
			set_error_msg(message_line("saved error")); 
			redirect(site_url('message'));
		}
        
    }
	
	
	
	/* delete multiple message */
	public function ajaxdeleteMessageMulti()
	{
		$data=$this->input->post();
		
		$ids=is_array($data['id'])?implode(',',$data['id']):$data['id'];
		$folder = $data['folder'];
		if($ids)
		{
			$arr = array();
				
			if($folder=='inbox')	
			{
				$condition="i_id IN(".$ids.") AND i_receiver_id=".get_userLoggedIn("id");
				$arr["e_receiver_folder"] = 'deleted';
			}
			else	
			{	
				$condition="i_id IN(".$ids.") AND i_sender_id=".get_userLoggedIn("id");
				$arr["e_sender_folder"] = 'deleted';
			}
			$ret=$this->user_message_model->soft_delete_user_messag($condition,$arr);
		}
		if($folder=='outbox') 
			$_SESSION['to_outbox'] = true;
		else if($folder =='draft')
			$_SESSION['to_draft'] = true;
		else
			$_SESSION['to_inbox'] = true;
			
		
	}
	
	/* ajax read message*/
	public function ajaxreadMessage()
	{
		$userId = get_userLoggedIn('id');
		$msg_idx = $this->input->post();
		if($msg_idx["id"]>0)
		{
			$arr = array();
			$arr["e_read_status"] = 'read';
			$condition = array('i_message_index_id'=>$msg_idx["id"],'i_receiver_id'=>$userId);
			$ret=$this->user_message_model->update_user_message($arr,$condition);
		}
		
		// get total new messages
		$s_where=" WHERE umd.i_receiver_id = '".$userId."' AND umi.e_receiver_folder = 'inbox' AND umd.e_read_status='unread' ";
		$new_msg = $this->user_message_model->gettotal_msg($s_where);
		
		echo $new_msg;
	}
	
	/* ajax save to draft message*/
	public function ajaxSaveToDraft()
	{
		$userId = get_userLoggedIn('id');
		$data=$this->input->post();
		if($data["message"]!="" || $data["subject"]!="")
		{
			$msg_idx = array();
			$msg_idx["i_sender_id"] 		= $userId;
			$msg_idx["i_receiver_id"] 		= $data["id"]?$data["id"]:0;
			$msg_idx["s_subject"] 			= $data["subject"]?$data["subject"]:"";
			$msg_idx["e_sender_folder"] 	= 'draft';
			$msg_idx["e_receiver_folder"] 	= '';
			$msg_idx["dt_created_on"] 		= date("Y-m-d H:i:s",time());
			
			$i_ins = $this->user_message_model->add_table_data('user_message_index',$msg_idx);
			if($i_ins)
			{
				$msg_data = array();
				$msg_data["i_sender_id"] 			= $userId;
				$msg_data["i_receiver_id"] 			= $data["id"]?$data["id"]:0;
				$msg_data["s_body"] 				= $data["message"];
				$msg_data["i_message_index_id"] 	= $i_ins;
				$msg_data["dt_created_on"] 			= date("Y-m-d H:i:s",time());
				
				$i_ret = $this->user_message_model->add_table_data('user_message_data',$msg_data);
			}
			
		}
		
		set_success_msg(message_line("saved success")); 
		$_SESSION['to_draft'] = true;
		echo "ok";
		
	}
    
   
}


/* End of file message.php */
/* Location: ./application/controllers/message.php */
