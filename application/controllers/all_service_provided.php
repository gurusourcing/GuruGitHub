<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* message
*/

class All_service_provided extends MY_Controller {    
    
    public function __construct()
    {   
        parent::__construct();
		
		$this->load->model('user_model');
        $this->load->model('user_service_model');   
		$this->load->model('payment_model');  
		$this->load->model('user_feature_package_model');    
    }
    
    public function index()
    {
        $this->data['page_title'] = 'List of Service Provided';		
		$userId = get_userLoggedIn("id");
		if(!$userId)
		{
			redirect(site_url('account/signin'));
		}
		
		////Auto Pagination
        $this->user_service_model->pager["base_url"]=site_url("all_service_provided/index");
        $this->user_service_model->pager["uri_segment"]=3;
		
		//$user_service=$this->user_service_model->user_service_load(array("uid"=>intval($userId)));
		$user_service=$this->user_service_model->user_service_load(
																array("uid"=>intval($userId)),
																  10,
																  $this->uri->segment(3,0)
		
																);
																
		$this->data['link_pager']=$this->user_service_model->get_pager();
		$this->data["user_service"]=$user_service;
		//pr($this->data["user_service"],1);
        $this->render();  
    }
	
	public function make_featured($sid='')
    {
        $this->data['page_title'] = 'Make Service Featured';		
		$userId = get_userLoggedIn("id");
		if(!$userId)
		{
			redirect(site_url('account/signin'));
		}
		
		$service_id = decrypt($sid);
		/*if($service_id>0)
		{
			$condition = array("s.uid"=>intval($userId),"s.id"=>$service_id);
			$this->data['sid']=$service_id;
		}
		else*/
			$condition = array("s.uid"=>intval($userId));
		
		$this->data['sid']=$service_id;
		$this->data['s_condition']=$condition;
		$user_service=$this->user_service_model->user_service_load($condition);
		$this->data["user_service"] = $user_service;
		
		$filter = "i_active ='1' ";
		$this->data['packages'] = $this->user_feature_package_model->user_feature_package_load($filter);
		//pr($this->data['packages']);
		
		if($_POST)
		{
			$posted = array();
			/*$posted["service_id"] 	= $this->input->post('service_id');
			$posted["i_type"] 		= $this->input->post('i_type');
			$posted["i_price"] 		= $this->input->post('i_price');*/
			$posted=$this->input->post();			
			
			$this->form_validation->set_rules('service_id', 'service', 'required');
            $this->form_validation->set_rules('i_type', 'service type', 'required');
            $this->form_validation->set_rules('i_price', 'price', 'required');
			
			if($this->form_validation->run() == FALSE)/////invalid
            {
                set_error_msg(validation_errors());
            }			
            else//valid, saving into db
            {
               	$this->session->set_userdata(array('paypal_oreder'=>$posted));				
                redirect("all_service_provided/place_order_paypal");
            }
			
		}
		
		$this->data['form_token']=$userId;
        $this->render();  
    }
	
	// Place order
	public function place_order_paypal()
	{
		$userId = get_userLoggedIn("id");
		// 1. Update cart master for total amount
		$paypal_oreder = $this->session->userdata('paypal_oreder');
		//pr($paypal_oreder);
		//echo '</br>=='.$this->currencyConversion($paypal_oreder['i_price'],"INR","USD"); exit;
		//echo '</br>=='.$this->currency_convert($paypal_oreder['i_price'],"INR","USD"); exit;
		$price_in_dollar = $this->currency_convert($paypal_oreder['i_price'],"INR","USD"); 
		$price_in_dollar = str_replace("$","",$price_in_dollar);
		
		$user_service=$this->user_service_model->user_service_load(intval($paypal_oreder["service_id"]));   
		
		// 2. Go to payment getway [paypal]
		//   2.1 Include paypal library	
		include_once('application/libraries/paypal.php');
		
		//default settings
		$settings = array(
			'business'	=> 'sidneynazz@gmail.com', //paypal email address
			'currency'	=> 'USD', //paypal currency
			'cursymbol'	=> '$', //currency symbol
			'location'	=> 'USA', //location code (ex GB) //IE for IRELAND
			'returnurl' => base_url().'all_service_provided/payment_success', //where to go back when the transaction is done.
			'returntxt' => 'Return to GuruSourcing.com', //What is written on the return button in paypal
			'cancelurl' => base_url().'all_service_provided/payment_failure', //Where to go if the user cancels.
			'shipping'	=> 0, //Shipping Cost
			'custom'	=> $userId.'#'.$userId //Custom attribute ::User id and cart master id
		);
		
		//   2.2 Create object
		$pp = new paypalcheckout($settings);  // 
		
		//   2.3 Fetch cart details
		$items[0] = array(
			"name" => $user_service->s_service_name,
			"price" => $price_in_dollar,
			"quantity" => 1,
			"shipping" => 0
		);
		$pp->addMultipleItems($items);
		
		//    2.4 Generate paypal form
		$this->data['paypal_form'] = $pp->getCheckoutForm();
		$this->data['msg'] = "You are currently being redirected to Payment Gateway. Please do not press back button or refresh. It will take a little while to get you there!";
		
		$this->load->view('fe/all_service_provided/place_order.tpl.php', $this->data);
		
		// For now redirect to 
		//redirect(base_url('checkout/payment_success'));
	}
	
	// Payment Success / Notification
	public function payment_success()
	{
		$ret = $_REQUEST;
		if($ret['payer_status'] == 'verified')
		{
			//pr($ret,1);
			// 1. Save all the data to order master and order details table
			$paypal_oreder = $this->session->userdata('paypal_oreder');
			$userId = get_userLoggedIn("id");			
					
			// FETCHING DATA FROM USER_SERVICE TABLE FOR UPDATING 's_dummy' COLOUMN //
			$dml_val = array();
			$user_service=$this->user_service_model->user_service_load(intval($paypal_oreder["service_id"]));
			$s_dummy= $this->user_service_model->fetch_dummy(intval($paypal_oreder["service_id"]));   
			/* setting up the s_dummy field value */
			$val = decodeFromDummyField($s_dummy->s_dummy);
			$val['s_service_name'] = $user_service->s_service_name;
			$temp_s_dummy = encodeArrayToDummyField($val);
			// updating $dml_val array()  with 's_dummy' field value ///
			$dml_val['s_dummy']=$temp_s_dummy;
			
			$dml_val["i_featured"] = 1;
			//$dml_val["feature_id"] = 1;
			$dml_val["feature_id"] = $paypal_oreder["feature_id"]?$paypal_oreder["feature_id"]:1;
			
			$filter = "id ='".$dml_val["feature_id"]."' ";
			$packages = $this->user_feature_package_model->user_feature_package_load($filter);
			$validity_days = $packages[0]->i_months_validity?intval($packages[0]->i_months_validity)*30:1*30;
		
			$dml_val["dt_featured_expiry"] = date("Y-m-d",strtotime("+{$validity_days} days",time()));
			$dml_val["i_featured_online"] = $paypal_oreder["i_type"]==1?1:0;
			$dml_val["i_featured_location"] = $paypal_oreder["i_type"]==2?1:0;
			
			$this->user_service_model->update_user_service(
								$dml_val,
								array("id"=>intval($paypal_oreder["service_id"]))
								);
			unset($dml_val);
			
			$this->payment_process($ret);
		}
		else
		{
			set_error_msg(message_line("saved error"));
			redirect(base_url().'all_service_provided');
		}
		// 6. Redirect to order confirmation page
		//redirect(base_url('checkout/order_confirmation'));
	}
	
	// Payment Failure 
	public function payment_failure()
	{
		// 1. Delete cart items from cart table
		$cart_master_id = $this->session->userdata('cart_master_id');
		$this->session->unset_userdata('cart_master_id');
		$this->empty_cart($cart_master_id);
		
		// 2. Redirect to my account page
		set_error_msg(message_line("saved error"));
		redirect(base_url().'all_service_provided');
	}
	
	// Generate order details, send email confirmation, update user available bids
	public function payment_process($ret)
	{
		$paypal_oreder = $this->session->userdata('paypal_oreder');
		$userId = get_userLoggedIn("id");
		if(!empty($ret))
		{
			$pay_arr = array();
			$pay_arr["uid"] 			= $userId;
			$pay_arr["s_transaction"] 	= serialize($ret);
			$pay_arr["s_payment_mode"] 	= 'paypal';
			$pay_arr["e_type"] 			= 'feature service';
			$pay_arr["e_status"] 		= 'completed';
			$pay_arr["i_type_id"] 		= $paypal_oreder["service_id"];
			
			$i_insert = $this->payment_model->add_payment($pay_arr);
			if($i_insert)
			{
				set_success_msg(message_line("saved success")); 
				redirect(base_url('all_service_provided'));
			}
			else
			{
				set_error_msg(message_line("saved error"));
				redirect(base_url().'all_service_provided');
			}
		}
		
		set_success_msg(message_line("saved success")); 
		redirect(base_url('all_service_provided'));
	}
	
	/*public function currencyConversion($amount,$from_Currency,$to_Currency)
	{
		$amount = urlencode($amount);
		$from_Currency = urlencode($from_Currency);
		$to_Currency = urlencode($to_Currency);
		$url = "https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
		$get = file_get_contents($url);
		$get = explode("<span class=bld>",$get);
		$get = explode("</span>",$get[1]);  
		return $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);
	}*/
	
	function currency_convert($Amount,$currencyfrom,$currencyto)
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
	
	
   
}
/* End of file message.php */
/* Location: ./application/controllers/message.php */
