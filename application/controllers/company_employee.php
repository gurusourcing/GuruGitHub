<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Company employee
* ////right now we have only one service assigned to employee/// 
* but as per db it has to be multiple services.
* 
* TODO:: what can employee admin do?
*/

class Company_employee extends MY_Controller {


    public function __construct()
    {   
        parent::__construct();

        $this->load->model("user_model");
        $this->load->model("user_company_employee_model");
        $this->load->model('user_service_model');

    }

    public function index($id="")
    {
        $this->data['page_title'] = 'List of Service Providers';

        if(empty($id))
        {
            is_userLoggedIn(TRUE);//if not login then redirect to access deny 
            /**
            * Only company owner can add employees
            */
            if(!get_userLoggedIn("i_is_company_owner"))
            {
                goto_accessDeny();
            }

            $id=get_userCompany(); 
        }
        else
            $id=decrypt($id);

        ////right now we have only one service assigned to employee/// 

        ////Auto Pagination
        $this->user_company_employee_model->pager["base_url"]=base_url("recommendation");
        $this->user_company_employee_model->pager["uri_segment"]=2;

        $data_obj=$this->user_company_employee_model->user_company_employee_load(array("u.e_status"=>"active" , "uce.comp_id"=>intval($id)),$this->noRecFe,intval($this->uri->segment(2,0)));

        $this->data['pagination']=$this->user_company_employee_model->get_pager();

        array_walk_recursive($data_obj,"modifyUnSerialCallback","service_ids");

        //pr($data_obj);
        $this->data["values"]=$data_obj;
        $this->render();    
    }




    /**
    * other company employee listing
    */
    public function other_company_employee($comp_id='')
    {
        $this->data['page_title'] = 'List of other company Service Providers';

        $id=decrypt($comp_id);

        ////right now we have only one service assigned to employee/// 

        ////Auto Pagination
        $this->user_company_employee_model->pager["base_url"]=base_url("recommendation");
        $this->user_company_employee_model->pager["uri_segment"]=2;

        $data_obj=$this->user_company_employee_model->user_company_employee_load(array( "u.e_status"=>"active" ,"uce.comp_id"=>intval($id)),$this->noRecFe,intval($this->uri->segment(2,0)));

        $this->data['pagination']=$this->user_company_employee_model->get_pager();

        array_walk_recursive($data_obj,"modifyUnSerialCallback","service_ids");

        if(!empty($data_obj))
            foreach($data_obj as $k=>$v)
                $data_obj[$k]->designation=(array)$this->user_model->user_designation($v->uid);    

        $this->data["values"]=$data_obj;
        $this->render();    
    }



    /**
    * add service provider(company employee)
    * after sign-up mail verification with welcome mail.
    */
    public function add_company_employee()
    {
        $this->data['page_title']='Add Service Provider'; /// page title////
        is_userLoggedIn(TRUE);//if not login then redirect to access deny    

        $comp_id=get_userCompany();  // getting company id////

        /**
        * Only company owner can add employees
        */
        if(!get_userLoggedIn("i_is_company_owner"))
        {
            goto_accessDeny();
        }


        $this->data['action']='add'; /// setting the mode////

        $posted=array();

        if($_POST)
        {
            $posted['s_name']   = trim($this->input->post("s_name"));
            $posted['s_email']  = trim($this->input->post("s_email"));
            $posted['e_gender'] = trim($this->input->post("e_gender"));
            /* $posted['dt_dob']   = trim($this->input->post("dt_dob"));*/
			
            /// validation///
            $this->form_validation->set_rules("s_name",'employee name', 'required');
            $this->form_validation->set_rules("s_email",'email', 'required|valid_email|is_unique[user_details.s_email]');
            $this->form_validation->set_rules("e_gender",'gender', 'required');
            /*$this->form_validation->set_rules("dt_dob",'date of birth', 'required');*/

            if($this->form_validation->run()==FALSE) ///validation fail
            {
                $this->data["posted"]=$posted;
                set_error_msg(validation_errors()); //setting error message
            }
            else
            {
                /*$posted['dt_dob']          =format_date($posted['dt_dob'],"Y-m-d");*/
                $posted['s_ip']            =$this->input->ip_address();
                $posted['dt_registration'] =date("Y-m-d H:i:s");
                $posted['dt_last_login'] =date("Y-m-d H:i:s");
                $posted['s_user_name']     =$posted["s_email"];
                $posted['s_password']      =random_string('alnum', 4);
                $posted['i_is_company_emp']   =1;
                $posted['comp_id']         =$comp_id;
                $posted['s_verification_code']=random_string('alnum', 8);
                $posted["s_display_name"]   =$posted['s_name'];   

                $ret=$this->user_model->add_user($posted); /// adding data into "user" & "user_details" table///

                if($ret)
                {
                    $s_short_url=generate_unique_shortUrl(); ///generating "short_url"///

                    $this->user_model->update_user(array("s_short_url"=> $s_short_url),
                    array("id"=>$ret)
                    ); ////inserting short_url into "user" table


                    //// inserting data into "user_company_employee" table////
                    $this->user_company_employee_model->add_user_company_employee(
                    array(  "uid"=>$ret,
                    "comp_id"=>$comp_id,
                    "e_employee_role"=>"service provider",
                    "i_active"=>1
                    )
                    );



                    // sending email verification code to the user via email
                    $mailData['from']   = "Admin <".site_mail().">";
                    $mailData['to']     = $posted["s_email"];
                    $mailData['subject']= 'Email Verification for '.$posted['s_user_name'].' at '.site_name();
                    $mailData['message']=  theme_signup_confirmation_mail($posted);
                    $e_ret=sendMail($mailData); //return TRUE on success;                            

                    // sending welcome message to the user via email
                    $mailData['from']   = "Admin <".site_mail().">";
                    $mailData['to']     = $posted["s_email"];
                    $mailData['subject']= 'Account details for '.$posted['s_user_name'].' at '.site_name();
                    $mailData['message']=  theme_signup_welcome_mail($posted);
                    $e_ret=sendMail($mailData); //return TRUE on success;                    

                    set_success_msg(message_line('service provider add success'));

                }
                else
                {
                    set_error_msg(message_line("service provider add error"));
                    $this->data["posted"]=$posted;
                }     
            }
        }

        $this->render('company_employee/add_edit_company_employee');

    }


    /**
    * edit company employee(service provider)
    * @param encrypted employee id
    */
    public function edit_company_employee($uid='')
    {
        $this->data['page_title']='Edit Service Provider'; /// page title////
        is_userLoggedIn(TRUE);//if not login then redirect to access deny    

        $comp_id=get_userCompany();  // getting company id////
        /**
        * Only company owner can add employees
        */
        if(!get_userLoggedIn("i_is_company_owner"))
        {
            goto_accessDeny();
        }        

        $this->data['action']='edit'; /// setting the mode////

        $this->data["form_token"]=$uid; // setting the form token//


        $posted=array();

        if($_POST)
        {
            $posted['s_name']   = trim($this->input->post("s_name"));
            $posted['s_email']  = trim($this->input->post("s_email"));
            $posted['e_gender'] = trim($this->input->post("e_gender"));
            /*$posted['dt_dob']   = trim($this->input->post("dt_dob"));*/
            $posted['form_token']=decrypt($this->input->post("form_token"));

            /// validation///
            $this->form_validation->set_rules("form_token",'form token', 'required');
            $this->form_validation->set_rules("s_name",'employee name', 'required');

            $this->form_validation->set_rules("s_email",'email', 'required|valid_email');
            $this->form_validation->set_rules("e_gender",'gender', 'required');
            /*$this->form_validation->set_rules("dt_dob",'date of birth', 'required');*/

            $res=$this->user_model->user_load(array("s_email"=>$posted['s_email']));

            if($res[0]->s_email!=$posted["s_email"])
                $this->form_validation->is_unique($posted['s_email'],"user_details.s_email");



            if($this->form_validation->run()==FALSE) ///validation fail
            {
                $this->data["posted"]=$posted;
                set_error_msg(validation_errors()); //setting error message
            }
            else
            {
                //$posted['dt_dob']          =format_date($posted['dt_dob'],"Y-m-d");
                $posted['s_user_name']     =$posted["s_email"];

                $ret=$this->user_model->update_user(array("s_user_name"=>$posted['s_user_name']),
                array('id'=>intval($posted['form_token']),'comp_id'=>$comp_id)
                ); /// updating data into "user" table///
                $ret=$this->user_model->update_user_details(array("s_name"=>$posted['s_name'],
                "s_email"=>$posted['s_email'],
                /*"dt_dob"=>$posted['dt_dob'],*/
                "s_display_name"=>$posted['s_name'],
                "e_gender"=>$posted['e_gender']
                ),
                array('uid'=>intval($posted['form_token']),/*"comp_id"=>$comp_id*/
                )
                ); /// updating data into "user_details" table///

                if($ret)
                {
                    set_success_msg(message_line('saved success'));
                    redirect(get_destination());
                }
                else
                {
                    set_error_msg(message_line("saved error"));
                    $this->data["posted"]=$posted;
                }     
            }
        }

        $data_obj=$this->user_model->user_load(intval(decrypt($uid)));

        $info["s_name"]     =$data_obj->s_name;
        $info["s_email"]    =$data_obj->s_email;
        $info["e_gender"]   =$data_obj->e_gender;
        //nfo["dt_dob"]     =format_date($data_obj->dt_dob);

        $this->data["posted"]=$info;

        $this->render('company_employee/add_edit_company_employee');


    }



    /**
    * change active inactive status
    */
    public function ajax_change_status()
    {
        $uid=$this->input->post("uid");
        $where='uid='.$uid;
        $ret=$this->user_company_employee_model->update_user_company_employee_status("",$where);
        if($ret)
            set_success_msg(message_line("status update success"));
        else
            set_error_msg(message_line("status update error"));
    }


    /**
    * ajax delete
    */
    public function ajaxDeleteEmployee()
    {
        $data=$this->input->post();

        $uid=is_array($data['uid'])?implode(',',$data['uid']):$data['uid'];

        $comp_id=get_userCompany();  // getting company id////

        // setting i_active status => '0' in user_company_employee tbl////
        $condition='uid IN ('.$uid.') AND comp_id='.$comp_id;
        $ret=$this->user_company_employee_model->update_user_company_employee(array("i_active"=>0),$condition);


        // setting e_status => 'suspended' in user tbl////
        /*$condition='id IN ('.$uid.') AND comp_id='.$comp_id;*/
        $condition='comp_id='.$comp_id.' AND id IN ('.$uid.') ';
        $ret=$this->user_model->update_user(array("e_status"=>"suspended"),$condition);

        if($ret)
            set_success_msg(message_line('delete success'));
        else
            set_error_msg(message_line('delete error'));
    }


    /**
    * change employee role
    */
    public function ajaxChangerole()
    {
        $e_employee_role=$this->input->post("role");
        $id=$this->input->post("id");
        $comp_id=get_userCompany();  // getting company id////
        $ret=$this->user_company_employee_model->update_user_company_employee(array("e_employee_role"=>$e_employee_role), 
        array("id"=>$id,'comp_id'=>$comp_id));
        if($ret)
            set_success_msg(message_line("saved success"));
        else
            set_error_msg(message_line("saved error"));
    }

    /**
    * change employee role
    * 
    * now we are considering the fact that one employee can have only one service id
    */
    public function ajaxChangeservice()
    {
        $service_ids[]=$this->input->post("service");
        $id=$this->input->post("id");
        $emp_id = $this->input->post('emp_id');
        $comp_id=get_userCompany();  // getting company id////
        $prev_service_id = $this->input->post('prev_service_id');
        

        $ret=$this->user_company_employee_model->update_user_company_employee(array("service_ids"=>serialize($service_ids)), 
        array("id"=>$id,'comp_id'=>$comp_id));
        
        //===========================================================================//
        
        //updating s_dummy field for current service table
        
        $current_service_employee = get_company_service_provider($comp_id,$service_ids[0]);
        
        // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

        $s_dummy= $this->user_service_model->fetch_dummy(intval($service_ids[0]));                    

        /* setting up the s_dummy field value  */

        $val = decodeFromDummyField($s_dummy->s_dummy);
        
        $temp_name ='';
        foreach($current_service_employee as $k=>$vl)
            $temp_name .= (!empty($temp_name)) ? ','.get_user_display_name($vl[uid],'') : get_user_display_name($vl[uid],'');
        $val['s_name'] = $temp_name;
        $temp_s_dummy = encodeArrayToDummyField($val);
        
        //pr($temp_s_dummy,1);
        // updating $dml_val array()  with 's_dummy' field value ///

        $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
        array("id"=>$service_ids[0])
        ); 
        
        // end updating s_dummy field for current service table
        
    //===============================================================================//    
        // updating s_dummy firld for previous service table//
        
        $prev_service_employee = get_company_service_provider($comp_id,intval($prev_service_id));
        
        // FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //

        $s_dummy= $this->user_service_model->fetch_dummy(intval($prev_service_id));                    

        /* setting up the s_dummy field value  */

        $val = decodeFromDummyField($s_dummy->s_dummy);
        
        $temp_name ='';
        foreach($prev_service_employee as $k=>$vl)
            $temp_name .= (!empty($temp_name)) ? ','.get_user_display_name($vl[uid],'') : get_user_display_name($vl[uid],'');
        $val['s_name'] = $temp_name;
        $temp_s_dummy = encodeArrayToDummyField($val);
        
        //pr($temp_s_dummy,1);
        // updating $dml_val array()  with 's_dummy' field value ///

        $this->user_service_model->update_user_service(array('s_dummy'=>$temp_s_dummy),
        array("id"=>$prev_service_id)
        ); 
        
        // end updating s_dummy firld for previous service table//
   //==========================================================================//     
        if($ret)
            set_success_msg(message_line("saved success"));
        else
            set_error_msg(message_line("saved error"));
    }


}
/* End of file company_employee.php */
/* Location: ./application/controllers/company_employee.php */
