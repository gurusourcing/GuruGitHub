<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Country
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Advertisements extends MY_Controller {


    public function __construct()
    {
        parent::__construct();

        $this->load->model("advertisement_model");
        $this->uploaddir=$this->config->item('upload_advertisement_image_path');        
    }

    /**
    * View Listing
    */
    public function index()
    {
        user_access("administer advertisement");//access check  

        $table=array();
        $table["header"]=array(
        array("title"=>"Image",
        ),
        array("title"=>"URL",
        ),
        array("title"=>"Type",
        ),
        array("title"=>"Description",
        ),
        array("title"=>"Expired On",
        ),
        array("title"=>"Actions",
        "attributes"=>array("width"=>"150")
        ),
        );
        $table["no result text"]="No information found.";

        ////Auto Pagination
        $this->advertisement_model->pager["base_url"]=admin_base_url("advertisements/index");
        $this->advertisement_model->pager["uri_segment"]=4;

        //////Filter/////
        $filter="";
        if($this->input->post("submit"))
        {
            $this->data["posted"]=$this->input->post();
            if(!empty($this->data["posted"]["e_ads_type"]))
                $filter="e_ads_type ='".$this->data["posted"]["e_ads_type"]."'";


        }
        //pr($filter);
        //////end Filter/////

        $rec=$this->advertisement_model->advertisement_load(
        $filter,
        $this->noRecAdmin,
        $this->uri->segment(4,0)
        );
        if(!empty($rec))
        {
            foreach($rec as $r)
            {
                $action="";
                $action.='<div id="active_action" class="floatL mr10 on_off">'.form_checkbox('i_active',$r->id,(bool)$r->i_active,'id=i_active').'</div>';    
                $action.='<a id="edit_action" href="'.admin_base_url("advertisements/operation/edit/".encrypt($r->id) ).'" >Edit</a>';
                $action.='&nbsp;<a id="delete_action" href="'.admin_base_url("advertisements/delete/".encrypt($r->id) ).'" >Delete</a>';  


                $logo='<a class="lightbox" title="advertisement image" href=""><img src="'.site_url($r->s_image).'" style="max-width:50px;max-height:50px"></a><br/>';

                $table["rows"][]=array(
                $logo,
                $r->s_url,
                humanize($r->e_ads_type),
                word_limiter($r->s_desc,20),
                date("d-m-y",strtotime($r->dt_expire)),
                $action
                );                
            }
        }

        /**
        * Pager goes to the footer of the table
        */
        $table["footer"]=$this->advertisement_model->get_pager();

        $this->data["page_title"]="Advertisements";
        $this->data["add_link"]= anchor(admin_base_url('advertisements/operation/add'), '<span class="icos-add"></span>', 'title="Add Advertisement" class="tipS"');
        $this->data["table"]=theme_table($table);

        $this->render();
        ////end login form starts from here////        
    }

    /**
    * Add edit form
    * 
    * @param mixed $action
    * @param mixed $form_token
    */
    public function operation($action="add",$form_token="")
    {
        user_access("administer advertisement");//access check    
        //echo get_adminLoggedIn('id');
        ////Inedit configuration////
        $id=0;
        if($action=="edit")
            $id=decrypt($form_token);

        $modify_data=$this->advertisement_model->advertisement_load(intval($id));    
        //$amount=@$modify_data->d_cpc > 0 ? @$modify_data->d_cpc : @$modify_data->d_cpm;



        if(intval($modify_data->d_cpc)!=0)
            $op_type='cpc';
        else
            $op_type='cpm';

        /*$default_value[0]=json_encode(array(
        "s_desc"=>trim(@$modify_data->s_desc),
        "s_url"=>trim(@$modify_data->s_url),
        "e_ads_type"=>trim(@$modify_data->e_ads_type),
        // "dt_expire"=>date('d-m-Y',strtotime(@$modify_data->dt_expire)),
        //"d_amount"=>trim($amount),
        //"i_total_paidfor_count"=>trim(@$modify_data->i_total_paidfor_count),
        "s_image"=>trim(@$modify_data->s_image),
        "h_image"=>trim(@$modify_data->s_image),
        "e_type"=>$op_type,
        "form_token"=>$form_token,
        "action"=>$action,
        ));*/

        $default_value=array(
        "s_desc"=>trim(@$modify_data->s_desc),
        "s_url"=>trim(@$modify_data->s_url),
        "e_ads_type"=>trim(@$modify_data->e_ads_type),
        // "dt_expire"=>date('d-m-Y',strtotime(@$modify_data->dt_expire)),
        //"d_amount"=>trim($amount),
        //"i_total_paidfor_count"=>trim(@$modify_data->i_total_paidfor_count),
        "s_image"=>trim(@$modify_data->s_image),
        "h_image"=>trim(@$modify_data->s_image),
        "e_type"=>$op_type,
        "form_token"=>$form_token,
        "action"=>$action,
        );        
        $this->data["default_value"]= $default_value;
        $this->data['action']=$action;
        //pr($default_value);
        $this->data["page_title"]="Option ".ucwords($action);
        $this->render();
        ////end login form starts from here////         
    }


    /**
    * Ajax add edit post
    */
    public function ajax_operation()
    {
        user_access("administer Option");//access check

        $ajx_ret=array(
        "mode" => "", //success|error
        "message"=>""//html string 

        );

        $posted=array();

        if(isset($_POST["e_ads_type"]))
        {

            $posted["action"] 		            = trim($this->input->post("action")); 

            $posted['aid']                      = get_adminLoggedIn('id');
            $posted["s_desc"]                   = trim($this->input->post("s_desc"));
            $posted["e_ads_type"] 	            = trim($this->input->post("e_ads_type"));
            $posted["e_type"]                  = trim($this->input->post("e_type"));
            $posted["d_amount"]                 = trim($this->input->post("d_amount"));
            $posted["i_total_paidfor_count"]    = trim($this->input->post("i_total_paidfor_count"));
            $posted["dt_expire"]                = trim($this->input->post("dt_expire"));
            $posted["s_image"]                  = trim($this->input->post("h_image"));
            $posted["s_url"]                  = trim($this->input->post("s_url"));


            //pr($_FILES,1);
            $upload_error="";
            if(!empty($_FILES['s_image']['name']))
            {

                $config['upload_path'] = $this->uploaddir ;
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']    = '100';

                $this->load->library('upload', $config);
                if(!$this->upload->do_upload("s_image"))
                {
                    $upload_error = $this->upload->display_errors();
                }
                else
                {
                    $upload_data = $this->upload->data();
                    $posted["s_image"]='/resources/advertisements/'.$upload_data["file_name"];
                }                

                /*if(move_uploaded_file($_FILES['s_image']['tmp_name'],$this->uploaddir.$_FILES['s_image']['name'])){
                $posted["s_image"] = '/resources/advertisements/'.$_FILES['s_image']['name'];
                }*/
            }
            //pr(array($config,$upload_data,$posted["s_image"],$upload_error),1);

            //encrypted id(PK) is the form_token 
            $posted["form_token"]= decrypt(trim($this->input->post("form_token")));  

            ////rules 
            $this->form_validation->set_rules('e_ads_type', 'advertisement type', 'required');

            if($posted["action"]=='add')
                $this->form_validation->set_rules('e_type', 'cpc/cpm', 'required');

            $this->form_validation->set_rules('d_amount', 'amount', 'numeric');
            $this->form_validation->set_rules('i_total_paidfor_count', 'count', 'integer');

            if($posted["action"]=="edit") //rules for edit
                $this->form_validation->set_rules('form_token', 'form token', 'required');


            if($this->form_validation->run() == FALSE || !empty($upload_error))/////invalid
            {
                $ajx_ret["mode"]="error";
                //$ajx_ret["message"]=form_error('s_admin_name');
                /*
                **For inedit validation***
                $ajx_ret["message"]= validation_errors();   
                $ajx_ret["uploaded_img"]="";//img url to be shown within the form 
                echo json_encode($ajx_ret);
                return FALSE; 
                */

                set_error_msg(validation_errors().'<br/>'.$upload_error);
                redirect(admin_base_url("advertisements/operation/".$posted["action"]."/".trim($this->input->post("form_token"))));
            }
            else//valid, saving into db
            {
                $info =array();
                $info["aid"] = $posted["aid"];
                $info["s_image"] = $posted["s_image"];
                $info["s_desc"] = $posted["s_desc"];
                $info["s_url"] = isset($posted["s_url"])?$posted["s_url"]:'';
                $info["e_ads_type"] = $posted["e_ads_type"];
                if($posted["action"]=='add')
                {
                    $info["dt_expire"] = date('Y-m-d',strtotime($posted["dt_expire"]));
                    $info[ "d_".$posted['e_type'] ]= $posted["d_amount"];
                    $info["i_total_paidfor_count"] = $posted["i_total_paidfor_count"];
                    $info["i_displayed_count"] = $posted["i_total_paidfor_count"];//same as i_total_paidfor_count    
                }



                $ret=FALSE;
                if($posted["action"]=="edit")
                {
                    $ret=$this->advertisement_model->update_advertisement($info
                    ,array("id"=>$posted["form_token"]));

                }
                elseif($posted["action"]=="add")
                {

                    $ret=$this->advertisement_model->add_advertisement($info);
                }

                //                  /pr($this->db->last_query());
                if($ret)//success
                {

                    /* $ajx_ret["mode"]="success";
                    $ajx_ret["message"]= message_line("saved success");
                    $ajx_ret["uploaded_img"]="";//img url to be shown within the form 

                    if($posted["action"]=="add")
                    $ajx_ret["form_token"]=encrypt($ret); 

                    echo json_encode($ajx_ret);
                    return TRUE;                    */
                    set_success_msg(message_line("saved success"));
                    redirect(admin_base_url("advertisements"));                       

                }
                else//error
                {
                    /*  $ajx_ret["mode"]="error";
                    $ajx_ret["message"]= message_line("saved error");   
                    echo json_encode($ajx_ret);
                    return TRUE;                    */
                    set_error_msg(message_line("saved error"));
                    redirect(admin_base_url("advertisements"));
                }                
            }     

        }//end if         
    } 



    /**
    * Delete 
    * 
    * @param mixed $form_token
    */
    public function delete($form_token)
    {
        user_access("administer option");//access check
        $id=decrypt($form_token);    

        $ret=$this->advertisement_model->delete_advertisement(array("id"=>$id));
        if($ret)//success
        {
            set_success_msg(message_line("delete success"));
            redirect(admin_base_url("advertisements"));                       
        }
        else//error
        {
            set_error_msg(message_line("delete error"));
            redirect(admin_base_url("advertisements"));
        }         
    }

    /**
    * changing the i_active  status
    */

    public function ajax_changeStatus()
    {
        $id=$_POST['id'][0];
        //pr($id);
        $ret=$this->advertisement_model->update_advertisement_status($id);

        if($ret)
            set_success_msg(message_line("status update success"));
        else
            set_error_msg(message_line("status update error"));
    }


    /**
    * Assigning permisions available 
    */
    public function advertisement_permission()
    {
        return array(
        "administer advertisement"=>array(
        "title"=>"Administer advertisement",
        "description"=>"Can view, add, edit, delete Advertisement.".message_line("security concern"),
        ),

        );
    }//end welcome_permission
}

