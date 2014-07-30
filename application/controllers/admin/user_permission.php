<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin User Permission
* ACL
* 
*/

class User_permission extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("admin_type_permission_model");
        $this->load->model("admin_type_model");
    }
    
    /**
    * Permission edit form page
    */
	public function index()
	{
        user_access("administer user role");//access check  
        
        //$assigned_permission=$this->admin_type_permission_model->admin_type_permission_load();
        $all_permissions=get_permissions();
        ///cache all permissions//
        /*clear_allCache("all_permissions");
        cache_var("all_permissions",$all_permissions);*/
        ///end cache all permissions//        
        
        /**
        * get all roles except "Super Admin", because
        * he will accure all permissions
        * @var mixed
        */
        $all_roles=$this->admin_type_model->admin_type_load(array("id !="=>1));
        
        ///////Headers/////
        $table["header"]=array(
           array("title"=>"Permission",
                
          ),
            );
        $table["no result text"]="No information found.";
            
        foreach($all_roles as $r)
        {
            $table["header"][]=array("title"=>$r->s_type);
        }
        ///////end Headers/////
        ///////Rows/////
        if(!empty($all_permissions))
        {
            $row=0;
            foreach($all_permissions as $k=>$perm)
            {
                $cnt=0;
                ////column Permission name///
                $table["rows"][$row][$cnt++]='<strong>'.$perm["title"].'</strong>
                                            <p><small>'.$perm["description"].'</small></p>';
                ////Column Form section//
                foreach($all_roles as $r)
                {
                    $temp=$this->admin_type_permission_model
                            ->admin_type_permission_load(array("admin_type_id"=>$r->id,
                                                            "s_permission_name"=>$k));
                                                            
                    ///generating the check box matrix////
                    /**
                    * Format of checkbox value is 
                    * perm_name###role_id###admin_type_permission_id
                    */
                    $str='<input type="checkbox" id="permission" name="permission[]" 
                            value="'.$k.'###'.$r->id.'###'.intval(@$temp[0]->id).'" 
                            '.(intval(@$temp[0]->id)?'checked="checked"':'').' 
                            />';
                    ///end generating the check box matrix////
                    $table["rows"][$row][$cnt++]=$str;
                }                
                ////end Form section//
                $row++;
            }//end for
        }
        ///////end Rows/////
        
        
        $this->data["page_title"]="User Permission";
        $this->data["table_roles"]=theme_table($table);
        $this->render();
        ////end login form starts from here////        
	}
    
    
    /**
    * Form post, 
    * 
    */
    public function acl_update()
    {
        user_access("administer user role");//access check
        
        /**
        * The new permission is inserted. 
        * All unchecked permissions are removed,
        * if we find permission id in the posted value then
        * that remains unchanged.         
        */
        $permission=$this->input->post("permission");
        
        if(!empty($permission))
        {
            $t_perm=array();
            foreach($permission as $m=>$perm)
            {
                $temp=explode("###",$perm);
                $perm_value=trim($temp[0]);
                $perm_role_id=intval($temp[1]);
                $perm_id=intval(@$temp[2]);  
                
                $t_perm[]=array(
                    "admin_type_id"=>$perm_role_id,
                    "s_permission_name"=>$perm_value
                );
            }           
            /*pr($permission);
            pr($t_perm);*/  
               
            $this->admin_type_permission_model
                 ->acl_permission($t_perm);                     
        }
        
		
		/* feb 2014 when save clear all cache such after refresh anyone can see the effect */
		clear_allCache();    
        get_allThemes();  ///Re-scan the theme folder and update the db
		
        set_success_msg(message_line("saved success"));
        redirect( get_destination() );
        
    }///end of function    
    
    
    /**
    * Assigning permisions available 
    * No permissions. Users who have "administer user role" 
    * permission can also set permission for the users
    */
    /*public function my_profile_permission()
    {
        return array(
            "edit own profile"=>array(
                "title"=>"Edit own profile",
                "description"=>"If checked, then users under that role can modify his own profile.",
            ),
            
        );
    }//end welcome_permission*/
    
    /**
    * changing the access permission for single entry
    */
    public function ajaxPermissionUpdate()
    {
        $permission=$this->input->post('permission');
        $checked=$this->input->post('checked');
        if(!empty($permission))
        {
            $t_perm=array();
            $temp=explode("###",$permission);
            $perm_value=trim($temp[0]);
            $perm_role_id=intval($temp[1]);
            $perm_id=intval(@$temp[2]);  
            
            $t_perm=array(
                "admin_type_id"=>$perm_role_id,
                "s_permission_name"=>$perm_value
            );
                      
           /* pr($permission);
            pr($t_perm,1);   */
            
            $checked=='true'?$this->admin_type_permission_model->add_admin_type_permission($t_perm):$this->admin_type_permission_model->delete_admin_type_permission($t_perm);
                                   
        }
        
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */