<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe user profile. 
* Fe user profile edit.
* 
* 
*/

/*
* Change these when linkedin application changed
* below define the required variables Feb 2014
*/
define('API_KEY',      '75c409rbpm1p03');
define('API_SECRET',   'V1nJ9JyvYoAkMZE7');
define('REDIRECT_URI', 'http://dev.gurusourcing.com/user_profile');
define('SCOPE',        'r_fullprofile r_emailaddress rw_nus r_contactinfo');

class User_profile extends MY_Controller {

    public $profile_type="user";    

    public function __construct()
    {   

        parent::__construct();
        $this->data['hideSocialConnect'] = '';
        /**
        * @see, views/fe/generate.tpl.php
        * @see, controllers/shorturl.php, generate()
        */
        $this->data['profile_type'] = $this->profile_type;        

        $this->load->model('user_model');
        $this->load->model('user_service_model');
        $this->load->model('user_company_model');         
        $this->load->model('user_public_private_model');
        $this->load->model('user_profession_model');
        $this->load->model('user_education_model');
        $this->load->model('user_certificate_model');
        $this->load->model('user_license_model');

        $this->load->model('user_skill_model');

    }

    public function index($id="")
    {
        if(empty($id))
        {
            is_userLoggedIn(TRUE);//if not login then redirect to access deny    
            $id=get_userLoggedIn("id"); 
        }
        else
            $id=decrypt($id);
			
        $this->data["page_title"]="User Profile";
		
		/*** 
		* You have a valid token from linkedin . Now fetch your profile below 
		***/
		if(isset($_GET['code']))
		{
			if ($_SESSION['state'] == $_GET['state']) {
				// Get token so you can make API calls
				$this->getAccessToken();
			}
			$user = $this->fetch('GET', '/v1/people/~:(firstName,lastName,summary,emailAddress,phoneNumbers,educations,headline,pictureUrl,positions,id,certifications,dateOfBirth)');
		}		
		if(isset($_GET['code']))
		{
			//pr($user,1);
			// get here all the information available from linkedin and update the profile info
			$detail_arr = array();
			$detail_arr["s_about_me"]=$user->headline;
			$detail_arr["s_name"]=$user->firstName.' '.$user->lastName;
			$detail_arr["s_display_name"]=$user->firstName.' '.$user->lastName;
			
			$dob = $user->dateOfBirth;
			if($dob->day!='' && $dob->month!='' && $dob->year!='')
			{
				$detail_arr["dt_dob"] =$dob->year.'-'.$dob->month.'-'.$dob->day.' 00:00:00';
				$detail_arr["i_age"] =calculate_age($detail_arr["dt_dob"]);
			}		
			
			$cond = array('uid'=>$id);
			$i_aff = $this->user_model->update_user_details($detail_arr,$cond);
			
			// now fetch the education details
			$educations = $user->educations->values;
			if(!empty($educations))
			{
				// delete previous records of education
				$where = array('uid'=>$id);
				$i_del = $this->user_education_model->delete_user_education($where);
				
				foreach($educations as $k=>$v)
				{
					$edu_arr = array();
					$edu_arr["uid"] = $id;
					$edu_arr["s_instutite"] 		= $v->schoolName;
					$edu_arr["s_degree"] 			= $v->degree;
					$edu_arr["s_specilization"] 	= $v->fieldOfStudy;
					$edu_arr["s_desc"] 				= $v->notes;
					$edu_arr["dt_from"] 			= $v->startDate->year.'-'.'01'.'-'.'01';
					$edu_arr["dt_to"] 				= $v->endDate->year.'-'.'01'.'-'.'01';
					
					$i_ins = $this->user_education_model->add_user_education($edu_arr);
				}
			}
			
			// now fetch the professional details
			$positions = $user->positions->values;
			if(!empty($positions))
			{
				// delete previous records of profession
				$where = array('uid'=>$id);
				$i_del = $this->user_profession_model->delete_user_profession($where);
				
				foreach($positions as $k=>$v)
				{
					$prof_arr = array();
					$prof_arr["uid"] = $id;
					$prof_arr["s_company"] 			= $v->company->name;
					$prof_arr["s_title"] 			= $v->title;
					$prof_arr["s_job_desc"] 			= $v->summary;
					$prof_arr["s_location"] 			= "";
					$prof_arr["dt_from"] 			= $v->startDate->year.'-'.$v->startDate->month.'-'.'01';
					$prof_arr["dt_to"] 				= $v->endDate->year.'-'.$v->endDate->month.'-'.'01';
					$prof_arr["i_currently_working"] = ($v->isCurrent==1)?1:0;
					
					$i_ins = $this->user_profession_model->add_user_profession($prof_arr);
				}
			}
		}
		/*** END LINKEDIN PROFILE FETCH ***/
		
        $user=$this->user_model->user_load(intval($id));        
        //pr($user);
		
		if(!empty($user))		
		{
			// update profile complete percent
			$completed = user_profile_prc_calculation($id);
			$this->user_model->update_user(
				array(  "i_profile_complete_percent"=>intval($completed) ),
				array("id"=>intval($id)));
			
		}
			
		$private_prof = true;
        ///incrementing the view count and visits count////
        if($id!=get_userLoggedIn("id"))
        {
			
            /* this portion is for fb fetch friend cron purpose */
            $s_extra_fetch_friend=unserialize($user->s_extra_fetch_friend);

            if(empty($s_extra_fetch_friend)) // if s_extra_fetch_friend is empty
                $s_extra_fetch_friend=array('count_visits'=>1,'count_login'=>0); // initialize the array
            else
                $s_extra_fetch_friend['count_visits']+=1;  // increase the count_visits 

            /* end of fb fetch friend cron purpose */

			$private_prof = false;
            $this->user_model->update_user(
            array(  "i_view_count"=>(intval($user->i_view_count)+1),
            "s_extra_fetch_friend"=>serialize($s_extra_fetch_friend) ),
            array("id"=>intval($id)));
        }        
		
		$this->data["private_prof"] = $private_prof;
        $form_token=encrypt($id);
        /////full name////
        $action="edit";
        $default_value[0]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_name"=>trim(@$user->s_name),
        ));
        ////user gender//// 
        $default_value[1]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "e_gender"=>trim(@$user->e_gender),
        ));        

        ////user gender//// 
        $default_value[2]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "dt_dob"=>format_date(@$user->dt_dob),
		"i_age"=>intval(@$user->i_age)?intval(@$user->i_age):calculate_age(@$user->dt_dob),
        )); 

        ////user location//// 
        $default_value[3]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "country_id"=>intval(@$user->country_id),
        "zip_id"=>intval(@$user->zip_id),
        "state_id"=>intval(@$user->state_id),
        "city_id"=>intval(@$user->city_id),
        "zip_code"=>trim(get_zipCode(intval(@$user->zip_id))),
        "city_name"=>trim(get_cityName(intval(@$user->city_id))),
        ));

        ////////user languages///////////
        $default_value[4]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        //s_languages IS array(array("lang"=>"English","proficency"=>"Language proficient")...)
        "add_more_lang"=>unserialize(@$user->s_languages),
        ));     

        ////////user about me///////////
        $default_value[5]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        //s_languages IS array(array("lang"=>"English","proficency"=>"Language proficient")...)
        "s_about_me"=>format_text(@$user->s_about_me),
        ));     

        ////////user profession///////////
        $profession=$this->user_profession_model->user_profession_load(array("uid"=>intval($id)));
        //adding encrypting id("s_token") within each row stdClass//
        $profession=array_map("addEncIDCallback",$profession);
        array_walk_recursive($profession,"modifyDispDateCallback","dt_from");
        array_walk_recursive($profession,"modifyDispDateCallback","dt_to");
		array_walk_recursive($profession,"modifyFormatCallback",'s_company');
		array_walk_recursive($profession,"modifyFormatCallback",'s_title');
		array_walk_recursive($profession,"modifyFormatCallback",'s_job_desc');


        //pr($profession);
        $default_value[6]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_profession"=>$profession,
        ));                                                          

        ////////user education///////////
        $education=$this->user_education_model->user_education_load(array("uid"=>intval($id)));
        //adding encrypting id("s_token") within each row stdClass//
        $education=array_map("addEncIDCallback",$education);
        array_walk_recursive($education,"modifyDispDateCallback",array("dt_from","dt_from_education"));
        array_walk_recursive($education,"modifyDispDateCallback",array("dt_to","dt_to_education"));
        
		//array_walk_recursive($education,"modifyFormatCallback",'s_desc');
        array_walk_recursive($education,"modifyFormatCallback",'s_instutite');
        array_walk_recursive($education,"modifyFormatCallback",'s_degree');
        array_walk_recursive($education,"modifyFormatCallback",'s_specilization');

        //pr($education);
        $default_value[7]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_education"=>$education,
        ));


        ////////user certificate///////////
        $certificate=$this->user_certificate_model->user_certificate_load(array("uid"=>intval($id)));
        //adding encrypting id("s_token") within each row stdClass//
        $certificate=array_map("addEncIDCallback",$certificate);
        array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_from","dt_from_certificate"));
        array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_to","dt_to_certificate"));
        array_walk_recursive($certificate,"modifyFormatCallback",'s_desc');
		array_walk_recursive($certificate,"modifyFormatCallback",'s_certified_from');
        array_walk_recursive($certificate,"modifyFormatCallback",'s_certificate_name');
		array_walk_recursive($certificate,"modifyFormatCallback",'s_certificate_number');

        //pr($certificate);
        $default_value[8]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_certificate"=>$certificate,
        ));        


        ////////user license///////////
        $license=$this->user_license_model->user_license_load(array("uid"=>intval($id)));
        //adding encrypting id("s_token") within each row stdClass//
        $license=array_map("addEncIDCallback",$license);
        array_walk_recursive($license,"modifyDispDateCallback",array("dt_from","dt_from_license"));
        array_walk_recursive($license,"modifyDispDateCallback",array("dt_to","dt_to_license"));
        array_walk_recursive($license,"modifyFormatCallback",'s_desc');
		array_walk_recursive($license,"modifyFormatCallback",'s_license_number');
		array_walk_recursive($license,"modifyFormatCallback",'s_license_name');
		array_walk_recursive($license,"modifyFormatCallback",'s_licensed_from');

        //pr($license);
        $default_value[9]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_license"=>$license,
        ));    


        ////////user skill///////////
        $user_skill=$this->user_skill_model->user_skill_load(array("uid"=>intval($id)));
        array_walk_recursive($user_skill,"modifyUnSerialCallback",array("s_endorses","s_endorses_unserialized"));
        $default_value[10]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_skill_name"=>"",
        ));                                           


		
        $this->data["default_value"]= $default_value;
        //$this->data["action"]=$action;        
		
        /**
        * user service
        * User can add only one service. 
        * Where as company can add multiple services.
        */
        $user_service=$this->user_service_model->user_service_load(array("s.uid"=>intval($id)));
        $this->data["user_service"]= @$user_service[0];

        /**
        * User public private field access
        */
        $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>intval($id)));
        $this->data["user_pp"]= @$user_pp[0];
        $this->data["uid"]= $id;
		
        /**
        * User Skills
        */
        /*$user_skill=$this->user_skill_model->user_skill_load(array("uid"=>intval($id)));*/       
        $this->data['user_skill']=$user_skill;
        //pr($user_skill); 
        $this->render();
    }


    public function ajax_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );    

        $posted=array();
        $posted["action"] = trim($this->input->post("action"));
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));         


        ///user fullname///
        if(isset($_POST["s_name"]))
        {
            $posted["s_name"] = trim($this->input->post("s_name"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('s_name', 'full name', 'required');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $dml_val=array(
                "s_name"=>$posted["s_name"],
                );


                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_name']     = $posted["s_name"];

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));



                if($ret)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }

        ///user gender///
        if(isset($_POST["e_gender"]))
        {
            $posted["e_gender"] = trim($this->input->post("e_gender"));
            $posted["i_gender"] = trim($this->input->post("frm_gender_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('e_gender', 'gender', 'required');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $dml_val=array(
                "e_gender"=>$posted["e_gender"],
                );

                //pr($posted,1);
                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );
                ///updating the field privacy///
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_gender"=>user_public_private($posted["i_gender"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['e_gender']     = $posted["e_gender"];

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));                
                if(!$ret_upp && empty($user_pp))
                {
                    $temp=$temp+array("uid"=>$posted["form_token"]);
                    $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }
                ///end updating the field privacy///


                if($ret || $ret_upp)//success
                {
                    
                    // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
                    $uid=intval($posted["form_token"]);
                    $services= $this->user_service_model->user_service_load(array('uid'=>$uid));
                    if(!empty($services))  
                    {
                        foreach($services as $k=>$service )
                        {
                            /* setting up the s_dummy field value */
                            $val = decodeFromDummyField($service->s_dummy);
                            $val['filter_search_gender'] =$posted["e_gender"];
                            $temp_s_dummy = encodeArrayToDummyField($val);                
                            
                            $this->user_service_model->update_user_service(
                                    array(
                                        "s_dummy"=>$temp_s_dummy,
                                        ),
                                    array('id'=>$service->id));                            
                        }
                    }                    
                    // end FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
                    
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }        

        ///user dob///
        if(isset($_POST["dt_dob"]))
        {
            $posted["dt_dob"] = trim($this->input->post("dt_dob"));
            $posted["i_dob"] = trim($this->input->post("frm_age_privacy"));
			
            //////validation////////
            /*$this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('dt_dob', 'gender', 'required');			
            if($this->form_validation->run() == FALSE)/////invalid*/
			
			
			if($posted["dt_dob"] == '' || $posted["form_token"]=='')/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
				
                $ret=FALSE;
                $dml_val=array(
                "dt_dob"=>format_date($posted["dt_dob"],"Y-m-d"),
                "i_age"=>calculate_age($posted["dt_dob"])
                );
				
                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );
				
                ///updating the field privacy///
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_dob"=>user_public_private($posted["i_dob"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                ///end updating the field privacy///

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['dt_dob']      = format_date($posted["dt_dob"],"Y-m-d");
                $val['i_age']       = calculate_age($posted["dt_dob"]);

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));        

                if(!$ret_upp && empty($user_pp))
                {
                    $temp=$temp+array("uid"=>$posted["form_token"]);
                    $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }



                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }   

        ///user location///
        if(isset($_POST["country_id"]))
        {
            $posted["country_id"] = trim($this->input->post("country_id"));
            $posted["zip_id"] = trim($this->input->post("zip_id"));
            $posted["city_id"] = trim($this->input->post("city_id"));
            $posted["state_id"] = trim($this->input->post("state_id"));
            $posted["i_location"] = trim($this->input->post("frm_location_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('country_id', 'country', 'required');
            $this->form_validation->set_rules('zip_id', 'zip', 'required');
            $this->form_validation->set_rules('city_id', 'city', 'required');


            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $dml_val=array(
                "country_id"=>intval($posted["country_id"]),
                "zip_id"=>intval($posted["zip_id"]),
                "city_id"=>intval($posted["city_id"]),
                "state_id"=>intval($posted["state_id"]),
                );

                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );
                ///updating the field privacy///
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_location"=>user_public_private($posted["i_location"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                ///end updating the field privacy///

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['country_id']      =   $posted["country_id"];
                $val['zip_id']          =   $posted["zip_id"];
                $val['city_id']         =   $posted["city_id"];
                $val['state_id']        =   $posted["state_id"];

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));        

                if(!$ret_upp && empty($user_pp))
                {
                    $temp=$temp+array("uid"=>$posted["form_token"]);
                    $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }



                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }           

        ///user languages///
        //s_languages IS array(array("lang"=>"English","proficency"=>"Language proficient")...)
        if(isset($_POST["add_more_lang"]))
        {
            $posted["add_more_lang"] = $this->input->post("add_more_lang");
            $posted["i_language"] = trim($this->input->post("frm_language_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_lang[]', 'language', 'required');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {

                $ret=FALSE;
                $dml_val=array(
                "s_languages"=>serialize($posted["add_more_lang"]),
                );

                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );
                ///updating the field privacy///
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_language"=>user_public_private($posted["i_language"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );

                //generating the string  wtih comma(,) separated language name//
                $languages='';

                foreach($posted['add_more_lang'] as $k=>$vl)
                    $languages.=(!empty($languages)) ? ','.$vl['lang'] : $vl['lang'];

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                //pr($val,1);
                $val['s_languages'] = $languages;
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token'])); 

                if(!$ret_upp && empty($user_pp))
                {
                    $temp=$temp+array("uid"=>$posted["form_token"]);
                    $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }
                ///end updating the field privacy///


                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }      

        ///user about me///
        if(isset($_POST["s_about_me"]))
        {
            $posted["s_about_me"] = trim($this->input->post("s_about_me"));
            $posted["i_about"] = trim($this->input->post("frm_about_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            //$this->form_validation->set_rules('s_about_me', 'gender', 'required');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                $dml_val=array(
                "s_about_me"=>format_text(trim($posted["s_about_me"]),'encode'),
                );

                $ret=$this->user_model->update_user_details($dml_val,
                array("uid"=>$posted["form_token"])
                );
                ///updating the field privacy///
                $ret_upp=FALSE;
                /*
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_about"=>user_public_private($posted["i_about"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
                ///end updating the field privacy///


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_about_me']      =   format_text($posted["s_about_me"],"encode");

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));        


                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }        


    } 


    public function ajax_profession_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );    

        $posted=array();
        //here action could be add or edit in addmore, so ignoring this.
        $posted["action"] = trim($this->input->post("action"));  
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));   

        ///user languages///
        if(isset($_POST["add_more_profession"]))
        {
            //pr($_POST);
            $posted["add_more_profession"] = $this->input->post("add_more_profession");

            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('add_more_profession_vld[s_title][]', 'title', 'required');
            $this->form_validation->set_rules('add_more_profession_vld[s_company][]', 'organization', 'required');
            $this->form_validation->set_rules('add_more_profession_vld[s_location][]', 'job location', 'required');
            $this->form_validation->set_rules('add_more_profession_vld[dt_from][]', 'date from', 'required');            

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                //pr($posted,1);

                if(!empty($posted["add_more_profession"]))
                {
                    $active_ids=array();///tracking all active ids
                    $profession=$this->user_profession_model->user_profession_load(array("uid"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_profession"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "uid"=>intval($posted["form_token"]),
                        "s_company"=>trim(@$p["s_company"]),
                        "s_title"=>trim(@$p["s_title"]),
                        "s_job_desc"=>format_text(trim(@$p["s_job_desc"]),'encode'),
                        "s_location"=>trim(@$p["s_location"]),
                        "dt_from"=>format_date(@$p["dt_from"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to"],"Y-m-d"),
                        "i_currently_working"=>intval(@$p["i_currently_working"]),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_profession_model->add_user_profession($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_profession_model->update_user_profession($dml_val,
                            array("id"=>$p["s_token"])
                            );
                            $active_ids[]= $p["s_token"];                             
                        }


                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;
                    }

                    /**
                    * Delete
                    */
                    $active_ids=array_unique($active_ids);
                    foreach($profession as $k=>$p)
                    {
                        if(!in_array($p->id,$active_ids))
                        {
                            $stat=$this->user_profession_model->delete_user_profession(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }
                }

                ///updating the field privacy///
                $ret_upp=FALSE;
                /*
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_professional"=>user_public_private($posted["i_professional"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
                ///end updating the field privacy///

                $s_company = 
                $s_title = 
                $s_job_desc = 
                $s_location = 
                $dt_from= 
                $dt_to = 
                $i_currently_working = '';

                foreach($posted['add_more_profession'] as $k=>$vl)
                {
                    $s_company .= (!empty($s_company)) ? ','.$vl['s_company'] : $vl['s_company'];
                    $s_title .= (!empty($s_title)) ? ','.$vl['s_title'] : $vl['s_title'];
                    //$s_job_desc .= (!empty($s_job_desc)) ? ','.$vl['s_job_desc'] : $vl['s_job_desc'];
                    $s_location .= (!empty($s_location)) ? ','.$vl['s_location'] : $vl['s_location'];
                    $dt_from .= (!empty($dt_from)) ? ','.format_date($vl['dt_from'],'Y-m-d') : format_date($vl['dt_from'],'Y-m-d');
                    $dt_to .= (!empty($dt_to)) ? ','.format_date($vl['dt_to'],"Y-m-d") : format_date($vl['dt_to'],"Y-m-d");
                    $i_currently_working .= (!empty($i_currently_working)) ? ','.$vl['i_currently_working'] : $vl['i_currently_working']; ;
                }


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_company']=$s_company;
                $val['s_title']=$s_title;
                //$val['s_job_desc']=$s_job_desc;
                $val['s_location']=$s_location;
                $val['dt_from_profession']=$dt_from; 
                $val['dt_to_profession']=$dt_to;     
                $val['i_currently_working']=$i_currently_working;

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));                        


                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }             


    } 


    public function ajax_education_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );



        $posted=array();
        //here action could be add or edit in addmore, so ignoring this.
        $posted["action"] = trim($this->input->post("action"));  
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));   

        ///user languages///
        if(isset($_POST["add_more_education"]))
        {
            //pr($_POST);
            $posted["add_more_education"] = $this->input->post("add_more_education");

            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('add_more_education_vld[s_instutite][]', 'school', 'required');
            $this->form_validation->set_rules('add_more_education_vld[s_specilization][]', 'field of study', 'required');
            $this->form_validation->set_rules('add_more_education_vld[dt_from_education][]', 'druration from', 'required');
            $this->form_validation->set_rules('add_more_education_vld[dt_to_education][]', 'druration to', 'required');  
            $this->form_validation->set_rules('add_more_education_vld[s_degree][]', 'degree', 'required');


            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;


                if(!empty($posted["add_more_education"]))
                {
                    $active_ids=array();///tracking all active ids
                    $education=$this->user_education_model->user_education_load(array("uid"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_education"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "uid"=>intval($posted["form_token"]),
                        "s_instutite"=>trim(@$p["s_instutite"]),
                        "s_degree"=>trim(@$p["s_degree"]),
                        "s_specilization"=>trim(@$p["s_specilization"]),
                        "s_desc"=>format_text(trim(@$p["s_desc"]),'encode'),
                        "dt_from"=>format_date(@$p["dt_from_education"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to_education"],"Y-m-d"),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_education_model->add_user_education($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_education_model->update_user_education($dml_val,
                            array("id"=>$p["s_token"])
                            );
                            $active_ids[]= $p["s_token"];                             
                        }


                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;
                    }

                    /**
                    * Delete
                    */
                    $active_ids=array_unique($active_ids);
                    foreach($education as $k=>$p)
                    {
                        if(!in_array($p->id,$active_ids))
                        {
                            $stat=$this->user_education_model->delete_user_education(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }
                }

                ///updating the field privacy///
                $ret_upp=FALSE;
                /*
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_professional"=>user_public_private($posted["i_professional"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
                ///end updating the field privacy///

                $s_instutite = 
                $s_degree = 
                $s_specilization = 
                $dt_from = 
                $dt_to= 
                $s_desc = '';

                foreach($posted['add_more_education'] as $k=>$vl)
                {
                    $s_instutite .= (!empty($s_instutite)) ? ','.$vl['s_instutite'] : $vl['s_instutite'];
                    $s_degree .= (!empty($s_degree)) ? ','.$vl['s_degree'] : $vl['s_degree'];
                    $s_specilization .= (!empty($s_specilization)) ? ','.$vl['s_specilization'] : $vl['s_specilization'];
                    $dt_from .= (!empty($dt_from)) ? ','.format_date($vl['dt_from_education'],"Y-m-d") : format_date($vl['dt_from_education'],"Y-m-d");
                    $dt_to .= (!empty($dt_to)) ? ','.format_date($vl['dt_to_education'],"Y-m-d") : format_date($vl['dt_to_education'],"Y-m-d");
                    $s_desc .= (!empty($s_desc)) ? ','.format_text($vl['s_desc'],"encode") : format_text($vl['s_desc'],"encode");
                }


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_instutite']=$s_instutite;
                $val['s_degree']=$s_degree;
                $val['s_specilization']=$s_specilization;
                $val['dt_from_education']=$dt_from;
                $val['dt_to_education']=$dt_to;
                $val['s_desc_education']=$s_desc;     

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));                


                if($ret || $ret_upp)//success
                {

                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }             


    } 


    public function ajax_certificate_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );    

        $posted=array();
        //here action could be add or edit in addmore, so ignoring this.
        $posted["action"] = trim($this->input->post("action"));  
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));   

        ///user languages///
        if(isset($_POST["add_more_certificate"]))
        {
            //pr($_POST);
            $posted["add_more_certificate"] = $this->input->post("add_more_certificate");

            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('add_more_certificate_vld[s_certificate_name][]', 'certification on', 'required');
            $this->form_validation->set_rules('add_more_certificate_vld[s_certificate_number][]', 'number', 'required');
            $this->form_validation->set_rules('add_more_certificate_vld[dt_from_certificate][]', 'druration from', 'required');
            $this->form_validation->set_rules('add_more_certificate_vld[dt_to_certificate][]', 'druration to', 'required');  
            $this->form_validation->set_rules('add_more_certificate_vld[s_certified_from][]', 'organigation', 'required');


            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                //pr($_POST);
                // pr($posted,1);

                if(!empty($posted["add_more_certificate"]))
                {
                    $active_ids=array();///tracking all active ids
                    $certificate=$this->user_certificate_model->user_certificate_load(array("uid"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_certificate"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "uid"=>intval($posted["form_token"]),
                        "s_certificate_name"=>trim(@$p["s_certificate_name"]),
                        "s_certificate_number"=>trim(@$p["s_certificate_number"]),
                        "s_desc"=>format_text(trim(@$p["s_desc"]),'encode'),
                        "s_certified_from"=>trim(@$p["s_certified_from"]),
                        "dt_from"=>format_date(@$p["dt_from_certificate"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to_certificate"],"Y-m-d"),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        //pr($p);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_certificate_model->add_user_certificate($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_certificate_model->update_user_certificate($dml_val,
                            array("id"=>$p["s_token"])
                            );
                            $active_ids[]= $p["s_token"];                             
                        }


                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;
                    }

                    /**
                    * Delete
                    */
                    $active_ids=array_unique($active_ids);
                    foreach($certificate as $k=>$p)
                    {
                        if(!in_array($p->id,$active_ids))
                        {
                            $stat=$this->user_certificate_model->delete_user_certificate(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }
                }

                ///updating the field privacy///
                $ret_upp=FALSE;
                /*
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_professional"=>user_public_private($posted["i_professional"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
                ///end updating the field privacy///
                $certificate_name = 
                $certificate_number = 
                $certified_from = 
                $dt_from_certificate = 
                $dt_to_certificate = 
                $desc = '';

                foreach($posted['add_more_certificate'] as $k=>$vl)
                {
                    $certificate_name .= (!empty($certificate_name)) ? ','.$vl['s_certificate_name'] : $vl['s_certificate_name'];
                    $certificate_number .= (!empty($certificate_number)) ? ','.$vl['s_certificate_number'] : $vl['s_certificate_number'];
                    $certified_from .= (!empty($certified_from)) ? ','.$vl['s_certified_from'] : $vl['s_certified_from'];
                    $dt_from_certificate .= (!empty($dt_from_certificate)) ? ','.format_date($vl['dt_from_certificate'],"Y-m-d") : format_date($vl['dt_from_certificate'],"Y-m-d");
                    $dt_to_certificate .= (!empty($dt_to_certificate)) ? ','. format_date($vl['dt_to_certificate'],"Y-m-d") : format_date($vl['dt_to_certificate'],"Y-m-d");
                    $desc .= (!empty($desc)) ? ','.format_text($vl['s_desc'],"encode") : format_text($vl['s_desc'],"encode");
                }


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                $val['s_certificate_name']=$certificate_name;
                $val['s_certificate_number']=$certificate_number;
                $val['s_certified_from']=$certified_from;
                $val['dt_from_certificate']=$dt_from_certificate;
                $val['dt_to_certificate']=$dt_to_certificate;
                $val['s_desc_certificate']=$desc;     

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));





                if($ret || $ret_upp)//success
                {
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }             


    } 

    public function ajax_license_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );    

        $posted=array();
        //here action could be add or edit in addmore, so ignoring this.
        $posted["action"] = trim($this->input->post("action"));  
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));   

        ///user languages///
        if(isset($_POST["add_more_license"]))
        {
            //pr($_POST);
            $posted["add_more_license"] = $this->input->post("add_more_license");

            //$posted["i_license"] = trim($this->input->post("frm_license_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('add_more_license_vld[s_license_name][]', 'certification on', 'required');
            $this->form_validation->set_rules('add_more_license_vld[s_license_number][]', 'number', 'required');
            $this->form_validation->set_rules('add_more_license_vld[dt_from_license][]', 'druration from', 'required');
            $this->form_validation->set_rules('add_more_license_vld[dt_to_license][]', 'druration to', 'required');  
            $this->form_validation->set_rules('add_more_license_vld[s_licensed_from][]', 'organigation', 'required');


            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                $ret=FALSE;


                if(!empty($posted["add_more_license"]))
                {
                    $active_ids=array();///tracking all active ids
                    $license=$this->user_license_model->user_license_load(array("uid"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_license"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "uid"=>intval($posted["form_token"]),
                        "s_license_name"=>trim(@$p["s_license_name"]),
                        "s_license_number"=>trim(@$p["s_license_number"]),
                        "s_desc"=>format_text(trim(@$p["s_desc"]),'encode'),
                        "s_licensed_from"=>trim(@$p["s_licensed_from"]),
                        "dt_from"=>format_date(@$p["dt_from_license"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to_license"],"Y-m-d"),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        //pr($p);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_license_model->add_user_license($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_license_model->update_user_license($dml_val,
                            array("id"=>$p["s_token"])
                            );
                            $active_ids[]= $p["s_token"];                             
                        }


                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;
                    }

                    /**
                    * Delete
                    */
                    $active_ids=array_unique($active_ids);
                    foreach($license as $k=>$p)
                    {
                        if(!in_array($p->id,$active_ids))
                        {
                            $stat=$this->user_license_model->delete_user_license(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }
                }

                ///updating the field privacy///
                $ret_upp=FALSE;
                /*
                $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["form_token"]));
                $temp=array("i_professional"=>user_public_private($posted["i_professional"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
                ///end updating the field privacy///

                $license_name = 
                $license_number = 
                $license_from = 
                $dt_from = 
                $dt_to= 
                $desc = '';

                foreach($posted['add_more_license'] as $k=>$vl)
                {
                    $license_name .= (!empty($license_name)) ? ','.$vl['s_license_name'] : $vl['s_license_name'];
                    $license_number .= (!empty($license_number)) ? ','.$vl['s_license_number'] : $vl['s_license_number'];
                    $license_from .= (!empty($license_from)) ? ','.$vl['s_licensed_from'] : $vl['s_licensed_from'];
                    $dt_from .= (!empty($dt_from)) ? ','. format_date($vl['dt_from_license'],"Y-m-d") : format_date($vl['dt_from_license'],"Y-m-d");
                    $dt_to .= (!empty($dt_to)) ? ','. format_date($vl['dt_to_license'], "Y-m-d") : format_date($vl['dt_to_license'], "Y-m-d");
                    $desc .= (!empty($desc)) ? ','. format_text($vl['s_desc'],"encode") : format_text($vl['s_desc'], "encode");
                }


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_license_name']=$license_name;
                $val['s_license_number']=$license_number;
                $val['s_licensed_from']=$license_from;
                $val['dt_from_license']=$dt_from;
                $val['dt_to_license']=$dt_to;
                $val['license_s_desc']=$desc;     

                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy), array('id'=>$posted['form_token']));




                if($ret || $ret_upp)//success
                {
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }
                else//error
                {
                    $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    
                }                
            }            

        }             


    }     

    public function ajax_skill_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "form_token"=>"",
        );    

        $posted=array();
        //here action could be add or edit in addmore, so ignoring this.
        $posted["action"] = trim($this->input->post("action"));  
        $posted["form_token"]= decrypt(trim($this->input->post("form_token")));   

        ///user languages///
        if(isset($_POST["s_skill_name"]))
        {
            //pr($_POST);
            $posted["s_skill_name"] = $this->input->post("s_skill_name");

            //$posted["i_license"] = trim($this->input->post("frm_license_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_skill_name', 'skill', 'required|callback_duplicate_skill[form_token]');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
				$duplicate = $this->user_skill_model->checking_duplicate_skills(intval($posted['form_token']),$posted["s_skill_name"]);
				
				if($duplicate)
				{
						$ajx_ret["mode"]="error";
						$ajx_ret["message"]= message_line("duplicate_skill");   
						echo json_encode($ajx_ret);
						return FALSE;
				}
				else
				{
						$ret=FALSE;

						$ret_upp=FALSE;
						$dml_val=array(
						"uid"=>$posted["form_token"],
						"s_skill_name"=>trim($posted["s_skill_name"]),
						);                
		
						$ret=$this->user_skill_model->add_user_skill($dml_val);
		
						$dummy = $this->user_model->fetch_dummy(intval($posted["form_token"]));
						$skills = $this->user_skill_model->user_skill_load(array("uid"=>intval($posted['form_token'])));
		
						$val = decodeFromDummyField($dummy->s_dummy);
						foreach($skills as $k=>$vl)
							$data  .= (!empty($data)) ? ','.$vl->s_skill_name : $vl->s_skill_name;
						$val['s_skill_name'] = $data;
						$temp_s_dummy = encodeArrayToDummyField($val);
		
						$retd=$this->user_model->update_user(array('s_dummy'=>$temp_s_dummy),
						array("id"=>intval($posted["form_token"]))
						);
		
						if($ret)//success
						{
							$ajx_ret["mode"]="success";
							$ajx_ret["message"]= message_line("saved success");
							$ajx_ret["rel"]=$ret;
							echo json_encode($ajx_ret);
							return TRUE;                    
						}
						else//error
						{
							$ajx_ret["mode"]="error";
							$ajx_ret["message"]= message_line("saved error");   
							echo json_encode($ajx_ret);
							return TRUE;                    
						}  
				} // end duplicate else part
                              
            }            

        }             


    } 

    public function duplicate_skill($str,$form_token)
    {
        $form_token=decrypt($form_token);
        $user_skills=$this->user_skill_model->user_skill_load(array("uid"=>$form_token,"s_skill_name"=>trim($str)));

        if(!empty($user_skills))
        {
            $this->form_validation->set_message('duplicate_skill', 'The %s field value is already exists.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }        
    } 


    /**
    * delete skill
    */
    public function ajax_skillDelete_operation()
    {
        $posted=$this->input->post();
        $posted["form_token"]=decrypt($posted["form_token"]);

        $condition=array("id"=>intval($posted["rel"]),"uid"=>intval($posted["form_token"]));

        $ret=$this->user_skill_model->delete_user_skill($condition);

        $dummy = $this->user_model->fetch_dummy(intval($posted["form_token"]));
        $skills = $this->user_skill_model->user_skill_load(array("uid"=>intval($posted['form_token'])));

        $val = decodeFromDummyField($dummy->s_dummy);
        foreach($skills as $k=>$vl)
            $data  .= (!empty($data)) ? ','.$vl->s_skill_name : $vl->s_skill_name;
        $val['s_skill_name'] = $data;
        $temp_s_dummy = encodeArrayToDummyField($val);

        $ret=$this->user_model->update_user(array('s_dummy'=>$temp_s_dummy),
        array("id"=>intval($posted["form_token"]))
        );

        if($ret)
            echo 'success';

    }    

    /**
    * delete skill
    */
    public function ajax_skillEndorse_operation()
    {
        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>"",//html string  
        "endorsed_user"=>"",
        );         

        if(!is_userLoggedIn())
        {
            $ajx_ret["mode"]="error";
            echo json_encode($ajx_ret);
            return FALSE;
        } 


        $posted=$this->input->post();
        $posted["form_token"]=decrypt($posted["form_token"]);

        $data=$this->user_skill_model->user_skill_load(intval($posted["rel"]));

        $endorses=array();
        if(!empty($data->s_endorses))
        {
            $endorses=unserialize(@$data->s_endorses);
            /**
            * Checking the user had already endorsed 
            */
            foreach($endorses as $k=>$v)
            {
                if(intval($v["endorsed_by"])==get_userLoggedIn('id'))
                {
                    $ajx_ret["mode"]="error";
                    echo json_encode($ajx_ret);
                    return FALSE;                   
                }
            }///end for
        }


        $endorses[]=array("endorsed_by"=>get_userLoggedIn('id'),"On"=>date("Y-m-d"));

        $ret=$this->user_skill_model->update_user_skill(
        array("s_endorses"=>serialize($endorses),"i_endorse_count"=>count($endorses)),
        array("id"=>intval($posted["rel"]))
        );
        if($ret)
        {
            $ajx_ret["mode"]="success";
            $ajx_ret["endorsed_user"]=theme_user_thumb_picture(get_userLoggedIn('id'));
            $ajx_ret["endorsed_count"]=count($endorses);
            echo json_encode($ajx_ret);
            return FALSE;           
        }

    } 


    /**
    * @uses Change short url check uniqueness beforeinserting in table
    * @author Kallol Basu <mail@kallol.net>
    *  element Description
    */
    public function change_short_url(){
        if($posted = $this->input->post()){

            //checking in user_company table
            $condition=array("uc.s_short_url"=>$posted['s_short_url']);
            $companyrs =$this->user_company_model->user_company_load($condition); 

            //checking in user_service table
            $condition=array("s.s_short_url"=>$posted['s_short_url']);
            $servicers =$this->user_service_model->user_service_load($condition);

            $this->form_validation->set_rules('s_short_url', 'Short Url', 'required|is_unique[users.s_short_url]');
            if($this->form_validation->run() && empty($companyrs) && empty($servicers))/////invalid
            {
                $ret=$this->user_model->update_user(
                    array("s_short_url"=>$posted['s_short_url'],"i_is_short_url_editable"=>0),
                    array("id"=>get_userLoggedIn("id"))
                    );
                    
                if($ret)      
                    echo $posted['s_short_url'];
            }
        } 
    }


	/**
	*  following function are used for connect with linkedin
	*  see @ top above
	*/
	public function connect_linkedin()
    {	
		
       	// OAuth 2 Control Flow
		if (isset($_GET['error'])) {
			// LinkedIn returned an error
			print $_GET['error'] . ': ' . $_GET['error_description'];
			exit;
		} elseif (isset($_GET['code'])) {
			// User authorized your application
			if ($_SESSION['state'] == $_GET['state']) {
				// Get token so you can make API calls
				$this->getAccessToken();
			} else {
				// CSRF attack? Or did you mix up your states?
				exit;
			}
		} else { 
			$this->getAuthorizationCode();
			if ((empty($_SESSION['expires_at'])) || (time() > $_SESSION['expires_at'])) {
				// Token has expired, clear the state
				$_SESSION = array();
			}
			if (empty($_SESSION['access_token'])) {
				// Start authorization process
				$this->getAuthorizationCode();
			}
		}
		 
    }	
	
	public function getAuthorizationCode() {
		$params = array('response_type' => 'code',
						'client_id' => API_KEY,
						'scope' => SCOPE,
						'state' => uniqid('', true), // unique long string
						'redirect_uri' => REDIRECT_URI,
				  );
	 
		// Authentication request
		$url = 'https://www.linkedin.com/uas/oauth2/authorization?' . http_build_query($params);
		 
		// Needed to identify request when it returns to us
		$_SESSION['state'] = $params['state'];
	 
		// Redirect user to authenticate
		header("Location: $url");
		exit;
	}
		 
	public function getAccessToken() {
		$params = array('grant_type' => 'authorization_code',
						'client_id' => API_KEY,
						'client_secret' => API_SECRET,
						'code' => $_GET['code'],
						'redirect_uri' => REDIRECT_URI,
				  );
		 
		// Access Token request
		$url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
		 
		// Tell streams to make a POST request
		$context = stream_context_create(
						array('http' => 
							array('method' => 'POST',
							)
						)
					);
	 
		// Retrieve access token information
		$response = file_get_contents($url, false, $context);
	 
		// Native PHP object, please
		$token = json_decode($response);
	 
		// Store access token and expiration time
		$_SESSION['access_token'] = $token->access_token; // guard this! 
		$_SESSION['expires_in']   = $token->expires_in; // relative time (in seconds)
		$_SESSION['expires_at']   = time() + $_SESSION['expires_in']; // absolute time
		 
		return true;
	}
	 
	public function fetch($method, $resource, $body = '') {
		$params = array('oauth2_access_token' => $_SESSION['access_token'],
						'format' => 'json',
				  );
		 
		// Need to use HTTPS
		$url = 'https://api.linkedin.com' . $resource . '?' . http_build_query($params);
		// Tell streams to make a (GET, POST, PUT, or DELETE) request
		$context = stream_context_create(
						array('http' => 
							array('method' => $method,
							)
						)
					);
	 
	 
		// Hocus Pocus
		$response = file_get_contents($url, false, $context);
	 
		// Native PHP object, please
		return json_decode($response);
	}
	/** end function are used for connect with linkedin **/

    public function __destruct(){}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */