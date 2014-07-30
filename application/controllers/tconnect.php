<?php
/*********
* Author: Mrinmoy Mondal
* Date  : 10 Dec 2012
* Modified By: 
* Modified Date: 
* 
* @includes My_Controller.php
* @implements InfControllerFe.php
*/


include_once('twitt/twitteroauth.php');

class Tconnect extends My_Controller
{

    public $cls_msg;//////All defined error messages. 
    public $pathtoclass;
    public function __construct()
    {
        try
        { 
            parent::__construct(); 
            $this->data['title'] = "TWitter Connect";////Browser Title
            $this->data['ctrlr'] = "tconnect";		
            $this->cls_msg=array();
            $this->cls_msg["no_result"]				= "No information found."; 
            $this->pathtoclass=base_url().$this->router->fetch_class()."/";//for redirecting from this class
            $this->load->helper('cookie'); 
            $this->load->model('social_connect_model');	
		  
        }

        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }
    }
	
    public function index()
    {
        try
        {
            $this->load->view('fe/tconnect/login.tpl.php',$this->data);
	}

        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 	    
    }
	
	public function login()
    {
        try
        {	
			$consumer_key = $this->config->item('TWITTER_CONSUMER_KEY');
			$consumer_secret = $this->config->item('TWITTER_CONSUMER_SECRET');		
			
			$twitteroauth = new TwitterOAuth($consumer_key, $consumer_secret);
			// Requesting authentication tokens, the parameter is the URL we will be redirected to
			$request_token = $twitteroauth->getRequestToken(base_url().'tconnect/twitter_aurtho');
			
			$_SESSION['oauth_token'] = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
			
			// If everything goes well..
			if($twitteroauth->http_code == 200) 
			{

				// Let's generate the URL and redirect
				$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
				header('Location: ' . $url);
			} 
			else 
			{
				// It's a bad idea to kill the script, but we've got to know when there's an error.
				die('Something wrong happened.');
			}
			
			
			//$this->load->view('fe/tconnect/index.tpl.php',$this->data);
		}

        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 	    
    }
	
    public function twitter_aurtho()
    {
		try
		{
//                                $information = array(
//                                'i_user_id' => $information['i_user_id'] ,
//                                's_twitter_access_tokens' => $information['s_twitter_access_tokens'],
//                                's_twitter_id' => $information['s_twitter_id'],
//                                );

			if (!empty($_REQUEST['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']))
			{
				
                                $consumer_key = $this->config->item('TWITTER_CONSUMER_KEY');
				$consumer_secret = $this->config->item('TWITTER_CONSUMER_SECRET');
				// We've got everything we need
				$twitteroauth = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				// Let's request the access token
				$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
				// Save it in a session var
				$_SESSION['access_token'] = $access_token;
				// Let's get the user's info
				$user_info = $twitteroauth->get('account/verify_credentials');
								
				if (isset($user_info->error)) 
				{					
					//redirect(base_url().'tconnect');
                                        $this->session->set_userdata(array('message'=>'Sorry!! Unable to Connect with Twitter','message_type'=>'succ'));					
                                        ?>
                                        <script type="text/javascript">
                                           window.opener.location.reload();
                                           window.close();
                                        </script>
                                        <?
				} 
				else 
				{
                                        $_SESSION['screen_name'] = $user_info->screen_name;
                                        
                                        $this->add_twitter_account();
                                        $this->session->set_userdata(array('message'=>'Congrats!! Twitter is connected successfully','message_type'=>'succ'));					
                                        ?>
                                        <script type="text/javascript">
                                           window.opener.location.reload();
                                           window.close();
                                        </script>
                                        <?
					//redirect(base_url().'account/settings');					
				}
			} 
			else 
			{
				
				//redirect(base_url().'tconnect');
                            //redirect(base_url().'tconnect');
                                        $this->session->set_userdata(array('message'=>'Sorry!! Unable to Connect with Twitter','message_type'=>'succ'));					
                                        ?>
                                        <script type="text/javascript">
                                           window.opener.location.reload();
                                           window.close();
                                        </script>
                                        <?
			}
		}
		catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
    } 
	// End twitter login
	
	
	public function after_login()
    {
        try
        {	
			if($_SESSION['screen_name']!='')
				echo 'You have login successfully';
			else
				echo 'Please Login again';	
			
			?>
			
			<script type="text/javascript">
			window.opener.location.href='<?php echo base_url().'tconnect/my_tweets'; ?>';
			window.close();
			</script>
			<?php 
					
			//echo 'here';
		}

        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        } 	    
    }
	
	public function my_tweets()
	{
		$query_str = $_SESSION['screen_name'];
                
		$rssUrl = 'https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name='.$query_str.'&count=5';
		
		$rss = $this->getSslPage($rssUrl);
                $feed = json_decode($rss);
		if($feed)					
		{
			foreach($feed as $key=>$val)
			{
				echo $val->text.'</br>';
			}
		}
	}
        
        public function user_latest_tweet(){
//            $user_id = trim($this->input->post('twitter_id'));
//            $this->db->select('s_twitter');
//            $this->db->where('i_id', $user_id); 
//            $this->db->from($this->db->USER, $data);
//            $query= $this->db->get();
//            $data = $query->result_array();
            $tweeter_id = 'testacumen';
            if($tweeter_id!='') {
                
                $rssUrl = 'https://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name='.$tweeter_id.'&count=5';
		$rss = $this->getSslPage($rssUrl);
                $feed = json_decode($rss,TRUE);
                $feed[0]['created_at'] = date('d/m/Y',strtotime(my_show_text($feed[0]['created_at'])));
                echo json_encode($feed[0]);
                return;
            } else {
                $feed[0]['created_at'] = '';
                $feed[0]['text'] = 'Ups.. Not connected with twitter!!';
                
            }
        }
        
        private function add_twitter_account()
        {
           
            
                
            if($loggedin_user_id = $this->session->userdata('loggedin')) {
                $information  = array(
                    'i_user_id'=>$loggedin_user_id['user_id'],
                    's_twitter_access_tokens'=>$_SESSION['oauth_token'].','.$_SESSION['oauth_token_secret'],
                    'i_user_id'=>$loggedin_user_id['user_id'],
                    's_twitter_id'=> $_SESSION['screen_name']
                                    );
                $cookie = array(
                    'name'   => 'fosho_socials',
                    'value'  => encrypt(json_encode($information)),
                    'expire' => '86500',
                    'domain' => '192.168.1.239',
                    'path'   => '/',
                    'prefix' => 'foshotime_',
                    'secure' => FALSE
                );
                $this->input->set_cookie($cookie); 
                $this->social_connect_model->add_twitter($information); 
            }
           
        }
        
        public function twitter_authenticate()
        {   $information = NULL;
            $information = json_decode(decrypt($this->input->cookie('foshotime_fosho_socials')),true);
            $access_tokens = explode(',',$information['s_twitter_access_tokens']);
            if(count($this->social_connect_model->verify_twitter_offline_access($information))){
                echo 'No Need';
            }else{
                header('Location: '.base_url().'/tconnect/login');
            }
            
        }


        function getSslPage($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/home.php */

