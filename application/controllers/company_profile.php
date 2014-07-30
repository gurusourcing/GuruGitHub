<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Company profile
* 
* when company is deactivated OR banned then 
* user_company , "i_active"=0
* user_service , "i_is_company_service"=0
* 
*/

class Company_profile extends MY_Controller {

    public $profile_type="company";
	public $doc, $xpath, $s_writer,  $s_full_body, $body_query , $title_query;  

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
        $this->load->model('user_public_private_model');
        $this->load->model('user_profession_model');
        $this->load->model('user_education_model');
        $this->load->model('user_certificate_model');
        $this->load->model('user_company_license_model');
        $this->load->model('user_company_model');
        $this->load->model('user_company_certificate_model');
        $this->load->model('user_company_employee_model');
    }

    public function index($id="")
    {

        $this->data['page_title'] = 'Company Profile';

        if(empty($id))
        {
            is_userLoggedIn(TRUE);//if not login then redirect to access deny    
            $id=get_userCompany(); 
        }
        else
            $id=decrypt($id);

        //$id=encrypt(1); // for testing purpose

        $form_token=encrypt($id);
        //$id=decrypt($id);


        //$form_token=$id;

        $company=$this->user_company_model->user_company_load(array('uc.id'=>intval($id)));
        //pr($company);
		$this->data["contact_view"] = false;
		if(get_userLoggedIn("id")!="")
			$this->data["contact_view"] = true;
			
        ///incrementing the view count//
        if(@$company[0]->uid!=get_userLoggedIn("id"))
        {
            $this->user_company_model->update_user_company(
            array("i_view_count"=>(intval(@$company[0]->i_view_count)+1) ),
            array("id"=>intval($id)));
        }        
        ///end incrementing the view count//

        /////company name////
        $action="edit";
        //pr($company);
        //company name
        $default_value[0]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_company"=>trim(@$company[0]->s_company),
        ));

        //About company
        $default_value[1]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "s_about_company"=>format_text(@$company[0]->s_about_company),
        ));

        // Company certificate
        $certificate=$this->user_company_certificate_model->user_company_certificate_load(array('uc.id'=>intval($id)));
        //pr($certificate);
        //adding encrypting id("s_token") within each row stdClass//
        $certificate=array_map("addEncIDCallback",$certificate);
        array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_from","dt_from_certificate"));
        array_walk_recursive($certificate,"modifyDispDateCallback",array("dt_to","dt_to_certificate"));
        array_walk_recursive($certificate,"modifyFormatCallback",'s_desc');

        //pr($certificate);
        $default_value[2]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_certificate"=>$certificate,
        ));

        ////////Company license///////////
        $license=$this->user_company_license_model->user_company_license_load(array("ucl.comp_id"=>intval($id)));
        //pr($license);
        //adding encrypting id("s_token") within each row stdClass//
        $license=array_map("addEncIDCallback",$license);
        array_walk_recursive($license,"modifyDispDateCallback",array("dt_from","dt_from_license"));
        array_walk_recursive($license,"modifyDispDateCallback",array("dt_to","dt_to_license"));
        array_walk_recursive($license,"modifyFormatCallback",'s_desc');

        //pr($license);
        $default_value[3]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_license"=>$license,
        ));  

        /// company link//

        $company=array_map("addEncIDCallback",$company);
        array_walk_recursive($company,"modifyUnSerialCallback","s_links");
        //pr($company[0]);
        $default_value[4]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "add_more_link"=>@$company[0]->s_links,
        ));  

        ////user location//// 
        $country_id=(intval(@$company[0]->country_id)?intval(@$company[0]->country_id):get_globalCountry());
        
        $default_value[5]=json_encode(array(
        "form_token"=>$form_token,
        "action"=>$action,
        "country_id"=>$country_id,
        "zip_id"=>intval(@$company[0]->zip_id),
        "state_id"=>intval(@$company[0]->state_id),
        "city_id"=>intval(@$company[0]->city_id),
        "zip_code"=>trim(get_zipCode(intval(@$company[0]->zip_id))),
        "city_name"=>trim(get_cityName(intval(@$company[0]->city_id))),
        "state_name"=>trim(get_stateName(intval(@$company[0]->state_id))),
        "country_name"=>trim(get_countryName($country_id)),
        "s_email"=>trim(@$company[0]->s_email),
        "s_phone"=>trim(@$company[0]->s_phone),
        "s_mobile"=>trim(@$company[0]->s_mobile),
        "s_address"=>trim(@$company[0]->s_address)
        ));                              

        ///Service Provider//
        /*$service_provider=$this->user_company_employee_model->user_company_employee_load(array("uce.comp_id"=>$id, "uce.i_active"=>1,'uce.e_employee_role'=>'service provider',"u.e_status"=>"active"));*/
        $service_provider=$this->user_company_employee_model->user_company_employee_load(array("u.e_status"=>"active","uce.comp_id"=>$id, "uce.i_active"=>1,'uce.e_employee_role'=>'service provider'));

        //pr($service_provider);

        ///service offered////
        $val=$this->user_service_model->user_service_load(array('s.comp_id'=>intval($id),"s.i_active"=>1));
        $service_offered=array();

        foreach($val as $k=>$s)
        {
            $service_offered[$k]['s_service_name']=$s->s_service_name;
            $service_offered[$k]['i_service_provider']=total_company_service_provider(intval($id),intval($s->id));
            $service_offered[$k]['s_short_url']=$s->s_short_url;
        }

        //pr($service_offered);                                        
        $this->data["default_value"]= $default_value;
        //pr($default_value);

        //company employee skill//
        $skill=$this->user_company_model->company_employee_skill(intval($id));
        array_walk_recursive($skill,"modifyUnSerialCallback",array("s_endorses","s_endorses_unserialized"));
        //pr($skill);
		
		/* twitter feeds 29 Nov 2013*/
		$twitt_feeds = $this->twitt_feeds(20);
		$this->data["twitt_feeds"]=$twitt_feeds;
		/* twitter feeds 29 Nov 2013*/
		
		
		/********************************** start blog title and description 2014 *********************************/
		$this->doc = new DOMDocument();
		@$this->doc->loadHTML(@file_get_contents('http://evealdiniz.use.com')); 
		$this->xpath = new DOMXPath($this->doc);
				
		$this->get_title_text(); // @see below		
		$this->data["blog_title"]		= $this->s_writer?strip_tags($this->s_writer):"";
		$this->data["blog_description"]	= strip_tags($this->s_full_body,'<p>');
		/*********************************** end blog title and description 2014 *********************************/
		
		

        $this->data["service_provider"]=$service_provider;
        $this->data["service_offered"]=$service_offered;
        $this->data['comp_id']=intval($id);//$id;
        $this->data['owner_id']=@$company[0]->uid;// company owner id
        $this->data["skill"]=$skill;
        $this->render();    


    }
	
	/********************************** start blog title and description 2014 *********************************/
	
	public function get_title_text()
    {
		try
		{
			// set query path
			$title_query = '//div[@class="title"]/a';
			$body_query = '//div[@class="text"]';			
			// Get Author
			$this->s_writer = $this->get_dom_data($this->doc, $this->xpath, $title_query);			
			// Get Main Content
			$this->s_full_body =  $this->get_dom_data($this->doc, $this->xpath, $body_query);
		}
		catch(Exception $err_obj)
		{
			 show_error($err_obj->getMessage());
		}
	}
	
	public function get_dom_data($dmo_obj, $xpath, $query, $get_content_image = false)
	{
		if(!$dmo_obj || !$xpath || $query == '') return '';
		$tmp = '';
		$dom_data = $xpath->query($query);
		foreach($dom_data as $d)
			$tmp .= '<p>'.$dmo_obj->saveXML($d).'</p>';		
		return $tmp;
	}
	
	/********************************** end blog title and description 2014 *********************************/


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


        ///company name///
        if(isset($_POST["s_company"]))
        {
            $posted["s_company"] = trim($this->input->post("s_company"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');
            $this->form_validation->set_rules('s_company', 'Company name', 'required');

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
                "s_company"=>$posted["s_company"],
                );

                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    

                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                $val['s_company'] = $posted["s_company"];
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_company_model->update_user_company($dml_val,
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


        ///About company//
        if(isset($_POST["s_about_company"]))
        {
            $posted["s_about_company"] = trim($this->input->post("s_about_company"));

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
                "s_about_company"=>format_text($posted["s_about_company"],'encode'),
                );


                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    

                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);
                $val['s_about_company'] = format_text($posted["s_about_company"],"encode");
                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_company_model->update_user_company($dml_val,
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


        ///company location///
        if(isset($_POST["state_id"]))
        {

            $posted["zip_id"] = trim($this->input->post("zip_id"));
            $posted["city_id"] = trim($this->input->post("city_id"));
            $posted["state_id"] = trim($this->input->post("state_id"));
            $posted["s_phone"] = trim($this->input->post("s_phone"));
            $posted["s_mobile"] = trim($this->input->post("s_mobile"));
            $posted["s_email"] = trim($this->input->post("s_email"));
            $posted["s_address"] = trim($this->input->post("s_address"));

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
                "zip_id"=>intval($posted["zip_id"]),
                "city_id"=>intval($posted["city_id"]),
                "state_id"=>intval($posted["state_id"]),
                "s_phone"=>$posted["s_phone"],
                "s_mobile"=>$posted["s_mobile"],
                "s_email"=>$posted["s_email"],
                "s_address"=>format_text($posted["s_address"],'encode'),                    
                );



                // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($s_dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['zip_id']     = $posted["zip_id"];
                $val['city_id']    = $posted["city_id"];
                $val['state_id']   = $posted["state_id"];
                $val['s_phone']     = $posted["s_phone"];
                $val['s_mobile']    = $posted["s_mobile"];
                $val['s_email']     = $posted["s_email"];
                $val['s_address']   = format_text($posted["s_address"],"encode");


                $temp_s_dummy = encodeArrayToDummyField($val);

                // updating $dml_val array()  with 's_dummy' field value ///
                $dml_val['s_dummy']=$temp_s_dummy;

                $ret=$this->user_company_model->update_user_company($dml_val,
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


        ///company Link///
        if(isset($_POST["add_more_link"]))
        {
            //pr($_POST);
            $posted["add_more_link"] = $this->input->post("add_more_link");

            //$posted["i_license"] = trim($this->input->post("frm_license_privacy"));

            //////validation////////
            $this->form_validation->set_rules('form_token', 'form token', 'required');//is the userid
            //$this->form_validation->set_rules('add_more_link_vld[s_links][]', 'links', 'required');


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


                if(!empty($posted["add_more_link"]))
                {
                    /**
                    * Always update link
                    */
                    $s_links=array();

                    foreach($posted["add_more_link"] as $k=>$p)
                    { 
                        $s_links[]=array("s_links"=>trim($p["s_links"]));
                    }


                    //generating the string  wtih comma(,) separated specilization name//
                    $links='';

                    foreach($posted['add_more_link'] as $k=>$vl)
                        $links.=(!empty($links)) ? ','.$vl['s_links'] : $vl['s_links'];

                    // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                    $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    
                    //pr($dummy);
                    /* setting up the s_dummy field value */

                    $val = decodeFromDummyField($dummy->s_dummy);

                    $val['s_links'] = $links;
                    $temp_s_dummy = encodeArrayToDummyField($val);


                    $ret=$this->user_company_model->update_user_company(array('s_links'=>serialize($s_links), 's_dummy'=>$temp_s_dummy),
                    array("id"=>$posted["form_token"])
                    );
                    //echo $this->db->last_query();
                    //secho $ret; exit;
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


                if(!empty($posted["add_more_certificate"]))
                {
                    $active_ids=array();///tracking all active ids
                    $certificate=$this->user_company_certificate_model->user_company_certificate_load(array("ucc.comp_id"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_certificate"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "comp_id"=>intval($posted["form_token"]),
                        "s_certificate_name"=>trim(@$p["s_certificate_name"]),
                        "s_certificate_number"=>trim(@$p["s_certificate_number"]),
                        "s_desc"=>trim(@$p["s_desc"]),
                        "s_certified_from"=>trim(@$p["s_certified_from"]),
                        "dt_from"=>format_date(@$p["dt_from_certificate"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to_certificate"],"Y-m-d"),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        //pr($p);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_company_certificate_model->add_user_company_certificate($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_company_certificate_model->update_user_company_certificate($dml_val,
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
                            $stat=$this->user_company_certificate_model->delete_user_company_certificate(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }



                    // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                    $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    
                    //pr($dummy);
                    /* setting up the s_dummy field value */

                    $val = decodeFromDummyField($dummy->s_dummy);

                    $val['s_certificate_name']      = $posted["s_certificate_name"];
                    $val['s_certificate_number']    = $posted["s_certificate_number"]; 
                    $val['certificate_s_desc']      = format_text($posted["s_desc"],'encode'); 
                    $val['s_certified_from']        = $posted["s_certified_from"]; 
                    $val['certificate_dt_from']     = format_date($posted["dt_from_certificate"],"Y-m-d"); 
                    $val['certificate_dt_to']       = format_date($posted["dt_to_certificate"],"Y-m-d");  
                    $temp_s_dummy                   = encodeArrayToDummyField($val);

                    $this->user_company_model->update_user_company(array('s_dummy'=>$temp_s_dummy), array('id'=>intval($posted["form_token"])));
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
                    $license=$this->user_company_license_model->user_company_license_load(array("uid"=>intval($posted["form_token"])));
                    /**
                    * Add and Edit
                    */
                    foreach($posted["add_more_license"] as $k=>$p)
                    {
                        $stat=FALSE;
                        $dml_val=array(
                        "comp_id"=>intval($posted["form_token"]),
                        "s_license_name"=>trim(@$p["s_license_name"]),
                        "s_license_number"=>trim(@$p["s_license_number"]),
                        "s_desc"=>trim(@$p["s_desc"]),
                        "s_licensed_from"=>trim(@$p["s_licensed_from"]),
                        "dt_from"=>format_date(@$p["dt_from_license"],"Y-m-d"),
                        "dt_to"=>format_date(@$p["dt_to_license"],"Y-m-d"),
                        );

                        $p["s_token"]=decrypt($p["s_token"]);
                        //pr($p);
                        if(empty($p["s_token"]))///add
                        {
                            $stat=$this->user_company_license_model->add_user_company_license($dml_val);    
                            $active_ids[]= $stat;                        
                        }
                        else//update
                        {
                            $stat=$this->user_company_license_model->update_user_company_license($dml_val,
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
                            $stat=$this->user_company_license_model->delete_user_company_license(array("id"=>$p->id));
                        }

                        //if atleast one value successfully inserted then we will show success message//
                        if($stat)
                            $ret=TRUE;                        
                    }
                    
                    
                    // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

                $dummy= $this->user_company_model->fetch_dummy(intval($posted["form_token"]));                    
                //pr($dummy);
                /* setting up the s_dummy field value */

                $val = decodeFromDummyField($dummy->s_dummy);

                $val['s_license_name']      = $posted["s_license_name"];
                $val['s_license_number']    = $posted["s_license_number"]; 
                $val['license_s_desc']      = format_text($posted["s_desc"],'encode'); 
                $val['s_licensed_from']     = $posted["s_licensed_from"]; 
                $val['license_dt_from']     = format_date($posted["dt_from_license"],"Y-m-d"); 
                $val['license_dt_to']       = format_date($posted["dt_to_license"],"Y-m-d");  
                $temp_s_dummy               = encodeArrayToDummyField($val);

                $this->user_company_model->update_user_company(array('s_dummy'=>$temp_s_dummy), array('id'=>intval($posted["form_token"])));
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

    }  


    /**
    * @uses Change short url check uniqueness beforeinserting in table
    *  element Description
    */
    public function change_short_url(){
        if($posted = $this->input->post()){
            $condition=array("u.s_short_url"=>$posted['s_short_url']);
            //checking in user table
            $users =$this->user_model->user_load($condition); 
            $condition=array("s.s_short_url"=>$posted['s_short_url']);
            //checking in user_service table
            $servicers =$this->user_service_model->user_service_load($condition);

            $this->form_validation->set_rules('s_short_url', 'Short Url', 'required|is_unique[user_company.s_short_url]');

            if($this->form_validation->run() && empty($users) && empty($servicers))/////invalid
            {
                if($this->user_company_model->update_user_company(array("s_short_url"=>$posted['s_short_url'],"i_is_short_url_editable"=>0),array("id"=>get_userLoggedIn("comp_id"))))      
                    echo $posted['s_short_url'];
            }

        } 
    }
	
	
	/* this function get twitter feeds of a particular user
	*  29 Nov 2013
	*/
	public function twitt_feeds($count=50)
	{
		$access_token = "358605229-yIGNfLaVcyrl6e6edWz8CcOlUmP7PHygjNVub6pM";
		$token_secret = "Z4vccrJvX2FBNrL0LFj7guu7vP733cqxiT524uLcQh7SQ";
		$screen_name = "testacumen";
		
		include_once('twitt/twitteroauth.php');
		$consumer_key = "GLW8txUHYwGN0qR335b3A";
		$consumer_secret = "7nYwKFs4wCQM91fsApwK50t7AvWrMxFO4Na4pnpmk";
		
		/* starts here **/
		
		$host = 'api.twitter.com';
	   $method = 'GET';
	   $path = '/1.1/statuses/user_timeline.json'; // api call path       
	   $query = array( // query parameters
		'screen_name' => $screen_name,
		'count' => $count
	   );
	   
	   $oauth = array(
		'oauth_consumer_key' => $consumer_key,
		'oauth_token' => $access_token,
		'oauth_nonce' => (string)mt_rand(), // a stronger nonce is recommended
		'oauth_timestamp' => time(),
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_version' => '1.0'
	   );
	   
	   $oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
	   $query = array_map("rawurlencode", $query);
	   
	   $arr = array_merge($oauth, $query); // combine the values THEN sort       
	   asort($arr); // secondary sort (value)
	   ksort($arr); // primary sort (key)       
	   // http_build_query automatically encodes, but our parameters
	   // are already encoded, and must be by this point, so we undo
	   // the encoding step
	   $querystring = urldecode(http_build_query($arr, '', '&'));       
	   $url = "https://$host$path";       
	   // mash everything together for the text to hash
	   $base_string = $method."&".rawurlencode($url)."&".rawurlencode($querystring);       
	   // same with the key
	   $key = rawurlencode($consumer_secret)."&".rawurlencode($token_secret);       
	   // generate the hash
	   $signature= rawurlencode(base64_encode(hash_hmac('sha1',$base_string,$key, true)));
	   
	   // this time we're using a normal GET query, and we're only encoding the query params
	   // (without the oauth params)
	   $url .= "?".http_build_query($query);
	   $url=str_replace("&amp;","&",$url); //Patch by @Frewuill
	   
	   $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
	   ksort($oauth); // probably not necessary, but twitter's demo does it
	   
	   // also not necessary, but twitter's demo does this too
	   function add_quotes($str) { return '"'.$str.'"'; }
	   $oauth = array_map("add_quotes", $oauth);
	   
	   // this is the full value of the Authorization line
	   $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));
	   
	   // if you're doing post, you need to skip the GET building above
	   // and instead supply query parameters to CURLOPT_POSTFIELDS
	   $options = array( CURLOPT_HTTPHEADER => array("Authorization: $auth"),
			 //CURLOPT_POSTFIELDS => $postfields,
			 CURLOPT_HEADER => false,
			 CURLOPT_URL => $url,
			 CURLOPT_RETURNTRANSFER => true,
			 CURLOPT_SSL_VERIFYPEER => false);
	   
	   // do our business
	   $feed = curl_init();
	   curl_setopt_array($feed, $options);
	   $json = curl_exec($feed);
	   curl_close($feed);
	   
	   $feed = json_decode($json);
	   
	  // pr($feed);
	   return $feed;
	}


}
/* End of file company_profile.php */
/* Location: ./application/controllers/company_profile.php */
