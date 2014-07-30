<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe Dashboard
*  
*/

class Dashboard extends MY_Controller { 

	
    
    public function __construct()
    {   
        parent::__construct();
        is_userLoggedIn($redirect_deny_page=TRUE);
    }
	    
    public function index()
    {
        $this->data['page_title'] = 'Dashboard';   
		
		$user = is_userLoggedIn();
		$cond = array('udv.uid'=>$user->uid);		
		$this->load->model("user_doc_verification_model");
		$this->data["verification"]=$this->user_doc_verification_model->user_doc_verification_load($cond);
				
		// check profile percent then update this
		user_profile_prc_calculation($user->uid); // @see user_profile_prc_calculation	
        $this->render();
    }
	
	
    
}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */