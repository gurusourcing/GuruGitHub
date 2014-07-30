<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Start company
*/

class Start_company extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
		
		$this->load->model('payment_model');    
        
    }
    
    public function index()
    {
        is_userLoggedIn(TRUE);
        
        $this->data['page_title'] = 'Start Company';
        
        $this->data['not_company']=get_cms(7); // cms  content for "not a company" section
        $this->data['company']=get_cms(8); // cms content for "already a company" section
        //pr(get_cms(8));
        $this->render();    
        
        
    }
    
    /**
    * add company name, 
    * And loggedin user session is updated
    */
    public function ajaxSaveCompany_24june2014()
    {
      $s_company=trim($this->input->post('s_company'));
      if($s_company!='')
      {
          $uid=get_userLoggedIn('id');
          
          // adding user id, company name into user_company tbl
          $this->load->model('user_company_model');
          $newId=$this->user_company_model->add_user_company(array('uid'=>$uid,'s_company'=>$s_company, 'i_active'=>1));
          if($newId)
          {
            $s_short_url=generate_unique_shortUrl(); ///generating "short_url"///
            $this->user_company_model->update_user_company(array("s_short_url"=>$s_short_url),array('id'=>$newId));
          }
            /**
            * To reflect the new changes we need to reinit the 
            * session values of the user
            */
            $this->load->model("user_model");
            $user=$this->user_model->user_load(intval($uid));
            $this->set_userLoginInfo($user);
            //////end reinit the user session/////       
            
               
          
      }
      if($newId)
        echo 'success';
      else 
        echo 'Sorry, fail to create your company. Try again.';
    }
	
	/**
    * add company name, 
    * And loggedin user session is updated
    */
    public function ajaxSaveCompany()
    {
      $s_company=trim($this->input->post('s_company'));
	  $url = "";
      if($s_company!='')
      {
		$this->session->set_userdata(array('paypal_company'=>$s_company));	
		$url = base_url("start_company/place_order_paypal");			
		//redirect("start_company/place_order_paypal");
      }
	  echo $url;
    }
	
	// Place order
	public function place_order_paypal()
	{
		$userId = get_userLoggedIn("id");
		// 1. Update cart master for total amount
		$paypal_company = $this->session->userdata('paypal_company');
		//pr($paypal_company);
				
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
			'returnurl' => base_url().'start_company/payment_success', //where to go back when the transaction is done.
			'returntxt' => 'Return to GuruSourcing.com', //What is written on the return button in paypal
			'cancelurl' => base_url().'start_company/payment_failure', //Where to go if the user cancels.
			'shipping'	=> 0, //Shipping Cost
			'custom'	=> $userId.'#'.$userId //Custom attribute ::User id and cart master id
		);
		
		//   2.2 Create object
		$pp = new paypalcheckout($settings);  // 
		
		//   2.3 Fetch cart details
		$items[0] = array(
			"name" => $paypal_company,
			"price" => $price_in_dollar,
			"quantity" => 1,
			"shipping" => 0
		);
		$pp->addMultipleItems($items);
		
		//    2.4 Generate paypal form
		$this->data['paypal_form'] = $pp->getCheckoutForm();
		$this->data['msg'] = "You are currently being redirected to Payment Gateway. Please do not press back button or refresh. It will take a little while to get you there!";
		
		$this->load->view('fe/start_company/place_order.tpl.php', $this->data);		
		// For now redirect to 
		//redirect(base_url('checkout/payment_success'));
	}
	
	// Payment Success / Notification
	public function payment_success()
	{
		$ret = $_REQUEST;
		if($ret['payer_status'] == 'verified')
		{
			// 1. Save all the data to order master and order details table
			$paypal_company = $this->session->userdata('paypal_company');
			$userId = get_userLoggedIn("id");	
			
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
		redirect(base_url().'start_company');
	}
	
	// Generate order details, send email confirmation, update user available bids
	public function payment_process($ret)
	{
		$paypal_company = $this->session->userdata('paypal_company');
		$userId = get_userLoggedIn("id");
		if(!empty($ret))
		{
			$this->load->model('user_company_model');
			$newId=$this->user_company_model->add_user_company(array('uid'=>$uid,'s_company'=>$paypal_company, 'i_active'=>1));
			if($newId)
			{
				$s_short_url=generate_unique_shortUrl(); ///generating "short_url"///
				$this->user_company_model->update_user_company(array("s_short_url"=>$s_short_url),array('id'=>$newId));
				
				$pay_arr = array();
				$pay_arr["uid"] 			= $userId;
				$pay_arr["s_transaction"] 	= serialize($ret);
				$pay_arr["s_payment_mode"] 	= 'paypal';
				$pay_arr["e_type"] 			= 'make_company';
				$pay_arr["e_status"] 		= 'completed';
				$pay_arr["i_type_id"] 		= $newId; // pk of company table
				
				$i_insert = $this->payment_model->add_payment($pay_arr);
				
				/**
				* To reflect the new changes we need to reinit the 
				* session values of the user
				*/
				$this->load->model("user_model");
				$user=$this->user_model->user_load(intval($userId));
				$this->set_userLoginInfo($user);
				
				set_success_msg(message_line("saved success"));
				redirect(base_url().'company_profile');
				
			}
			else
			{
				set_error_msg(message_line("saved error"));
				redirect(base_url().'start_company');
			}
		}
		else
		{		
			set_error_msg(message_line("saved error"));
			redirect(base_url().'start_company');
		}
	}
	
	
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


/* End of file Endorsement.php */
/* Location: ./application/controllers/endorsement.php */
