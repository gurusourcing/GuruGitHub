<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Theme, 
*  Who can view all users of admin sections.
*  
*  Users permissions for this controller are  
*       "administer theme", "select own domain theme"
* 
* The franchisee admin users can only select the theme form FE. 
* The admin theme will remain same for franchisee and guru site admin.
* 
* TODO :: Franchisee Section shifted into Phase2. 
* 
* 
* Admin 
*  Edit  
* 
*/

class Theme extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("theme_model");
    }
    
    /**
    * View Listing
    */
	public function index()
	{
        //redirect("admin/theme/franchisee");//for testing franchisee theme
        /////Can select theme for own domain, then redirect to sepeate page///
        //TODO :: Franchisee Section shifted into Phase2. 
        /*
        if( user_access("select own domain theme",FALSE) 
            && ! user_access("administer theme") 
        )
        {
            redirect("admin/theme/franchisee");
        }*/
            
        
        user_access("administer theme");//access check  
        
        
        $table=array();
        $table["header"]=array(
          array("title"=>"Theme",
          ),
          array("title"=>"Default Admin Theme",
          ),
          array("title"=>"Default Frontend Theme",
          ),
          array("title"=>"Enabled",
          ),
          
        );
        $table["no result text"]="No information found.";
        
        get_allThemes();///scan the theme folder and update the db//   
        $rec=$this->theme_model->theme_load(array());
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $info=unserialize($r->s_theme_settings);
                //pr($info);
                $table["rows"][]=array(
                    $info["theme_info"]["name"].'<br/><i>'.$info["theme_info"]["description"].'</i>',
                    form_radio("i_is_default_admin", $r->id, intval($r->i_is_default_admin) ),                    
                    form_radio("i_is_default_fe",$r->id, intval($r->i_is_default_fe) ),                    
                    form_checkbox("i_active[]",$r->id, intval($r->i_active) )
                );              
            }
        }
        
        
        
        $this->data["page_title"]="Themes";
        $this->data["add_link"]= anchor(admin_base_url('admin_user/operation/add'), '<span class="icos-add"></span>', 'title="Add Admin" class="tipS"');
        $this->data["table_themes"]=theme_table($table);
        
        $this->render();
        ////end login form starts from here////        
	}
    
    
    /**
    * Ajax add edit post
    */
    public function update()
    {
        user_access("administer theme");//access check
        
        $posted=array();
        
        if(isset($_POST))
        { 
            $posted["i_is_default_admin"] = intval($this->input->post("i_is_default_admin"));
            $posted["i_is_default_fe"] = intval($this->input->post("i_is_default_fe"));
            $posted["i_active"] = $this->input->post("i_active");
            //pr($posted);
            
            $rec=$this->theme_model->theme_load(array());
            foreach($rec as $r)
            {
                $dml_val=array(
                    "i_is_default_admin"=>($r->id!=$posted["i_is_default_admin"]?0:1),
                    "i_is_default_fe"=>($r->id!=$posted["i_is_default_fe"]?0:1),
                    "i_active"=>(in_array($r->id,$posted["i_active"])?1:0),
                );
                
                $ret=$this->theme_model->update_theme($dml_val,
                                                array("id"=>$r->id)
                                                );                 
            }//end for
            
            set_success_msg(message_line("saved success"));
            //redirect(admin_base_url().'home/logout');   
            redirect(admin_base_url('home/clear_cache'));
        }
    }    
    
    /**
    * TODO :: Franchisee Section shifted into Phase2. 
    */
    /*
    public function franchisee()
    {
        user_access("select own domain theme");
        
        $this->data["form_token"]=encrypt(get_adminLoggedIn("id"));
        $this->data["page_title"]="Select Theme For Franchisee";
        $this->render();
    }*/
    
    /**
    * Since these fields are mandatory in franchisee 
    * creation. 
    * We assume there will be no add/insert operation 
    * will be done in this admin section.
    * 
    * TODO :: Franchisee Section shifted into Phase2. 
    */
    /*
    public function save_franchisee_theme()
    {
        user_access("select own domain theme");
        
        $posted=array();
        if($_POST)
        {
            $posted["theme_id"] = intval($this->input->post("theme_id"));
            $posted["f_logo"] = $_FILES["f_logo"];
            $posted["h_f_logo"] = trim(rawurldecode($this->input->post("h_f_logo")));
            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));  
            
            $this->form_validation->set_rules('theme_id', 'theme', 'required');
            //$this->form_validation->set_rules('form_token', 'form token', 'required');//testing
            if($this->form_validation->run() == FALSE)/////invalid
            {
                $this->data["posted"]=$posted;
                $this->franchisee();
            }
            else//valid
            {
                
                $dml=array();
                ///uploading the logo///
                /**
                * uploading the logo
                * we have single file to upload
                * /
                $temp_upload= parse_jqUploader($posted["h_f_logo"]);
                //pr($temp_upload);
                $dest= set_realpath(get_themeLogoDir()).random_string().".".$temp_upload[0]["extension"];
                if(copy($temp_upload[0]["upload_path"],$dest))//make sure to update the logo
                {
                    $dml["s_logo"]=serverToUrlPath($dest);
                } 
                
                $this->load->model("franchisee_theme_model");
                $dml["theme_id"]=$posted["theme_id"];
                
                $ret=$this->franchisee_theme_model->update_franchisee_theme($dml,array("aid"=>$posted["form_token"]));      
                
                if($ret)//success
                {
                    set_success_msg(message_line("saved success"));
                    redirect("admin/theme");                   
                }
                else//error
                {
                    
                    set_error_msg(message_line("saved error"));
                    redirect("admin/theme");                  
                }                  
            }////end else            
        }///end if
    }*/    
    
    
    
    /**
    * Assigning permisions available 
    * TODO :: Franchisee Section shifted into Phase2. 
    */
    public function theme_permission()
    {
        return array(
            "administer theme"=>array(
                "title"=>"Administer themes",
                "description"=>"Can enabled/disable theme. Select default theme for admin and frontend.
                                ".message_line("security concern"),
            ),
            /*"select own domain theme"=>array(
                "title"=>"Select a theme",
                "description"=>"Can select a theme among enabled thems for the domain user registered.
                                <br/> Applicable for \"Franchisee admins\"."
            ),*/                         
        );
    }//end welcome_permission
    
}

