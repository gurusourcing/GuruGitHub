<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Dashboard
* Admin Login 
* 
* TODO :: Franchisee Section shifted into Phase2. 
*/
//class Shorturl extends User_profile  {
class Shorturl extends MY_Controller  {    
    
    public function __construct()
    {   
        parent::__construct();
    }
    
    public function generate()
    {
        /**
        * Switching proper controller 
        * w.r.t short url.
        * Short url is unique through 
        * users, company, service tables.
        */
        //pr($this->db);
        /*$obj=new User_profile();
        $obj->index("TnpJallXTjE=");*/
        
        $s_short_url = $this->uri->segment(1);
        
        /*$this->load->model('user_model');
        $this->load->model('user_service_model');
        $this->load->model('user_public_private_model');
        $condition=array('s_short_url'=>$s_short_url);
        $user=$this->user_model->user_load($condition);        
        if(!empty($user)){
            $this->index(encrypt($user[0]->id));
        } else { 
            show_404();
        }        
        */
        /**
        * Building SQL Query for fetching proper short url
        */
        $sql="SELECT id,'user' as type FROM users WHERE s_short_url='".trim($s_short_url)."'"; 
        $sql.=" UNION ALL ";
        $sql.="SELECT id,'company' as type FROM user_company WHERE s_short_url='".trim($s_short_url)."'"; 
        $sql.=" UNION ALL ";
        $sql.="SELECT id,'service' as type FROM user_service WHERE s_short_url='".trim($s_short_url)."'"; 
        
        $rs=$this->db->query($sql);
        $profile=$rs->row();
        //pr($profile,1);
        if(empty($profile))
            show_404();
        else
        {
            //$this->data["profile_type"]=trim($profile->type);//user,company,service
            switch(trim($profile->type))
            {
                case "user":
                    require_once APPPATH."controllers/user_profile.php";
                    $obj=new User_profile();
                    $obj->index(encrypt(intval($profile->id)));
                    $obj->__destruct();
                    return TRUE;
                break;
                case "company":
                    require_once APPPATH."controllers/company_profile.php";
                    $obj=new Company_profile();
                    $obj->index(encrypt(intval($profile->id)));
                    $obj->__destruct();
                    return TRUE;
                break;       
                case "service":
                    require_once APPPATH."controllers/service_profile.php";
                    $obj=new Service_profile();
                    $obj->index(encrypt(intval($profile->id)));
                    $obj->__destruct();
                    return TRUE;
                break; 
                default:
                    show_404();
                break;                         
            }
        }    
    }
    
    /**
    * user profile image uploading...
    * 
    */
    public function profile_picture_upload(){
		$log_user_id=get_userLoggedIn("id");
		
		/* below code to unlink old image from system 29 nov 2013 */
		$old_img_path ="";
		if($log_user_id)
		{
			$user_det=$this->db
					->get_where("user_details d",
								array("d.uid"=>$log_user_id)
					)
					->row();
			$old_img_path = $user_det->s_profile_photo?ltrim($user_det->s_profile_photo,'/'):"";
		}
		
		/* end code to unlink old image from system 29 nov 2013 */
		
        if($posted["h_user_pic"] = $this->input->post('h_user_pic')){
            $temp_upload= parse_jqUploader($posted["h_user_pic"]);
            $dest= set_realpath(get_themeLogoDir()).random_string().".".$temp_upload[0]["extension"];			
                if(copy($temp_upload[0]["upload_path"],$dest))//make sure to update the logo
                {
					
                    $dml["h_user_pic"]=serverToUrlPath($dest);
					
					//image resize option with gd library
					$this->load->helper('image_resize_helper');
					$ThumbDir = BASEPATH.'../resources/logo/';
					$thumbfile = 'thumb_'.str_replace('/resources/logo/','',$dml["h_user_pic"]);					
					$s_uploaded_file 	= upload_image_file($temp_upload[0]["upload_path"],$ThumbDir,$thumbfile,198,210);
					
					unlink(FCPATH.'resources/logo/thumb_'.str_replace('resources/logo/','',$old_img_path));
					
					
                    $this->db->where('uid', get_userLoggedIn("id"));
                    $this->db->update('user_details', array('s_profile_photo' => $dml["h_user_pic"]));
                    unlink($temp_upload[0]["upload_path"]);
					if($old_img_path!="")
					{
						unlink(FCPATH.$old_img_path); // unlink old image from system
					}
                    ?>
                    <img id="profile_img_container" src="<?=base_url();?><?= $dml["h_user_pic"]?>" width="100%" height="100%" alt="pic" />
                     <?php
                 }
              
        }
        else {
                $this->render("",true); 
        }
    }
    /***
    * company profile image uploading...
    * 
    */
     public function company_profile_picture_upload(){
	 
	 	$log_user_id=get_userLoggedIn("id");
		
		/* below code to unlink old image from system 29 nov 2013 */
		$old_img_path ="";
		if($log_user_id)
		{
			$user_det=$this->db
					->get_where("user_company uc",
								array("uc.uid"=>$log_user_id)
					)
					->row();
			$old_img_path = $user_det->s_logo?ltrim($user_det->s_logo,'/'):"";
		}
		
		/* end code to unlink old image from system 29 nov 2013 */
        if($posted["h_user_pic"] = $this->input->post('h_user_pic')){
            $temp_upload= parse_jqUploader($posted["h_user_pic"]);
            $dest= set_realpath(get_themeCompanyLogoDir()).random_string().".".$temp_upload[0]["extension"];
                if(copy($temp_upload[0]["upload_path"],$dest))//make sure to update the logo
                {
                    $dml["h_user_pic"]=serverToUrlPath($dest);
					
					//image resize option with gd library
					$this->load->helper('image_resize_helper');
					$ThumbDir = BASEPATH.'../resources/company/';
					$thumbfile = 'thumb_'.str_replace('/resources/company/','',$dml["h_user_pic"]);					
					$s_uploaded_file 	= upload_image_file($temp_upload[0]["upload_path"],$ThumbDir,$thumbfile,198,210);					
					unlink(FCPATH.'resources/company/thumb_'.str_replace('resources/company/','',$old_img_path));
					
                    $this->db->where('id', get_userLoggedIn("comp_id"));
                    $this->db->update('user_company', array('s_logo' => $dml["h_user_pic"]));
                    unlink($temp_upload[0]["upload_path"]);
					if($old_img_path!="")
						unlink(FCPATH.$old_img_path); // unlink old image from system
                    ?>
                    <img id="profile_img_container" src="<?=base_url();?><?= $dml["h_user_pic"]?>" width="100%" height="100%" alt="pic" />
					
                    <?php
                 }
              
        }
        else {
                $this->render("",true); 
        }
                 

    }
	
	
	 /**
    * suggestion block...
    * 
    */
    public function suggestion_block($param){
		
		$this->data['type'] = $param;
        if($posted["h_suggestion_type"] = $this->input->post('h_suggestion_type')){
           
		   
		    $value_given = TRUE;
			
            $suggest_arr = array();  
			$suggest_arr['s_suggestion'] 	= trim($this->input->post('txt_suggestion'));
			$suggest_arr['e_type'] 			= $this->input->post('h_suggestion_type');
			
			$loc_arr['country_id'] 		= $this->input->post('country_id');
			$loc_arr['state_name'] 		= trim($this->input->post('state_name'));
			$loc_arr['city_name'] 		= trim($this->input->post('city_name'));
			$loc_arr['zip_code'] 		= trim($this->input->post('zip_code'));
			
			$country_name = "";
			if($loc_arr['country_id']!="")
				$country_name = get_countryName($loc_arr['country_id']);
			
			if($loc_arr['country_id']=="" && 
					$loc_arr['state_name']=="" && 
					$loc_arr['city_name']=="" && 
					$loc_arr['zip_code']=="" && $suggest_arr['e_type']=="location")
			{
				$value_given = FALSE;
			}
			if($suggest_arr['e_type']=="location" && $suggest_arr['s_suggestion']=="")
			{
				$suggest_arr['s_suggestion'] = "Country : ".$country_name."\n\r";
				$suggest_arr['s_suggestion'] .= "State : ".$loc_arr['state_name']."\n\r";
				$suggest_arr['s_suggestion'] .= "City : ".$loc_arr['city_name']."\n\r";
				$suggest_arr['s_suggestion'] .= "Zipcode : ".$loc_arr['zip_code']."\n\r";
			}
			
			
			if(!empty($suggest_arr['s_suggestion']) && $value_given)
			{
				$i_exist = check_duplicate_suggestion($suggest_arr);
				if(!$i_exist)
				{
					// entry into suggestion table @see common_option_helper
					$i_add = add_user_suggestion($suggest_arr); 			
					if($i_add)
						echo '<span class="info_msg_success">you have successfully suggested.</span>';
					else
						echo '<span class="info_msg_error">you suggestion has been failed to save.</span>';
				}
				else
				{
					echo '<span class="info_msg_error">This suggestion already exist.</span>';
				}
			}
			else
			{
				echo '<span class="info_msg_error">Please provide some value.</span>';
			}
			
			$this->render("",true);
        }
        else {
               $this->render("",true); 
        }
                 

    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */