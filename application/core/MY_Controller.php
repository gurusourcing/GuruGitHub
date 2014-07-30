<?php
/**
* Author : Sahinul Haque
* Date : 22Mar2013  
* 
* Additional features to Controllers.
* 
* 
* TODO :: Franchisee Section shifted into Phase2 
*/

class MY_Controller extends CI_Controller
{

    protected $data = array();//the globally used data from controller to template.
    protected $s_controller_name;
    protected $s_action_name;    
    
    /**
    * Ex- at admin login page we cannot show the 
    * admin menus. 
    * @see, controllers/admin/home.php, index()
    */
    public $hide_menus=FALSE;

    /**
    * For access controll just set the permission name here.
    * Default is granted access.
    */
    public $permission=FALSE;
    
    public $noRecAdmin=5;
    public $noRecFe=5;
    
    public function __construct()
    {      
           
        parent::__construct();
                
        /**
        * Before we go below, we will check the 
        * access control for proper permission.
        * @see, permission_helper.php
        */
        //pr(($this->permission));//sh for admin.
        if(!empty($this->permission))
            user_access($this->permission,TRUE);
        
        //get_permissions();//returns all permissions
        
        ////keep the admin loggedin info
        $this->data["admin_loggedin"]=get_adminLoggedIn();   
        
        ////keep the user loggedin info
        $this->data["user_loggedin"]=get_userLoggedIn();           
             
        //pr($this->session->userdata("user"));
        
        //pr(current_domain());
        
        
        /**
        * For rerouting admin links into back office
        * We have to change the listing_path
        */    
        if(rtrim($this->router->fetch_directory(),"/")=="admin")
            $this->data["listing_path"] = admin_base_url($this->router->fetch_class());
        else
            $this->data["listing_path"] = site_url($this->router->fetch_directory().$this->router->fetch_class());
        
        
        /**
        * Caching variables
        * Testing.. 
        * TODO: finalize using of APC or Memcached
        *  CI supports FILE abapter by default.
        */
        /*$this->load->driver('cache');
        $perm = $this->cache->get('permission');
        if(empty($perm))
        {
            $this->cache->clean();
            $this->cache->delete('permission');
            $this->cache->save('permission', $this->permission);
            ///using memcached
            //$this->cache->memcached->save('cPermission', 'permission', $this->permission);
            
            pr($CI->cache->is_supported("file")); //**File cache worked 
        }*/
        
        
        //Fb local system, Working
        /*$this->data['fb_app_id'] = "446638222085338"; // FB App ID/API Key
        $this->data['fb_app_secret'] = "cfd1d50bb96383d8da14eb7b884191bc"; // FB App Secret
        */
        /*$this->data['fb_app_id'] = "597340323642438"; // FB App ID/API Key, working
        $this->data['fb_app_secret'] = "f9dfc2951f77cd7ad9ed6eaa5e2ac97c"; // FB App Secret 
        */
        //Fb dev.gurusourcing.com system
        /*
        $this->data['fb_app_id'] = "343275819152239"; // FB App ID/API Key, not worked
        $this->data['fb_app_secret'] = "3c2692c25d5aeabb06943618afa914ab"; // FB App Secret 
        */    
		   
        ///Live FB API
        $this->data['fb_app_id'] = "729557787061612"; // FB App ID/API Key
        $this->data['fb_app_secret'] = "ad9e0306e1e385c837a118ffb83633b7"; // FB App Secret    
		
		//Fb local system, Working		
		if($_SERVER['HTTP_HOST']=='192.168.1.33')
		{
			$this->data['fb_app_id'] = "446638222085338"; // FB App ID/APIKey
			$this->data['fb_app_secret'] = "cfd1d50bb96383d8da14eb7b884191bc"; // FB AppSecret
		}
        
		
		//$cookie = $this->get_facebook_cookie($this->data['fb_app_id'], $this->data['fb_app_secret']);

		if($cookie){
			
			if($result = @file_get_contents("https://graph.facebook.com/me/?access_token=".$cookie['access_token'])){
				$result = json_decode($result, true);
				
				$user_email = $result["email"];
				$condition ="u.e_status ='active' AND (u.s_user_name ='".$user_email."' OR d.s_email ='".$user_email."') ";                
                $this->load->model('user_model');
				$user=$this->user_model->user_load($condition);
				
                $loggedin_id = $this->data["user_loggedin"]->uid;
                if(!empty($user) && $loggedin_id!=$user[0]->id)
                {
                    $this->set_userLoginInfo($user[0]);
                    redirect(base_url()."dashboard");
                }
			}    
		}
        
    }
    
    /**
    * Assign the admin login info into session
    * 
    * @param mixed $admin => std object of admin
    */
    protected function set_adminLoginInfo($admin)
    {
        if(!empty($admin))
        {
            ///attach the theme info/////
            /**
            * The franchisee admin users can only 
            * select the theme form FE. 
            * 
            * The admin theme will remain same 
            * for franchisee and guru site admin.
            * 
            * TODO :: Franchisee Section shifted into Phase2. 
            * Donot remove the below code. 
            */
            /*$this->load->model("franchisee_theme_model");
            $theme=$this->franchisee_theme_model->franchisee_theme_load(
                                array("aid"=>$admin->id,
                                "fdomain_id"=>get_userDomain("fdomain_id"))
                            );

            if(!empty($theme))////get the selected fe theme for franchisee site admin 
            {
               $admin->user_theme_settings=$theme[0]->theme_settings;
               $admin->user_theme_id=$theme[0]->theme_id;                 
            }
            else////get the default fe theme for guru site admin
            {
                $this->load->model("theme_model");
                $theme=$this->theme_model->theme_load(
                                array("i_is_default_fe"=>1)
                            );
                            
               $admin->user_theme_settings=unserialize($theme[0]->s_theme_settings);
               $admin->user_theme_id=$theme[0]->id;                
            }*/
            
            ////get the default fe theme for guru site admin///
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_fe"=>1)
                        );
                        
           $admin->user_theme_settings=unserialize($theme[0]->s_theme_settings);
           $admin->user_theme_id=$theme[0]->id;  
            
            /**
            * get the default admin theme 
            * for franchisee site admin and guru site admin 
            */
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_admin"=>1)
                        );
                        
           $admin->admin_theme_settings=unserialize($theme[0]->s_theme_settings);
           $admin->admin_theme_id=$theme[0]->id;               
            
           //$this->session->set_userdata(array("admin"=>$admin));//donot remove
           $this->session->set_userdata("admin",$admin);
           
        }//end if
    }
    
    /**
    * Called after login, 
    * So that the login session is reset.
    * 
    */
    protected function reset_adminLoginInfo()
    {
        $this->session->unset_userdata("admin");
        $this->session->sess_destroy();
    }    
    
    
    /**
    * Assign the members/users login info into session
    * 
    * @param mixed $user => std object of members/users
    */
    protected function set_userLoginInfo($user)
    {
        if(!empty($user))
        {
            ///attach the theme info/////
            /**
            * The franchisee admin users can only 
            * select the theme form FE. 
            * 
            * The admin theme will remain same 
            * for franchisee and guru site admin.
            * 
            * TODO :: Franchisee Section shifted into Phase2. 
            * Donot remove the below code. 
            */
            /*$this->load->model("franchisee_theme_model");
            $theme=$this->franchisee_theme_model->franchisee_theme_load(
                                array("aid"=>$admin->id,
                                "fdomain_id"=>get_userDomain("fdomain_id"))
                            );

            if(!empty($theme))////get the selected fe theme for franchisee site admin 
            {
               $admin->user_theme_settings=$theme[0]->theme_settings;
               $admin->user_theme_id=$theme[0]->theme_id;                 
            }
            else////get the default fe theme for guru site admin
            {
                $this->load->model("theme_model");
                $theme=$this->theme_model->theme_load(
                                array("i_is_default_fe"=>1)
                            );
                            
               $admin->user_theme_settings=unserialize($theme[0]->s_theme_settings);
               $admin->user_theme_id=$theme[0]->id;                
            }*/
            
            ////get the default fe theme for guru site frontend///
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_fe"=>1)
                        );
                        
           $user->user_theme_settings=unserialize($theme[0]->s_theme_settings);
           $user->user_theme_id=$theme[0]->id;    
           
           ////assign the usertype for fe//
           /**
           * Checking if super admin has impersonated 
           * as user in fe
           */
           $admin_type_id=get_adminLoggedIn("admin_type_id");
           if(empty($admin_type_id))//login is as fe member/user
            $admin_type_id=get_fixedAdminTypeId("Members");
            
           $user->admin_type_id=$admin_type_id;             
           ////end assign the usertype for fe//
            //pr($user,1);
           
           //$this->session->set_userdata(array("user"=>$user));//donot remove
           $this->session->set_userdata("user",$user);
           //pr($this->session->userdata('user'),1);
           
        }//end if
    }
    
    /**
    * Called after login, 
    * So that the login session is reset.
    */
    protected function reset_userLoginInfo()
    {
        $this->session->unset_userdata("user");
        $this->session->sess_destroy();
    }         
    
    /**
    * Reset the themes settings when cache is cleared.
    * @see, controllers/admin/homes.php clear_cache();
    */
    protected function reset_themes()
    {
        /**
        * For loggedin admin 
        */
        $admin=get_adminLoggedIn();
        if(!empty($admin))
        {
            ////get the default fe theme for guru site admin///
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_fe"=>1)
                        );
                        
           $admin->user_theme_settings=unserialize($theme[0]->s_theme_settings);
           $admin->user_theme_id=$theme[0]->id;  


            
            /**
            * get the default admin theme 
            * for franchisee site admin and guru site admin 
            */
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_admin"=>1)
                        );
                        
           $admin->admin_theme_settings=unserialize($theme[0]->s_theme_settings);
           $admin->admin_theme_id=$theme[0]->id;               
            
           $this->session->set_userdata(array("admin"=>$admin));  
        }
       /////////////End Admin//////////////
       /**
       * Frontend loggedin user 
       */
       $user=get_userLoggedIn();
       if(!empty($user))
       {
            ////get the default fe theme for guru site frontend///
            $this->load->model("theme_model");
            $theme=$this->theme_model->theme_load(
                            array("i_is_default_fe"=>1)
                        );
                        
           $user->user_theme_settings=unserialize($theme[0]->s_theme_settings);
           $user->user_theme_id=$theme[0]->id;               
           $this->session->set_userdata(array("user"=>$user));
       }
    }      
    
    
    /***
    * Rendering default template and others.
    * Default : application/views/admin/controller_name/method_name.tpl.php
    * For Popup window needs to include the main css and jquery exclusively.
    * 
    * @param string $s_template, ex- dashboard/report then looks like application/views/admin/.$s_template.tpl.php
    * @param boolean $b_popup, ex- true if displaying popup, false to render within the main template
    */
    protected function render($s_template="",$b_popup=FALSE)
    {
        ///caching the html//
        //$this->output->cache(1);//cache 1mins
        
            
            
        $this->s_controller_name = $this->router->fetch_directory() . $this->router->fetch_class();
        $this->s_action_name = $this->router->fetch_method();            
        
        $s_view_path = $this->s_controller_name . '/' . $this->s_action_name . '.tpl.php';
        
        $s_router_directory=$this->router->fetch_directory();
        $s_router_directory = rtrim($s_router_directory, '/').'/';//ensure there's a trailing slash
        
        //////////if no Directory Found then, Set folder "fe" for views only////////
        if($s_router_directory=="/")///For forntend views
        {
            $s_router_directory="fe/";
            $s_view_path=$s_router_directory.$s_view_path;
        }
        //////////end if no Directory Found then, Set folder "fe" for views only////////
        
        $this->data['main_content']=""; 
        if(file_exists(APPPATH . 'views/'.$s_router_directory.$s_template.'.tpl.php'))
            $this->data['main_content'] .= $this->load->view($s_router_directory.$s_template.'.tpl.php', $this->data, true);
        elseif(file_exists(APPPATH . 'views/'.$s_view_path))
            $this->data['main_content'] .= $this->load->view($s_view_path, $this->data, true);

        ////////rendering the Main Tpl////
        //pr($s_view_path);
        if(!$b_popup)////If not opening in popup window
        {  
            $this->template_engine(); 
            //$this->load->view($s_router_directory ."main.tpl.php",$this->data);
        }
        else/////Rendering for popup window
        {
            //print $this->data['main_content'];
            $this->data['popup']=$b_popup;
            $this->template_engine();
        }
        ////////end rendering the Main Tpl////
        unset($s_template,$s_view_path,$s_router_directory);            
            
        return TRUE;
    }
    
    /**
    * This is the main template engine.
    * It chooses the default theme or 
    * selected themes for main page rendering.
    * Only main.tpl.php file can be renderable right now
    * 
    * When main tpl is empty or opens in popup. 
    * We will first check if the suggested file exists in the 
    * "/theme/template/" folder, 
    * Template suggestion is 
    *   "directory"--"controller_name"--"action_name".tpl.php
    *   ex- admin--home--index.tpl.php
    * 
    * 1> at theme section in admin we will crawl every folder within the 
    * "theme" and put the information into the db. 
    * From here we will remove the file crawing section and will call
    * from db
    * 
    * 2> Suggestion to be implemented for any innerpages
    */
    private function template_engine()
    {   
        //TODO need to call from db for default theme
        /*$default_theme=get_allThemes();
        $theme=$default_theme["default"];*/
        
        $theme=get_userTheme();//auto switch fe/admin theme
        if(!empty($theme))
        {
            $this->data["header"]="";
            //////////Header//////////
            ///protected add css///
            if(!empty($theme["theme_info"]["css"]))
            {
                $str="";
                foreach($theme["theme_info"]["css"] as $cf)
                {
                    $str.='<link href="'.site_url($theme["theme_path"].$cf).'" 
                                rel="stylesheet" type="text/css" media="all" />';
                }
                $this->data["header"].=$str;
            }
            ///protected add js///
            if(!empty($theme["theme_info"]["js"]))
            {
                $str="";
                foreach($theme["theme_info"]["js"] as $jf)
                {
                    $str.='<script src="'.base_url($theme["theme_path"].$jf).'" 
                                type="text/javascript"  ></script>';
                }
                $this->data["header"].=$str;
            } 
            //////////end Header//////////     
            
            
            /////main template///
            $main_tpl= $theme["theme_path"]."main.tpl.php";
            if( file_exists($main_tpl) )
            {
                $main_tpl="../../".$theme["theme_path"]."main.tpl.php";
                //$this->load->view("../../".$main_tpl,$this->data);
            }
            else
                $main_tpl="";
            /////main template///
            
            ///rendering the standalone template//
            /**
            * we check main tpl is empty or 
            * opens in popup. 
            * We will first check if the suggested file exists in the 
            * "/theme/template/" folder, 
            * Template suggestion is 
            *   "directory"--"controller_name"--"action_name".tpl.php
            *   ex- admin--home--index.tpl.php
            */
            if(empty($main_tpl) || @$this->data['popup'])
            {
                $s_router_directory=$this->router->fetch_directory();
                $s_router_directory = rtrim($s_router_directory, '/');//ensure there's a trailing slash                
                
                $s_view_path = $theme["theme_path"]."templates/".
                                (!empty($s_router_directory)
                                    ?$s_router_directory."--"
                                    :"").
                                $this->router->fetch_class().'--'. 
                                $this->s_action_name.'.tpl.php';
                
                if(file_exists($s_view_path))
                {
                    $this->load->view("../../".$s_view_path,$this->data);
                }
                else
                {
                    /**
                    * Attaching the default css and js.
                    */
                    $this->data['main_content']=$this->data['header'].$this->data['main_content'];
                    
                    //render the content from "views" folder
                    print $this->data['main_content'];
                }
            }
            else//render the main template 
            {
                $this->load->view($main_tpl,$this->data);
            }
            ///end rendering the standalone template//
        }///end if
    }
    
    
	private function get_facebook_cookie($app_id,$application_secret) {
     $CI =& get_instance();
      //if(isset($_COOKIE['fbsr_' . $app_id])){
	  if($_COOKIE['fbsr_' . $app_id]){
         list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_' . $app_id], 2);
    
         $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
         $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
   
         if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
             return null;
         }
         $expected_sig = hash_hmac('sha256', $payload,
         $application_secret, $raw = true);
          if ($sig !== $expected_sig) {
              return null;
          }
          $token_url = "https://graph.facebook.com/oauth/access_token?"
         . "client_id=" . $app_id . "&client_secret=" . $application_secret. "&redirect_uri=" . "&code=" . $data['code'];
    
          $response = @file_get_contents($token_url);
		  //var_dump($response);exit;
		  //pr($response);
          $access_token = null;
          parse_str($response);
		  //var_dump($access_token);exit;
          //$data['access_token'] = $params['access_token']; 
		  $data['access_token'] = $access_token; 
		  
          return $data;
      }else{
          return null;
     }
}

	
    public function __destruct(){}
}
?>
