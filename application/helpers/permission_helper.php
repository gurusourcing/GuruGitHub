<?php
/**
* Author: Sahinul Haque
* Date : 22Mar2013
* 
* Purpose : To provide Site wide Permission 
*   and security check related to user access 
*   control system.
* 
*   IF we have any special checking required while permission checking. 
*   The we have to a function within its own checking and the function must 
*   call the user_access($permission);
*/

function get_httpResponseCode($value)
{
$responses = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Time-out',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Large',
    415 => 'Unsupported Media Type',
    416 => 'Requested range not satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Time-out',
    505 => 'HTTP Version not supported',
  );
  
  if( is_int($value) )
    return $responses[$value];
  else
    return array_search($value,$responses);
    
}


function get_fixedAdminTypeId($admin_type_name="Super Admin",$ret="id",$get_fixed_ids=FALSE)
{
    $fixed_admintype_id=array();
    $CI=&get_instance();
    
    $CI->db->select("id,s_type");
    if(!empty($admin_type_name))
    {
        $CI->db->where("s_type",trim($admin_type_name));           
    }
    ///fetch only fixed ids
    if($get_fixed_ids)
    {
        $CI->db->where("i_not_deletable",1);
    }
    
    $rs=$CI->db->get("admin_type")->result();
    
    foreach($rs as $row )
    {
        $fixed_admintype_id[$row->s_type]=$row->id;
    }
    
    if($ret=="id")
    {
        return intval($fixed_admintype_id[$admin_type_name]);
    }
    
    return $fixed_admintype_id;
}


function check_userAdminType($type_name,$admin_type_id)
{
    if($admin_type_id==get_fixedAdminTypeId($type_name))
        return TRUE;
    else
        return FALSE;
}




/**
* Check whether user have a specific 
* permission. 
* Role : Super Admin will accure all permissions by default. 
* 
* ex- user_access("add user");
* @param mixed $permission
* @param mixed $redirect_deny_page, if set TRUE the when the permission is false 
*                   redirect to access deny page.
*                   if set to FALSE, then return the $access
*/
function user_access($permission,$redirect_deny_page=TRUE)
{   
    /*pr(__FUNCTION__);
    pr(debug_backtrace());*/
    
    
    $access=TRUE;
    if(!empty($permission)
        && is_string($permission)
    )
    {
        $CI=&get_instance();
        
        $user=get_userLoggedIn();//TODO
        $user=(!empty($user)?$user:get_adminLoggedIn());//if the loggedin user is admin
        
        if(check_userAdminType("Super Admin",@$user->admin_type_id))//allow super admin
            return TRUE;
        
        
        if($user)
        {
            $rs=$CI->db
                ->get_where("admin_type_permission",
                            array("admin_type_id"=>$user->admin_type_id,
                                "s_permission_name"=>trim($permission)
                            )
                )
                ->row();          
        }
        else//visitor
        {
                $rs=$CI->db
                ->get_where("admin_type_permission",
                            array("admin_type_id"=>get_fixedAdminTypeId("Visitors"),
                                "s_permission_name"=>trim($permission)
                            )
                )
                ->row();            
        }
        
                
        if(empty($rs))
            $access=FALSE;          
        
        /*pr($access);
        pr($rs);*/
        unset($rs);        
    }
    elseif(!$permission )//$permission=FALSE
        $access=FALSE;      
    else //allow access if $permission=TRUE
        $access=FALSE;
    
    
    if($access==FALSE
        && $redirect_deny_page
    )
    {
        goto_accessDeny();          
    }
    
    return $access;
    
    /*////allow function extending, hooking////////
    $all_controllers=get_filenames(APPPATH."/controllers");
    array_shift($all_controllers);//removing the index.html within controllers directory
    //pr($all_controllers);
    /////end allow function extending, hooking////*/
    
    //$arr = get_defined_functions();
    //pr($arr["user"]);
    //pr(get_declared_classes());
    //array get_class_methods ( mixed $class_name )
    
    //redirect('http://localhost/guru', 'location', 401);//access denied
    /*show_error("You are not authorized to access. Please contact site administrator.",
               get_httpResponseCode("Forbidden") ,
               "Access Denied");*/
}

/**
* Check multiple permissions in OR conditions. 
* Checks If any of the permission is TRUE.
* 
* @param mixed $permission, array of permissions
* @param mixed $access, function name to call
*/
function check_multiPermAccess($permission=array(),$access="user_access")
{
    $access=FALSE;
    if(!empty($permission))
    {
        foreach($permission as $perm )
        {
            $access=call_user_func_array( (!empty($access)?trim($access):"user_access"),
                                      array(trim($perm),FALSE)  
                                    );
            if($access)
                return TRUE;//no need to check further loop
        }
    }
    
    return $access;
}

function is_userLoggedIn($redirect_deny_page=FALSE)
{
    $CI=&get_instance();
    //pr($CI->session->userdata('user'),1);
    $user=$CI->session->userdata('user');
    if(!empty($user))
        return $user;
    else
    {
        if($redirect_deny_page)
            goto_accessDeny();
            
        return FALSE;
    }
}


function is_adminLoggedIn()
{
    $CI=&get_instance();
    $user=$CI->session->userdata('admin');
    if(!empty($user))
        return $user;
    else
        return FALSE;
}

/**
* Fetch the loggedin admin obj. 
* It is also possiable to get a particular field 
* from admin stdClass obj.
* 
* @param, $field=> 
* return stdClass obj
*/
function get_adminLoggedIn($field="")
{
    $CI=&get_instance();
    $admin=$CI->session->userdata("admin");  
    if(!empty($field))
        return @$admin->$field;
    else
        return $admin;
}


/**
* Fetch the loggedin user obj. 
* It is also possiable to get a particular field 
* from admin stdClass obj.
* 
* @param, $field=> 
* return stdClass obj
*/
function get_userLoggedIn($field="")
{
    /*if(!is_userLoggedIn())
        return FALSE;*/
    
    $CI=&get_instance();
    $user=$CI->session->userdata("user");
    
    if(!empty($field))
        return @$user->$field;
    else
        return $user; 
}


/**
* Modify or Add an field into existing the loggedin user obj. 
* It is also possiable to set a particular field 
* from admin stdClass obj.
* 
* @param, $fields=>array("column"=>"value to replace or modify") 
* return stdClass obj
*/
function set_userLoggedIn($fields=array())
{
    $CI=&get_instance();
    $user=$CI->session->userdata("user");
    
    if(!empty($fields))
    {
        foreach($fields as $col=>$val)
        {
            if(!empty($col) && !empty($val))
                @$user->$col=$val;
        }
    }
    
    $this->session->set_userdata(array("user"=>$user));
    return $user;
}


/**
* Redirect to access deny page, with 
* proper header response.
* 
*/
function goto_accessDeny()
{
    $CI=&get_instance();
    $msg=( rtrim($CI->router->fetch_directory(),"/")!="admin"?"frontend access deny":"access deny");
    
    show_error(message_line($msg),
           get_httpResponseCode("Forbidden") ,
           "Access Denied");
}

/**
* This function will return all prmissions
* applicable for all the controllers.
* Any controller that have implemented 
* hook_permission() => <controller name>_permission()
* will be called autometically, in the ACL.
* 
*  HOW TO Implement :- 
*   public function welcome_permission()
*   {
*       $item=array(
*           "view own profile"=>array(
*               "title"=>"View Own Profile",
*               "description"=>"Can view only own pofile."
*           ),
*       );
*       return $item;
*   }
*   
* 
*/
function get_permissions()
{
    /////allow function extending, hooking////////
    $all_controllers=get_filenames(APPPATH."/controllers"); 
    $items=array();
    if(!empty($all_controllers))
    {
        $cnt=count($all_controllers)-1;
        
        while($cnt>=0)
        {
            $class=pathinfo($all_controllers[$cnt], PATHINFO_FILENAME );
            $class=(class_exists($class)
                ? $class 
                : ( class_exists( ucfirst($class) )
                    ? ucfirst($class)
                    : (class_exists( ucwords($class) )
                        ? ucwords($class)
                        : FALSE    
                    )
                )
            );
           
            if( $class 
                && method_exists($class, strtolower($class)."_permission" ) 
            )
            {
                    $items=$items+call_user_func_array(array($class, 
                                        strtolower($class)."_permission"),array()
                                        );
            }            
            
            
            $cnt--;
        }///end while
    }
    
    
    //pr($items);
    return $items;
    //pr($all_controllers);
    /////end allow function extending, hooking/////    
}

?>
