<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe Dashboard
*  
*/

class Add_verification extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        is_userLoggedIn($redirect_deny_page=TRUE);
		
		$this->load->model('doc_verification_model');
    }
    
    public function index()
    {
        $this->data['page_title'] = 'Add More Verification'; 
		if($_POST)  
		{
			$posted = $_POST;
			if(!empty($_FILES['docs']))
			{
				$user = is_userLoggedIn();
				$uploads_dir = FCPATH.'resources/verification/';
				$arr_ext = array('jpeg', 'jpg', 'bmp', 'png', 'gif', 'pdf','doc','docx');
				for($i=0;$i<count($_FILES['docs']['tmp_name']);$i++)
				{					
					$src_path = $_FILES['docs']['tmp_name'][$i];
					$file_name = $_FILES['docs']['name'][$i];
					
					$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
					$new_file = $this->getFilenameWithoutExtension($file_name).'.'.$ext; // with timestamp
					$dest_path = $uploads_dir.$new_file;
					$doc_verify_id = $this->input->post('doc_id_'.$i);
					//pr($_FILES['docs'],1);
					if($new_file!="" && in_array($ext,$arr_ext))
					{
						move_uploaded_file($src_path, $dest_path);	
						
						$doc_arr = array();
						$doc_arr['uid'] 			= $user->uid;
						$doc_arr['doc_verify_id'] 	= $doc_verify_id;
						$doc_arr['i_verified'] 		= 0;
						$doc_arr['s_file'] 			= $new_file;
						
						$arr_where = array('doc_verify_id'=>$doc_verify_id,'uid'=>$user->uid);
						$i_exist = $this->doc_verification_model->count_user_doc_verification('user_doc_verification',$arr_where);
						if($i_exist<=0)
							$i_insert = $this->doc_verification_model->add_user_doc_verification($doc_arr);
						else
							$i_insert = $this->doc_verification_model->update_user_doc_verification($doc_arr,$arr_where);
					}
					
					
				}
				
				if($i_insert)
					set_success_msg(message_line("saved success"));  //seccess
				else
					set_error_msg(message_line("saved error"));  //error
				
			}
			
		}
        $this->render();
    }
	
	public function ajaxGetDocuments()
	{
		$profile=$this->input->post("profile_type"); 
		
		$condition = array('e_doc_type'=>$profile,'i_active'=>1);		
		$res = $this->doc_verification_model->doc_verification_load($condition);
		
		$user = is_userLoggedIn();
		
		
		$html = '';
		if(!empty($res))
		{
			foreach($res as $key=>$val)
			{
				/*$cond = array('uid'=>$user->uid,'doc_verify_id'=>$val->id);
				$docs = $this->doc_verification_model->user_doc_verification_load($cond);
				$existing_files = $docs[0]->s_file?$docs[0]->s_file:"";*/
				
				$html .='<p><label class="alignleft">Upload '.$val->s_document_required.'</label> 
					<input type="hidden" name="doc_id_'.$key.'" value="'.$val->id.'"><span class="like_input"><input type="file" size="38" class="multi_files" name="docs[]" id="docs_'.$key.'" onchange="return validateFileExtension(this)"></span></p>';
			}
			
			$html.='<p><label class="alignleft">&nbsp;</label>
						<span class="alignleft">
						Supported formats: jpeg, jpg, png, gif, doc, docx
						</span>
						<span class="alignleft">
						<input type="submit" id="btn_verify" class="top_mar" value="Upload">
						</span>
					</p> ';
		}
		
		echo $html;
	}
	
	public function getFilenameWithoutExtension($s_filename = '')
	{
		
		if(empty($s_filename))
		return FALSE;

		$mix_matches = array();
		preg_match('/(.+?)(\.[^.]*$|$)/', $s_filename, $mix_matches);
		unset($s_filename);
		$entities = array(" ",'.','!', '*', "'","-", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", "!");
		//$s_new_filename = str_replace($entities,"_",$mix_matches[1]);
		
		$s_new_filename = str_replace($entities,"_",$mix_matches[1]).'_'.time();
		return strtolower($s_new_filename);
				 
			
	}
	
	
    
}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */