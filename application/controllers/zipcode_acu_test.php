<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe Dashboard
*  zipcode_acu, zip_lat_test, popular_location_lat_test
*/

class Zipcode_acu_test extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
        is_userLoggedIn($redirect_deny_page=TRUE);
    }   
	 
    public function index()
    {
		
		/*$sql = "SELECT id,s_zip FROM zip_lat_test WHERE country_id = 2 ";
        $res= $this->db->query($sql);
		$rs = $res->result_array();
		
		if(!empty($rs))
		{
			foreach($rs as $val)
			{
				
				$sql2 = "SELECT latitude,longitude FROM zipcode_acu WHERE postal_code = '".$val['s_zip']."' ";
				$res2= $this->db->query($sql2);
				$rs2 = $res2->result_array();
				
				
				$values = array();
				$values['s_latitude'] 	= $rs2[0]['latitude'];
				$values['s_longitude'] 	= $rs2[0]['longitude'];
				$where = array('id'=>$val['id']);
				$this->update_table_("zip_lat_test",$values,$where);
				
				
			}
		}*/
		
		$sql = "SELECT id,s_zip FROM zip_lat_test WHERE country_id = 2 AND  s_latitude='' ";
		
		
		/*$sql = "SELECT z.id,z.s_zip,c.s_city,s.s_state,cnt.s_country  
					FROM zip_lat_test AS z 
					LEFT JOIN state s ON s.id = z.state_id 
					LEFT JOIN country cnt ON cnt.id = z.country_id 
					LEFT JOIN city c ON c.id = z.city_id 
					WHERE z.country_id = 2 AND  z.s_latitude='' ";*/
        $res= $this->db->query($sql);
		$rs = $res->result_array();
		echo count($rs);		
		pr($rs,1);
		if(!empty($rs))
		{
			foreach($rs as $val)
			{
				// method one who does not get lat or long yet
				/*$sql2 = "SELECT latitude,longitude,postal_code FROM zipcode_acu WHERE postal_code = '".$val['s_zip']."' ";
				$res2= $this->db->query($sql2);
				$rs2 = $res2->result_array();
				
				$values = array();
				$values['s_latitude'] 	= $rs2[0]['latitude'];
				$values['s_longitude'] 	= $rs2[0]['longitude'];
				$where = array('id'=>$val['id']);
				$this->update_table_("zip_lat_test",$values,$where);*/
				
				 //method two who does not get lat or long yet
				/*$lat_lng = $this->getLnt($val['s_zip']);	
				$values = array();
				$values['s_latitude'] 	= $lat_lng['lat'];
				$values['s_longitude'] 	= $lat_lng['lng'];
				$where = array('id'=>$val['id']);
				$this->update_table_("zip_lat_test",$values,$where);*/
				
				
				// last method
				/*$address = $val['s_city'].','.$val['s_state'].','.$val['s_country'];
				//echo $address = str_replace(' ','+',$address);
				$lat_lng = $this->getLatLn($address);	
				
				$values = array();
				$values['s_latitude'] 	= $lat_lng->results[0]->geometry->location->lat;
				$values['s_longitude'] 	= $lat_lng->results[0]->geometry->location->lng;
				$where = array('id'=>$val['id']);
				$this->update_table_("zip_lat_test",$values,$where);*/
				
				
			}
		}
		
	
		echo 'here';
		exit;
    }
	
	
	// updating popular location latitude / longitude
	public function popular_location_lat()
	{
		/*$sql = "SELECT zlm.popular_location_id,zlm.zip_id,zlt.s_latitude,zlt.s_longitude 
				FROM zip_location_mapping AS zlm
				LEFT JOIN zip_lat_test zlt ON zlt.id = zlm.zip_id WHERE 1 ORDER BY zlm.id desc ";
        $res= $this->db->query($sql);
		$rs = $res->result_array();
		
		if(!empty($rs))
		{
			foreach($rs as $val)
			{
				
				$values = array();
				$values['s_latitude'] 	= $val['s_latitude'];
				$values['s_longitude'] 	= $val['s_longitude'];
				$where = array('id'=>$val['popular_location_id']);
				$this->update_table_("popular_location_lat_test",$values,$where);
				
			}
		}*/
		echo 'here';
		exit;
	}
	
	public function city_lat_update()
	{
		$sql = "SELECT c.id,c.s_city,s.s_state,cnt.s_country 
				FROM city_lat_test AS c
				LEFT JOIN state s ON s.id = c.state_id 
				LEFT JOIN country cnt ON cnt.id = c.country_id 
				WHERE c.country_id = 2 AND c.s_latitude='' ORDER BY c.id ASC ";
        $res= $this->db->query($sql);
		$rs = $res->result_array();
		//pr($rs,1);
		if(!empty($rs))
		{
			foreach($rs as $val)
			{
				
				$address = $val['s_city'].','.$val['s_state'].','.$val['s_country'];
				//echo $address = str_replace(' ','+',$address);
				$lat_lng = $this->getLatLn($address);	
				
				$values = array();
				$values['s_latitude'] 	= $lat_lng->results[0]->geometry->location->lat;
				$values['s_longitude'] 	= $lat_lng->results[0]->geometry->location->lng;
				$where = array('id'=>$val['id']);
				//pr($values,1);
				$this->update_table_("city_lat_test",$values,$where);
				
			}
		}
		echo 'here';
		exit;
	}
	
	function getLnt($zip)
	{
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=
		".urlencode($zip)."&sensor=false";
		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		$result1[]=$result['results'][0];
		$result2[]=$result1[0]['geometry'];
		$result3[]=$result2[0]['location'];
		return $result3[0];
	}
	
	function getLatLn($address)
	{
		$address = str_replace(" ", "+", $address);
		$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response_a = json_decode($response);
	}
	
	
	 public function update_table_($table,$values,$where)
    {
        $ret=FALSE;
        if(!empty($values) 
           && !empty($where) 
           && (is_array($values) || is_a($values,"stdClass"))
           
        )        
        {
            $this->db->where($where);
            $this->db->update($table,$values);
            $ret=$this->db->affected_rows();
        }
        //pr($this->db->last_query());
        return $ret;
    }  
	
    
}

/* End of file dashboard.php */
/* Location: ./application/controllers/dashboard.php */