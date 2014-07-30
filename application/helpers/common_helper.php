<?php
/**
* Author: Sahinul Haque
* Date : 22Mar2013
* 
* Purpose : To provide common functionality 
*   for all purposes. 
*/

function pr($v,$exit=0)
{
    echo '<pre>';
    if(is_array($v)||is_object($v))
        print_r($v);
    else
        var_dump($v);
    echo '</pre>';

    if($exit)
        exit;
}

/**
* Get Site Name
*/
function site_name()
{
    $CI =& get_instance();
    return $CI->config->item("site_name");
}

/**
* Get Site Name
*/
function site_mail()
{
    $CI =& get_instance();
    return $CI->config->item("site_mail");
}


/**
* Get Current domain name. 
* Required for admin and franchisee 
*/
function current_domain()
{
    $CI =& get_instance();

    /*if(ENVIRONMENT=="development")///for localhost 
        return "gurusourcing.com";
    else*/
        return $CI->config->item("current_domain");    
}

/**
* Saves the error messages into session.
* @param string $s_msg
* @return void
*/
function set_error_msg($s_msg)
{
    $ret_="";
    if(trim($s_msg)!="")
    {
        $o_ci=&get_instance();
        $ret_=$o_ci->session->userdata('error_msg');
        $ret_.='<p>'.$s_msg.'</p>';
        $o_ci->session->set_userdata('error_msg',$ret_);
    }
}

/**
* Saves the error messages into session.
* 
* @param string $s_msg
* @return void
*/
function set_success_msg($s_msg)
{
    $ret_="";
    if(trim($s_msg)!="")
    {
        $o_ci=&get_instance();
        $ret_=$o_ci->session->userdata('success_msg');
        $ret_.='<p>'.$s_msg.'</p>';
        $o_ci->session->set_userdata('success_msg',$ret_);
        //echo $o_ci->session->userdata('success_msg');
    }
}


/**
* Displays the success or error or both messages.
* And removes the messages from session
* 
* @param string $s_msgtype, "error","success","both" 
* @return void
*/
function show_msg($s_msgtype="both")
{
    $o_ci=&get_instance();
    $error_msg  =$o_ci->session->userdata('error_msg');
    $success_msg=$o_ci->session->userdata('success_msg');

    if(empty($error_msg) && empty($success_msg))
        return FALSE;


    switch($s_msgtype)
    {
        case "error":
            if(!empty($error_msg))
            {
                echo '<div class="nNote nFailure">'.$error_msg.'</div>
                <div class="divider"><span></span></div>';                
            }
            $o_ci->session->unset_userdata('error_msg');
            break;    
        case "success":
            if(!empty($success_msg))
            {
                echo '<div class="nNote nSuccess">'.$success_msg.'</div>
                <div class="divider"><span></span></div>';                
            }
            $o_ci->session->unset_userdata('success_msg');
            break;    
        default:
            if(!empty($success_msg))
                echo '<div class="nNote nSuccess">'.$success_msg.'</div>';
            if(!empty($error_msg))
                echo '<div class="nNote nFailure">'.$error_msg.'</div>';
            if(!empty($success_msg) || !empty($error_msg))
                echo '<div class="divider"><span></span></div>';
            $array_items = array('success_msg' => '', 'error_msg' => '');
            $o_ci->session->unset_userdata($array_items);
            unset($array_items);
            break;            
    }
}

/**
* Returns the error or success message text
*/
function message_line($title="")
{
    $msg=array(
    "invalid user"=>"Invalid email or password. Please try again.",
    "access deny"=> 'You are not authorized to access. Please contact site administrator. 
    <br/>'. anchor(admin_base_url("home"),"Login As Admin") ,
    "frontend access deny"=> 'You are not authorized to access. Please contact site administrator. 
    <br/>'. anchor("account/signin","Please Login") ,                        
    "saved success" => "Information saved successfully.",
    "saved error" => "Information failed to save. Please try again.",
    "delete success" => "Information deleted successfully.",
    "delete error" => "Information failed to delete. Please try again.",
    "security concern" => '<br/><i class="security_leak">Security Concern. 
    Please give this permission to trusted role.</i>',
    "approve success"   => 'Suggestion approved successfully.', 
    "already exist"   => 'This suggestion already exists in option.', 
    "status update success"   => 'Status updated successfully.',
    "status update error"   => 'Status failed to update.',         
    "services status update success"   => 'Status of the service updated successfully.',
    "services status update error"   => 'Status of the service failed to update.', 
    "advertisement saved success" => "Information about advertisement saved successfully.",
    "advertisement saved error" => "Information about advertisement failed to save. Please try again.",
    "captcha missmatch"     =>"Captcha doesnt match.",
    "success registration" => "You have successfully registered in ".site_name().". 
    A confirmation mail has send to your email %s. Please check you email inbox.",
    "error registration"=>"Registration falied. Try again.",        
    "server error" =>"Something went wrong.. Please try again later",
    "forget password mail sent" =>"Your password is successfully mailed to your email id",
    "email verification success"=>"Welcome to ".site_name().", your email has been verified successfully. 
    Please click to <a href='".site_url('account/signin')."'>sign in</a>.",
    "email verification error"=>"Your email is already verified. 
    Please click to <a href='".site_url('account/signin')."'>sign in</a>.",
    "email verification code error"=>"Invalid verification code." ,

    "email not exist"=>"The email is not in our database! Please enter a proper email id",
    "contact us success"=>"Thanks for your interest. We will contact you shortly",
    "email send error"=>"Please provide proper email id.",
    "email send success"=>"email send successfully.",
    "facebook account add success"=>"Your facebook account is added successfully",
    "facebook account email not match"=>"Sorry! Your email does not match with facebook email id",
    "facebook session timeout"=>"Your facebook session has timeout. Please login using Facebook. <a href='".site_url('account/signin')."'>sign in</a>.",
    "facebook friend add success"=>"Your facebook friends are connected successfully",
    "email share message"=>'Hi,<br/> I would like share this link with you. Please check it.<br/>
    <a href="%s">%s</a>
    <br/>With regards.<br/>%s',
    "abuse service"=>"Report the service as fake.",
    "abuse user"=>"Report the user as fake.",
    "abuse company"=>"Report the company as fake.",
    "user exceed service creation"=>"You cannot create another service. Company can add more than one services."
    .anchor("start_company","Click Here to become a company.") ,
    "company exceed service creation"=>"You cannot create another service. Please contact site admin.",  
    "service provider service creation reject"=>"You cannot create service. Please contact company owner or site admin.",  
    "service provider add success" => "Service provider successfully added.",      
    "service provider add error" => "Service provider fail to add.",
    "service provider assigned" => "Service provider is already assigned to the service.",
    "make_service_feature_succ" => "Make service feature request send successfully",
    "fb_reg_fail" => "Facebook signup failed. Either your email id is not verified in facebook or your email is not publicly available. You can sign up here.",
    "mobile_verification_error"=>"Invalid mobile verification code",
    "mobile verification success"=>"Your mobile has been verified successfully.",
    "mobile verification send mail failed"=>"Due some problem your verification process cannot be completed. Please contact site admin.",
    "no_information_found"=>"No information found.",
	"duplicate_skill"=>"This skill already added.",
    "max_exceed_service_creation"=>"You cannot create more service."
    );

    return $msg[$title];
}


function admin_base_url($uri = '')
{
    //return base_url("admin/".$uri);
    return base_url("backoffice/".$uri);
}

/////////Encryption and Decryption////////
/***
* Encryption double ways.
* 
* @param string $s_var
* @return string
*/

function encrypt($s_var)
{
    try
    { 
        $ret_=$s_var."#acu";///Hardcodded here for security reasons
        $ret_=base64_encode(base64_encode($ret_));
        unset($s_var);
        return $ret_;
    }
    catch(Exception $err_obj)
    {
        show_error($err_obj->getMessage());
    }      
}

/**
* Decryption double ways.
* 
* @param string $s_var
* @return string
*/
function decrypt($s_var)
{
    try
    {
        $ret_=base64_decode(base64_decode($s_var));
        $ret_=str_replace("#acu","",$ret_);
        unset($s_var);
        return $ret_;
    }
    catch(Exception $err_obj)
    {
        show_error($err_obj->getMessage());
    }      
}
/////////end Encryption and Decryption////////

function get_destination()
{
    $CI=&get_instance();
    $CI->load->library('user_agent');
    return $CI->agent->referrer();
}

/**
* Converts a serverpath like D:\xampp\htdocs\guru\php\resources
* into url like http://localhost/guru/php/resources
* 
* By default it returns relative path
* 
* @param string $path
* @param string $ret, relative | absolute
*/
function serverToUrlPath($server_path="", $ret="relative")
{
    $server_path=set_realpath($server_path,FALSE);
    $server_path=rtrim($server_path,"\\");
    $server_path=rtrim($server_path,"/");    

    $server_base=set_realpath("",FALSE);
    $server_base=rtrim($server_base,"\\");
    $server_base=rtrim($server_base,"/");    

    $temp=str_replace($server_base,"",$server_path);
    $temp=str_replace("\\","/",$temp);

    //pr(array($server_path,$server_base,$temp));

    return ($ret=="relative"?$temp:base_url($temp));

}


function get_countryName($id)
{
    $CI=&get_instance();
    $CI->load->model("country_model");

    $rs=$CI->country_model->country_load(intval($id));
    if(empty($rs))
        return FALSE;
    else
        return trim($rs->s_country);
}

function get_stateName($id)
{
    $CI=&get_instance();
    $CI->load->model("state_model");

    $rs=$CI->state_model->state_load(intval($id));
    if(empty($rs))
        return FALSE;
    else
        return trim($rs->s_state);
}

function get_cityName($id)
{
    $CI=&get_instance();
    $CI->load->model("city_model");

    $rs=$CI->city_model->city_load(intval($id));
    if(empty($rs))
        return FALSE;
    else
        return trim($rs->s_city);
}

function get_zipCode($id)
{
    $CI=&get_instance();
    $CI->load->model("zip_model");

    $rs=$CI->zip_model->zip_load(intval($id));
    if(empty($rs))
        return FALSE;
    else
        return trim($rs->s_zip);
}


function get_popularLocation($id)
{
    $CI=&get_instance();
    $CI->load->model("popular_location_model","modp");

    $rs=$CI->modp->popular_location_load(intval($id));

    if(empty($rs))
        return FALSE;
    else
        return trim($rs->s_location);
}

/**
* @see, search_model.php, refine_search() 
* 
* @param mixed $name
*/
function get_locationId($parm)
{
    $CI=&get_instance();
    $this->load->model('zip_location_mapping_model');
    
    $condition="cy.s_city='".trim($parm[0])."' AND pl.s_location ='".trim($parm[1])."'";
    
    $rs=$CI->zip_location_mapping_model->zip_location_mapping_load($condition);

    if(empty($rs))
        return FALSE;
    else
        return array("zip_id"=>$rs[0]->zip_id,
                "city_id"=>$rs[0]->city_id,
                "state_id"=>$rs[0]->state_id,
                "popular_location_id"=>$rs[0]->popular_location_id
                );
}


/**
* Encode the ### seperated values from array 
* to insert and update into db. 
* 
* @see, controllers/category.php, ajax_operation()
* 
* @param mixed $arr
* @return string;
*/
function dbHashSeperateEnc($arr=array())
{
    if(empty($arr))
        return FALSE;

    return implode("###",$arr);

}

/**
* Decode the ### seperated values from array 
* to insert and update into db. 
* 
* @see, controllers/category.php, index()
* 
* @param mixed $arr
* @return string;
*/
function dbHashSeperateDec($str="")
{
    if(empty($str))
        return FALSE;

    return explode("###",$str);

}


/**
* Bulk mail sender
* use to store mail data to database
* and the mail will be tiggered by the
* cron job 
* sendBulkMail('eve.aldiniz@gmail.com','kallol.b@acumensoft.com','this is a test');
* @see, controllers/category.php, index()
* 
* @param $to,$from,$body
* @return boo;
*/
//sendBulkMail('eve.aldiniz@gmail.com','kallol.b@acumensoft.com','this is a test');
function sendBulkMail($to=FALSE,$from=FALSE,$subject=FALSE,$body=FALSE)
{   

    if($to&&$from&&$body&&$subject){
        $CI=&get_instance();
        $CI->load->model("bulk_mail_model");
        $arr['s_mail_to'] = $to;
        $arr['s_mail_from'] = $from;
        $arr['s_mail_subject'] = $subject;
        $arr['s_mail_body'] = $body;
        $rs=$CI->bulk_mail_model->add_bulk_mail($arr);
        return $rs;
    }

    return FALSE;
}

/**
* Simple mail sender
* @param array $mailData => array(
*       'from'=>'',
*       'to'=>'',
*       'cc'=>'',
*       'bcc'=>'',
*       'subject'=>'',
*       'message'=>'',
*   )
* @return bool;
*/
function sendMail($mailData = NULL)
{   
    $CI=&get_instance();
    $CI->load->library('email');

    if($mailData){
        //char encoding
        $mailconfig['charset'] = 'iso-8859-1';
        $mailconfig['wordwrap'] = TRUE;
        $mailconfig['mailtype'] = 'html';
        $mailconfig['crlf'] = "\r\n";
        $mailconfig['newline'] = "\r\n";
        //smtp settings
        $mailconfig['protocol'] = 'smtp';
        $mailconfig['smtp_host'] = 'smtp.sendgrid.net';
        $mailconfig['smtp_user'] = 'rajat.todi@gmail.com';
        $mailconfig['smtp_pass'] = 'gurudex20';
        //construct ci mail
        $CI->email->initialize($mailconfig);
        $CI->email->from($mailData['from']);
        $CI->email->to($mailData['to']); 
        $CI->email->cc(@$mailData['cc']); 
        $CI->email->bcc(@$mailData['bcc']); 
        $CI->email->subject(@$mailData['subject']);
        $CI->email->message(@$mailData['message']); 
        $ret= $CI->email->send();
        //pr($CI->email->print_debugger());       
        return $ret;
    }


    return FALSE;
}
//pr(md5("123456"));

function string_part($str,$limit=20)
{
	$n_str =  explode(' ',substr($str,0,$limit));
	if(count($n_str)>1)
	{
		array_pop($n_str);
		$f_str = implode(' ',$n_str).' ...';
	}
	else
	{
		$f_str = implode(' ',$n_str);
	}
	return $f_str;
}

/**
* Generating Captcha
*/
function captcha()
{   
    $CI=&get_instance();
    $CI->load->helper('captcha');

    $vals = array(
    'word' => 'Random word',
    'img_path' => './captcha/',
    'img_url' => 'http://192.168.1.253/guru/php/captcha/',
    'font_path' => './path/to/fonts/texb.ttf',
    'img_width' => '150',
    'img_height' => 30,
    'expiration' => 7200
    );

    $cap = create_captcha($vals);
    echo $cap['image'];
}

function calculate_age($dob)
{
    /*$datetime1 = new DateTime(date("Y-m-d",strtotime($dob)));
    $datetime2 = new DateTime();//now
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%Y years');*/    
	
	/* changed on 28 nov 2013*/
	$tdob = strtotime(str_replace("/","-",$dob));       
	$tdate = time();				
	$age = 0;
	while( $tdate > $tdob = strtotime('+1 year', $tdob))
	{
		++$age;
	}
	return $age;
}


function time_ago($assign_time,$current_time='')
{
    try
	{
		if($current_time=='')
		{
			$current_time   =   time();
		}

		$str_left_time      =   '';
		$i_one_month_diff   =   time()-strtotime('-1 month');
		$i_left_time    =   $current_time-$assign_time ;

			if($i_left_time<60)
			{
				$str_left_time  =    ($i_left_time<=1 )?('a second ago'):$i_left_time.(' seconds ago') ;                   
			}
			else if($i_left_time<3600)
			{
				$i_time         =    floor($i_left_time/60) ;
				$str_left_time  =    ($i_time==1)?('a minute ago'):$i_time.(' minutes ago') ;
			}
			else if($i_left_time<86400)
			{
				$i_time         =    floor($i_left_time/3600) ;
				$str_left_time  =    ($i_time==1)?('about an hour ago'):$i_time.(' hours ago') ;    
			}
			/*else if($i_left_time < $i_one_month_diff)
			{
				$i_time         =    floor($i_left_time/86400) ;
				$str_left_time  =    ($i_time==1)?(' Yesterday'):$i_time.' '.('days ago') ;
			}*/
			else
			{
				$str_left_time  =    date('d-m-Y',$assign_time);
			}
		return $str_left_time ;
	}
	catch(Exception $err_obj)
	{
		show_error($err_obj->getMessage());
	}
}

/**
* date diff in days
* 
* @param mixed $dt_greater
* @param mixed $dt_lesser
*/
function calculate_days_gap($dt_highest,$dt_lowest)
{
    $datetime1 = new DateTime(date("Y-m-d",strtotime($dt_highest)));
    $datetime2 = new DateTime(date("Y-m-d",strtotime($dt_lowest)));
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%d');    
}


/*
+-----------------------------------------------+
| Set congfiguration for front end pagination 	|
+-----------------------------------------------+
*/
function fe_ajax_pagination($ctrl_path = '',$total_rows = 0, $start, $limit = 0, $paging_div = '')
{
	$CI =   &get_instance();
	$CI->load->library('jquery_pagination');
	
	$config['base_url'] = $ctrl_path;
	$config['total_rows'] = $total_rows;
	$config['per_page'] = $limit;
	$config['cur_page'] = $start;
	$config['uri_segment'] = 0;
	$config['num_links'] = 1;
	$config['page_query_string'] = false;	
	$config['full_tag_open'] = '<ul>';
	$config['full_tag_close'] = '</ul>';	
	$config['prev_link'] = "<img width='24' height='24' alt='arrow' src='".base_url()."theme/guru_frontend/images/arrow2.jpg'>";
	$config['next_link'] = "<img width='24' height='24' alt='arrow' src='".base_url()."theme/guru_frontend/images/arrow3.jpg'>";
	
	/*$config['num_tag_close'] = '</li>';
	$config['cur_tag_open'] = '<li><a class="select">';
	$config['cur_tag_close'] = '</a></li>';
	$config['next_tag_open'] = '<li>';
	$config['next_tag_close'] = '</li>';
	$config['prev_tag_open'] = '<li>';
	$config['prev_tag_close'] = '</li>';*/

	$config['first_link'] = '';
	$config['last_link'] = '';
	$config['cur_tag_open'] = '<li class="del">';
	$config['cur_tag_close'] = '</li>';
	$config['num_tag_open'] = '<li class="del">';
	$config['num_tag_close'] = '</li>';
	$config['div'] = '#'.$paging_div;
	
	$CI->jquery_pagination->initialize($config);
	return $CI->jquery_pagination->create_links();
}


/**
* Callback function for an array to put encrypted id withinit.
* @see, controllers/user_profile.php, index(),
*/
function addEncIDCallback($item)
{

    if(is_a($item,"stdClass"))
        $item->s_token=encrypt($item->id);
    elseif(is_array($item))
        $item["s_token"]=encrypt($item["id"]);
    else //the item itself is the string
        $item=encrypt($item);

    return $item;
}


/**
* Callback function for an array to modify display date format (d-m-Y) id withinit.
* @see, controllers/user_profile.php, index(),
*/
function modifyDispDateCallback(&$item,$key,$pram)
{
    //pr(array($item,$col,$newcol));

    $col="";
    $newcol="";
    if(is_array($pram))
    {
        $col=$pram[0];
        $newcol=$pram[1];
    }
    else
    {
        $col=$pram;
        $newcol=$pram;
    }


    //pr($pram);

    if(is_a($item,"stdClass"))
        $item->$newcol= format_date($item->$col) ;
    elseif(is_array($item))
        $item[$newcol]=format_date($item[$col]);
    else //the item itself is the string
        $item=format_date($item);

    //return $item;
}

/**
* Callback function for an array to modify display unserialize withinit.
* @see, controllers/user_profile.php, index(),
*/
function modifyUnSerialCallback(&$item,$key,$pram)
{
    //pr(array($item,$col,$newcol));

    $col="";
    $newcol="";
    if(is_array($pram))
    {
        $col=$pram[0];
        $newcol=$pram[1];
    }
    else
    {
        $col=$pram;
        $newcol=$pram;
    }


    //pr($pram);

    if(is_a($item,"stdClass"))
        $item->$newcol= unserialize($item->$col) ;
    elseif(is_array($item))
        $item[$newcol]=unserialize($item[$col]);
    else //the item itself is the string
        $item=unserialize($item);

    //return $item;
}

/*
function find_connected_friends($viewer,$viewing,&$chain=array())
{
    $CI=&get_instance();
    // pr($chain);
    if(empty($chain))
        $chain[0]=$viewer;

    //echo $viewer."---".$viewing;
    //exit;

    $CI->load->model('user_fb_list_model');
    $condition=array("uid"=>$viewer);

    $result=$CI->user_fb_list_model->user_fb_list_load($condition);   

    //pr($valueexsist,1);
    $temp=array();
    if(!empty($result))
    {

        //echo "here";
        //exit;

        foreach($result as $res_key=>$res_value)
        {          
            $temp[]=$res_value->uid_friend;
            //echo $res_value["uid_friend"];
        }  

    }
    elseif(empty($chain))
        return false;

    //pr($temp,1);   

    if(in_array($viewing,$temp))
    {          
        /**
        * In this case viewer is directly friend of viewing.
        *  
        * @var mixed
        * /

        $chain[count($chain)]=$viewing;

        return $chain; 

    }
    else
    {
        foreach($temp as $temp_key=>$temp_value)
        {

            //  echo $temp_value."---<br>---".$viewing;
            //pr($chain);
            if($chain[0]!=$temp_value)
                find_connected_friends($temp_value,$viewing,$chain);
            else
                return false;

        }
    }


    //echo "here...";

}
*/

/**
* @param $i_user_id
* @param $unique, true=>friend id will not repeated, 
*                 false=>friend id may repeat in case common friends  exists
*                  @see find_connected_chain_within_friends() 
*/
function find_all_friend_and_their_friend($i_user_id,$unique=true)
{
    $CI=&get_instance();
    $CI->load->model('user_fb_list_model');	
    $result=$CI->user_fb_list_model->get_all_friend_and_their_friend($i_user_id,-1,-1,$unique);  
    return $result;
}

function find_all_friend_of_friend($i_user_id)
{
    $CI=&get_instance();
    $CI->load->model('user_fb_list_model');
    $result=$CI->user_fb_list_model->get_all_friend_of_friend($i_user_id);  

    return $result;
}

function find_all_friend_of_friend_of_friend($i_user_id)
{
    $CI=&get_instance();
    $CI->load->model('user_fb_list_model');
    $result=$CI->user_fb_list_model->get_all_friend_of_friend_of_friend($i_user_id);  

    return $result;
}

function find_all_friend($i_user_id)
{
    $CI=&get_instance();
    $CI->load->model('user_fb_list_model');
    $result=$CI->user_fb_list_model->get_all_friend($i_user_id);  

    return $result;
}

function find_connected_chain_within_friends($viewer_id,$viewing_id)
{
   
    global $temp;
    $CI=&get_instance();   
	
    //$result_arr = find_all_friend_and_their_friend($viewer_id,false);
    $result_arr = find_all_friend_and_their_friend($viewer_id,false);//testing
    // temporary chain array    
	
    $temp = array('viewer_id'=>$viewer_id,'viewing_id'=>$viewing_id,'original_arr'=>$result_arr,'i'=>0,'result'=>'');   
    //pr($result_arr,1);//debug
	
    if(!empty($result_arr))
    {
        $reversed = array_reverse($result_arr["records"],true);   
        /* 
        *  array_walk() will append the return values of $temp
        *  $temp['result'] , $temp['chain_html']
        */
        array_walk($reversed,'get_chain'); 
        
        //pr($viewer_id.','.$viewing_id); 
        $chain_html = array();
        foreach($temp['chain_html'] as $ck=>$cv)
        {
            $chain_html[$ck]    =   array_reverse($cv,true);
            $chain_html[$ck]    =   '<li><span>'.get_user_display_name($viewer_id,false,15).' (you)</span></li>'
                                    .implode("",$chain_html[$ck]) ; // ex: <li><span>Mr. Abir</span></li>
        }
        //pr($chain_html);
       return array('result'=>$temp['result'] ,'chain_html'=>$chain_html);      
    }
   
    return false;  
}

function get_chain(&$item,$key)
{
    
    global $temp;
    //pr(array($item,$key,$temp));
    $parent_id  = $item->parent_id;
    $label      = $item->label;
    $id         = $item->id;
    //echo '<br>'.$parent_id.'=='.$label.'===='.$id;
    if($id==$temp['viewing_id'])   
    {
       $temp['result'][$temp['i']][$id] = $item;
       
       $temp["chain_html"][$temp['i']][$id] = '<li><span>'.get_user_display_name($item->id,true,15).'</span></li>';
       get_parent_friend($item,$temp1);
       $temp['i']++;
       
    }       
   
}

function get_parent_friend($item,&$temp1)
{   
    global $temp;
    if(empty($item))   
        return false;
    
    /* getting parent */
    $key =     $temp['original_arr']["ids"][$item->parent_id] ;
    
    if(!empty($temp['original_arr']["records"][$key]))
    {
        $user =  $temp['original_arr']["records"][$key];
        $temp['result'][$temp['i']][$item->parent_id] =  $user;
        $temp["chain_html"][$temp['i']][$item->parent_id] = '<li><span>'.get_user_display_name($user->id,false,15).'</span></li>';
    }
    
    
    /*
    pr($item);
    pr($key);
    pr($temp['original_arr']["records"][$key],1); 
    */             
    get_parent_friend($temp['original_arr']["records"][$key],$temp);    
         
}



/**
* 
* @param  $cat_id
* 
*/
function service_extended_column_operation($cat_id)
{
	$CI=&get_instance();
	$excluded = array('id','uid','cat_id','sub_cat_id','service_id');	
    
    
    $CI->load->model('category_service_extended_defination_model','csef_mod');
	
	$fields = $CI->csef_mod->get_fields_name('user_service_extended');
	
	
	if(!empty($fields))
	{
		foreach($fields as $val)
		{
			if(!in_array($val,$excluded))
			{
				/*$column_name = $val;
				
				$column_name = substr($column_name, strpos($column_name, '_') + 1); 				
				//pr(array($column_name,strrpos($column_name, '_'),$column_name));
				$column_name = str_replace("_"," ",$column_name);
				$column_name = str_replace("ids","",$column_name);///remove ids word from label
                $column_name = str_replace("id","",$column_name);///remove id word from label
                
				//pr(array($match,$temp));			
				$label = ucfirst($column_name);*/	
                $label=get_extended_column_lebels($val);
                //pr($label);
                			
				
				/* check if the record exist */
				$arr_where = array('s_column_name'=>$val,'cat_id'=>$cat_id);
				$i_count = $CI->csef_mod->count_records('category_service_extended_defination',$arr_where);
				if($i_count<=0)
				{
                    /**
                    * on 7Oct 2013, 
                    * country wise extended fields are implemented, 
                    * So, by default we will insert all fields for all 
                    * countries
                    */
                    $country=dd_country(array("id"=>$id));
                    unset($country[""]);
                    
                    foreach($country as $country_id=>$s_country)
                    {
                        $insert_arr = array();
                        $insert_arr['cat_id']                = $cat_id; 
                        $insert_arr['sub_cat_id']            = 0; 
                        $insert_arr['country_id']            = $country_id; 
                        $insert_arr['s_column_name']         = $val;
                        $insert_arr['s_search_page_label']   = $label;
                        $insert_arr['s_service_page_label']  = $label;
                        
                        $i_add = $CI->csef_mod->add_category_service_extended_defination($insert_arr);
                    }
				}
			}
		}
	}
	
	return;
}

function get_extended_column_lebels($s_column)
{
    global $fieldLabel;
    /**
    * There are few fields 
    * Which we need to lebel differently.
    */
    $fieldLabel=array("s_qualification_ids"=>"Highest Qualification Level",
        "s_classes_ids"=>"Classes Teach",
        "s_medium_ids"=>"Language Medium",
        "d_rate" => "Hourly Rate",//Hourly Rate ($) and Hourly Rate (Rs)
        "s_other_subject_ids"=>"Subject",
        "s_tools_ids"=>"Knowledge of Tool",
        "s_designation_ids"=>"Work Experience", 
    );
    
    if(array_key_exists($s_column,$fieldLabel))
        return $fieldLabel[$s_column];
    
    //ex= s_qualification_ids will become Qualification as  level
    $column_name = $s_column;
    
    $column_name = substr($column_name, strpos($column_name, '_') + 1);                 
    //pr(array($column_name,strrpos($column_name, '_'),$column_name));
    $column_name = str_replace("_"," ",$column_name);
    $column_name = str_replace("ids","",$column_name);///remove ids word from label
    $column_name = str_replace("id","",$column_name);///remove id word from label    

    return  ucfirst($column_name);
}



/**
* Encode decode string
* 
* @param mixed $str
* @param mixed $format=> encode, decode, display
* 
*/
function format_text($str,$format="display")
{
    /*if($encode) 
    return htmlentities(trim($str)); 
    else
    return html_entity_decode(trim($str),ENT_QUOTES);*/

    if($format=="encode") 
    {
        //$str=str_replace(array("\n","\r"),"<br/>",$str);
        $str=str_replace(array("\n","\r"),"",$str);///removing the \n and br conflict,
        $str=str_replace("<br/>","",$str);///removing the \n and br conflict,
        $str=str_replace("&#034;",'\'',$str);///TODO checking if in all places it is working.
        $str=str_replace("&#039;",'\'',$str);///TODO checking if in all places it is working.
		
        //$str=quotes_to_entities(trim($str)); 
        $str=addslashes($str);
        //return htmlspecialchars($str,ENT_QUOTES) ; ///commented, as per Ashim sir, change request.
        return $str; 
    }
    elseif($format=="decode")
    {
        $str=trim($str); 
        return htmlspecialchars_decode($str);   
        //return $str;      
    }    
    else
    {
        $str=trim($str); 
        $str=str_replace("\n","",$str);
        $str=str_replace("<br/>","",$str);///removing the \n and br conflict,
        $str=htmlspecialchars($str,ENT_QUOTES) ;//added
        $str=str_replace('&quot;',"&#034;",$str);///TODO checking if in all places it is working.
        $str=str_replace("'","&#039;",$str);///TODO checking if in all places it is working.//added 
		
        //pr(htmlentities($str) );
        //$str=htmlspecialchars_decode($str);   
        $str=stripslashes($str);  
        
        //return htmlspecialchars_decode($str);  //working 
        return $str;      
    }     

}



/**
* Callback function for an array to modify display format text within it.
* @see, controllers/user_profile.php, index(),
*/
function modifyFormatCallback(&$item,$key,$pram)
{
    //pr(array($item,$col,$newcol));

    $col="";
    $newcol="";
    if(is_array($pram))
    {
        $col=$pram[0];
        $newcol=$pram[1];
    }
    else
    {
        $col=$pram;
        $newcol=$pram;
    }


    //pr($pram);
    if(is_a($item,"stdClass"))
        $item->$newcol= format_text($item->$col);
    elseif(is_array($item))
        $item[$newcol]=format_text($item[$col]);
    else //the item itself is the string
        $item=format_text($item);

    //return $item;
}

/**
* This function unserialize and adds a key into the 
* unserialized array value. 
* Used for unserializing and using into INEDIT 
* of "user_service_extended" columns 
* @see, controllers/service_profile.php, index(), 
*/
function addKeyCallback(&$item,$key,$pram)
{
    //pr(array($item,$col,$newcol));

    $temp=$item;

    $item=array();
    $item[$pram]=$temp;

    //return $item;
}



