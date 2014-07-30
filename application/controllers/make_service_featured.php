<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Make Service Featured
*  
* ////Messages are thereaded inside the db column like below.////
* $s_message=array(array("endorsed_by"=> $user_name,"On"=>date("Y-m-d"));
*/

class Make_service_featured extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        
        $this->load->model('user_service_recommendation_model');
        $this->load->model('user_feature_package_model');
        $this->load->model('user_service_model');
        $this->load->model('payment_model');
        
    }
    
    /**
    * put your comment there...
    * 
    * @param encrypted $u_id of service owner
    */
    public function index()
    {
        is_userLoggedIn(TRUE);
        
        $this->data['page_title'] = 'Make Service Featured';
        
        $package = $this->user_feature_package_model->user_feature_package_load(array('i_active'=> 1));
        
        if(!empty($package))
        {
            $rate = array();
            foreach($package as $k=>$vl)
            {
                $rate[$k]['pkg_id'] = $vl->id;
                $rate[$k]['pkg_val'] = intval($vl->i_price)/intval($vl->i_months_validity);  
                $rate[$k]['i_validity'] = $vl->i_months_validity;  
            }
        }
        
        $this->data['rate'] = $rate;
        if($_POST)
        {
            //pr($_POST,1);
            $posted['service_id']             = $this->input->post('service_id');
            $posted['i_featured_location']    = $this->input->post('i_featured_location');
            $posted['i_featured_online']      = $this->input->post('i_featured_online');
            $posted['feature_id']             = $this->input->post('i_price');
            $posted['h_payment_mode']         = $this->input->post('h_payment_mode');
            
            $this->form_validation->set_rules('service_id','service name', 'required');
            $this->form_validation->set_rules('i_price','package name', 'required');
            $this->form_validation->set_rules('h_payment_mode','how to pay us', 'required');
            
            $this->form_validation->set_message('required','Please choose %s');
            
            if($this->form_validation->run()==FALSE)
            {
                set_error_msg(validation_errors());
                $this->data['posted']= $posted;
            }
            else
            {
                $pack_details = $this->user_feature_package_model->user_feature_package_load(array('id'=>$posted['feature_id']));
                //echo $pack_details[0]->i_months_validity;  
                //date("Y-m-d H:i:s",strtotime(format_plural(2,"month"), "now" );
                       
                $featured_expiry_date = date("Y-m-d H:i:s",strtotime("+".$pack_details[0]->i_months_validity." month", time()));
               // echo $featured_expiry_date; exit;
                
                $s_transaction_data = serialize(array_merge($posted,$pack_details));
                
               // pr($s_transaction_data,1);
                
                
                $p_ret_ = $this->payment_model->add_payment(
                            array(  'uid'=>get_userLoggedIn('id'),
                                    'i_type_id'=>intval($posted['service_id']),
                                    'e_type'=>'feature service',
                                    's_payment_mode'=>$posted['h_payment_mode'],
                                    's_transaction'=>$s_transaction_data));
                if($p_ret_)
                {
                    $s_ret_ = $this->user_service_model->update_user_service(
                            array(  'i_featured'=>1,
                                    'dt_featured_expiry'=>$featured_expiry_date,
                                    'i_featured_location'=>$posted['i_featured_location'],
                                    'i_featured_online'=>$posted['i_featured_online'],
                                    'feature_id'=>$posted['feature_id']),
                            array('id'=>intval($posted['service_id'])));
                            
                    if($s_ret_)
                        message_line(set_success_msg('saved success'));
                    else
                        message_line(set_error_msg('saved error'));
                }
                else
                    message_line(set_error_msg('saved error'));
            }
                
            
        }
        $this->render();    
        
        
    }
       
}
