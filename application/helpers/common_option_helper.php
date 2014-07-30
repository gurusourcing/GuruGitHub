<?php
/**
* Author: Sahinul Haque
* Date : 29Mar2013
* 
* Purpose : To provide common functionality 
*   for all dropdowns. 
*    
*/

/**
* Define the admin menus array.
* 
* @see, permission_helper.php, user_access()
*/
function adminMenus()
{
    $item=array();
    ///////General//////
    $item["General"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("home/dashboard"),
        /**
        * "access" is a permission function which will be called, mut be defined in permission_helper.php
        * 
        * if "access" and "access_permission" is empty the no access will be checked.
        * IF "access" is empty and "access_permission" is not empty then "user_access" function 
        * will be called to check the access permission.
        */
        "access"=>"user_access",
        /**
        * What permission to be checked by "access" function. 
        * We can check multiple permissions in "OR" condition. see below("Admins") for example.
        */
        "access_permission"=>array(
            "view admin dashboard","edit own profile","administer admin user",
            "view admin list","view own domain admin list","add admin",
            "add own domain admin","administer user role","clear cache",
            "administer admin user","view admin list","view own domain admin list",
            "add admin","add own domain admin","administer user role",
            "administer themes","select own domain theme","administer advertisements"
        ),
        /**
        * html to display before the menu name.
        * Used in Main Nav
        */
        "prefix"=>'<img src="'.site_url(get_theme_path()."images/icons/mainnav/dashboard.png").'" alt="" /><span>',
        "suffix"=>'</span>'
    );
    //sub menu of "General"
    /**
    * A sub menu "My Profile" will be created under 
    * "General" menu
    */
    $item["General"]["sub_menu"]["My Profile"]=array(
        "url"=>admin_base_url("my_profile"),
        "access"=>"user_access",
        "access_permission"=>array("edit own profile"),
        
    ); 
    $item["General"]["sub_menu"]["Clear Cache"]=array(
        "url"=>"",//admin_base_url("home/clear_cache"),
        "access"=>"user_access",
        "access_permission"=>array("clear cache"),
    );     
    $item["General"]["sub_menu"]["Clear Cache"]["sub_menu"]["Clear Theme Cache"]=array(
        "url"=>admin_base_url("home/clear_cache"),
        "access"=>"user_access",
        "access_permission"=>array("clear cache"),
    );    
       
//    $item["General"]["sub_menu"]["Admins"]["sub_menu"]["User Role"]=array(
//        "url"=>site_url("admin/user_role"),
//        "access"=>"user_access",
//        "access_permission"=>array("administer user role"),
//        "sub_menu"=>array(
//              "User Permission"=>array(
//                "url"=>site_url("admin/user_permission"),    
//                "access_permission"=>array("administer user role"), 
//              )
//        ),
//    );        
    $item["General"]["sub_menu"]["Admins"]=array(
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        /**
        * Here we will check "view admin list" OR "add admin" permission is TRUE.
        */
        "access_permission"=>array(
                "administer admin user","view admin list","view own domain admin list",
                "add admin","add own domain admin","administer user role",
            ),
    );   
        ///sub menu for "Admins"   
        $item["General"]["sub_menu"]["Admins"]["sub_menu"]["Manage Admins"]=array(
            "url"=>admin_base_url("admin_user"),
            "access"=>"user_access",
            "access_permission"=>array(
                "administer admin user",
                "view admin list",
                "view own domain admin list",
            ),
        );    
         $item["General"]["sub_menu"]["Admins"]["sub_menu"]["User Role"]=array(
            "url"=>admin_base_url("user_role"),
            "access"=>"user_access",
            "access_permission"=>array(
                "administer user role",
            ),
        );  
         $item["General"]["sub_menu"]["Admins"]["sub_menu"]["User Permission"]=array(
            "url"=>admin_base_url("user_permission"),
            "access"=>"user_access",
            "access_permission"=>array(
                "administer user role",
            ),
           
        ); 
         
        
//        $item["General"]["sub_menu"]["Admins"]["sub_menu"]["Add Admin User"]=array(
//            "url"=>site_url("admin/admin_user/operation/add"),
//            "access"=>"user_access",
//            "access_permission"=>array("add admin", "add own domain admin", "administer admin user"),
//            //testing nth level of sub menus//
//            /*"sub_menu"=>array(
//                "Sub1"=>array("url"=>site_url("home/dashboard"),
//                    "sub_menu"=>array("Sub1-1"=>array("url"=>""))
//                ),
//                "Sub2"=>array("url"=>site_url("home/dashboard")),
//            )*/
//            //end  testing nth level of sub menus//
//        );       
        ///end sub menu for "Admins"   
        
    ///Themes///
    $item["General"]["sub_menu"]["Themes"]=array(
        "url"=>admin_base_url("theme"),
        "access"=>"user_access",
        "access_permission"=>array("administer themes","select own domain theme"),
    ); 
    ///end Themes///   
    
    //Advertisements//
    $item["General"]["sub_menu"]["Advertisements"]=array(
        "url"=>admin_base_url("advertisements"),
        "access"=>"user_access",
        "access_permission"=>array("administer advertisements"),
    );
    //End of Advertisements//    
    //end sub menu of "General"
    ///////end General//////
    ///////configuration//////
    $item["Configuration"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array(
            "administer country","administer state","administer city",
            "administer zip","administer popular location",
            "administer zip location mapping","administer option",
            "administer user suggestion","administer document",
            "administer reserved keyword","administer cms"            
        ),
        /**
        * html to display before the menu name.
        * Used in Main Nav
        */
        "prefix"=>'<img src="'.site_url(get_theme_path()."images/icons/mainnav/forms.png").'" alt="" /><span>',
        "suffix"=>'</span>'        
    );
    // sub menu of "Location"
    $item["Configuration"]["sub_menu"]["Location"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array(

            ),
    );    
    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Country"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("country"),
        "access"=>"user_access",
        "access_permission"=>array("administer country"),
    ); 

    /*$item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add Country"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"admin/country/operation/add",
        "access"=>"user_access",
        "access_permission"=>array("administer country"),
    );*/   
    
    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["State"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("state"),
        "access"=>"user_access",
        "access_permission"=>array("administer state"),
    );
//    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add State"]=array(
//        ///When menu "General" clicked will goto this path//
//        "url"=>"admin/state/operation/add",
//        "access"=>"user_access",
//        "access_permission"=>array("administer state"),
//    );  
    
     $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["City"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("city"),
        "access"=>"user_access",
        "access_permission"=>array("administer city"),
    );
     
//    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add City"]=array(
//        ///When menu "General" clicked will goto this path//
//        "url"=>"admin/city/operation/add",
//        "access"=>"user_access",
//        "access_permission"=>array("administer city"),
//    );
    
     $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Zip"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("zip"),
        "access"=>"user_access",
        "access_permission"=>array("administer zip"),
    );
     
//    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add Zip"]=array(
//        ///When menu "General" clicked will goto this path//
//        "url"=>"admin/zip/operation/add",
//        "access"=>"user_access",
//        "access_permission"=>array("administer zip"),
//    );
    
         
    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Popular Location "]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("popular_location"),
        "access"=>"user_access",
        "access_permission"=>array("administer popular location"),
    );
     
//    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add Popular Location"]=array(
//        ///When menu "General" clicked will goto this path//
//        "url"=>"admin/popular_location/operation/add",
//        "access"=>"user_access",
//        "access_permission"=>array("administer popular_location"),
//    );
               
    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Zip Location Mapping "]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("zip_location_mapping"),
        "access"=>"user_access",
        "access_permission"=>array("administer zip location mapping"),
    );
     
//    $item["Configuration"]["sub_menu"]["Location"]["sub_menu"]["Add Popular Location"]=array(
//        ///When menu "General" clicked will goto this path//
//        "url"=>"admin/zip_location_mapping/operation/add",
//        "access"=>"user_access",
//        "access_permission"=>array("administer zip_location_mapping"),
//    );
        
    //end sub menu of "Location"     
    // sub menu of "Service"
    $item["Service"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array(
            "administer category","administer category service extended definition",
            "administer featured packages","administer featured services",
            "administer user services",
        ),
        /**
        * html to display before the menu name.
        * Used in Main Nav
        */
        "prefix"=>'<img src="'.site_url(get_theme_path()."images/icons/mainnav/tables.png").'" alt="" /><span>',
        "suffix"=>'</span>'  
    );    
     
    // sub menu of "Category , sub-Category"
    $item["Service"]["sub_menu"]["Category"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("category"),
        "access"=>"user_access",
        "access_permission"=>array("administer category"),
    );
    /*$item["Service"]["sub_menu"]["Sub Category"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"admin/subcategory",
        "access"=>"user_access",
        "access_permission"=>array("administer category"),
    );*/
    $item["Service"]["sub_menu"]["Extended Service"]=array(
            "url"=>admin_base_url("category_service_extended_definition"),
            "access"=>"user_access",
            "access_permission"=>array(
                "administer category service extended definition"
            )
        );            
    //end sub menu of "Category , sub-Category"  
    /////featured packages
    $item["Service"]["sub_menu"]["Featured Packages"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("featured_packages"),
        "access"=>"user_access",
        "access_permission"=>array("administer featured packages"),
    );
    /////end of featured packages
    
    ////Featured services
    $item["Service"]["sub_menu"]["Featured Services"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("featured_services"),
        "access"=>"user_access",
        "access_permission"=>array("administer featured services"),
    );
    //// end of featured services
    /////user services
    $item["Service"]["sub_menu"]["User Services"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_services"),
        "access"=>"user_access",
        "access_permission"=>array("administer user services"),
    );
    //end of user services
    //end sub menu of "Service"
	
	// sub menu of "Option"
    $item["Configuration"]["sub_menu"]["Option"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
      	"access_permission"=>array("administer option","administer user suggestion"),
    );    
    $item["Configuration"]["sub_menu"]["Option"]["sub_menu"]["Manage Option"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("option"),
        "access"=>"user_access",
        "access_permission"=>array("administer option"),
    ); 

    $item["Configuration"]["sub_menu"]["Option"]["sub_menu"]["Manage User Suggestion"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_suggestion"),
        "access"=>"user_access",
        "access_permission"=>array("administer user suggestion"),
    );
    
    $item["Configuration"]["sub_menu"]["Document"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("document"),///"" => will produce "javascript:void(0);"
        "access"=>"user_access",
          "access_permission"=>array("administer document"),  
    ); 
    $item["Configuration"]["sub_menu"]["Reserved Keyword"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("reserved_keyword"),///will produce "javascript:void(0);"
        "access"=>"user_access",
          "access_permission"=>array("administer reserved keyword"),
    ); 
    $item["Configuration"]["sub_menu"]["CMS"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("cms"),///will produce "javascript:void(0);"
        "access"=>"user_access",
          "access_permission"=>array("administer cms"),
    ); 
    
    $item["User"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array(
            "view user", "administer user", "user_doc_verification",
            "administer user doc verification","administer user save search",
            "administer user company","administer user report abuse",
            ),
        /**
        * html to display before the menu name.
        * Used in Main Nav
        */
        "prefix"=>'<img src="'.site_url(get_theme_path()."images/icons/mainnav/ui.png").'" alt="" /><span>',
        "suffix"=>'</span>'         
    );
    $item["User"]["sub_menu"]["Manage User"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("manage_user"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user"),
    );
     $item["User"]["sub_menu"]["User Document Verification"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_doc_verification"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user doc verification"),
    );
     $item["User"]["sub_menu"]["User Document Verification"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_doc_verification"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user doc verification"),
    );
    $item["User"]["sub_menu"]["User Save Search"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_save_search"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user save search"),
    );
    $item["User"]["sub_menu"]["List of Company"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_company"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user company"),
    );
     $item["User"]["sub_menu"]["Report Abuse"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("user_report_abuse"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("administer user report abuse"),
    );
    $item["Reports"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>"",///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("view payment history"),
        "prefix"=>'<img src="'.site_url(get_theme_path()."images/icons/mainnav/statistics.png").'" alt="" /><span>',
        "suffix"=>'</span>'
    ); 
    $item["Reports"]["sub_menu"]["Payments"]=array(
        ///When menu "General" clicked will goto this path//
        "url"=>admin_base_url("payment_history"),///will produce "javascript:void(0);"
        "access"=>"user_access",
        "access_permission"=>array("view payment history"),
       
    ); 
   
    
    
    
    return $item;
}

/**
* Generated the admn menus html. 
* 
* 
* @param mixed $menu
* @param mixed $is_submenu
*/
function get_adminMenusHtml($menu=array(),$is_submenu=FALSE)
{
    $CI=&get_instance();
    if($CI->hide_menus)
        return FALSE;

     $item="";            
    
    if(empty($menu) && !$is_submenu)
        $admin_menus=adminMenus();///all recorded menus
    elseif(!empty($menu) && $is_submenu) 
        $admin_menus=$menu;//submenu
    else
        return FALSE;
    
     $html="";   
    //we are confirmed that $admin_menus will never be empty//
    foreach($admin_menus as $title=>$menu_0)
    {       
        ////Checking menu permission///
        $access=TRUE;
        
        if(!empty($menu_0["access_permission"]))
            $access=check_multiPermAccess(@$menu_0["access_permission"],@$menu_0["access"]);
        ////end Checking menu permission///
        
        if( $access )
        {
            $sub_menus=get_adminMenusHtml(@$menu_0["sub_menu"],TRUE);
            $html.='<li>
                    <a href="'.(empty($menu_0["url"])?"javascript:void(0);":trim($menu_0["url"])).'" 
                    '.(!empty($sub_menus)?'class="exp"':"").' >
                    '.trim(@$menu_0["prefix"])
                    .trim($title)
                    .trim(@$menu_0["suffix"]).'
                    </a>
                    '.$sub_menus.'
                </li>';
        }
    }///end for
    
    
    if(empty($menu))
        $item='<ul class="nav" id="admin_menu">';///top level wrapper
    else
        $item='<ul>';////submenu wrapper
        
    $item.= $html;   
    $item.= '</ul>';
    
    //pr($item,1);
    
    return $item;
}


/**
* We will use this in admin theme section
* It scans for all themes and put ininto "theme" table.
*/
function get_allThemes()
{
    $allThemes=get_dir_file_info("theme", true);
    $ret_=array();
    if(!empty($allThemes))
    {
        foreach($allThemes as $folder=>$theme)
        {
            $info_path='theme/'.$folder.'/'.$folder.'.info';
            if( file_exists($info_path) )
            {
                require $info_path;
                $ret_[$folder]["theme_info"]=$theme_info;
                $ret_[$folder]["theme_path"]='theme/'.$folder.'/';///root path
                unset($theme_info);
                update_theme_master($ret_[$folder],$folder);
            }
        }
    }
    //pr($allThemes);
    
    /**
    * Now another situation arises
    * If the theme folder has removed or renamed
    * Then we have to remove it from the db as well.
    */
    $CI=&get_instance();
    $CI->load->model("theme_model");
    $rec=$CI->theme_model->theme_load("");
    if(!empty($rec))
    {
        $theme_names=array_keys($ret_);
        foreach($rec as $t)
        {
            /*pr(array($t,$theme_names));*/
            if(!in_array($t->s_theme,$theme_names))
            {
                ///delete from db
                $CI->theme_model->delete_theme(array("id"=>$t->id));
                //pr($t->s_theme); 
            }
        }
    }
        
    return $ret_;
}

/**
* get the current theme path or 
* the path of the theme in parameter.
* @param mixed $theme_name
*/
function get_theme_path($theme_name="")
{
    $path="";
    if(empty($theme_name))
    {
        $theme=get_userTheme();
        return $theme["theme_path"];
    }
    
    return "theme/".$theme_name."/";
}

function get_userTheme()
{
    /**
    * check which template to fetch
    * for : fe or admin
    */
    $CI=&get_instance();
    $CI->load->model("theme_model");
    $theme=array();
    
    $folder=rtrim($CI->router->fetch_directory(),"/");
    if($folder=="admin")//admin theme requested
    {
        $theme=get_adminLoggedIn("admin_theme_settings");
        if(empty($theme))
        {
            $CI->load->model("theme_model");
            $temp=$CI->theme_model->theme_load(
                            array("i_is_default_admin"=>1)
                        );
            $theme=unserialize($temp[0]->s_theme_settings);
        }
    }    
    else//frontend theme requested
    {
        $theme=get_userLoggedIn("user_theme_settings");///loggedin as user
        if(empty($theme))
            $theme=get_adminLoggedIn("user_theme_settings");///logged in as admin but viewing fe. 
            
        /**
        * Still the theme is empty, 
        * then load the defualt theme
        */
        if(empty($theme))
        {
            $CI->load->model("theme_model");
            $temp=$CI->theme_model->theme_load(
                            array("i_is_default_fe"=>1)
                        );            
            $theme=unserialize($temp[0]->s_theme_settings);
        }                
    }
    //pr($theme);
    return $theme;
}

/**
* checks if the theme already exists if yes then undate 
* the settings field. 
* 
* else insert a new theme
* 
*/
function update_theme_master($theme_info,$theme_name)
{
    $CI=&get_instance();
    $CI->load->model("theme_model");
    $ret=$CI->theme_model->theme_load(array( "s_theme"=>$theme_name  ));
    if( !empty($ret) )
    {
        $CI->theme_model->update_theme(
                    array("s_theme_settings"=>serialize($theme_info)),
                    array( "id"=>$ret[0]->id )
                    );
    }
    else
    {
        $CI->theme_model->add_theme(
                    array(
                        "s_theme"=>$theme_name,
                        "s_theme_settings"=>serialize($theme_info),
                    ));        
    }
        
    return $ret;
}


/**
* Set and get variables from cache. 
* This function operates in various ways.
* 1> To get a value from chache
* 2> To get a value from chache using default value, 
*    if value not chached earlier, then set it.
* 
* All cached variables will live for 1hrs duration.
* 
* TODO ::
* Only file cache supported in CI and enabled here.
* dll required for memchache.
* 
* @param mixed $name, is the variable name that to be cached **mandatory
* @param mixed $value
*/
function cache_var($name,$value="")
{
    if(empty($name))
        throw new Exception("Must Provide A variable name for caching.");
    
    /**
    * TODO Cache the menu html here
    * Only file cache supported in CI.
    * dll required for memchache
    */
    
    $CI=&get_instance();    
    //$CI->load->driver('cache');
    $CI->load->driver('cache', array('adapter' => 'file'));
    $c_value = $CI->cache->get($name);
    //pr($CI->cache->is_supported("file"));
    //pr($c_value);
    
    /**
    * When the value is already cached then return the value
    * as soon as possiable.
    */
    if(!empty($c_value))
        return $c_value;    
    
    
    /**
    *  The value has provided. Then set the new value into cache
    * and return it.
    */
    if(!empty($value))
    {
        //$CI->cache->clean();//This will clear all cache.
        //$CI->cache->delete($name);//tested
        $CI->cache->save($name, $value, 3600); ///live for 1hrs
        
        ///using memcached
        //$CI->cache->memcached->save('cPermission', 'permission', $this->permission);
        
        return $value;
    }
    
    return false;
}

/**
* Clear all file based cache.
* 
* @param string, $var_name, if this is empty then clear all cache.
*                else delete the var from cache
*/
function clear_allCache($var_name="")
{
    $CI=&get_instance();
    $CI->load->driver('cache', array('adapter' => 'file'));
    if(empty($var_name))
        $CI->cache->clean();
    else
        @$CI->cache->delete($name);
        
    return TRUE;
}

/**
* render into a table
* 
* @param mixed $options= > array(
*               "header"=>array(
*                   0=>array(
*                       "title"=>"header1",
*                       "attributes"=>array("class"=>"","id"=>"", "rel"=>""),
*                       "sort"=>"asc",//asc|desc
*                   ),
*                   .....
*               
*               ),
*               "rows"=>array(
*                   0=>array(
*                       "data col1",
*                       "data col2",
*                       "data col3",
*                       ....
*                   ),
*                   .....      
*               ),
*               "footer"=>'<div><h2></h2></div>',
*               "attributes"=>array("class"=>"","id"=>"", "rel"=>""),
*               "no result text"=>"No information found"
*           );
*/
function theme_table($options=array())
{
    if(empty($options))
        return FALSE;
    
    $html=''; 
    ////Header attributes
    $header_attr='';
    if(!empty($options["attributes"])) 
    {
        foreach($options["attributes"] as $k=>$v)
        {
            $header_attr.=$k.'="'.$v.'" ';
        }        
    }
    else
    {
        $header_attr='cellpadding="0" cellspacing="0" width="100%" class="tDefault tMedia"';
    }  
    ////Header
    $html='<table '.$header_attr.'><thead><tr>';
    foreach($options["header"] as $k=>$v)
    {
        $tem_attr='';
        if(!empty($v["attributes"])) 
        {
            foreach($v["attributes"] as $m=>$a)
            {
                $tem_attr.=$m.'="'.$a.'" ';
            }        
        }        
        $html.= '<td '.$tem_attr.'>'.$v["title"].'</td>';
    }
    $html .='</tr></thead>';   
    ////table body
    $html.='<tbody>';
    if(!empty($options["rows"]))
    {
        foreach($options["rows"] as $k=>$v)
        {
            $html.='<tr>';      
            foreach($v as $cell){
                $html.= '<td>'.$cell.'</td>';
            } 
            $html.='</tr>';
        }        
    }
    else
    {
        $html.='<tr><td colspan="'.count($options["header"]).'">'.$options["no result text"].'</td></tr>';
    }
    $html .='</tbody>';
    
    if(!empty($options["footer"]))
    {
        $html.='<tfoot>';
        $html.='<tr><td colspan="'.count($options["header"]).'">'.$options["footer"].'</td></tr>';
        $html.='</tfoot>';
    }
    
    
         
    $html .='</table>';
    
    return $html;
}

/**
*
* @param arary $option 
*       => array(
*           "upload_container"=> "f_container", //*required the main wrapper which will include the the uploader codes. 
*           "field" =>  "f_logo" , //*required input file id 
*           "allow_maxUploadFiles" => 10, //the numbers of files to upload default is 10. for a single file uploading set 1
*           "previewMaxWidth" => 80, //the max width in preview window
*           "previewMaxHeight" => 80,  //the max height in preview window 
*           "acceptFileTypes"  => /(\.|\/)(gif|jpe?g|png)$/i, //the allowed file types 
*           "maxFileSize"      =>  5000000, //in bytes   
*      );
* 
*/
function theme_jqUploader($option=array())
{
    $CI=&get_instance();
    $CI->load->view("admin/theme/jqFileUploader.tpl.php",$option);
}

/**
* After uploading within form post section.
* This function will parse the hidden fields values
* and return the absolute server path.
* So that we can easily move the file into
* resource directory.
* 
* @param mixed $url
*/
function parse_jqUploader($value)
{
    if(empty($value))
        return FALSE;
    
    $file_parms=explode("###",$value);
    $ret_=array();
    
    if(is_array($file_parms))
    {
        foreach($file_parms as $f)
        {
            if(!empty($f))
            {
                $tmp=array();
                parse_str($f,$tmp); 
                $server_path= str_replace(base_url(),'',$tmp["url"]) ;
                $server_path=set_realpath($server_path,FALSE);
                $server_path=rtrim($server_path,"\\");
                $server_path=rtrim($server_path,"/");
                
                $info=$tmp+pathinfo($server_path)+array("upload_path"=>$server_path);
                $thumb_path=$info["dirname"]."/thumbnail/".$info["basename"];
                
                $ret_[]=$info+array("thumb_path"=>$thumb_path);
            }//end if
        }//end for
    }//end if
    
    return $ret_;
}


function dd_admin_type()
{
    $CI=&get_instance();
    $CI->load->model("admin_type_model");
    $all_types=$CI->admin_type_model->admin_type_load(array("i_not_deletable"=>0));//exclude the super admin.
    $opt=array(""=>"--Select--");
    if(!empty($all_types))
    {
        foreach($all_types as $k=>$at)
        {
            $opt[$at->id]=$at->s_type;
        }
    }
    return $opt;
}

/**
* Used where we have to show dropdowns 
* of domains. 
* 
* @param mixed $condition, array("s_domain"=>'guru.in',"s_sub_domain"=>'doctors')
*                          OR "s_domains_domain='guru.in' AND s_sub_domain='doctors' OR url='doctors.guru.in'" 
*/
function dd_domain($condition=array())
{
    $CI=&get_instance();
    $CI->load->model("franchisee_domain_model");
    $all_=$CI->franchisee_domain_model->franchisee_domain_load($condition);
    $opt=array(""=>"--Select--","guru.in"=>"guru.in");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $temp=complete_domain($at->s_domain,$at->s_sub_domain);
            $opt[$temp]=$temp;
        }
    }
    return $opt;
}

/**
* We need a common streagety to create the domain name 
* in url. using domain and subdomain concatination.
* 
* @param mixed $domain=> "guru.in"
* @param mixed $subdomain=>"doctors"
*/
function complete_domain($domain,$subdomain)
{
    return $subdomain.".".$domain; 
}

/**
* Returns the franchisee subdomain+domain name
* stored in franchisee_domain, 
* Also lookup through the franchisee_domain_map tables.
* 
*/
function get_userDomain($return="s_furl")
{
    $CI=&get_instance();
    $current_domain= $CI->config->item('current_domain');
    
    $CI->load->model('franchisee_domain_alias_model','fda_model');
    
    $condition="fda.s_url='".$current_domain."' OR fd.s_url='".$current_domain."'";
    $rec=$CI->fda_model->franchisee_domain_alias_load($condition);
    if(!empty($rec))
    {
        if($rec[0]->$return)
            return $rec[0]->$return;
    }
    
    return FALSE;    
}

function dd_theme()
{
    $CI=&get_instance();
    $CI->load->model("theme_model");
    $ret=$CI->theme_model->theme_load(array("i_active"=>1,"i_is_default_admin"=>0));
    $opt=array();
    if(!empty($ret))
    {
        foreach($ret as $r)
        {
            $info=unserialize($r->s_theme_settings);
            $opt[$r->id]=$info["theme_info"]["name"];
        }
    }
    return  $opt;   
}

function dd_country()
{
    $CI=&get_instance();
    $CI->load->model("country_model");
    $all_=$CI->country_model->country_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_country;
        }
    }
    return $opt;
}


function dd_user()
{
    $CI=&get_instance();
    $CI->load->model("user_model");
    $all_=$CI->user_model->user_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_user_name;
        }
    }
    return $opt;
}

function dd_zip()
{
    $CI=&get_instance();
    $CI->load->model("zip_model");
    $all_=$CI->zip_model->zip_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_zip;
        }
    }
    return $opt;
}
function dd_popular_location()
{
    $CI=&get_instance();
    $CI->load->model("popular_location_model");
    $all_=$CI->popular_location_model->popular_location_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_location;
        }
    }
    return $opt;
}
function dd_city()
{
    $CI=&get_instance();
    $CI->load->model("city_model");
    $all_=$CI->city_model->city_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_city;
        }
    }
    return $opt;
}


function dd_service($condition=array())
{
    $CI=&get_instance();
    $CI->load->model("user_service_model");
    $all_=$CI->user_service_model->user_service_load($condition);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_service_name;
        }
    }
    return $opt;
}


function dd_state()
{
    $CI=&get_instance();
    $CI->load->model("state_model");
    $all_=$CI->state_model->state_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_state;
        }
    }
    return $opt;
}

function dd_category($condition=array())
{
    $CI=&get_instance();
    $CI->load->model("category_model","modc");
    $all_=$CI->modc->category_load($condition,$limit=NULL,$offset=NULL,$order_by="s_category ASC");
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_category;
        }
    }
    return $opt;
}


function dd_sub_category($condition=array())
{
    $CI=&get_instance();
    $CI->load->model("subcategory_model");
    $all_=$CI->subcategory_model->subcategory_load($condition);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_sub_category;
        }
    }
    return $opt;
}


function dd_option_type()
{
    /*$CI=&get_instance();
    $CI->load->model("category_model","modc");
    $all_=$CI->modc->category_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_category;
        }
    }*/
	
	/*$opt=array(""=>"--Select--",
				"institute"			=>	"Institute",
				"degree"			=> 	"Degree",
				"specilization"		=> 	"Specilization",
				"designation"		=> 	"Designation",
				"job_description"	=> 	"Job description",
				"industry_type"		=> 	"Industry type",
				"language"			=> 	"Language",
			);
    return $opt;*/
    
    $CI=&get_instance();
    $CI->load->model('option_model');
    $all_=$CI->option_model->fetch_option_type();
   
   // pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   
   return $opt;    
    
    
}

function dd_user_suggestion_type ()
{
    /*$CI=&get_instance();
    $CI->load->model("category_model","modc");
    $all_=$CI->modc->category_load(array());
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at->id]=$at->s_category;
        }
    }*/

    /*$opt=array(""=>"--Select--",
                "category"          =>   "Category",
                "institute"         =>    "Institute",
                "degree"            =>     "Degree",
                "specilization"     =>     "Specilization",
                "designation"       =>     "Designation",
                "job_description"   =>     "Job description",
                "industry_type"     =>     "Industry type",
                "language"          =>     "Language",
            );
    return $opt;*/
    
    
    $CI=&get_instance();
    $CI->load->model('user_suggestion_model');
    $all_=$CI->user_suggestion_model->fetch_user_suggestion_type();
   
   // pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   
   return $opt;    
    
}

function dd_document_type()
{
    $CI=&get_instance();
    $CI->load->model('doc_verification_model');
    $all_=$CI->doc_verification_model->fetch_doc_verification_type();
   
   // pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   
   return $opt;
}
/*
 *@param $start,$end
 * @return mix|array
 */
function dd_order($start=-20,$end=20)
{
    $opt = array();
    for ($index = $start; $index <= $end; $index++) {
        $opt[$index] = $index;
    }
   return $opt;
}

/**
* TODO Testing this DD
*/
function dd_abuse_action(){
   //return array( 'not_guilty'=>'not_guilty','punishment'=>'punishment','guilty'=>'guilty');
    $CI=&get_instance();
    $CI->load->model('user_report_abuse_model');
    $all_=$CI->user_report_abuse_model->fetch_abuse_action_status();
   
   // pr($all_);
    //$opt=array(""=>"--Select--");
    $opt=array();
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   return $opt;   
}

function dd_abuse_for()
{
    $CI=&get_instance();
    $CI->load->model('user_report_abuse_model');
    $all_=$CI->user_report_abuse_model->fetch_abuse_for_status();
   
   // pr($all_);
    //$opt=array(""=>"--Select--");
    $opt=array();
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=message_line("abuse ".$at);
        }
    }
   
   return $opt;
}


/*function dd_category_type()
{
    $CI=&get_instance();
    $CI->load->model('category_model');
    $all_=$CI->category_model->category_load(array());
    //pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
           $opt[encrypt($at->id)]=$at->s_category;
        }
    }
   
   return $opt;
}*/


function dd_langProficency()
{
    $opt=array(
                "proficient" =>    "Proficient",
                "beginner"  =>     "Beginner",
                "expert"    =>     "Expert",
            );
    return $opt;
}
   
function get_themeLogoDir()
{
    return "resources/logo/";
}


function dd_status($condition=array())
{
    $CI=&get_instance();
    $CI->load->model('payment_model');
    $all_=$CI->payment_model->fetch_status();
   
   // pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   
   return $opt;
}

/**
* creating advertisement type ['large','small','banner_landscape','banner_potrait'] drpdownlist
*/

function dd_advertisement_type()
{
    $CI=&get_instance();
    $CI->load->model('advertisement_model');
    $all_=$CI->advertisement_model->fetch_advertisement_type();
   
   // pr($all_);
    $opt=array(""=>"--Select--");
    if(!empty($all_))
    {
        foreach($all_ as $k=>$at)
        {
            $opt[$at]=humanize($at);
        }
    }
   
   return $opt;
}

function dd_advertisement_impresion_type()
{
     return array( 'cpc'=>' CPC ','cpm'=>' CPM ');
}

/* function to get user service Mar 2014 
* to keep user service selected
* see @ all_service_provided/make_featured.tpl
*/
function makeOptionServices($condition=array(),$sid='')
{    
	$CI = & get_instance();	
	$CI->load->model('user_service_model');
	$mix_value = $CI->user_service_model->user_service_load($condition);
	
	$s_option = '';
	if(!empty($mix_value))
    {
        foreach($mix_value as $k=>$at)
        {
			$s_select = '';
			if($at->id == $sid)
				$s_select = " selected ";
			$s_option .= "<option $s_select value='".$at->id."' >".$at->s_service_name."</option>";
        }
    }
	unset($res, $mix_value, $s_select, $mix_where, $s_id);
	return $s_option;
}

/**
* Fetch the CMS  obj. 
* It is also possiable to get a particular field 
* from CMS stdClass obj.
* @author Kallol Basu <mail@kallol.net>
* @param, $field=> 
* return stdClass obj
*/
function get_cms($id,$field="")
{
    $CI=&get_instance();
    $CI->load->model('cms_model');
    $all_=$CI->cms_model->cms_load($id);
    if(!empty($field))
        return @$all_->$field;
    else
        return $all_;
}

function theme_user_navigation($option=array())
{
    $CI=&get_instance();
    $CI->load->view("../../".get_theme_path()."templates/user--navigation.tpl.php",$option);
}


    /**
     * Profile short description
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid User Id
     * @return object $user_data_obj [profile_complete],[no_of_services],[s_display_name],[uid]
     */
     function short_desc($uid = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_model');
        $user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
        $temp=$CI->user_model->user_short_description($uid);
        $user_data_obj[0]->total_services=@$temp[0]->total_services;
        $user_data_obj[0]->i_profile_complete_percent=@$temp[0]->i_avg_profile_complete_percent;
        return set_user_profile_name($user_data_obj[0]);
        
    }
    
    /**
     * Display Profile Name
     * Simple logic to display s_name in case of display name is not present
     * incase s_name is not present, then s_user_name .
     * @author Kallol Basu <mail@kallol.net>
     * @param object $userObj User Object[must have any of above data]
     * @return object Same data object with [name] the desided name
     */
    
    function set_user_profile_name($userObj) {
        $userObj->name =isset($userObj->s_name)?$userObj->s_name:(isset($userObj->s_display_name)?$userObj->s_display_name:(isset($userObj->s_user_name)?$userObj->s_user_name:'N/A'));
        return $userObj;
    }
    
/**
* field access for user profile
* 0=> Public, 1=> Private, 2=> Members only
*     
* @param mixed $col
* @param string $ret, "value" or "key"
* @return the value=>"Public" OR key=>0
*/
function user_public_private($val,$ret="value")
{
    $upp=array(
        0=> "Public", 
        1=> "Private", 
        2=> "Members only"
    );
    
    if(empty($val))
        $val=($ret=="value"?0:"Public");
    
    return ($ret=="value"?$upp[$val]:array_search($val,$upp));
}
    
function format_date($dt="",$fmt="d-m-Y")
{
    return date($fmt,strtotime(trim($dt)));
}


/**
* @param array $value['code'] => email verification code
* 
*/

    function theme_signup_confirmation_mail($value=array())
    {
        $str=$value["s_name"].', <br/>
                <p>
                Your account at '.site_name().' has been activated. <br/>
                Please verify your account by clicking this link:<br/>
                '.anchor('account/verify_email/'.$value['s_verification_code'],"Email Verification Click Here").'
                </p>
                <p>
                This link can only be used once to verify your account and will lead you to a page where you can 
                login to your account.<br/>
                You will be able to log in at :'.anchor('account/signin',site_url('account/signin')).' <br/>
                username: '.$value["s_user_name"].'<br/>
                password: Your password
                </p>
                <p>--  '.site_name().' team</p>';

        return $str;
    }
    
    function theme_signup_welcome_mail($value=array())
    {
         $str=$value["s_name"].', <br/>
                <p>
                Thank you for registering at '.site_name().'.<br/> 
                You may now log in by clicking this link or copying and pasting it to your browser:<br/>
                '.anchor('account/signin',site_url('account/signin')).'
                </p>
                <p>
                You will be able to log in at :'.anchor('account/signin',site_url('account/signin')).' <br/>
                username: '.$value["s_user_name"].'<br/>
                password: '.(!empty($value["s_password"])?$value["s_password"]:"Your password").'<br/>
                </p>
                <p>--  '.site_name().' team</p>';

        return $str;
    }
        
    function theme_fbsignup_success_mail($value=array())
    {
         $str=$value["s_name"].', <br/>
            <p>
            Thank you for registering at '.site_name().' using.<br/> 
            You may now log in by clicking this link or copying and pasting it to your browser:<br/>
            '.anchor('account/signin',site_url('account/signin')).'
            </p>
            <p>
            You will be able to log in at '.anchor('account/signin',site_url('account/signin')).' : <br/>
            username: '.$value["s_user_name"].'<br/>
            password: '.$value["s_password"].'
            </p>
            <p>--  '.site_name().' team</p>';     
		
		/* below code is not to send password for facebook registration 29 Nov 2013*/
		/*$str=$value["s_name"].', <br/>
            <p>
            Thank you for registering at '.site_name().' using.<br/> 
            You may now log in by clicking this link or copying and pasting it to your browser:<br/>
            '.anchor('account/signin',site_url('account/signin')).'
            </p>
            <p>
            You will be able to log in at '.anchor('account/signin',site_url('account/signin')).' : <br/>
            username: '.$value["s_user_name"].'<br/>
            </p>
            <p>--  '.site_name().' team</p>';  */ 

        return $str;
    }

    /*
    * Layout generater of Forget Password mail
    * @author Kallol Basu <mail@kallol.net>
    * @param mix|array [s_name],[s_password]
    * @return string HTML layout of Forget Password
    */
    function theme_forget_password_mail($value=array())
    {
        $str=$value["s_name"].', <br/>
        <p>
        A request to reset the password for your account has been made at '.site_name().'.<br/> 
        You may now log in by clicking this link or copying and pasting it to your browser:<br/>
        '.anchor('account/signin',site_url('account/signin')).'
        </p>
        <p>
        You will be able to log in at '.anchor('account/signin',site_url('account/signin')).' : <br/>
        username: '.$value["s_user_name"].'<br/>
        password: '.$value["s_password"].'
        </p>
        <p>--  '.site_name().' team</p>';
        
        return $str;
    }


    /*
    * Layout generater of Contact  mail
    * @author Kallol Basu <mail@kallol.net>
    * @param mix|array [name],[email],[purpose],[description]
    * @return string HTML layout of  Contact  mail
    */
    function theme_contact_mail($posted)
    {
        return 'Hi Admin , <br/>Here is your new Contact Request.
                <br/><b>Email </b>:'.$posted['email'].'
                <br/><b>Name </b>:'.$posted['name'].' 
                <b>Purpose</b>:'.$posted['purpose'].'
                <b>Description</b>:'.$posted['description'].'
                <br/> Regards, Guru.in';
    }
    
    function theme_mobile_verify_mail($value=array())
    {
         $str=$value["s_name"].', <br/>
                <p>
                Your request for verifying mobile has been submitted at '.site_name().'.<br/> 
                Your mobile verification code is '.$value["s_mobile_verify_code"].'.<br/> 
                You may now verify your mobile by clicking this link or copying and pasting it to your browser:<br/>
                '.anchor('account/verify_mobile',site_url('account/verify_mobile')).'
                </p>
                <p>
                You must login in at :'.anchor('account/signin',site_url('account/signin')).' <br/>
                </p>
                <p>--  '.site_name().' team</p>';

        return $str;
    } 
	
	function theme_email_verify_mail($value=array())
    {
         $str=$value["s_name"].', <br/>
                <p>
                Thank you for verify your email at '.site_name().'.<br/> 
                You may now log in by clicking this link or copying and pasting it to your browser:<br/>
                '.anchor('account/signin',site_url('account/signin')).'
                </p>
                <p>
                You will be able to log in at :'.anchor('account/signin',site_url('account/signin')).' <br/>
                username: '.$value["s_user_name"].'<br/>
                </p>
                <p>--  '.site_name().' team</p>';

        return $str;
    }   
    
    /**
     * Block data generater of Your Services
     * @see dashboard|Your Services
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid User Id
     * @param string $field Return Particular data field|Default return All fields
     * @return object $user_data_obj 
     */
     function get_user_services($uid = NULL,$field = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_service_model');
        $user_data_obj=$CI->user_service_model->user_service_load(array('u.id'=>$uid));
        $returned_obj = null;
        if($field){
            foreach ($user_data_obj as $key => $obj_value) {
                 $returned_obj[$key]->$field =$obj_value->$field ;
            }
           return $returned_obj;
        }
            
        else
            return $user_data_obj;

    }
    
    
    /**
     * Block data generater of Your Services
     * @see dashboard|Your Services
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid User Id
     * @return object $return_object|['i_fb_verified']['i_mobile_verified']['i_email_verified'] 
     */
     function get_user_verification($uid = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_model');
        $user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
        $return_object = array();
        $return_object ['i_fb_verified'] = empty($user_data_obj[0]->s_facebook_credential)?0:1;
        $return_object ['i_mobile_verified']= isset($user_data_obj[0]->i_mobile_verified)?$user_data_obj[0]->i_mobile_verified:0;
        $return_object ['i_email_verified'] = isset($user_data_obj[0]->i_email_verified)?$user_data_obj[0]->i_email_verified:0;
        return $return_object;
    }
    
     /**
     * Block data generater of Profile Picture
     * if user is a company it shows company pic else user pic
     * @see dashboard| Profile Picture
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid User Id
     * @return string url of image
     */
     function get_dashboard_profile_pic($uid = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_model');
        
        $ret_="";
        
        /**
        * Actual user profile to be returned
        */
        $user_data_obj=$CI->user_model->user_load(intval($uid));
        $ret_=$user_data_obj->s_profile_photo;
        
        
         ////if the user is a company owner
        if($user_data_obj->i_is_company_owner){
            $CI->load->model('user_company_model');
            $company_data_obj=$CI->user_company_model->user_company_load(intval($user_data_obj->comp_id));
            
            if($company_data_obj->i_active)
                $ret_=$company_data_obj->s_logo;
        }
        
        return $ret_; 
    }
    
     /**
     * Block data generater of Profile Name
     * if user is a company it shows company name else user name
     * @see dashboard| Profile name
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid User Id
     * @return string Profile Name
     */
     function get_dashboard_profile_name($uid = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_model');
        $user_data_obj=$CI->user_model->user_load(intval($uid));
        if($user_data_obj->i_is_company_owner){
            $CI->load->model('user_company_model');
            $company_data_obj=$CI->user_company_model->user_company_load(intval($user_data_obj->comp_id));
            return $company_data_obj->s_company;
        } else {
           $user_data_obj= set_user_profile_name($user_data_obj);
            return $user_data_obj->s_name;
        }
    }
     /**
     * Block html generater of Your Services
     * @see dashboard|Your Services
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id|Deault current logged in user id
     * @return string $output_html  
     */
    
    function theme_block_dashboard_services($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
                
        $CI=&get_instance();
        $option['view_data']=get_user_services($uid);
        return $CI->load->view("../../".get_theme_path()."templates/dashboard--service.block.tpl.php",$option,true);
    }
    
    
     /**
     * Block html generater of Verification
     * @see dashboard|verification
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_dashboard_verification($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $option['view_data']=  get_user_verification($uid);
        return $CI->load->view("../../".get_theme_path()."templates/dashboard--verification.block.tpl.php",$option,true);
    }
    
    
    
     /**
     * Block html generater of Profile Picture
     * @see dashboard|Profile Picture
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_dashboard_profile_pic($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $option['view_data']=  get_dashboard_profile_pic($uid);
        $option['view_profile_link']=site_url(short_url_code($uid));
        return $CI->load->view("../../".get_theme_path()."templates/dashboard--profile_pic.block.tpl.php",$option,true);
    }
    
     /**
     * Block html generater of Profile Picture
     * @see dashboard|Profile Picture
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    function theme_block_user_profile_pic($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_model');
        
        $ret_="";
        
        /**
        * Actual user profile to be returned
        */
        $user_data_obj=$CI->user_model->user_load(intval($uid));
        $ret_=$user_data_obj->s_profile_photo;
        
        
        $option['view_data']=  $ret_;
        //$option['view_data']=  get_dashboard_profile_pic($uid);
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--profile_pic.block.tpl.php",$option,true);
    }
    
    /**
     * Block html generater of Short Url
     * @see user_profile|Short URL
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_user_profile_short_url($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $option['uid']=  $uid;
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--short_url.block.tpl.php",$option,true);
    }
    
    /**
     * Block html generater Share With Friend
     * @see user_profile|Share With Friend
     * @author Kallol Basu <mail@kallol.net>
     * @param int $id ,pk
     * @param $type , "user","company","service"
     * @param $disp , "button"=>profile pages as buttons,"icon"=>search page as icon
     * @return string $output_html  
     */
    function theme_block_user_profile_share_with_friend($id = NULL,$type="user",$disp="button") {
        /*if(empty($id) || trim($type)!="user")
            $id = get_userLoggedIn('id');*/
        $CI=&get_instance();
        $option['form_token']=encrypt($id);
        $option['type']=$type;
        $option['link']= short_url_code($id,$type);  
        $option['disp']=$disp;

        return $CI->load->view("../../".get_theme_path()."templates/user_profile--share_with_friend.block.tpl.php",$option,true);
    }
    
    
    /**
     * Block html generater Share via Facebook
     * @see user_profile|Share via Facebook
     * @author Kallol Basu <mail@kallol.net>, mainak
     * @param int $id,pk
     * * @param $type , "user","company","service"
     * @param $disp , "button"=>profile pages as buttons,"icon"=>search page as icon     
     * @return string $output_html  
     */
    function theme_block_user_profile_share_via_facebook($id = NULL,$type="user",$disp="button") {
        $uid = get_userLoggedIn('id');
        
        $CI=&get_instance();
        $option['view_data']="";
        switch($type)
        {
            case "company":
                $CI->load->model('user_company_model');
                $company_data_obj=$CI->user_company_model->user_company_load(intval($id));
                $option['view_data']=$company_data_obj->s_logo;         
            break;
            case "service":
                $CI->load->model('user_service_model');
                $service_data_obj=$CI->user_service_model->user_service_load(intval($id));
                $option['view_data']=  get_dashboard_profile_pic($service_data_obj->uid);     
            break;            
            default: /// for user the profile picture 
                $option['view_data']=  get_dashboard_profile_pic($id);
            break;
        }
        
        $option['form_token']=encrypt($id);
        $option['type']=$type;
        $option['link']= short_url_code($id,$type);  
        $option['disp']=$disp;
        $option['id']=$id;
        
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--share_via_facebook.block.tpl.php",$option,true);
    }
    
     /**
     * Block html generater Share via twitter
     * @see user_profile|Share via twitter
     * @author Kallol Basu <mail@kallol.net>, mainak
     * @param int $id ,pk
     * @param $type , "user","company","service"
     * @param $disp , "button"=>profile pages as buttons,"icon"=>search page as icon     
     * @return string $output_html  
     */
    function theme_block_user_profile_share_via_twitter($id = NULL,$type="user",$disp="button") {
        $CI=&get_instance();
        $option['view_data']="";
        switch($type)
        {
            case "company":
                $CI->load->model('user_company_model');
                $company_data_obj=$CI->user_company_model->user_company_load(intval($id));
                $option['view_data']=$company_data_obj->s_logo;         
            break;
            case "service":
                $CI->load->model('user_service_model');
                $service_data_obj=$CI->user_service_model->user_service_load(intval($id));
                $option['view_data']=  get_dashboard_profile_pic($service_data_obj->uid);     
            break;            
            default: /// for user the profile picture 
                $option['view_data']=  get_dashboard_profile_pic($id);
            break;
        }
        
        $option['form_token']=encrypt($id);
        $option['type']=$type;
        $option['link']= short_url_code($id,$type);  
        $option['disp']=$disp;
        
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--share_via_twitter.block.tpl.php",$option,true);
    }
    
    
    
     /**
     * Block html generater Report Abuse
     * @see user_profile|Report Abuse
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_user_profile_report_abuse($uid = NULL) {
        /*if(!$uid)
            $uid = get_userLoggedIn('id');
        $option['view_data']=  get_dashboard_profile_pic($uid);*/
        
        
        //uncomment after completion
        if($uid==get_userLoggedIn('id'))
            return FALSE;
        
        $CI=&get_instance();
        $option=array("uid"=>$uid,"s_absue_for_id"=>encrypt($uid),"e_absue_for"=>"user");
                
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--report_abuse.block.tpl.php",$option,true);
    }
	
	
	/**
     * Block html generater of suggestion block
     * @see dashboard|Profile Picture=>Education
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_user_suggestion_block($uid = NULL,$enum_type="degree") {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_model');
        
        $ret_="";        
        
        $option['view_data']=  $ret_;        
		$option['enum_type']=  $enum_type; 
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--suggestion.block.tpl.php",$option,true);
    }
	
	
	function add_user_suggestion($param)
	{
		if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_suggestion_model');
		
		$ret_="";
		if(!empty($param))
		{
			$ret_ = $CI->user_suggestion_model->add_user_suggestion($param);			
		}
		return $ret_;
	}
	
	function check_duplicate_suggestion($param)
	{
		if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_suggestion_model');
		
		$ret_="";
		if(!empty($param))
		{
			$ret_ = $CI->user_suggestion_model->check_duplicate_suggestion($param);			
		}
		return $ret_;
	}
    
    
    
    /**
     * Block html generater Report Abuse
     * @see company_profile|Report Abuse
     * @param int $comp_id company id
     * @return string $output_html  
     * @author mainak
     */
    
    function theme_block_company_profile_report_abuse($comp_id = NULL) {
        /*if(!$uid)
            $uid = get_userLoggedIn('id');
        $option['view_data']=  get_dashboard_profile_pic($uid);*/
        
        
        //uncomment after completion
        if($comp_id==get_userLoggedIn('i_is_company_owner'))
            return FALSE;
        
        $CI=&get_instance();
        $CI->load->model("user_company_model");
        $comp_data_obj=$CI->user_company_model->user_company_load(intval($comp_id));
        $uid=$comp_data_obj->uid;
        
        $option=array("uid"=>$uid,"s_absue_for_id"=>encrypt($comp_id),"e_absue_for"=>"company");
                
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--report_abuse.block.tpl.php",$option,true);
    }
    
    
        
    
    /**
     * Block html generater Report Abuse
     * @see company_profile|Report Abuse
     * @param int $service_id service id, service owner id $uid
     * @author mainak 
     * @return string $output_html  
     */
    
    function theme_block_service_profile_report_abuse($service_id = NULL,$uid=NULL) {
        
       /*$uid=get_userLoggedIn('id');*/
         
        $CI=&get_instance();
        $CI->load->model('user_service_model');
        
        $service_data_obj=$CI->user_service_model->user_service_load(array("s.uid"=>intval($uid),"s.id"=>intval($service_id)));

        if($service_data_obj[0]->uid==get_userLoggedIn('id'))
                return FALSE; ///if login user is owner of the service
                
        $option=array("uid"=>intval($service_data_obj[0]->uid),"s_absue_for_id"=>encrypt($service_id),"e_absue_for"=>"service");
                
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--report_abuse.block.tpl.php",$option,true);
    }
    
    
     /**
     * Block html generater Facebook Fan
     * *on 4Oct2013, as per client request, 
     *  this section has changed to fetch 
     * Facebook friends who are listed in guru.
     * 
     * @see user_profile|Report Abuse
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    function theme_block_user_profile_facebook_fan($uid = NULL) {
        
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $option['uid']=  $uid;
        
        /**
        * getting fb friends from "user_fb_list"
        */
        $friends = find_all_friend_and_their_friend(get_userLoggedIn('id'));
        $option['friends']=$friends;
        
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--facebook_fan.block.tpl.php",$option,true);
    }
    
     /**
     * Block html generater Rank
     * @see user_profile|Rank, view count, recomendation count
     * @author Kallol Basu <mail@kallol.net>
     * @edited by Mainak
     * @param int $id, pk
     * @param $type , "user","company","service"     
     * @return string $output_html  
     */
    
    function theme_block_user_profile_rank($id = NULL,$type="user") {
        /*if(!$uid)
            $uid = get_userLoggedIn('id');*/
        $CI=&get_instance();
        $option['view_data']=  get_view_count_profile($id,$type);
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--rank.block.tpl.php",$option,true);
    }
    
    /**
     * Block html generater Connection
     * @see user_profile|See the Connection
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id
     * @return string $output_html  
     */
    
    function theme_block_user_profile_connection($uid = NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_model');
        $CI->load->model('user_fb_list_model');
        
        /**
        * User connection section.
        * Here user can view their friends label wise. 
        * 
        * @var mixed
        */
          
         
        $viewer=get_userLoggedIn('id');
        $viewing=$uid;
		
        $option['chain_html']=array();
        if($viewer!=$viewing)
        {
            $temp_ = find_connected_chain_within_friends($viewer,$viewing);  
            //$temp_ = find_connected_chain_within_friends(23,73);//testing  			
            $option['chain_html'] = $temp_["chain_html"];           
        }
            
      	//pr($option['friend_list']);            
       	$option['viewing']=$viewing;  
        //facebook login
        $user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
        $option['view_data']= unserialize(@$user_data_obj[0]->s_facebook_credential);
		
		$viewer_data_obj=$CI->user_model->user_load(array('u.id'=>$viewer));
        $option['viewer_data']= unserialize(@$viewer_data_obj[0]->s_facebook_credential);
		
        return $CI->load->view("../../".get_theme_path()."templates/user_profile--connection.block.tpl.php",$option,true);
    }
    
     
      /**
     * Get Short Url Code 
     * @uses short_url_code Generater Short Url Code
     * @see user_profile|See the Connection
     * @author Kallol Basu <mail@kallol.net>
     * @param int $uid user id|company_id, Service_id
     * @param $arg, "company", "service" , "user"
     * @return string $output_html  
     */
    function short_url_code($uid = NULL,$type="user") {
        
       /**
       * fetch from cache.
       */
       $ret_=cache_var($type."-shorturl-".$uid);        
       if($ret_)
        return $ret_;           
        
        if($type=='company')
        {   
           $CI=&get_instance();
           $CI->load->model('user_company_model');
           $company_data_obj=$CI->user_company_model->user_company_load(intval($uid));
           /**
           * set short url into cache.
           * if and only if i_is_short_url_editable=0
           */
           if(intval($company_data_obj->i_is_short_url_editable)==0)
           {
               cache_var($type."-shorturl-".$uid,$company_data_obj->s_short_url);             
           }           
           
           return $company_data_obj->s_short_url;  
        }
        else if($type=='service')
        {
           $CI=&get_instance();
           $CI->load->model('user_service_model');
           $service_data_obj=$CI->user_service_model->user_service_load(intval($uid));
           
           /**
           * set short url into cache.
           * if and only if i_is_short_url_editable=0
           */
           if(intval($service_data_obj->i_is_short_url_editable)==0)
           {
               cache_var($type."-shorturl-".$uid,$service_data_obj->s_short_url);             
           }            
           return $service_data_obj->s_short_url;          
        }
        else{
            if(!$uid)
                $uid = get_userLoggedIn('id');
                
            $CI=&get_instance();
            $CI->load->model('user_model');
            //$user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
            $user_data_obj=$CI->user_model->user_load(intval($uid));
           /**
           * set short url into cache.
           * if and only if i_is_short_url_editable=0
           */
           if(intval($user_data_obj->i_is_short_url_editable)==0)
           {
               cache_var($type."-shorturl-".$uid,$user_data_obj->s_short_url);             
           }            
            
            return $user_data_obj->s_short_url;
        }
        
       }
       
   
   /**
   *@author Mainak
   * checks if the url is editable and the loggedin user is the owner 
   * 
   * @param pk of the table, type  (default type ="user")   
   * returns the value of i_is_short_url_editable field (0 OR 1)
   */
   function is_short_url_editable($id='',$type='user')
   {
        $loggedIn=get_userLoggedIn('id');
        if($type=='company')
        {   
           $CI=&get_instance();
           $CI->load->model('user_company_model');
           $company_data_obj=$CI->user_company_model->user_company_load(intval($id));
           //return $company_data_obj->i_is_short_url_editable ;
           return ($company_data_obj->i_is_short_url_editable && $company_data_obj->uid==$loggedIn);  
        }
        else if($type=='service')
        {
           $CI=&get_instance();
           $CI->load->model('user_service_model');
           $service_data_obj=$CI->user_service_model->user_service_load(intval($id));
           //return $service_data_obj->i_is_short_url_editable;       
           return ($service_data_obj->i_is_short_url_editable && $service_data_obj->uid==$loggedIn);       
        }
        else{
            if(!$id)
                $id = $loggedIn;
                
            $CI=&get_instance();
            $CI->load->model('user_model');
            $user_data_obj=$CI->user_model->user_load(intval($id));
            //return $user_data_obj->i_is_short_url_editable;
            return ($user_data_obj->i_is_short_url_editable && $user_data_obj->id==$loggedIn);
        }           
   }
       
    /**
     *  s_short_url is generated by default at the time of registration.
     * @author Mainak 
     * @param int $uid user id, anchor tag style[optional], image tag style[optional]
     * @return string $output_html , user thumb profile picture 
     */
    function theme_user_thumb_picture($uid = NULL,$anchor_style=NULL,$img_style=NULL) {
        if(!$uid)
            $uid = get_userLoggedIn('id');
        $CI=&get_instance();
        $CI->load->model('user_model');
        $user_data_obj=$CI->user_model->user_load(intval($uid));
        $source=(isset($user_data_obj->s_profile_photo)?$user_data_obj->s_profile_photo:'resources/no_image.jpg');
        return '<a href="'.site_url($user_data_obj->s_short_url).'" '.$anchor_style.'><img src="'.site_url($source).'" height="33" width="33" '.$img_style.'></a>';
    }
    
    /**
     * Block data generater of view count, profile Rank
     * @see user profile
     * @author Mainak Das
     * @param int $id, pk, 
     * @param $type , "user","company","service"
     * @return integer view count of user profile, rank of the profile, recomendation count
	 * i_verified_value added feb
     */
     function get_view_count_profile($id = NULL,$type="user") {
         
        $CI=&get_instance();
        
        switch(trim($type))
        {
            case "company":
                $CI->load->model('user_company_model');
                $company_data_obj=$CI->user_company_model->user_company_load(intval($id));
                // company owner id
                $uid=$company_data_obj->uid;
                // profile view count
                $data['view_count']=$company_data_obj->i_view_count;      
                // rank, only possiable for services because it is related to service and user
                $data['rank']=0;   
                break;
            case "service":
                $CI->load->model('user_service_model');
                $CI->load->model('user_rank_model');
                $service_data_obj=$CI->user_service_model->user_service_load(intval($id));
                // service owner id
                $uid=$service_data_obj->uid;
                // profile view count
                $data['view_count']=$service_data_obj->i_view_count;  
                
                // rank, only possiable for services because it is related to service and user
                $data['rank']=0;
				$log_id = get_userLoggedIn("id")?get_userLoggedIn("id"):"0"; // apr 2014 added if user not login
				
                $rankS=$CI->user_rank_model->user_rank_load(array(
                                "uid"=>$log_id,
                                "service_id"=>intval($id)
                        ));
				//echo "uid=>".$log_id.", "."service_id=>".intval($id);
                //pr($rankS,1);
				if(!empty($rankS))
                {
                    $rankS=$rankS[0];
                    $data['rank']=(intval($rankS->i_featured_value)
                            +intval($rankS->i_fb_level_value) 
                            +intval($rankS->i_active_level_value)
                            +intval($rankS->i_end_recommended_value)
                            +intval($rankS->i_profile_completion_value)
							+intval($rankS->i_verified_value)
                            );
                }
                
                
            break;            
            default: /// for user  
                $user_data_obj=$CI->user_model->user_load(intval($id));
                // user id
                $uid=$id;
                // profile view count
                $data['view_count']=$user_data_obj->i_view_count;      
                // rank, only possiable for services because it is related to service and user
                $data['rank']=0;   
            break;
        }
        
        /*
            // profile view count
            $CI->load->model('user_model');
            $user_data_obj=$CI->user_model->user_load(array('u.id'=>$uid));
            $data= array();
            $data['view_count']=$user_data_obj[0]->i_view_count;
            // rank
            $data['rank']=0;
        */
        
       //recommendation count
        $CI->load->model('user_service_recommendation_model');
        ////Auto Pagination
        $CI->user_service_recommendation_model->pager["base_url"]=base_url();
        $CI->user_service_recommendation_model->pager["uri_segment"]=4;
        $user_recommendation_obj=$CI->user_service_recommendation_model->user_service_recommendation_load(array('ur.e_status'=>'approved' , 'ur.uid'=>$uid));
        
        $data['recommendation']=$CI->user_service_recommendation_model->pager['total_rows'];
       
        return $data;
    }
       
    /**
     * Display Name Or Profile Name
     * Simple logic to display s_name in case of display name is not present
     * incase s_name is not present, then s_user_name .
     * @author Mainak Das
     * @param user id, 
     * @param $ret, anchor or name
     * @return the desired name
     */
    function get_user_display_name($user_id,$ret='anchor',$chop_char=0)
    {
       $CI=&get_instance();
       
       $CI->load->model('user_model');
       $user_data_obj=$CI->user_model->user_load(array('u.id'=>$user_id));
       //pr($user_data_obj);
       $name =isset($user_data_obj[0]->s_display_name)?$user_data_obj[0]->s_display_name:(isset($user_data_obj[0]->s_name))?$user_data_obj[0]->s_name:'N/A';
       if(!empty($chop_char))
            $name=character_limiter($name,$chop_char);
       
       if($ret=='anchor')
            return '<a href="'.site_url($user_data_obj[0]->s_short_url).'">'.$name.'</a>';
       else
             return $name;
            
    }
    
    /**
     * Simple logic to find if any user is employee of a company or not
     * @author Mainak Das
     * @param user id, 
     * @return 'true'=> not an employee, 'false'=> employee 
     */
    
    function is_not_company_employee($uid=null)
    {
       $CI=&get_instance();
       if(empty($uid))
            $uid=get_userLoggedIn('id');
       
       $CI->load->model('user_model');
       $user_data_obj=$CI->user_model->user_load(intval($uid));
       //pr($user_data_obj);
      // return $user_data_obj->i_is_company_emp > 0 ? false : true;
      
      if(empty($user_data_obj))
      {
        $user_data_obj=new stdClass();  
        $user_data_obj->i_is_company_emp=0;            
      }         
      
      return !$user_data_obj->i_is_company_emp;
    }
    
    /**
     * Simple logic to find if any user is owner of a company or not
     * @author Mainak Das
     * @param user id, 
     * @return 'true'=> not an owner, 'false'=> owner 
     */
    function is_not_company_owner($uid=null)
    {
       $CI=&get_instance();
       if(empty($uid))
            $uid=get_userLoggedIn('id');
       
       $CI->load->model('user_model');
       $user_data_obj=$CI->user_model->user_load(intval($uid));
       //pr($user_data_obj);
       //return $user_data_obj->i_is_company_owner > 0 ? false : true;
       
          if(empty($user_data_obj))
          {
            $user_data_obj=new stdClass();  
            $user_data_obj->i_is_company_owner=0;            
          }         
          
          return !$user_data_obj->i_is_company_owner;
    }
    
    /**
    * If country is not selected then select 
    * user's country from user_deetails table
    */
    function get_globalCountry()
    {
        $CI=&get_instance();
        $global_country_id=$CI->session->userdata("global_country_id");    
        if(empty($global_country_id))
        {
            $global_country_id=get_userLoggedIn("country_id");
        }
        
        /**
        * if user is not loggedin, then return the 
        * default country India
        */
        if(empty($global_country_id))
        {
            //$global_country_id=1;//India=>1 
            $global_country_id=2;//USA=>2
        }        
        
        return intval($global_country_id);
    }
    
    /**
    * @param (int)company id, (int)service id
    * @return no of service providers of a service
    */
    function total_company_service_provider($comp_id,$service_id){
       $CI=&get_instance();
       $CI->load->model('user_company_employee_model');
       $rec=$CI->user_company_employee_model->user_company_employee_load(array(
                                                                           "u.e_status"=>"active",
                                                                           "uce.comp_id"=>$comp_id,
                                                                           "uce.i_active"=>1,
                                                                           ));
       
       $service_providers=array();
       if(!empty($rec))
       {
           foreach($rec as $k=>$e)
           {
                $t=unserialize($e->service_ids);
                if(empty($t))
                    continue;
                //pr($t);
                if(in_array($service_id,$t))// checks if the service is present in array
                    $service_providers[]=$e->uid;                  
           }
       }
       
       return count($service_providers); 

   }
   
    /**
    * @param (int)company id, (int)service id
    * @return array of service providers of a service
    */
    function get_company_service_provider($comp_id,$service_id){
       $CI=&get_instance();
       $CI->load->model('user_company_employee_model');
       $rec=$CI->user_company_employee_model->user_company_employee_load(array(
                                                                            "u.e_status"=>"active",
                                                                            "uce.comp_id"=>$comp_id,
                                                                            "uce.i_active"=>1,
                                                                            ));
       
       $service_providers=array();
       if(!empty($rec))
       {
            foreach($rec as $k=>$e)
            {
                $t=unserialize($e->service_ids);
                if(empty($t))
                    continue;
                
                //pr($t);
                if(in_array($service_id,$t))// checks if the service is present in array
                {
                    $service_providers[]=array(
                        "uid"=>$e->uid,
                        "s_title"=>$e->s_title,
                        "dt_registration"=>$e->dt_registration,
                        "dt_last_login"=>$e->dt_last_login,
                        "s_profile"=>addslashes(theme_user_thumb_picture($e->uid,"",'class="alignleft"').
                            '<strong>'.get_user_display_name($e->uid).'</strong>'.
                            '<p class="short"><span>'.$e->s_title.'</span></p>'
                            ),
                    );
                }              
            }
       }
       
       return $service_providers; 

   }   
   
       /**
     * Display service  Name
     * Simple logic to display s_service_name 
     * @author Mainak Das
     * @param service id, 
     * @param $ret, anchor or name
     * @return the desired name
     */
    
    function get_service_name($service_id,$ret='anchor')
    {
       $CI=&get_instance();
       
       $CI->load->model('user_service_model');
       $data_obj=$CI->user_service_model->user_service_load(intval($service_id));
       //pr($data_obj);
       $name =isset($data_obj->s_service_name )?$data_obj->s_service_name:'N/A';
       if($ret=='anchor' && $name!='N/A')
            return '<a href="'.site_url($data_obj->s_short_url).'">'.$name.'</a>';
       else
             return $name;
            
    }
    
    /**
     * Block html generater of Company Profile Picture
     * @see company dashboard|Profile Picture
     * @param int $comp_id company id
     * @return string $output_html  
     */
    
    function theme_block_company_profile_pic($comp_id = NULL) {
        $CI=&get_instance();
        $CI->load->model('user_company_model');
        $data=$CI->user_company_model->user_company_load(intval($comp_id));
        //pr($data);
        if(intval($data->i_active)==1)
        {
               $option['view_data']= $data->s_logo; 
               return $CI->load->view("../../".get_theme_path()."templates/company_profile--profile_pic.block.tpl.php",$option,true);    
        }
        else
            return FALSE;
        
    }
    
    /**
    * comany profile upload path
    */
    function get_themeCompanyLogoDir()
    {
        return "resources/company/";
    }
    
    /**
     * Block html generater of Short Url
     * @see service_profile|Short URL
     * @param int $service_id service id
     * @return string $output_html  
     */
    
    function theme_block_service_profile_short_url($service_id = NULL) {
       $CI=&get_instance();
       $option['service_id']=  $service_id;
       return $CI->load->view("../../".get_theme_path()."templates/service_profile--short_url.block.tpl.php",$option,true);
    }  
    
    
    /**
     * Block html generater of Short Url
     * @see company_profile|Short URL
     * @param int $comp_id company id
     * @return string $output_html  
     */
    
    function theme_block_company_profile_short_url($comp_id = NULL) {
       $CI=&get_instance();
       $option['comp_id']=  $comp_id;
       return $CI->load->view("../../".get_theme_path()."templates/company_profile--short_url.block.tpl.php",$option,true);
    } 
    
    
    /**
    * Generate and check unique short url.  
    * This short url is unique through 
    * users, company, services tables. 
    * @param, $url, if url is supplied then it checks the uniqueness and return it
    *               if $url is not unique then it generates a new one and return it.
    */
    function generate_unique_shortUrl($short_url="")
    {
        $CI=&get_instance();
        
        if(empty($short_url))
            $short_url=random_string('alnum', 5);
        
        //checking in user_company table
        $CI->load->model("user_company_model");
        $condition=array("uc.s_short_url"=>$short_url);
        $companyrs =$CI->user_company_model->user_company_load($condition); 
        
        //checking in user_service table
        $CI->load->model("user_service_model");
        $condition=array("s.s_short_url"=>$short_url);
        $servicers =$CI->user_service_model->user_service_load($condition);   
        
        //checking in user table
        $CI->load->model("user_model");
        $condition=array("u.s_short_url"=>$short_url);
        $users =$CI->user_model->user_load($condition);                
        
        if(empty($companyrs) 
            && empty($servicers) 
            && empty($users) 
        )
            return $short_url;
        else
            geneate_unique_shortUrl();//recurssion
        
    }
    
    /**
    * fetch the company id of a user
    * 
    * @param mixed $uid
    * @see, controllers/service_profile.php, add_service()
    */
    function get_userCompany($uid=null)
    {
       $CI=&get_instance();
       if(empty($uid))
            $uid=get_userLoggedIn('id');
       
       $CI->load->model('user_model');
       $user_data_obj=$CI->user_model->user_load(intval($uid));
       return @$user_data_obj->comp_id;
    }
        
    /**
    * Edit company profile or service profile.
    * For logged in user only.
    * 
    * @param, $profile, => "user","company","service"
    *               if empty then it determines the user profile link or company profile link
    */
    function get_editProfileLink($profile="")
    {
        
        if(empty($profile))
        {
            $CI=&get_instance();
            $uid=get_userLoggedIn("id");
            $CI->load->model('user_model');
            $user_data_obj=$CI->user_model->user_load(intval($uid));    
            if(@$user_data_obj->comp_id)//has a company 
                return "company_profile";
            else
                return "user_profile";
        }
        else
            return trim($profile)."_profile";
            
    }
    
     /**
     * Block data generater of Profile Name
     * if user is a company it shows company name else user name
     * @see views/fe/service_profile/index.tpl.php
     * @param int $uid User Id
     * @param $anchor , if True then return the anchored profile name
     * @return string Profile Name Or Company name
     */
     function get_profile_name($uid = NULL,$anchor=TRUE) {
        $CI=&get_instance();
        $CI->load->model('user_model');
        $user_data_obj=$CI->user_model->user_load(intval($uid));
        
        /**
        * Actual user profile to be returned
        */
        $ret_="";
        $user_data_obj= set_user_profile_name($user_data_obj);
        if($anchor) 
            $ret_='<a href="'.site_url($user_data_obj->s_short_url).'" >'.$user_data_obj->s_name.'</a>';
        else
            $ret_=$user_data_obj->s_name;        
        
        
        ////if the user is a company owner
        if($user_data_obj->i_is_company_owner){
            $CI->load->model('user_company_model');
            $company_data_obj=$CI->user_company_model->user_company_load(intval($user_data_obj->comp_id));
            if(intval($company_data_obj->i_active)==1)
            {
                 if($anchor)
                    $ret_= '<a href="'.site_url($company_data_obj->s_short_url).'" >'.$company_data_obj->s_company.'</a>';
                else
                    $ret_=$company_data_obj->s_company;     
            }
        }///end if
        
        return $ret_;
    }  
    
    /***
    * company employee role dropdown  
    */
    function dd_employeeRole()
    {
        $CI=&get_instance();
        $CI->load->model("user_company_employee_model");
        $all_=$CI->user_company_employee_model->fetch_company_employee_role();
       // pr($all_);
        $opt=array();
        if(!empty($all_))
        {
            foreach($all_ as $k=>$at)
            {
                $opt[$at]=humanize($at);
            }
        }
       
       return $opt;
    }
    
    /**
    * @param (int)company id, (int)service id
    * @return array of service providers of a service
    */
    function dd_company_service_provider($comp_id){
       $CI=&get_instance();
       $CI->load->model('user_company_employee_model');
       $all_=$CI->user_company_employee_model->user_company_employee_load(array(
                                                                            "u.e_status"=>"active",
                                                                            "uce.comp_id"=>$comp_id,
                                                                            "uce.i_active"=>1,
                                                                            
                                                                            ));
       
        $opt=array(""=>"Select Service Provider from list");
        if(!empty($all_))
        {
            foreach($all_ as $k=>$sp)
            {
                $opt[$sp->uid]=get_user_display_name($sp->uid,"name").
                                    (!empty($sp->s_title)?' ['.$sp->s_title.']':"");
            }
        }
       
       return $opt;

   }     
   
    /**
    * checks if the employee is assigned to the 
    * service.
    * @param (int)company id, (int)service id
    * @return true if service is assigned to that employee.
    */
    function is_company_service_assigned($comp_id,$service_id,$uid){
       $CI=&get_instance();
       $CI->load->model('user_company_employee_model');
       $rec=$CI->user_company_employee_model->user_company_employee_load(array(
                                                                            "u.e_status"=>"active",
                                                                            "uce.comp_id"=>intval($comp_id),
                                                                            "uce.i_active"=>1,
                                                                            "uce.uid"=>intval($uid)
                                                                            ));
       
       if(!empty($rec))
       {
          $e=$rec[0];
          $t=unserialize($e->service_ids);
          if(empty($t))
            return false;
          //pr($t);
          if(in_array($service_id,$t))// checks if the service is present in array
            return true;
       }
       
       return false;
   }   
   
   
   /**
   * experience range
   * @return jeson_encoded value of year
   * @see search engine
   * @see, search_model.php, refine_search()
   * **donot change the values, ex- 0 to 2 years is used for parsing in refine_search()
   */
   function save_search_experience_range()
   {
      return json_encode(array( "0 to 2 years", "2 to 4 years","4 to 6 years"
                            ,"6 to 8 years","8 to 10 years","more than 10 years"
      ));  
   } 
   
    /***
    * service Experience dropdown  
    * @see, controllers/service_profile.php
    * @see, search_model.php, refine_search()
    */
    function dd_experience_range()
    {
        $all_=array("2"=> "0 to 2 years", "4"=>"2 to 4 years","6"=>"4 to 6 years"
                            ,"8"=>"6 to 8 years","10"=>"8 to 10 years","60"=>"more than 10 years"
      );
        $opt=array();
        foreach($all_ as $k=>$at)
        {
            $opt[$k]=trim($at);
        }
       
       return $opt;
    }   
    
   /**
   * tution_fee range
   * @return jeson_encoded value of range
   * @see search engine
   * @see, search_model.php, refine_search()
   * **donot change the values, ex- 1 to 100 is used for parsing in refine_search()
   */
   function save_search_tution_fee_range()
   {
      return json_encode(array( "1 to 100", "101 to 200","201 to 300"
                            ,"301 to 400","401 to 500","501 to 600"
                            ,"601 to 700","701 to 800"
      ));  
   } 
   
   
   /**
   * tution_fee range and price range
   * @return the matched value of maximum of range
   * @see service profile
   */
   function setMaxRangeDummy($val="100")
   {
   		$ret = "";
      	if($val<=100)
			$ret = 100;
		else if($val>100 && $val<=200)
			$ret = 200;
		else if($val>100 && $val<=200)
			$ret = 200;
		else if($val>200 && $val<=300)
			$ret = 300;
		else if($val>300 && $val<=400)
			$ret = 400;
		else if($val>400 && $val<=500)
			$ret = 500;
		else if($val>500 && $val<=600)
			$ret = 600;
		else if($val>600 && $val<=700)
			$ret = 700;
		else if($val>700 && $val<=800)
			$ret = 800;
		else if($val>800)
			$ret = 1000;
		else
			$ret = 100;
			
		return $ret;
   }     


/**
* If a user is verified 
* i_email_verified AND i_doc_verified 
* AND i_mobile_verified
* 
* @param mixed $uid
* @param $check_verify, to check a specific column, 
*         i_email_verified or i_doc_verified or i_mobile_verified
*/
function is_guru_verified($uid,$check_verify=FALSE)
{
    $CI=&get_instance();
    $CI->load->model("user_model");
    $user=$CI->user_model->user_load(intval($uid));
    if(empty($user))
        return FALSE;
        
    if($check_verify)
        return (bool) $user->$check_verify;
        
    if($user->i_email_verified
        && $user->i_doc_verified
        && $user->i_mobile_verified
    )
        return TRUE;
        
    return FALSE;
}

/**
* DD of global search fields
*/
function dd_search_type()
{
    $opt=array("service"=>"Service","user"=>"People");
    return $opt;
}
function dd_location_type()
{
    $opt=array("zip"=>"Zip","city"=>"City");
    return $opt;
}
function dd_distance()
{
    $opt=array(
        ""=>"Distance",
        "1-10"=>"1-10 Miles",
        "10-50"=>"10-50 Miles",
        "50-100"=>"50-100 Miles",
        );
    return $opt;
}