/**
* put your comment there...
* 
* @param mixed $ret =>  prefix,suffix
* @return mixed
*/
function getDummyFieldPrefixSuffix($ret="prefix")
{
    if($ret=="prefix")
        return '@:@';
    elseif($ret=="suffix")
        return '@;@';
        
    return false;
}

/**
* This function encodes a 
* column and value pair into 
* dummy field syntax.
* 
* @param mixed $column
* @param mixed $value
*/
function encodeToDummyField($column,$value)
{
    if(empty($column))
        return false;

    //return $column.'@:@'.addslashes($value).'@;@';
    return $column.getDummyFieldPrefixSuffix("prefix").addslashes($value).getDummyFieldPrefixSuffix("suffix");
}

/**
* This function decodes the value 
* into column and value pair from 
* dummy field syntax.
* 
* @param mixed $value (s_dummy)
*/
function decodeFromDummyField($value)
{
    if(empty($value))
        return false;

    $ret=array();
    //$temp=explode("@;@",$value);
    $temp=explode(getDummyFieldPrefixSuffix("suffix"),$value);
    //pr($temp);
    if(!empty($temp))
    {
        foreach($temp as $v)
        {
            //$t=explode("@:@",$v);
            $t=explode(getDummyFieldPrefixSuffix("prefix"),$v);
            if(trim($t[1])!="")//donot use empty(), because 0 is also a value in field
                $ret[$t[0]]=stripslashes($t[1]);
        }
    }
    return $ret;
}

/**
* This function encodes an array of insert 
* values into column and value pair for  
* dummy field syntax(s_dummy).
* 
* @param mixed $value (array of values used for insert query)
*/
function encodeArrayToDummyField($value)
{
    if(empty($value))
        return false;

    $ret="";
    foreach($value as $k=>$v)
    {
        $ret.=encodeToDummyField($k,$v);
    }
    return $ret;
}


/**
* Encodes the column and value pair 
* into sphinx search criteria. 
* Sphinx operators => 
* & AND , ex- hello & world
* | OR ,  ex- hello | world
* -, ! NOT , ex- hello -world, hello !world
* () GROUP,  ex- ( hello world )
* 
* ex of usage - 
* $this->sphinxsearch->query("@(title,content) \"demo ::: 1\" | \"demo ::: 22\"","test1");
* 
* @see, controllers/search_engine.php
* @param mixed $column
* @param mixed $value
*/
function searchSphinxRule($column,$value)
{
    if(empty($column))//means buid query for value only
        return "\"".escapeSphinxQL($value)."\"";
        
    return "\"".escapeSphinxQL(encodeToDummyField($column,$value))."\"";
}

/**
* Escape sphinx special characters
*/
function escapeSphinxQL ( $string )
{
    $from = array ( '\\', '(',')','|','-','!','@','~','"','&', '/', '^', '$', '=', "'", "\x00", "\n", "\r", "\x1a" );
    $to   = array ( '\\\\', '\\\(','\\\)','\\\|','\\\-','\\\!','\\\@','\\\~','\\\"', '\\\&', '\\\/', '\\\^', '\\\$', '\\\=', "\\'", "\\x00", "\\n", "\\r", "\\x1a" );
    return str_replace ( $from, $to, $string );
}

/**
* No of recommendation for a service
*/
function count_recommendation($user_id)
{
    $CI =& get_instance();
    $CI->load->model('user_service_recommendation_model');
    return $CI->user_service_recommendation_model->count_recommendation_no(intval($user_id));
}

/**
* Returns singular or plural
* 
* @param mixed $count
* @param mixed $singular
* @return str
*/
function format_plural($count, $singular)
{
    if($count>1)
        return $count." ".plural($singular);

    return $count." ".$singular;
}


/**
* @param servic id
* @returns %, complete profile percentage of service profile
*/

function service_profile_prc_calculation($i_service_id)
{
    $CI = &get_instance();

    // getting data from user_service table//
    $CI->db->select('*');
    $res = $CI->db->get_where('user_service',array('id'=>intval($i_service_id)))->row();
    
    $percent = 0;
    $total = 0;

    foreach($res as $k=>$v)
    {
        switch($k)
        {
            case 's_service_name':
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_service_desc': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'country_id': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'state_ids': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'city_ids': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'zip_ids': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_email': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_phone': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_mobile':
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'i_online': 
                                    $total++;
                                    if(intval($v))
                                        $percent++;
                                    break;
            case 's_languages': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
        }
    }

    ////Extended Service Related
    //getting data from service extended defination 
    $CI->db->select('*');
    $ext_def = $CI->db->get_where('category_service_extended_defination',array('cat_id'=>intval($res->cat_id)))->result();
    
    
    if(!empty($ext_def))
        foreach($ext_def as $i=>$vl)
            $temp[]=$vl->s_column_name;
    
    
    //getting data from service extended table
    $CI->db->select('*');
    $ext = $CI->db->get_where('user_service_extended',array('service_id'=>intval($i_service_id)))->row();
    //pr($res,1);
    
    
    foreach($ext as $k=>$v)
    {
        
        if(in_array($k,$temp))
        {
            $total++;
            if(!empty($v))
                $percent++;    
        }        
    }
    ////end Extended Service Related
    
    //pr(intval(($percent*100)/$total));
    return intval(($percent*100)/$total);

   // exit;
}


/**
* @param service_id
*/
function get_service_profile_complete($i_service_id)
{
    $CI = &get_instance();

    // getting data from user_service table//
    $CI->db->select('*');
    $res = $CI->db->get_where('user_service',array('id'=>intval($i_service_id)))->row(); 
    if(!empty($res))
        return $res->i_profile_complete_percent;
    return FALSE; 
}

/**
* @param user id
* @returns %, complete profile percentage of user profile
*/

function user_profile_prc_calculation($i_user_id)
{
    $CI = &get_instance();

    // getting data from user table//
    $CI->load->model('user_model');
    $res = $CI->user_model->user_load(intval($i_user_id));
    
    
    $percent = 0;
    $total = 0;

    foreach($res as $k=>$v)
    {
        switch($k)
        {
            case 's_name':
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_email': 
                                    $total++;
                                   if(!empty($v))
                                        $percent++;
                                    break;
            case 's_phone': 
                                    $total++;
                                   if(!empty($v))
                                        $percent++;
                                    break;
            case 's_mobile': 
                                    $total++;
                                   if(!empty($v))
                                        $percent++;
                                    break;
            case 's_profile_photo': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'country_id': 
                                    $total++;
                                    if(intval($v))
                                        $percent++;
                                    break;
            case 'state_id': 
                                    $total++;
                                    if(intval($v))
                                        $percent++;
                                    break;
            case 'city_id': 
                                    $total++;
                                    if(intval($v))
                                        $percent++;
                                    break;
            case 'zip_id':
                                    $total++;
                                    if(intval($v))
                                        $percent++;
                                    break;
            case 'dt_dob': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'e_gender': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_about_me': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_languages': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'i_email_verified': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'i_doc_verified': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'i_mobile_verified': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 's_facebook_credential': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
            case 'i_is_company_owner': 
                                    $total++;
                                    if(!empty($v))
                                        $percent++;
                                    break;
           
        }
    }

    /// getting user profession data
    $total++;
    
    $CI->load->model('user_profession_model');
    $u_prof = $CI->user_profession_model->user_profession_load(array("up.uid"=>intval($i_user_id)));
    
    if(!empty($u_prof))
        $percent++;
    
    ///getting user education data
    $total++;
    $CI->load->model('user_education_model');
    $u_edu = $CI->user_education_model->user_education_load(array("uid"=>intval($i_user_id)));
    
    //pr($u_edu,1);
    if(!empty($u_edu))
        $percent++;
        
    /// getting user certificate
    $total++;
    $CI->load->model('user_certificate_model');
    $u_certf = $CI->user_certificate_model->user_certificate_load(array("uid"=>intval($i_user_id)));
    
    //pr($u_certf,1);
    if(!empty($u_certf))
        $percent++;
        
    // getting user license
    $total++;
    $CI->load->model('user_license_model');
    $u_licn = $CI->user_license_model->user_license_load(array("uid"=>intval($i_user_id)));
    
    //pr($u_licn,1);
    if(!empty($u_licn))
        $percent++;
        
    // getting user skills
    $total++;
    $CI->load->model('user_skill_model');
    $u_skills = $CI->user_skill_model->user_skill_load(array("uid"=>intval($i_user_id)));
    
    //pr($u_skills,1);
    if(!empty($u_licn))
        $percent++;            
    //pr(intval(($percent*100)/$total));
    $completed=intval(($percent*100)/$total);
    
    /// CRON required to update this process/////
    $CI->user_model->update_user(array("i_profile_complete_percent"=>$completed),array("id"=>intval($i_user_id)));
    
    
    return $completed;

   //exit;
}


/**
* @param user_id
*/
function get_user_profile_complete($i_user_id)
{
    $CI = &get_instance();

    // getting data from user table//
    $CI->db->select('*');
    $res = $CI->db->get_where('users',array('id'=>intval($i_user_id)))->row(); 
    //pr($res);
    if(!empty($res))
        return $res->i_profile_complete_percent;
    return FALSE; 
}

/**
* get category name
*  @param category id
*  @return category name
*/
function get_category_name($cat_id)
{
    $CI = &get_instance();
    $CI->load->model('category_model');
    $ret_ = $CI->category_model->category_load(array("id"=>intval($cat_id)));
   // pr($ret_,1);
    return $ret_[0]->s_category;
}

/**
* Rank calculation for a service. 
* As per rank doc by Mr. Ashim
*  Select id from service_table s where s.id IN ($id_csv) 
*   Order By 
*   IF(s.featured,5*pow(10,8),0) +
*   CHECK_FB_LEVEL($visitor_fb_id, 
*       s.fb_L1_id_csv,9 x pow(10,7),
*       s.fb_L2_id_csv,7x pow(10,7),
*       s.fb_L3_id_csv,5x pow(10,7)
*   )+
*   (
*       s.no_of_company_service_provider_added_this_month+
*       (30-Max(30,s.day_since_last_login)) +
*       s.no_of_person_contacted 
*    ) * pow(10,5) +
*   (s.no_of_testimony+s.total_no_of_service_providers_endrosement)*pow(10,4) + 
*   ( 
*       s.percentage_of_service_profile_completed +
*       s.percentage_of_all_service_providers_profile_completed
*   )/2*pow(10,4)+
*   (
*      Max(s.verified_guru_count,7)+
*      IF(s.owner_email_verifed,1,0)+
*      IF(s.owner_phone_verifed,1,0)
*   )*pow(10,3)    
* 
* 
* @param $i_service_id
* @param $friend, std obj returned from find_all_friend_and_their_friend()
*        @see, rankCallback()
* @return array
* 
* TODO :: 
*  what is s.no_of_person_contacted ?
*  There is no testimony in the site, are you referring to the recommendation?
*/
function calculate_rank($i_service_id,$friend=null)
{
    $CI = &get_instance();
	//pr($friend,1);
    /*// getting data from user table//
    $CI->db->select('*');
    $res = $CI->db->get_where('users',array('id'=>intval($i_user_id)))->row(); 
    //pr($res);
    if(!empty($res))
        return $res->i_profile_complete_percent;*/

    /*$sql='SELECT COUNT(*) FROM 
          user_company_employee uce 
          LEFT JOIN users u ON u.id=uce.uid
          Where uce.i_active=1
          
          ';
    $query  =    $this->db->query($qry1);
    $row    =    $query->row_array();*/          
    
    $CI->load->model("user_service_model");
    $CI->load->model("user_skill_model");
    
    $service=$CI->user_service_model->user_service_load(intval($i_service_id));
    
    $ret_=array();
    if(empty($service))
        return FALSE;
     
    $ret_["service_id"]=$service->id;
    
    //featured, IF(s.featured,5*pow(10,8),0) 
    $ret_["i_featured"]=$service->i_featured; 
    $ret_["i_featured_value"]=($ret_["i_featured"] 
                                ?($ret_["i_featured"]*5*pow(10,8)) 
                                :0); 
                                
    if(!empty($friend))
    {
        $ret_["uid"]=$friend->id;
        /**
        *   CHECK_FB_LEVEL($visitor_fb_id, 
        *       s.fb_L1_id_csv,9 x pow(10,7),
        *       s.fb_L2_id_csv,7x pow(10,7),
        *       s.fb_L3_id_csv,5x pow(10,7))        
        */
        $level=array(
            0=>0,
            1=>(9*pow(10,7)),
            2=>(7*pow(10,7)),
            3=>(5*pow(10,7)),
        );
        $ret_["i_fb_level"]=$friend->label; 
        $ret_["i_fb_level_value"]=$level[ $ret_["i_fb_level"] ];
    }

    
    /**
    * active_level, 
    * s.no_of_company_service_provider_added_this_month + 
    * (30-Max(30,s.day_since_last_login)) + 
    * s.no_of_person_contacted 
    */
    $no_of_company_service_provider=0;
    $day_since_last_login=0;    
    $total_endrose=0;//used below, end_recommendeds
    $profile_complete=0;//used below, completion 
    $guru_verified=0;//used below, verified 
       
    if($service->i_is_company_service)
    {
        $service_providers=get_company_service_provider($service->comp_id,$service->id);
		
        if(!empty($service_providers))
        {
            foreach($service_providers as $s=>$sp)
            {
				//pr($sp); inside code of this loop changed on apr 2014 as its return type array or object
				if(is_array($sp))
				{					
					if(date('m',strtotime($sp['dt_registration'])) == date('m') )
						$no_of_company_service_provider++;
						
					/**
					* (30-Max(30,s.day_since_last_login)) 
					* As per doc, 
					* Note: Store user day_since_last_login with service_table as well. 
					* That field will update if any user attached to that service login.
					*   Now, we need to calculate here, so no need to add a extra column 
					* in service table.
					*/
					$days_last_login=intval(calculate_days_gap(
											date('Y-m-d',strtotime($sp['dt_last_login'])),
											date("Y-m-d"))
											);
					$day_since_last_login=max($day_since_last_login,$days_last_login);
				   
					$total_endrose=$total_endrose+$CI->user_skill_model->count_endorsement(intval($sp['uid']));
					$profile_complete=$profile_complete+get_user_profile_complete(intval($sp['uid']));
					if(is_guru_verified(intval($sp['uid'])))
						$guru_verified++;
				} // end if $sp is array
				else if(is_object($sp))				
				{
					if(date('m',strtotime($sp->dt_registration)) == date('m') )
						$no_of_company_service_provider++;				
					/**
					* (30-Max(30,s.day_since_last_login)) 
					* As per doc, 
					* Note: Store user day_since_last_login with service_table as well. 
					* That field will update if any user attached to that service login.
					*   Now, we need to calculate here, so no need to add a extra column 
					* in service table.
					*/				
					$days_last_login=intval(calculate_days_gap(
											date('Y-m-d',strtotime($sp->dt_last_login)),
											date("Y-m-d"))
											);
					$day_since_last_login=max($day_since_last_login,$days_last_login);
				   
					$total_endrose=$total_endrose+$CI->user_skill_model->count_endorsement(intval($sp->uid));
					$profile_complete=$profile_complete+get_user_profile_complete(intval($sp->uid));
					if(is_guru_verified(intval($sp->uid)))
						$guru_verified++;  
				}     // end if $sp is object         
            }            
        }
    }
    
    /**
    * as per doc, 
    *   ( 
    *     s.no_of_company_service_provider_added_this_month+
    *     (30-Max(30,s.day_since_last_login))+
    *     s.no_of_person_contacted(?) 
    *   ) * pow(10,5)
    */
	//echo '==='.$day_since_last_login;
    $ret_["i_active_level"]=($no_of_company_service_provider
            + (30-max(30,$day_since_last_login))
        ); 
    $ret_["i_active_level_value"]=$ret_["i_active_level"]*pow(10,5);
  
  
    /**
    * end_recommended , 
    * (s.no_of_testimony+s.total_no_of_service_providers_endrosement)* pow(10,4)
    */
    $CI->load->model('user_service_recommendation_model');
    $no_of_testimony=$CI->user_service_recommendation_model->count_recommendation_sevice(intval($service->id)); 
    $ret_["i_end_recommended"]=$no_of_testimony+$total_endrose;
    $ret_["i_end_recommended_value"]=$ret_["i_end_recommended"]*pow(10,4);   
    
    /**
    * completion, 
    * ((s.percentage_of_service_profile_completed+
    * s.percentage_of_all_service_providers_profile_completed)/2)* pow(10,4) 
    */
	
    $service_profile_complete=get_service_profile_complete(intval($service->id));
	//echo $service_profile_complete.'+'.$profile_complete;
    $ret_["i_profile_completion"]=($service_profile_complete+$profile_complete)/2;
    $ret_["i_profile_completion_value"]=$ret_["i_profile_completion"]*pow(10,4);     
    
    /**
    * verified, 
    * (
    *      Max(s.verified_guru_count,7)+
    *      IF(s.owner_email_verifed,1,0)+
    *      IF(s.owner_phone_verifed,1,0)
    *   )*pow(10,3)    
    */
    $ret_["i_verified"]=(
        max($guru_verified,7)
        + (is_guru_verified($service->uid,"i_email_verified")?1:0)
        + (is_guru_verified($service->uid,"i_mobile_verified")?1:0)
    );
    $ret_["i_verified_value"]=$ret_["i_verified"]*pow(10,3);      
                
    return $ret_; 
}


/**
* Rank Callback,
* @see, cron.php, process_rank() 
*/
function rankCallback($friend,$key,$service)
{
    $CI = &get_instance();
    $CI->load->model("user_rank_model");
    //pr($friend,1);
    /////Calculating the ranks for the service///
    $rank=calculate_rank($service->id,$friend);
    ////end Calculating the ranks for the service/// 
   //pr($rank,1);//debug
    if(empty($rank))
        return TRUE;
    
    ///delete old record, rank table//
    $CI->user_rank_model->delete_user_rank(array(
                "service_id"=>$service->id,
                "uid"=>$friend->id
                ));
    ///add into rank table//                   
    $CI->user_rank_model->add_user_rank($rank);    
}

/**
* 1st, 2nd, 3rd, 4th ...
* 
* @param mixed $n
* @see, views/fe/search_engine/index.tpl.php
*/
function levelToStr($n)
{
    switch($n)
    {
        case 0:
            return $n;
        break;
        case 1:
            return $n.'st';
        break;
        case 2:
            return $n.'nd';
        break;
        case 3:
            return $n.'rd';
        break;
        default:
            return $n.'th';
        break;                
                
    }
}


function isEmptyDateField($dt)
{
    if(empty($dt))
        return TRUE;
    
    if(!is_null($dt)
        && intval(date("Y",$dt))>1970
    )
        return FALSE;//date is valid    
    
    return TRUE;//date is empty    
}

function get_currencySym($country_id)
{
    if(empty($country_id))
        $country_id=get_globalCountry();
    
    if($country_id==1)//1=>India
        return "Rs ";
    elseif($country_id==2)//1=>USA    
        return "\$ ";
        
}
