<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe User/Individual Service profile. 
* Fe User/Individual Service profile edit.
* And individual user can create only one Service.
* 
* Right now only one sevice can be assigned to an employee.
* 
* Employee cannot create service profile. 
* TODO :: what can employee admin do?
* 
* Fe Company Service profile. 
* Fe Company Service profile edit.
* A Company can create more than one i.e 5 Services.
* 
* IF a company is banned for some reason then we will 
* set the i_is_company_service=0 for all the services under that company.
* At that moment the default company service will be displayed as individual service.
* $user_service->i_is_company_service=0 and 
* $user_service->i_is_company_default=1 then we have to show
* the service as individual service. 
* 
* 
* when company is deactivated OR banned then 
* user_company , "i_active"=0
* user_service , "i_is_company_service"=0
* 
* calculate rank
* 
* ON 7Oct2013, 
* Extended Services are now fetched country specific
* 
* Extended columns at table "user_service_extended"
* "s_specialization_ids", s_qualification_ids, d_experience, 
* s_classes_ids, s_medium_ids, d_tution_fee, s_tution_mode_ids, 
* s_other_subject_ids, d_rate, s_employment_type_id, 
* s_availability_ids, s_tools_ids, s_designation_ids
* 
*/

class Service_profile extends MY_Controller {

    public $profile_type="service";

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
        $this->load->model('category_service_extended_defination_model','service_extended_def_model');
        $this->load->model('user_service_extended_model');
        $this->load->model('user_service_recommendation_model');
        $this->load->model('user_service_quotation_model');


        $this->load->model('user_public_private_model');
        $this->load->model('user_profession_model');
        $this->load->model('user_education_model');
        $this->load->model('user_certificate_model');
        $this->load->model('user_license_model');
        $this->load->model('user_skill_model');
        $this->load->model('user_company_model');
        $this->load->model('user_company_certificate_model');
        $this->load->model('user_company_license_model');
        
        $this->load->model('user_rank_model');
		$this->load->model('option_model'); // added on mar 2014 
		$this->load->model("user_suggestion_model"); 
		
