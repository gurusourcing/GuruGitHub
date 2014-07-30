<?php
/*********
* Author: Kallol Basu
* Date  : 28/3/2013
* Purpose:
*  Model For Social Connect
* 
* @package General
* @subpackage city
* 
* @link InfModel.php 
* @link MY_Model.php
* @link controllers/city.php
* @link views/admin/city/
*/


class Social_connect_model extends MY_Model 
{
    private $conf;
    private $tbl;///used for this class


    public function __construct()
    {
        try
        {
          parent::__construct();
         // $this->tbl 	= 	$this->db->SOCIALCONNECT;
         // $this->tbl_user = $this->db->USER;
	
        }
        catch(Exception $err_obj)
        {
            show_error($err_obj->getMessage());
        }          
    }

    public function add_twitter($information = NULL)
    {
        if($information){
            $this->db->like('i_user_id', $information['i_user_id']);
            $this->db->from($this->tbl);
            
            if($this->db->count_all_results()){
                $this->db->where('i_user_id',$information['i_user_id']);
                 $this->db->update($this->tbl, $information);
            } else {
                
                $this->db->insert($this->tbl, $information);
            }
             
            return;
        }
    }
    public function add_facebook($information = NULL)
    {
        
        if($information){
            $this->db->like('i_user_id', $information['i_user_id']);
            $this->db->from($this->tbl);
            
            if($this->db->count_all_results()){
                $this->db->where('i_user_id',$information['i_user_id']);
                 $this->db->update($this->tbl, $information);
            } else {
                
                $this->db->insert($this->tbl, $information);
            }
             
            return;
        }
        
        
    }
    
    public function update_twitter($information = NULL)
    {
       if($information){
            $this->db->where('i_user_id', $information['i_user_id']);
            $this->db->update($this->tbl, $information, $information); 
           return;
       }
    }
    
    public function verify_twitter_offline_access($information = NULL)
    {
        $query = $this->db->get_where($this->tbl, array('i_user_id' => $information['i_user_id'],'s_twitter_access_tokens'=>$information['s_twitter_access_tokens']),100,0);
      
        return$query->result_array();
    }
    
    public function is_twitter_connected($i_user_id = 0)
    {
        if($i_user_id){
            $query = $this->db->get_where($this->tbl, array('i_user_id' => $i_user_id),100,0);
            $data = $query->result_array();
            if(count($data)){
                return $data[0]['s_twitter_id'];
            }
        }
        
      
        return false;
    }
    
    public function is_facebook_connected($i_user_id = 0)
    {
        if($i_user_id){
            $query = $this->db->get_where($this->tbl, array('i_user_id' => $i_user_id),100,0);
            $data = $query->result_array();
            if($data[0]['s_facebook_access_tokens']!=''){
                return $data[0]['s_facebook_access_tokens'];
            }
        }
        
      
        return false;
    }
    public function match_facebook_accesstoken($accesstoken_value =  null)
    {
        
        if($accesstoken_value){
            $query = $this->db->get_where($this->tbl, array('s_facebook_access_tokens' => $accesstoken_value),100,0);
            $data = $query->result_array();
            if(count($data)){
                return $data[0]['i_user_id'];
            }
            return false;
            
        }
        
      
        return false;
    }
    public function add_offline_status($information){
        if($information){
            $this->db->where('i_id', $information['i_id']);
            $this->db->update($this->tbl_user, $information); 
           return;
       }
    }


	public function update_facebook_accesstoken($access_token_val,$user_id) {
		if($access_token_val){
			$information['s_facebook_access_tokens'] = $access_token_val;
            $this->db->where('i_user_id', $user_id);
            $this->db->update($this->tbl, $information); 
			return;
		}
	}





    public function __destruct()
    {}                 
  
  
}
///end of class
?>