		$this->load->model('payment_model');    
		$this->load->model('user_company_employee_model');
		//$this->load->model('user_fb_list_model');    // added on june 25
		


    }
	

    /**
    * Fe User/Individual Service profile. View/add/edit
    * 
    * @param mixed $id, encrypted service id and mandatory
    */
    public function index($id='')
    {
        $id=decrypt($id);
		//pr(is_userLoggedIn());
        $user_service=$this->user_service_model->user_service_load(intval($id));   
        //pr($user_service,1);
        if(empty($user_service))///no service found
        {
            show_404();
        }
        elseif($user_service->uid!=get_userLoggedIn("id")
        && $this->router->fetch_class()=="service_profile"
        )//if another user is trying to hack the edit mode
        {
            redirect( site_url($user_service->s_short_url) );
        }
        elseif(!$user_service->i_is_company_service 
            && !$user_service->i_is_company_default
            && $user_service->comp_id
        )///company service, goto company service page.
        {
            /**
            * IF the service id is not default company service for a company
            * and i_is_company_service is false, then goto 404.
            */
            show_404();       
        }

		// complete service profile completion percentage feb 2014
		if($id>0)
		{
			$completed = service_profile_prc_calculation($id);
			$this->user_service_model->update_user_service(array("i_profile_complete_percent"=>$completed),
															array("id"=>intval($id))
														);
			// to get the rank with user who are not friend for service mar 2014 @see, rankCallback() ,common_helper; //
			if($user_service->uid!=get_userLoggedIn("id") && get_userLoggedIn("id")>0)
			{
	   			//june 2014 check if rank exist in rank table then got fb level or level=0				
				$ranks = $this->user_rank_model->user_rank_load(array(
                "service_id"=>$id,
                "uid"=>get_userLoggedIn("id")
                ));
				$i_fb_level = $ranks[0]->i_fb_level;
				
								
				$friend = new stdClass();
				$friend->id = get_userLoggedIn("id");
				$friend->label = $i_fb_level?$i_fb_level:0;
				$tmp=  array($friend);
				if(!empty($tmp))
                {
                    array_walk($tmp,
                        "rankCallback",
                        $user_service
                        );
                } 
			}
			
			//////////////////// to get the rank with user for service mar 2014 ////////////////////////
		
		}
		
        ///loading owner of the service///       
		//echo '====>'.intval($user_service->uid); 
		//$this->data["service_created_date"] = $user_service->dt_created;
		
        $user=$this->user_model->user_load(intval($user_service->uid)); 
        $this->data["designation_since"]=$this->user_model->user_designation($user->id);
		
		$this->data["contact_view"] = false; //to hide contact view 15 feb 2014  
        if($user->id == get_userLoggedIn("id"))
            $this->data["contact_view"] = true;
        
        ///incrementing the view count//
        if($user->id!=get_userLoggedIn("id"))
        {
            $this->user_service_model->update_user_service(
            array("i_view_count"=>(intval($user_service->i_view_count)+1) ),
            array("id"=>intval($id)));
			
			// to hide contact view 15 feb 2014
			
			$this->load->model('user_service_quotation_model');
			$cond = array('uid_requested_by'=>get_userLoggedIn("id"),'service_id'=>$id);
			$res = $this->user_service_quotation_model->user_service_quotation_load($cond);
			if(count($res)>0)
				$this->data["contact_view"] = true;
        }        
        ///end incrementing the view count//

        ////Fetching category service extended fields////
        $temp=$this->service_extended_def_model
        ->category_service_extended_defination_load(
        array("country_id"=>$user_service->country_id, "cat_id"=>$user_service->cat_id,"i_active"=>1),
        null,
        null,
        "s_service_page_order ASC"
        );
        $service_extended_def=array();
        if(!empty($temp))
        {
            foreach($temp as $k=>$extended)
            {
                $service_extended_def[$extended->s_column_name]=$extended;
            }
        }
        ////end Fetching category service extended fields////

        ///Fetching user service extended////
        $service_extended=$this->user_service_extended_model
        ->user_service_extended_load(
        array("cat_id"=>$user_service->cat_id,
        "service_id"=>$user_service->id,
        "uid"=>$user_service->uid,
        ));
        if(!empty($service_extended))
            $service_extended=$service_extended[0];
        else
            $service_extended=new stdClass();
        ///end Fetching user service extended////

        /////////////////////////////////////////////////////////////////
        $form_token=encrypt($id);
        $default_counter=0;
        /////service name////
        $action="edit";
        $default_value[0]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_service_name"=>trim(@$user_service->s_service_name),
        ));    
            
      
        ////////service description///////////
        $default_value[1]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_service_desc"=>format_text(@$user_service->s_service_desc),
        ));        
            
        ///Service Provider//
        /**
        * IF 
        * $user_service->i_is_company_service=0 and 
        * $user_service->i_is_company_default=1 then we have to show
        * the service as individual service.
        */
        if(!empty($user_service->comp_id)
        && intval($user_service->i_is_company_service)==1 
        )//company employees, and company is not banned
        {
            $service_provider=get_company_service_provider(
            $user_service->comp_id,
            $user_service->id
            ); 

            $default_value['service_provider']=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "service_provider"=>$service_provider,
            "uid"=>"",//employee id to assign
            ));  
 

        }

        /*  else//individual service
        {   
        $service_provider[]=array(
        "uid"=>$user->id,
        "s_title"=>@$this->data["designation_since"]->s_title,
        "s_profile"=>addslashes(theme_user_thumb_picture($user->id,"",'class="alignleft"').
        '<strong>'.get_user_display_name($user->id).'</strong>'.
        '<p class="short"><span>'.@$this->data["designation_since"]->s_title.'</span></p>'
        ),
        );

        }

        $default_value[2]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "service_provider"=>$service_provider,
        "uid"=>"",//employee id to assign
        ));  
        */


        /// company link//

        if(!empty($user_service->comp_id)
        && intval($user_service->i_is_company_service)==1 
        )//company employees, and company is not banned
        {       
            $company=$this->user_company_model->user_company_load(array('uc.id'=>intval($user_service->comp_id)));
            $company=array_map("addEncIDCallback",$company);
            ///pr($company);                              
            //array_walk_recursive($company,"modifyUnSerialCallback","s_links");
            //pr($company);                              
            //pr($company[0]);
            $default_value['company_link']=json_encode(array(
            "form_token"=>encrypt(@$company[0]->id),
            "action"=>$action,
            "add_more_link"=>unserialize(@$company[0]->s_links),
            ));
        } 
        ////////user company languages///////////
        $default_value[2]=json_encode(array(
        "form_token"=>$form_token,
        "user_id"=>@$user_service->uid,
        "action"=>$action,
        //s_languages IS array(array("lang"=>"English","proficency"=>"Language proficient")...)
        "add_more_lang"=>unserialize(@$user_service->s_languages),
        ));



        //// service contact info////
        $default_value[3]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "country_id"=>intval(@$user_service->country_id),
        "zip_id"=>intval(@$user_service->zip_ids),
        "state_id"=>intval(@$user_service->state_ids),
        "city_id"=>intval(@$user_service->city_ids),
        "zip_code"=>trim(get_zipCode(intval(@$user_service->zip_ids))),
        "city_name"=>trim(get_cityName(intval(@$user_service->city_ids))),
        "state_name"=>trim(get_stateName(intval(@$user_service->state_ids))),
        "country_name"=>trim(get_countryName(intval(@$user_service->country_id))),
        "s_email"=>trim(@$user_service->s_email),
        "s_phone"=>trim(@$user_service->s_phone),
        "s_mobile"=>trim(@$user_service->s_mobile),
        "s_address"=>format_text(trim(@$user_service->s_address))
        )); 

        //// online service////
        $default_value[4]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "i_online"=>@$user_service->i_online,                       
        )); 


        ////service Extended tution fee//// 
        if(!empty($service_extended_def["d_experience"]))
        {
            $default_value["d_experience"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "d_experience"=>trim(@$service_extended->d_experience),
            ));             
        }        

        ////service Extended tution fee//// 
        if(!empty($service_extended_def["d_tution_fee"]))
        {
            $default_value["d_tution_fee"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "d_tution_fee"=>floatval(@$service_extended->d_tution_fee),
            ));             
        }
        
        ////service Extended rate//// 
        if(!empty($service_extended_def["d_rate"]))
        {
            $default_value["d_rate"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "d_rate"=>floatval(@$service_extended->d_rate),
            ));             
        }        
        

        ///////Service Extended Classes////
        //pr($service_extended_def);
        if(!empty($service_extended_def["s_classes_ids"]))
        {
            $class=unserialize(@$service_extended->s_classes_ids);
            if(empty($class))
                $class=array();
            array_walk_recursive($class,"addKeyCallback","s_classes_ids");
            array_walk_recursive($class,"modifyFormatCallback","s_classes_ids");
            //pr(serialize(array("XII","B.Sc","M.Tech")));
            //pr($class);
            $default_value["s_classes_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_classes"=>$class,
            ));             
        }        

        ////Service Extended subjects////
        if(!empty($service_extended_def["s_other_subject_ids"]))
        {
            $subjects=unserialize(@$service_extended->s_other_subject_ids);
            if(empty($subjects))
                $subjects=array();
            array_walk_recursive($subjects,"addKeyCallback","s_other_subject_ids");
            array_walk_recursive($subjects,"modifyFormatCallback","s_other_subject_ids");

            $default_value["s_other_subject_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_subjects"=>$subjects,
            ));             
        }         

        //// Service Extended Specialization////
        if(!empty($service_extended_def["s_specialization_ids"]))
        {
            $specialization=unserialize(@$service_extended->s_specialization_ids);
            if(empty($specialization))
                $specialization=array();
            array_walk_recursive($specialization,"addKeyCallback","s_specialization_ids");
            array_walk_recursive($specialization,"modifyFormatCallback","s_specialization_ids");

            $default_value["s_specialization_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_specialization"=>$specialization,
            ));             
        }

        //// Service Extended Qualification////
        if(!empty($service_extended_def["s_qualification_ids"]))
        {
            $qualification=unserialize(@$service_extended->s_qualification_ids);
            if(empty($qualification))
                $qualification=array();
            array_walk_recursive($qualification,"addKeyCallback","s_qualification_ids");
            array_walk_recursive($qualification,"modifyFormatCallback","s_qualification_ids");

            $default_value["s_qualification_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_qualification"=>$qualification,
            ));             
        }       

        //// Service Extended Medium////
        if(!empty($service_extended_def["s_medium_ids"]))
        {
            $medium=unserialize(@$service_extended->s_medium_ids);
            if(empty($medium))
                $medium=array();
            array_walk_recursive($medium,"addKeyCallback","s_medium_ids");
            array_walk_recursive($medium,"modifyFormatCallback","s_medium_ids");

            $default_value["s_medium_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_medium"=>$medium,
            ));             
        }   

        //// Service Extended Tution mode ////
        if(!empty($service_extended_def["s_tution_mode_ids"]))
        {
            $tution_mode=unserialize(@$service_extended->s_tution_mode_ids);
            if(empty($tution_mode))
                $tution_mode=array();
            array_walk_recursive($tution_mode,"addKeyCallback","s_tution_mode_ids");
            array_walk_recursive($tution_mode,"modifyFormatCallback","s_tution_mode_ids");

            $default_value["s_tution_mode_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_tution_mode"=>$tution_mode,
            ));             
        }        

        //// Service Extended availability ////
        if(!empty($service_extended_def["s_availability_ids"]))
        {
            $availability=unserialize(@$service_extended->s_availability_ids);
            if(empty($availability))
                $availability=array();
            array_walk_recursive($availability,"addKeyCallback","s_availability_ids");
            array_walk_recursive($availability,"modifyFormatCallback","s_availability_ids");

            $default_value["s_availability_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_availability"=>$availability,
            ));             
        }        
        
        //// Service Extended tools ////
        if(!empty($service_extended_def["s_tools_ids"]))
        {
            $tools=unserialize(@$service_extended->s_tools_ids);
            if(empty($tools))
                $tools=array();
            array_walk_recursive($tools,"addKeyCallback","s_tools_ids");
            array_walk_recursive($tools,"modifyFormatCallback","s_tools_ids");

            $default_value["s_tools_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_tools"=>$tools,
            ));             
        }
        
        //// Service Extended designation ////
        if(!empty($service_extended_def["s_designation_ids"]))
        {
            $s_designation_ids=unserialize(@$service_extended->s_designation_ids);
            if(empty($s_designation_ids))
                $s_designation_ids=array();
            array_walk_recursive($s_designation_ids,"addKeyCallback","s_designation_ids");
            array_walk_recursive($s_designation_ids,"modifyFormatCallback","s_designation_ids");

            $default_value["s_designation_ids"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_designation"=>$s_designation_ids,
            ));             
        }
       
        //// Service Extended Employment type, NA not in use////
        if(!empty($service_extended_def["s_employment_type_id"]))
        {
            $employment_type=unserialize(@$service_extended->s_employment_type_id);
            if(empty($employment_type))
                $employment_type=array();
            array_walk_recursive($employment_type,"addKeyCallback","s_employment_type_id");

            $default_value["s_employment_type_id"]=json_encode(array(
            "form_token"=>$form_token,
            "action"=>$action,
            "add_more_employment_type"=>$employment_type,
            ));             
        }         



        //// education ////
        $emp_id=$user_service->uid;  // service owner id is default value

        $service_provider_uid=array();
        if(!empty($service_provider))
        {
            $emp_id=''; // if company service then re-initialize the variable "$emp_id"

            foreach($service_provider as $i=>$v)
                $service_provider_uid[]=intval($v['uid']);

            //$emp_id.=((!empty($emp_id)) ? (','.$v['uid']) :$v['uid']);
            $emp_id=implode(",",$service_provider_uid);
        }



        ///@see, service_profile/index.tpl.php, recommendation section
        $this->data["service_provider_uid"]=@$service_provider_uid;


        //echo $emp_id;
        $where="ue.uid IN (".$emp_id.")"; 
        $education=$this->user_education_model->user_education_load($where) ;
        //adding encrypting id("s_token") within each row stdClass//
        $education=array_map("addEncIDCallback",$education);      
        array_walk_recursive($education,"modifyDispDateCallback","dt_from");
        array_walk_recursive($education,"modifyDispDateCallback","dt_to");
        //array_walk_recursive($education,"modifyFormatCallback",'s_desc');
        array_walk_recursive($education,"modifyFormatCallback",'s_instutite');
        array_walk_recursive($education,"modifyFormatCallback",'s_degree');
        array_walk_recursive($education,"modifyFormatCallback",'s_specilization');

        if(!empty($education)) 
        {
            foreach($education as $k=>$edu)
            {

                $temp=array(
                "form_token"=>$form_token,
                "user_id"=>encrypt($edu->uid),
                "action"=>$action,
                "s_profile_name"=>get_user_display_name($edu->uid,''),
                "designation"=>format_text(@$this->data["designation_since"]->s_title),
                "short_code"=>short_url_code($edu->uid)
                );
                //pr($temp);

                $temp=array_merge($temp,(array)$edu);
                //pr($temp);
                $default_value["service_provider_education"][$k]=json_encode($temp);               
            }

        } 

        $this->data['service_provider_education']=@$default_value["service_provider_education"];


        //pr($default_value["service_provider_education"]);
        //$education[0]=array("s_instutite"=>'',"s_specilization"=>""....);
        //// end education ///

        //// Company certificate////
        if(!empty($user_service->comp_id)
        && intval($user_service->i_is_company_service)==1 
        )//company employees, and company is not banned
        {

            $certificate=$this->user_company_certificate_model->user_company_certificate_load(array('uc.id'=>intval($user_service->comp_id)));
            //pr($certificate);
            //adding encrypting id("s_token") within each row stdClass//
            $certificate=array_map("addEncIDCallback",$certificate);
            array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_from","dt_from_certificate"));
            array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_to","dt_to_certificate"));
            array_walk_recursive($certificate,"modifyFormatCallback",'s_desc');
			

            //pr($certificate);
            /*$default_value[4]=json_encode(array(
            "form_token"=>encrypt(@$company[0]->id),
            "action"=>$action,
            "add_more_certificate"=>$certificate,
            ));  
            */


            if(!empty($certificate)) 
            {
                foreach($certificate as $k=>$cft)
                {

                    $temp=array(
                    "form_token"=>$form_token,
                    "comp_id"=>encrypt(@$company[0]->id),
                    "action"=>$action,
                    );
                    //pr($temp);

                    $temp=array_merge($temp,(array)$cft);
                    //pr($temp);
                    $default_value["company_certificate"][$k]=json_encode($temp);               
                }

            } 
        }
        $this->data['company_certificate']=@$default_value["company_certificate"]; 


        //// employee certificate///
        $where="uc.uid IN (".$emp_id.")"; 
        $employee_certificate=$this->user_certificate_model->user_certificate_load($where) ;
        //adding encrypting id("s_token") within each row stdClass//
        $employee_certificate=array_map("addEncIDCallback",$employee_certificate);      
        array_walk_recursive($employee_certificate,"modifyDispDateCallback",array("dt_from","dt_from_certificate"));
        array_walk_recursive($employee_certificate,"modifyDispDateCallback",array("dt_to","dt_to_certificate"));
        array_walk_recursive($employee_certificate,"modifyFormatCallback",'s_desc');
        array_walk_recursive($employee_certificate,"modifyFormatCallback",'s_certificate_name');
        array_walk_recursive($employee_certificate,"modifyFormatCallback",'s_certificate_number');
        array_walk_recursive($employee_certificate,"modifyFormatCallback",'s_certified_from');
       
        if(!empty($employee_certificate)) 
        { 
            foreach($employee_certificate as $k=>$emp_crt)
            {
                $temp=array(
                "form_token"=>$form_token,
                "user_id"=>encrypt($emp_crt->uid),
                "action"=>$action,
                "s_profile_name"=>get_user_display_name($emp_crt->uid,''),
                "designation"=>format_text(@$this->data["designation_since"]->s_title),
                "short_code"=>short_url_code(@$edu->uid)
                );

                $temp=array_merge($temp,(array)$emp_crt);
                //pr($temp);
                $default_value["service_provider_certificate"][$k]=json_encode($temp);               
            }
        } 

        //pr($default_value["service_provider_certificate"]);
        $this->data['service_provider_certificate']=@$default_value["service_provider_certificate"];        

        /// company license///
        if(!empty($user_service->comp_id)
        && intval($user_service->i_is_company_service)==1 
        )//company employees, and company is not banned
        {

            $license=$this->user_company_license_model->user_company_license_load(array("ucl.comp_id"=>intval($user_service->comp_id)));
            
            //adding encrypting id("s_token") within each row stdClass//
            $license=array_map("addEncIDCallback",$license);
            array_walk_recursive($license,"modifyDispDateCallback",array("dt_from","dt_from_license"));
            array_walk_recursive($license,"modifyDispDateCallback",array("dt_to","dt_to_license"));
            array_walk_recursive($license,"modifyFormatCallback",'s_desc');
           
            if(!empty($license)) 
            {
                foreach($license as $k=>$lcns)
                {
                    $temp=array(
                    "form_token"=>$form_token,
                    "comp_id"=>encrypt(@$company[0]->id),
                    "action"=>$action,
                    );

                    $temp=array_merge($temp,(array)$lcns);
                    //pr($temp);
                    $default_value["license"][$k]=json_encode($temp);               
                }
            } 
        }

        //pr($default_value["license"]);
       // $this->data['license']=@$default_value["license"];        

        /// service provider license///
        $where="ul.uid IN (".$emp_id.")"; 
        $employee_license=$this->user_license_model->user_license_load($where);
        //pr($license);
        //adding encrypting id("s_token") within each row stdClass//
        $employee_license=array_map("addEncIDCallback",$employee_license);
        array_walk_recursive($employee_license,"modifyDispDateCallback",array("dt_from","dt_from_license"));
        array_walk_recursive($employee_license,"modifyDispDateCallback",array("dt_to","dt_to_license"));
        array_walk_recursive($employee_license,"modifyFormatCallback",'s_desc');
        array_walk_recursive($employee_license,"modifyFormatCallback",'s_license_name');
        array_walk_recursive($employee_license,"modifyFormatCallback",'s_license_number');
        array_walk_recursive($employee_license,"modifyFormatCallback",'s_licensed_from');
       
        if(!empty($employee_license)) 
        {
            foreach($employee_license as $k=>$emp_lcns)
            {
                $temp=array(
                "form_token"=>$form_token,
                "user_id"=>encrypt($emp_lcns->uid),
                "action"=>$action,
                "s_profile_name"=>get_user_display_name($emp_lcns->uid,''),
                "designation"=>format_text(@$this->data["designation_since"]->s_title),
                "short_code"=>short_url_code($emp_lcns->uid)
                );

                $temp=array_merge($temp,(array)$emp_lcns);
                //pr($temp);
                $default_value["service_provider_license"][$k]=json_encode($temp);               
            }
        } 

        
        // pr($default_value["service_provider_license"]);
        $this->data['service_provider_license']=@$default_value["service_provider_license"];   


        /// recommendation////
        $recommendation=$this->user_service_recommendation_model->user_service_recommendation_load(
        array('ur.e_status'=>'approved' , 'ur.service_id'=>$id  ),3,0);
        array_walk_recursive($recommendation,'modifyUnSerialCallback','s_message');
        if(!empty($recommendation))
        {
            foreach($recommendation as $k=>$v)
            {
                $temp=$this->user_model->user_designation(intval($v->uid_recommended_by));        
                $recommendation[$k]->designation=$temp->s_title;

            }
        }
        $this->data['recommendation']=$recommendation;





        /**/ 
        ////////user skill///////////

        $where="uid IN (".$emp_id.")"; 
        $user_skill=$this->user_skill_model->user_skill_load( $where );
        $temp_skill=array();

        /// preventing repeatetive skill name////
        if(!empty($user_skill))
        {
            foreach($user_skill as $k=>$skl)
            {
                if(!in_array( strtolower(trim($skl->s_skill_name)),$temp_skill))
                    array_push($temp_skill, strtolower(trim($skl->s_skill_name)));
            }            
        }

        $this->data["default_value"]= $default_value;
        //$this->data["action"]=$action;        
        
        /**
        * user service
        * User can add only one service. 
        * Where as company can add multiple services.
        */
        //$user_service=$this->user_service_model->user_service_load(array("s.uid"=>intval($id)));
        $this->data["user_service"]= $user_service;
        

        /**
        * User public private field access
        */
        // $user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>intval($user_service->uid)));
        //$this->data["user_pp"]= @$user_pp[0];
        $this->data["uid"]= $user->id;
        $this->data["service_id"]= $id;
        $this->data["currency"]=get_currencySym($user_service->country_id);

        /**
        * company user
        * 
        */

        if(!empty($user_service->comp_id)
        && intval($user_service->i_is_company_service)==1 
        )//company employees, and company is not banned
        {
            $company_user=$this->user_company_employee_model->user_company_employee_load(array('uce.comp_id'=>intval(@$user_service->comp_id) , 'uce.i_active'=>'1'));
        }
        $this->data['company_user']=@$company_user;  


        /**
        * User Skills
        */
        /*$user_skill=$this->user_skill_model->user_skill_load(array("uid"=>intval($id)));*/       
        //

        $this->data['user_skill']=$temp_skill;
        $this->data["page_title"]="Service Profile"; 
        $this->render();
    }//end functions


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

		$det_service=$this->user_service_model->user_service_load(array('s.id'=>$posted["form_token"]));		
		$service_cat_id = $det_service[0]->cat_id;
        //pr($s_dummy);

        ///service name///
        if(isset($_POST["s_service_name"]))
        {
            $posted["s_service_name"] = trim($this->input->post("s_service_name"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('s_service_name', 'service name', 'required');

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
                "s_service_name"=>$posted["s_service_name"],
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    

                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($s_dummy->s_dummy);
                $val['s_service_name'] = $posted["s_service_name"];
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_service_model->update_user_service($dml_val,
                array("id"=>$posted["form_token"])
                );


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

        ///Service description///
        if(isset($_POST["s_service_desc"]))
        {
            $posted["s_service_desc"] = trim($this->input->post("s_service_desc"));

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
                "s_service_desc"=>format_text($posted["s_service_desc"],'encode'),
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($s_dummy->s_dummy);
                $val['s_service_desc'] = format_text($posted["s_service_desc"],'encode');
                $temp_s_dummy = encodeArrayToDummyField($val);


                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_service_model->update_user_service($dml_val,
                array("id"=>$posted["form_token"])
                );

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
        
        
        ///service extended d_experience///
        if(isset($_POST["d_experience"]))
        {
            $posted["d_experience"] = trim($this->input->post("d_experience"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('d_experience', 'experience', 'required');

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
                "d_experience"=>intval($posted["d_experience"])
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    $ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */
                $val = decodeFromDummyField($s_dummy->s_dummy);

                $val['filter_search_experience'] =$posted["d_experience"];
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_service_model->update_user_service(array("s_dummy"=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );                
                //end FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

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
        ///service extended d_tution_fee///
        if(isset($_POST["d_tution_fee"]))
        {
            $posted["d_tution_fee"] = intval($this->input->post("d_tution_fee"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('d_tution_fee', 'tution fee', 'required|integer');

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
                "d_tution_fee"=>$posted["d_tution_fee"]
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    $ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */
                $val = decodeFromDummyField($s_dummy->s_dummy);

                $val['filter_search_tution_fee'] =$posted["d_tution_fee"];
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_service_model->update_user_service(array("s_dummy"=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );                
                //end FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

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

        ///service extended d_rate///
        if(isset($_POST["d_rate"]))
        {
            $posted["d_rate"] = floatval($this->input->post("d_rate"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('d_rate', 'rate', 'required');

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
                "d_rate"=>$posted["d_rate"]
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
				
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    $ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }
                
                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */
				
				/* set range MM modified as to insert certain value for range 
				* see @ common_option_helper setMaxRangeDummy()
				*/
				$dum_rate = setMaxRangeDummy($posted["d_rate"]);

                $val = decodeFromDummyField($s_dummy->s_dummy);
                //$val['filter_search_rate'] =$posted["d_rate"]; // commented MM
				$val['filter_search_rate'] =$dum_rate;
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $this->user_service_model->update_user_service(array("s_dummy"=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );                
                //end FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //


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
        
        ///Service provider///
        if(isset($_POST["employee_uid"]))
        {
            $posted["employee_uid"] = trim($this->input->post("employee_uid"));

            $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            //$this->form_validation->set_rules('s_about_me', 'gender', 'required');
            $err_msg="";
            if(is_company_service_assigned($user_service->comp_id,$posted["form_token"],intval($posted["employee_uid"])))
            {
                $err_msg=message_line("service provider assigned");
            }

            if($this->form_validation->run() == FALSE || !empty($err_msg))/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                $ajx_ret["message"]= validation_errors();   
                $ajx_ret["message"].= $err_msg;
                echo json_encode($ajx_ret);
                return FALSE;
            }
            else//valid, saving into db
            {
                //pr(decrypt($_POST['form_token']),1);
                $ret=FALSE;
                /**
                * ** Right now we can assign only one sevice to 
                * an employee.
                */
                $dml_val=array(
                "service_ids"=>serialize(array(intval($posted["form_token"]))),
                "i_active"=>1,
                );
                $ret=$this->user_company_employee_model->update_user_company_employee($dml_val,
                array(
                "uid"=>intval($posted["employee_uid"]),
                "comp_id"=>intval($user_service->comp_id),
                )
                );

                $service_employee = get_company_service_provider(intval($user_service->comp_id),intval($posted["form_token"]));

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $s_dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    

                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($s_dummy->s_dummy);
                /**
                * Add the service owner or company owner 
                * display name, so that it can be found 
                * in sphinx search 
                */                
                $temp_name =get_user_display_name(intval($user_service->uid),'');
                
                foreach($service_employee as $k=>$vl)
                    $temp_name .= (!empty($temp_name)) ? ','.get_user_display_name($vl[uid],'') : get_user_display_name($vl[uid],'');
                    
                $val['s_name'] = $temp_name;
                $temp_s_dummy = encodeArrayToDummyField($val);
                //pr($temp_s_dummy,1);
                // updating $dml_val array()  with 's_dummy' field value ///

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );


                if($ret)//success
                {
                    $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");

                    $designation=$this->user_model->user_designation(intval($posted["employee_uid"]));
                    $ajx_ret["new_service_provider"]=array(
                    "uid"=>intval($posted["employee_uid"]),
                    "s_title"=>@$designation->s_title,
                    "s_profile"=>trim(
                    theme_user_thumb_picture(intval($posted["employee_uid"]),"",'class="alignleft"').
                    '<strong>'.get_user_display_name(intval($posted["employee_uid"])).'</strong>'.
                    '<p class="short"><span>'.@$designation->s_title.'</span></p>'
                    ),
                    );

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

        ///service extended s_classes_ids///
        if(isset($_POST["add_more_classes"]))
        {
            $posted["add_more_classes"] = $this->input->post("add_more_classes");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_classes_vld[s_classes_ids][]', 'class', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $classes='';//for dummy operation
                $s_classes_ids=array();//for dml operation
                foreach($posted['add_more_classes'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_classes_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'classes');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'classes';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
                    $classes.=(!empty($classes) ? ',': '').$fmt;//for dummy operation
                            
                    $s_classes_ids[]=$fmt;//for dml operation
                }                

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_classes'] = $classes;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );

                /*$s_classes_ids=array();
                foreach($posted["add_more_classes"] as $v)
                {
                    $s_classes_ids[]=trim($v["s_classes_ids"]);
                }*/

                $dml_val=array(
                "s_classes_ids"=>serialize($s_classes_ids)
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                
				
                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

				
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
					
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
					
                }

				
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

        ///service extended s_other_subject_ids///
        if(isset($_POST["add_more_subjects"]))
        {
            $posted["add_more_subjects"] = $this->input->post("add_more_subjects");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_subjects_vld[s_other_subject_ids][]', 'subjects', 'required');

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

                //generating the string  wtih comma(,) separated class name//                    
                $other_sub='';//for dummy operation
                $s_other_subject_ids=array();//for dml operation
                foreach($posted['add_more_subjects'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_other_subject_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'subject');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'subject';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
					
                    $other_sub.=(!empty($other_sub) ? ',': '').$fmt;//for dummy operation                            
                    $s_other_subject_ids[]=$fmt;//for dml operation
                }                
                
				
                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_subjects'] = $other_sub;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );


                /*$s_other_subject_ids=array();
                foreach($posted["add_more_subjects"] as $v)
                {
                    $s_other_subject_ids[]=trim($v["s_other_subject_ids"]);
                }*/

                $dml_val=array(
                "s_other_subject_ids"=>serialize($s_other_subject_ids)
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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


        ///service extended s_specialization_ids ///
        if(isset($_POST["add_more_specialization"]))
        {
            $posted["add_more_specialization"] = $this->input->post("add_more_specialization");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_specialization_vld[s_specialization_ids][]', 'Specialization', 'required');

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

                // pr($posted["add_more_specialization"],1);

                //generating the string  wtih comma(,) separated specilization name//
                $specialization='';//for dummy operation
                $s_specialization_ids=array();//for dml operation
                foreach($posted['add_more_specialization'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_specialization_ids']),'encode');
                    //$fmt=trim($vl['s_specialization_ids']);
					
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'specilization');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'specilization';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
                    $specialization.=(!empty($specialization) ? ',': '').$fmt;//for dummy operation
                            
                    $s_specialization_ids[]=$fmt;//for dml operation
                }                    

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_specialization'] = $specialization;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );


                /*$s_specialization_ids=array();
                foreach($posted["add_more_specialization"] as $v)
                {
                    $s_specialization_ids[]=trim($v["s_specialization_ids"]);
                }*/

                $dml_val=array(
                "s_specialization_ids"=>serialize($s_specialization_ids)
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
					
                }


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
        
        ///service extended s_qualification_ids ///
        if(isset($_POST["add_more_qualification"]))
        {
            $posted["add_more_qualification"] = $this->input->post("add_more_qualification");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_qualification_vld[s_qualification_ids][]', 'highest qualification level', 'required');

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

                // pr($posted["add_more_specialization"],1);

                //generating the string  with comma(,) separated specilization name//
                $qualification='';//for dummy operation
                $s_qualification_ids=array();//for dml operation
                foreach($posted['add_more_qualification'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_qualification_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'degree');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'degree';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
                    $qualification.=(!empty($qualification) ? ',': '').$fmt;//for dummy operation
                            
                    $s_qualification_ids[]=$fmt;//for dml operation
                }
                    

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_qualification'] = $qualification;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );


                /*$s_qualification_ids=array();
                foreach($posted["add_more_qualification"] as $v)
                {
                    $s_qualification_ids[]=trim($v["s_qualification_ids"]);
                }*/

                $dml_val=array(
                "s_qualification_ids"=>serialize($s_qualification_ids)/*see above for $s_qualification_ids*/
                );

                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );
                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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
        
        ///service extended s_medium_ids///
        if(isset($_POST["add_more_medium"]))
        {
            $posted["add_more_medium"] = $this->input->post("add_more_medium");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_medium_vld[s_medium_ids][]', 'medium', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $medium='';//for dummy operation
                $s_medium_ids=array();//for dml operation
                foreach($posted['add_more_medium'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_medium_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'language');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'language';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
                    $medium.=(!empty($medium) ? ',': '').$fmt;//for dummy operation
                            
                    $s_medium_ids[]=$fmt;//for dml operation
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_medium'] = $medium;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );
                

                $dml_val=array(
                "s_medium_ids"=>serialize($s_medium_ids)
                );



                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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
        
        ///service extended s_tution_mode_ids///
        if(isset($_POST["add_more_tution_mode"]))
        {
            $posted["add_more_tution_mode"] = $this->input->post("add_more_tution_mode");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_tution_mode_vld[s_tution_mode_ids][]', 'tution mode', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $tution_mode='';//for dummy operation
                $s_tution_mode_ids=array();//for dml operation
                foreach($posted['add_more_tution_mode'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_tution_mode_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'tution_mode');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'tution_mode';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
					
                    $tution_mode.=(!empty($tution_mode) ? ',': '').$fmt;//for dummy operation
                            
                    $s_tution_mode_ids[]=$fmt;//for dml operation
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_tution_mode'] = $tution_mode;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );
                

                $dml_val=array(
                "s_tution_mode_ids"=>serialize($s_tution_mode_ids)
                );



                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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

        ///service extended s_availability_ids///
        if(isset($_POST["add_more_availability"]))
        {
            $posted["add_more_availability"] = $this->input->post("add_more_availability");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_availability_vld[s_availability_ids][]', 'availability', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $availability='';//for dummy operation
                $s_availability_ids=array();//for dml operation
                foreach($posted['add_more_availability'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_availability_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'availability');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'availability';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
					
                    $availability.=(!empty($availability) ? ',': '').$fmt;//for dummy operation
                            
                    $s_availability_ids[]=$fmt;//for dml operation
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_availability'] = $availability;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );
                

                $dml_val=array(
                "s_availability_ids"=>serialize($s_availability_ids)
                );



                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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
        
        ///service extended s_tools_ids///
        if(isset($_POST["add_more_tools"]))
        {
            $posted["add_more_tools"] = $this->input->post("add_more_tools");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_tools_vld[s_tools_ids][]', 'tools', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $tools='';//for dummy operation
                $s_tools_ids=array();//for dml operation
                foreach($posted['add_more_tools'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_tools_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'tools');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'tools';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
					
                    $tools.=(!empty($tools) ? ',': '').$fmt;//for dummy operation
                            
                    $s_tools_ids[]=$fmt;//for dml operation
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_tools'] = $tools;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );
                

                $dml_val=array(
                "s_tools_ids"=>serialize($s_tools_ids)
                );



                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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
        
        ///service extended s_designation_ids///
        if(isset($_POST["add_more_designation"]))
        {
            $posted["add_more_designation"] = $this->input->post("add_more_designation");

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('add_more_designation_vld[s_designation_ids][]', 'designation', 'required');

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

                //generating the string  wtih comma(,) separated class name//
                $designation='';//for dummy operation
                $s_designation_ids=array();//for dml operation
                foreach($posted['add_more_designation'] as $k=>$vl)
                {
                    $fmt=format_text(trim($vl['s_designation_ids']),'encode');
                    //$fmt=trim($vl['s_qualification_ids']);
					/* Mar 2014 for data entry from autosuggest
					* check in option table for exist
					* if not, then entry into user_suggestion table , for admin approve
					* above construct loaded option_model
					*/
					$arr_cond = array('cat_id'=>$service_cat_id,'s_suggestion'=>$fmt,'e_type'=>'designation');
					$arr_res = $this->option_model->option_load($arr_cond);
					if(empty($arr_res))
					{
						$new_arr = array();
						$new_arr["cat_id"] = $service_cat_id;
						$new_arr["s_suggestion"] = $fmt;
						$new_arr["e_type"] = 'designation';
						$ins =$this->user_suggestion_model->add_user_suggestion($new_arr);
					}
					/* end insert into suggestion table*/
					
                    $designation.=(!empty($designation) ? ',': '').$fmt;//for dummy operation
                            
                    $s_designation_ids[]=$fmt;//for dml operation
                }

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['filter_search_designation'] = $designation;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
                array("id"=>$posted["form_token"])
                );
                

                $dml_val=array(
                "s_designation_ids"=>serialize($s_designation_ids)
                );



                //pr($posted,1);
                $user_service=$this->user_service_model->user_service_load(intval($posted["form_token"]));
                $service_extended=$this->user_service_extended_model
                ->user_service_extended_load(
                array("cat_id"=>$user_service->cat_id,
                "service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                ));                

                $ret=$this->user_service_extended_model->update_user_service_extended($dml_val,
                array("service_id"=>$posted["form_token"],
                "uid"=>$user_service->uid,
                "cat_id"=>$user_service->cat_id,
                ),true
                );

                ///if updation failed then try to insert it//
                if(!$ret && empty($service_extended))
                {
                    $dml_val=$dml_val+array(
                    "uid"=>$user_service->uid,
                    "service_id"=>$posted["form_token"],
                    "cat_id"=>$user_service->cat_id,    
                    );
                    //$ret=$this->user_service_extended_model->add_user_service_extended($temp);
					$ret=$this->user_service_extended_model->add_user_service_extended($dml_val);
                }


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
        if(isset($_POST["i_online"]))
        {

            $posted["i_online"] = trim($this->input->post("i_online"));
            $posted["form_token"]=decrypt(trim($this->input->post("form_token")));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('i_online', 'Online status', 'required');

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
                "i_online"=>$posted["i_online"],
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['i_online'] = $posted["i_online"];
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                //pr($posted,1);
                $ret=$this->user_service_model->update_user_service($dml_val,
                array("id"=>$posted["form_token"])
                );
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
        ///service location///
        if(isset($_POST["state_id"]))
        {
            $posted["zip_id"] = trim($this->input->post("zip_id"));
            $posted["city_id"] = trim($this->input->post("city_id"));
            $posted["state_id"] = trim($this->input->post("state_id"));
            $posted["s_phone"] = trim($this->input->post("s_phone"));
            $posted["s_mobile"] = trim($this->input->post("s_mobile"));
            $posted["s_email"] = trim($this->input->post("s_email"));
            $posted["s_address"] = trim($this->input->post("s_address"));
            $posted["country_id"] = trim($this->input->post("country_id"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');

            $this->form_validation->set_rules('zip_id', 'zip', 'required');
            $this->form_validation->set_rules('city_id', 'city', 'required');
            $this->form_validation->set_rules('s_email', 'email', 'valid_email');
            $this->form_validation->set_rules('s_mobile', 'mobile', 'numeric|exact_length[10]');

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
                "zip_ids"=>intval($posted["zip_id"]),
                "city_ids"=>intval($posted["city_id"]),
                "state_ids"=>intval($posted["state_id"]),
                "s_phone"=>$posted["s_phone"],
                "s_mobile"=>$posted["s_mobile"],
                "s_email"=>$posted["s_email"],
                "country_id"=>$posted["country_id"],
                "s_address"=>format_text($posted["s_address"],'encode'),                    
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['country_id']  = $posted["country_id"];
                $val['zip_ids']     = $posted["zip_id"];
                $val['city_ids']    = $posted["city_id"];
                $val['state_ids']   = $posted["state_id"];
                $val['s_phone']     = $posted["s_phone"];
                $val['s_mobile']    = $posted["s_mobile"];
                $val['s_email']     = $posted["s_email"];
                $val['s_address']   = format_text($posted["s_address"],'encode');


                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;


                $ret=$this->user_service_model->update_user_service($dml_val,
                array("id"=>$posted["form_token"])
                );

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

        ///user company languages///
        //s_languages IS array(array("lang"=>"English","proficency"=>"Language proficient")...)
        if(isset($_POST["add_more_lang"]))
        {
            $posted["add_more_lang"] = $this->input->post("add_more_lang");
            $posted["i_language"] = trim($this->input->post("frm_language_privacy"));
            $posted["user_id"] = trim($this->input->post("user_id"));
            $posted["form_token"]= trim($this->input->post("form_token"));

            //pr($_POST);

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

                //generating the string  wtih comma(,) separated language name//
                $languages='';

                foreach($posted['add_more_lang'] as $k=>$vl)
                    $languages.=(!empty($languages)) ? ','.$vl['lang'] : $vl['lang'];

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_service_model->fetch_dummy(intval(decrypt($posted["form_token"])));                    
                //pr($dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                //pr($val,1);
                $val['s_languages'] = $languages;
                $temp_s_dummy = encodeArrayToDummyField($val);

                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_service_model->update_user_service($dml_val,
                array("id"=>decrypt($posted["form_token"]))
                );

                //echo $this->db->last_query();                                                                                                        
                ///updating the field privacy///
                /*$user_pp=$this->user_public_private_model->user_public_private_load(array("uid"=>$posted["user_id"]));
                // pr($user_pp);
                $temp=array("i_language"=>user_public_private($posted["i_language"],"key"));

                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["user_id"])
                );

                if(!$ret_upp && empty($user_pp))
                {
                $temp=$temp+array("uid"=>$posted["user_id"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }
                ///end updating the field privacy///
                */                                   

                if($ret /*|| $ret_upp*/ )//success
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

        /// employee education///

        if(isset($_POST["s_instutite"]))
        {
            $posted["s_token"] = trim($this->input->post("s_token"));
            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));
            $posted["s_instutite"] = trim($this->input->post("s_instutite"));
            $posted["s_specilization"] = trim($this->input->post("s_specilization"));
            $posted["dt_from"] = trim($this->input->post("dt_from"));
            $posted["dt_to"] = trim($this->input->post("dt_to"));
            $posted["s_degree"] = trim($this->input->post("s_degree"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));
            $posted['user_id']= decrypt($this->input->post('user_id'));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_instutite', 'school', 'required');
            $this->form_validation->set_rules('s_specilization', 'field of study', 'required');
            $this->form_validation->set_rules('dt_from', 'druration from', 'required');
            $this->form_validation->set_rules('dt_to', 'druration to', 'required');  
            $this->form_validation->set_rules('s_degree', 'degree', 'required');

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
                "s_instutite"=>trim(@$posted["s_instutite"]),
                "s_degree"=>trim(@$posted["s_degree"]),
                "s_specilization"=>trim(@$posted["s_specilization"]),
                "s_desc"=>trim(@$posted["s_desc"]),
                "dt_from"=>format_date(@$posted["dt_from"],"Y-m-d"),
                "dt_to"=>format_date(@$posted["dt_to"],"Y-m-d"),
                );


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted['user_id']));                    

                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                //pr($val,1);
                $val['s_instutite']=$posted["s_instutite"];
                $val['s_degree']=$posted["s_degree"]; 
                $val['s_specilization']=$posted["s_specilization"]; 
                $val['s_desc']=format_text($posted["s_desc"],'encode'); 
                $val['instutite_dt_from']=format_date($posted["dt_from"],"Y-m-d"); 
                $val['instutite_dt_to']=format_date($posted["dt_to"],"Y-m-d");  
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy),array('id'=>intval($posted['user_id'])));      

                $ret=$this->user_education_model->update_user_education($dml_val,
                array("id"=>decrypt($posted["s_token"])));


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


        //// company certificate///
        if(isset($_POST["s_certificate_name"]))
        {
            //pr($_POST);
            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));
            $posted["comp_id"] = decrypt(trim($this->input->post("comp_id")));
            $posted["s_token"] = trim($this->input->post("s_token"));
            $posted["s_certificate_name"] = trim($this->input->post("s_certificate_name"));
            $posted["s_certificate_number"] = trim($this->input->post("s_certificate_number"));
            $posted["dt_from_certificate"] = trim($this->input->post("dt_from_certificate"));
            $posted["dt_to_certificate"] = trim($this->input->post("dt_to_certificate"));
            $posted["s_certified_from"] = trim($this->input->post("s_certified_from"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));

            //pr($posted,1);
            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_certificate_name', 'certification on', 'required');
            $this->form_validation->set_rules('s_certificate_number', 'number', 'required');
            $this->form_validation->set_rules('dt_from_certificate', 'druration from', 'required');
            $this->form_validation->set_rules('dt_to_certificate', 'druration to', 'required');  
            $this->form_validation->set_rules('s_certified_from', 'organigation', 'required');


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
                "s_certificate_name"=>trim(@$posted["s_certificate_name"]),
                "s_certificate_number"=>trim(@$posted["s_certificate_number"]),
                "s_desc"=>trim(@$posted["s_desc"]),
                "s_certified_from"=>trim(@$posted["s_certified_from"]),
                "dt_from"=>format_date(@$posted["dt_from_certificate"],"Y-m-d"),
                "dt_to"=>format_date(@$posted["dt_to_certificate"],"Y-m-d"),
                );

                $posted["s_token"]=decrypt($posted["s_token"]);

                $ret=$this->user_company_certificate_model->update_user_company_certificate($dml_val,
                array("id"=>$posted["s_token"])
                );


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["s_token"]));                    
                //pr($dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_certificate_name']=$posted["s_certificate_name"];
                $val['s_certificate_number']=$posted["s_certificate_number"]; 
                $val['certificate_s_desc']=format_text($posted["s_desc"],'encode'); 
                $val['s_certified_from']=$posted["s_certified_from"]; 
                $val['certificate_dt_from']=format_date($posted["dt_from_certificate"],"Y-m-d"); 
                $val['certificate_dt_to']=format_date($posted["dt_to_certificate"],"Y-m-d");  
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_company_model->update_user_company(array('s_dummy'=>$temp_s_dummy), array('id'=>intval($posted["s_token"])));

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

        //// company license///
        if(isset($_POST['s_license_name']))
        {

            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));
            $posted["comp_id"] = decrypt(trim($this->input->post("comp_id")));
            $posted["s_token"] = decrypt(trim($this->input->post("s_token")));
            $posted["s_license_name"] = trim($this->input->post("s_license_name"));
            $posted["s_license_number"] = trim($this->input->post("s_license_number"));
            $posted["dt_from_license"] = trim($this->input->post("dt_from_license"));
            $posted["dt_to_license"] = trim($this->input->post("dt_to_license"));
            $posted["s_licensed_from"] = trim($this->input->post("s_licensed_from"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));

            //pr($posted,1);
            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_license_name', 'license name', 'required');
            $this->form_validation->set_rules('s_license_number', 'number', 'required');
            $this->form_validation->set_rules('dt_from_license', 'druration from', 'required');
            $this->form_validation->set_rules('dt_to_license', 'druration to', 'required');  
            $this->form_validation->set_rules('s_licensed_from', 'authority', 'required');


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
                "s_license_name"=>trim(@$posted["s_license_name"]),
                "s_license_number"=>trim(@$posted["s_license_number"]),
                "s_desc"=>trim(@$posted["s_desc"]),
                "s_licensed_from"=>trim(@$posted["s_licensed_from"]),
                "dt_from"=>format_date(@$posted["dt_from_license"],"Y-m-d"),
                "dt_to"=>format_date(@$posted["dt_to_license"],"Y-m-d"),
                );



                $ret=$this->user_company_license_model->update_user_company_license($dml_val,
                array("id"=>$posted["s_token"])
                );


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["s_token"]));                    
                //pr($dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_license_name']=$posted["s_license_name"];
                $val['s_license_number']=$posted["s_license_number"]; 
                $val['license_s_desc']=format_text($posted["s_desc"],'encode'); 
                $val['s_licensed_from']=$posted["s_licensed_from"]; 
                $val['license_dt_from']=format_date($posted["dt_from_license"],"Y-m-d"); 
                $val['license_dt_to']=format_date($posted["dt_to_license"],"Y-m-d");  
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_company_model->update_user_company(array('s_dummy'=>$temp_s_dummy), array('id'=>intval($posted["s_token"])));


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


        /// request a quotation///
        if(isset($_POST['s_visitor_email']))
        {
            $posted['s_visitor_email']=trim($this->input->post('s_visitor_email'));
            $posted['s_message_thread'] = trim($this->input->post('s_message_thread'));
            $posted['uid'] = trim($this->input->post('uid'));
            $posted['service_id'] = trim($this->input->post('service_id'));

            //////validation////////

            $this->form_validation->set_rules('s_visitor_email', 'email', 'required|valid_email');
            $this->form_validation->set_rules('uid', 'uid', 'required');
            $this->form_validation->set_rules('service_id', 'service id', 'required');


            if($this->form_validation->run() == FALSE)/////invalid
            {
                echo validation_errors();
            }
            else//valid, saving into db
            {
                $posted['uid_requested_by']=get_userLoggedIn('id');

                //making serialize array
                $s_message_thread=array();
                $s_message_thread[]=array("uid_posted_by"=>intval($posted['uid_requested_by']),"s_msg"=>format_text($posted["s_message_thread"],'encode') ,"dt_posted"=>date("Y-m-d"));

                $data=array(
                'uid'=>$posted['uid'],
                "s_message_thread"=>serialize($s_message_thread),
                "service_id"=>intval($posted['service_id']),
                "uid_requested_by"=>intval($posted['uid_requested_by']),
                "s_visitor_email"=>$posted['s_visitor_email'],
                "e_status"=>"pending",);

                $ret=$this->user_service_quotation_model->add_user_service_quotation($data);

                if($ret)//success
                {
					/* insert into message tables also march 2014 */
					$this->load->model('user_message_model');
					$msg_idx = array();
					$msg_idx["i_sender_id"] 		= $posted['uid_requested_by'];
					$msg_idx["i_receiver_id"] 		= $posted['uid'];
					$msg_idx["s_subject"] 			= 'Service Request';
					$msg_idx["e_sender_folder"] 	= 'sent_item';
					$msg_idx["e_receiver_folder"] 	= 'inbox';
					$msg_idx["dt_created_on"] 		= date("Y-m-d H:i:s",time());
					
					$i_ins = $this->user_message_model->add_table_data('user_message_index',$msg_idx);
					if($i_ins)
					{
						$msg_data = array();
						$msg_data["i_sender_id"] 			= $posted['uid_requested_by'];
						$msg_data["i_receiver_id"] 			= $posted['uid'];
						$msg_data["s_body"] 				= $posted['s_message_thread'];
						$msg_data["i_message_index_id"] 	= $i_ins;
						$msg_data["dt_created_on"] 			= date("Y-m-d H:i:s",time());
						
						$i_ret = $this->user_message_model->add_table_data('user_message_data',$msg_data);
					}
					/* end insert into message tables also march 2014 */
                    echo "success";                       
                }
                else//error
                {
                    echo "failed";
                }                
            }            



        }


    } 

    public function ajax_employee_certificate_license_operation()
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

        //// employee certificate ///
        if(isset($_POST['s_certificate_name']))
        { 
            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));
            $posted["user_id"] = decrypt(trim($this->input->post("user_id")));
            $posted["s_token"] = decrypt(trim($this->input->post("s_token")));
            $posted["s_certificate_name"] = trim($this->input->post("s_certificate_name"));
            $posted["s_certificate_number"] = trim($this->input->post("s_certificate_number"));
            $posted["dt_from_certificate"] = trim($this->input->post("dt_from_certificate"));
            $posted["dt_to_certificate"] = trim($this->input->post("dt_to_certificate"));
            $posted["s_certified_from"] = trim($this->input->post("s_certified_from"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));

            //pr($posted,1);
            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_certificate_name', 'certification on', 'required');
            $this->form_validation->set_rules('s_certificate_number', 'number', 'required');
            $this->form_validation->set_rules('dt_from_certificate', 'druration from', 'required');
            $this->form_validation->set_rules('dt_to_certificate', 'druration to', 'required');  
            $this->form_validation->set_rules('s_certified_from', 'organigation', 'required');


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
                "s_certificate_name"=>trim(@$posted["s_certificate_name"]),
                "s_certificate_number"=>trim(@$posted["s_certificate_number"]),
                "s_desc"=>trim(@$posted["s_desc"]),
                "s_certified_from"=>trim(@$posted["s_certified_from"]),
                "dt_from"=>format_date(@$posted["dt_from_certificate"],"Y-m-d"),
                "dt_to"=>format_date(@$posted["dt_to_certificate"],"Y-m-d"),
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted['user_id']));     

                $val = decodeFromDummyField($dummy->s_dummy);
                //pr($val,1);
                $val['s_certificate_name']=$posted["s_certificate_name"];
                $val['s_certificate_number']=$posted["s_certificate_number"]; 
                $val['s_desc']= format_text($posted["s_desc"],"encode"); 
                $val['s_certified_from']=$posted["s_certified_from"]; 
                $val['certificate_dt_from']=format_date($posted["dt_from_certificate"],"Y-m-d"); 
                $val['certificate_dt_to']= format_date($posted["dt_to_certificate"],"Y-m-d");  
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy),array('id'=>intval($posted['user_id'])));       



                $ret=$this->user_certificate_model->update_user_certificate($dml_val,
                array("id"=>$posted["s_token"])
                );

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


        //// employee license////
        if(isset($_POST['s_license_name']))
        {
            $posted["form_token"] = decrypt(trim($this->input->post("form_token")));
            $posted["user_id"] = decrypt(trim($this->input->post("user_id")));
            $posted["s_token"] = decrypt(trim($this->input->post("s_token")));
            $posted["s_license_name"] = trim($this->input->post("s_license_name"));
            $posted["s_license_number"] = trim($this->input->post("s_license_number"));
            $posted["dt_from_license"] = trim($this->input->post("dt_from_license"));
            $posted["dt_to_license"] = trim($this->input->post("dt_to_license"));
            $posted["s_licensed_from"] = trim($this->input->post("s_licensed_from"));
            $posted["s_desc"] = trim($this->input->post("s_desc"));

            //pr($posted,1);
            //$posted["i_professional"] = trim($this->input->post("frm_professional_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            $this->form_validation->set_rules('s_license_name', 'license name', 'required');
            $this->form_validation->set_rules('s_license_number', 'number', 'required');
            $this->form_validation->set_rules('dt_from_license', 'druration from', 'required');
            $this->form_validation->set_rules('dt_to_license', 'druration to', 'required');
            $this->form_validation->set_rules('s_licensed_from', 'authority', 'required');


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
                "s_license_name"=>trim(@$posted["s_license_name"]),
                "s_license_number"=>trim(@$posted["s_license_number"]),
                "s_desc"=>trim(@$posted["s_desc"]),
                "s_licensed_from"=>trim(@$posted["s_licensed_from"]),
                "dt_from"=>format_date(@$posted["dt_from_license"],"Y-m-d"),
                "dt_to"=>format_date(@$posted["dt_to_license"],"Y-m-d"),
                );


                /// updating user_license table///  
                $ret=$this->user_license_model->update_user_license($dml_val,
                array("id"=>$posted["s_token"])
                );


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_model->fetch_dummy(intval($posted['user_id']));     

                $val = decodeFromDummyField($dummy->s_dummy);
                //pr($val,1);
                $val['s_license_name']=$posted["s_license_name"];
                $val['s_license_number']=$posted["s_license_number"]; 
                $val['license_s_desc']= format_text($posted["s_desc"],"encode"); 
                $val['s_licensed_from']=$posted["s_licensed_from"]; 
                $val['license_dt_from']=format_date($posted["dt_from_license"],"Y-m-d"); 
                $val['license_dt_to']= format_date($posted["dt_to_license"],"Y-m-d");  
                $temp_s_dummy = encodeArrayToDummyField($val);

                $this->user_model->update_user(array('s_dummy'=>$temp_s_dummy),array('id'=>intval($posted['user_id'])));    

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
                        "s_job_desc"=>trim(@$p["s_job_desc"]),
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
                /*$temp=array("i_professional"=>user_public_private($posted["i_professional"],"key"));
                $ret_upp=$this->user_public_private_model->update_user_public_private(
                $temp,
                array("uid"=>$posted["form_token"])
                );
                if(!$ret_upp)
                {
                $temp=$temp+array("uid"=>$posted["form_token"]);
                $ret_upp=$this->user_public_private_model->add_user_public_private($temp);
                }*/
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
                $ret=FALSE;

                $ret_upp=FALSE;
                $dml_val=array(
                "uid"=>$posted["form_token"],
                "s_skill_name"=>trim($posted["s_skill_name"]),
                );                

                $ret=$this->user_skill_model->add_user_skill($dml_val);

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

            //checking in user table
            $condition=array("u.s_short_url"=>$posted['s_short_url']);
            $users =$this->user_model->user_load($condition); 

            //checking in user_company table
            $condition=array("uc.s_short_url"=>$posted['s_short_url']);
            $company =$this->user_company_model->user_company_load($condition);

            $this->form_validation->set_rules('s_short_url', 'Short Url', 'required|is_unique[user_service.s_short_url]');

            if($this->form_validation->run() && empty($users) && empty($company))/////invalid
            {
                if($this->user_service_model->update_user_service(array("s_short_url"=>$posted['s_short_url'],"i_is_short_url_editable"=>0),array("id"=>$posted["service_id"])))      
                    echo $posted['s_short_url'];
            }

        } 
    }

    /**
    * Add a service intro page.
    * This page will appear for 1st time user creating a 
    * service only once. 
    * 
    * When user or company will tryto add another sevice then 
    * this page will not appear.
    */
    public function add_service_once()
    {
        is_userLoggedIn(TRUE);

        $this->load->model('user_company_model');
        $uid=get_userLoggedIn("id");
        $user_service=$this->user_service_model->user_service_load(array("uid"=>intval($uid)));
		//pr(get_userLoggedIn(),1);
        /**
        * Already service exists then goto
        * Add service page and skip this intro page. 
        * 
        **If an Employee without cannot add service.
        */
        if(get_userLoggedIn("i_is_company_emp"))//company employee
        {
            show_error(message_line("service provider service creation reject"),
            get_httpResponseCode("Method Not Allowed") ,
            "Service Creation Denied");
        }
        if(get_userLoggedIn("i_is_company_owner"))//company
        {
            redirect("service_profile/add_service");///add another service for company
        }
        elseif(!empty($user_service))//user/individual, since user can add only one service
        {
            show_error(message_line("user exceed service creation"),
            get_httpResponseCode("Method Not Allowed") ,
            "Service Creation Denied");
        }

        $posted=array();
        if($_POST)
        {
            $posted=$this->input->post();
            $uid=decrypt(trim($this->input->post("form_token")));         
			
            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('i_is_company_service', 'individual or company service', 'required');
            if($posted["i_is_company_service"])//company is selected
            {
                $this->form_validation->set_rules('s_company', 'company name', 'required');
            }

            if($this->form_validation->run() == FALSE)/////invalid
            {
                set_error_msg(validation_errors());
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                if($posted["i_is_company_service"])//add a company
                {
					
					$this->session->set_userdata(array('order_paypal_'=>$posted));
                	redirect("service_profile/place_order_paypal");
				
                    $dml_val=array(
                    "uid"=>$uid,
                    "s_company"=>$posted["s_company"],
                    "i_is_registered"=>intval($posted["i_is_registered"]),
                    "s_dummy"=>encodeToDummyField("s_company",$posted["s_company"])
                    );   

                    $ret=$this->user_company_model->add_user_company($dml_val);
                    if($ret)//success
                    {
                        set_success_msg(message_line("saved success")); 
                        //To reflect the new changes we need to reinit the session values of the user
                        $user=$this->user_model->user_load(intval($uid));
                        $this->set_userLoginInfo($user);
                        //////end reinit the user session/////

                        redirect("service_profile/add_service");///add service for company page
                    }
                    else//error
                    {
                        set_error_msg(message_line("saved error"));                    
                    }                      
                }
                else
                    redirect("service_profile/add_service");///add service for individual page
            }     
        }        


        $default_value=array(
        "form_token"=>encrypt($uid),
        "action"=>"add",
        );
        $default_value=$default_value+$posted;

        $this->data["default_value"]=$default_value;
        $this->data["page_title"]="Service Profile"; 
        $this->render();        


    }


    /**
    * Add a service page.
    * This page will appear after the intro page.
    * 
    * If a service already exists, then user wants to add another service
    * he will land into this page. 
    * 
    * user can create only one service.
    * company can create more than one services.
    * 
    * **There is only one address field displayed in the service profile page.
    * But nowhere it is displayed the service locations, cities, states, zips.
    * So we are considering the 1 fields right now.
    * 
    */
    public function add_service()
    {
        is_userLoggedIn(TRUE);

        $uid=get_userLoggedIn("id");
        //pr(get_userLoggedIn());
		
		// check for maximum 5 service creation feb 2014
        $user_service=$this->user_service_model->user_service_load(array("uid"=>intval($uid)));
		if(count($user_service)>=5) 
        {
			set_error_msg(message_line("max_exceed_service_creation"));
			redirect("user_profile");
        }

        $posted= array();

        if($_POST)
        {
            $posted=$this->input->post();
            $uid=decrypt(trim($this->input->post("form_token")));         

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('s_service_name', 'service name', 'required');
            $this->form_validation->set_rules('cat_id', 'service category', 'required');
            $this->form_validation->set_rules('country_id', 'country', 'required');
            $this->form_validation->set_rules('popular_location_ids', 'location field is either empty or not suggested. The location ', 'required');
            $this->form_validation->set_rules('zip_ids', 'zip field is either empty or not suggested. The zip ', 'required');
            $this->form_validation->set_rules('city_ids', 'city field is either empty or not suggested. The city ', 'required');
            $this->form_validation->set_rules('state_ids', 'state field is either empty or not suggested. The state ', 'required');

            if($this->form_validation->run() == FALSE)/////invalid
            {
                set_error_msg(validation_errors());
            }
            else//valid, saving into db
            {
                $ret=FALSE;
                /**
                * here $uid refers to the loggedin user id 
                * So, we will fetch any other user details from 
                * loggedin user data for service insertion
                */
                $comp_id=get_userCompany($uid);

                /**
                * there are no default service till now.
                */
                $i_is_company_default=0;
                if(!empty($comp_id))
                {
                    $company_services = $this->user_service_model
                    ->user_service_load(array("uid"=>$uid,"i_is_company_default"=>1));
                    $i_is_company_default=(empty($company_services)?1:0);                    
                }

                $val['cat_id']          = trim($posted["cat_id"]);
                $val['s_category']      = trim(get_category_name(intval(trim($posted["cat_id"]))));
                $val['s_service_name']  = trim($posted["s_service_name"]);
                $val['uid']             = trim($uid);
                $val['s_name']          = get_user_display_name($uid,'');
                $val['country_id']      = trim($posted["country_id"]);
                $val['state_ids']       = trim($posted["state_ids"]);
                $val['city_ids']        = trim($posted["city_ids"]);
                $val['zip_ids']         = trim($posted["zip_ids"]);
                $val["i_is_company_service"] = (!empty($comp_id)?1:0);
                $val['filter_search_gender'] = trim(get_userLoggedIn("e_gender"));

                $temp_s_dummy = encodeArrayToDummyField($val);
                //pr($temp_s_dummy);

                $dml_val=array(
                "uid"=>$uid,
                "cat_id"=>intval($posted["cat_id"]),
                "s_service_name"=>trim($posted["s_service_name"]),
                "s_short_url"=> generate_unique_shortUrl(),
                "country_id"=>intval($posted["country_id"]),
                "state_ids"=>dbHashSeperateEnc( array(intval($posted["state_ids"])) ),
                "city_ids"=>dbHashSeperateEnc( array(intval($posted["city_ids"])) ),
                "zip_ids"=>dbHashSeperateEnc( array(intval($posted["zip_ids"])) ),
                "popular_location_ids"=>dbHashSeperateEnc( array(intval($posted["popular_location_ids"])) ),
                "dt_created"=>date("Y-m-d H:i:s"),
                "i_active"=>1,
                "i_is_company_service"=>(!empty($comp_id)?1:0) ,
                "comp_id"=>$comp_id,
                "i_is_company_default"=>$i_is_company_default,
                "s_dummy"=>$temp_s_dummy,
                );   



                $ret=$this->user_service_model->add_user_service($dml_val);



                if($ret)//success
                {
                    //calculate rank//
                    $rank= calculate_rank($ret);
                    $rank['uid']=$uid;
                    ///add into rank table//                   
                    $this->user_rank_model->add_user_rank($rank);    
                    
                    //reset 'uid' to 0 and insert again//
                    $rank['uid']=0;
                    ///add into rank table//                   
                    $this->user_rank_model->add_user_rank($rank);    
                    
                    set_success_msg(message_line("saved success")); 
                    //redirect("service_profile/add_service");///add service for company page
                    redirect("service_profile/".encrypt($ret));//goto service profile page to add more details
                }
                else//error
                {
                    set_error_msg(message_line("saved error"));                    
                }                      

            }     
        }        


        $default_value=array(
        "form_token"=>encrypt($uid),
        "action"=>"add",
        "country_id"=>get_globalCountry(),
        );
        $default_value=$default_value+$posted;

        $this->data["default_value"]=$default_value;
        $this->data["page_title"]="Add new service"; 
        $this->render();        


    }    
	
	// Place order
	public function place_order_paypal()
	{
		$userId = get_userLoggedIn("id");
		// 1. Update cart master for total amount
		$paypal_oreder = $this->session->userdata('order_paypal_');
		//pr($paypal_oreder);
		
		$price_in_dollar = $this->currency_convert(60,"INR","USD"); 
		$price_in_dollar = str_replace("$","",$price_in_dollar);
		
		// 2. Go to payment getway [paypal]
		//   2.1 Include paypal library	
		include_once('application/libraries/paypal.php');
		
		//default settings
		$settings = array(
			'business'	=> 'sidneynazz@gmail.com', //paypal email address
			'currency'	=> 'USD', //paypal currency
			'cursymbol'	=> '$', //currency symbol
			'location'	=> 'USA', //location code (ex GB) //IE for IRELAND
			'returnurl' => base_url().'service_profile/payment_success', //where to go back when the transaction is done.
			'returntxt' => 'Return to GuruSourcing.com', //What is written on the return button in paypal
			'cancelurl' => base_url().'service_profile/payment_failure', //Where to go if the user cancels.
			'shipping'	=> 0, //Shipping Cost
			'custom'	=> $userId.'#'.$userId //Custom attribute ::User id and cart master id
		);
		
		//   2.2 Create object
		$pp = new paypalcheckout($settings);  // 
		
		//   2.3 Fetch cart details
		$items[0] = array(
			"name" => $paypal_oreder["s_company"],
			"price" => $price_in_dollar,
			"quantity" => 1,
			"shipping" => 0
		);
		$pp->addMultipleItems($items);
		
		//    2.4 Generate paypal form
		$this->data['paypal_form'] = $pp->getCheckoutForm();
		$this->data['msg'] = "You are currently being redirected to Payment Gateway. Please do not press back button or refresh. It will take a little while to get you there!";
		
		$this->load->view('fe/service_profile/place_order.tpl.php', $this->data);
		
		// For now redirect to 
		//redirect(base_url('checkout/payment_success'));
	}
	
	// Payment Failure 
	public function payment_failure()
	{
		
		// 2. Redirect to my account page
		set_error_msg(message_line("saved error"));
		redirect(base_url().'user_profile');
	}
	
	// Payment Success / Notification
	public function payment_success()
	{
		$ret = $_REQUEST;
		if($ret['payer_status'] == 'verified')
		{
			//pr($ret,1);
			// 1. Save all the data to order master and order details table
			$paypal_oreder = $this->session->userdata('order_paypal_');
			$userId = get_userLoggedIn("id");			
					
			/*$dml_val=array(
				"uid"=>$userId,
				"s_company"=>$paypal_oreder["s_company"],
				"i_is_registered"=>intval($paypal_oreder["i_is_registered"]),
				"s_dummy"=>encodeToDummyField("s_company",$paypal_oreder["s_company"])
				);   
				
				$ret=$this->user_company_model->add_user_company($dml_val);
				if($ret)//success
				{
					
					set_success_msg(message_line("saved success")); 
					// To reflect the new changes we need to reinit the session values of the user					
					$user=$this->user_model->user_load(intval($userId));
					$this->set_userLoginInfo($user);
					//////end reinit the user session/////
					$this->payment_process($ret);
					redirect("service_profile/add_service");///add service for company page
				}*/
			
			$this->payment_process($ret);
		}
		else
		{
			set_error_msg(message_line("saved error"));
			redirect(base_url().'user_profile');
		}
		// 6. Redirect to order confirmation page
		//redirect(base_url('checkout/order_confirmation'));
	}
	// Generate order details, send email confirmation, update user available bids
	public function payment_process($ret)
	{
		$paypal_oreder = $this->session->userdata('order_paypal_');
		$userId = get_userLoggedIn("id");
		if(!empty($ret))
		{
			$dml_val=array(
				"uid"=>$userId,
				"s_company"=>$paypal_oreder["s_company"],
				"i_is_registered"=>intval($paypal_oreder["i_is_registered"]),
				"s_dummy"=>encodeToDummyField("s_company",$paypal_oreder["s_company"])
				);   
				
			$i_ret=$this->user_company_model->add_user_company($dml_val);
			
			/**
			* To reflect the new changes we need to reinit the 
			* session values of the user
			*/
			$this->load->model("user_model");
			$user=$this->user_model->user_load(intval($userId));
			$this->set_userLoginInfo($user);
				
			$pay_arr = array();
			$pay_arr["uid"] 			= $userId;
			$pay_arr["s_transaction"] 	= serialize($ret);
			$pay_arr["s_payment_mode"] 	= 'paypal';
			$pay_arr["e_type"] 			= 'make_company';
			$pay_arr["e_status"] 		= 'completed';
			$pay_arr["i_type_id"] 		= $i_ret; // pk of company table
			
			$i_insert = $this->payment_model->add_payment($pay_arr);
			if($i_insert)
			{
				set_success_msg(message_line("saved success")); 
				redirect(base_url('service_profile/add_service'));
			}
			else
			{
				set_error_msg(message_line("saved error"));
				redirect(base_url().'user_profile');
			}
		}
		
		set_success_msg(message_line("saved success")); 
		redirect(base_url('user_profile'));
	}
	
	public function currency_convert($Amount,$currencyfrom,$currencyto)
	{
		$buffer=file_get_contents('http://finance.yahoo.com/currency-converter');
		preg_match_all('/name=(\"|\')conversion-date(\"|\') value=(\"|\')(.*)(\"|\')>/i',$buffer,$match);
		$date=preg_replace('/name=(\"|\')conversion-date(\"|\') value=(\"|\')(.*)(\"|\')>/i','$4',$match[0][0]);
		unset($buffer);
		unset($match);
		$buffer=file_get_contents('http://finance.yahoo.com/currency/converter-results/'.$date.'/'.$Amount.'-'.strtolower($currencyfrom).'-to-'.strtolower($currencyto).'.html');
		preg_match_all('/<span class=\"converted-result\">(.*)<\/span>/i',$buffer,$match);
		$match[0]=preg_replace('/<span class=\"converted-result\">(.*)<\/span>/i','$1',$match[0]);
		unset ($buffer);
		return $match[0][0];
	}


    public function __destruct(){}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */