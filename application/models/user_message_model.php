<?php
/**
* User_message_model Model
* 
*/

class User_message_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
    * @param mixed $condition, 
    *               "id" can be passed
    *               array("field1"=>value1,"field2 !="=>value2..)
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of admin db table.
    */
    public function user_message_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("user_message_index umi","umi.i_id=umd.i_message_index_id","left");//left join
			$this->db->join("user_details ud","ud.uid=umd.i_sender_id","left");//left join
			$this->db->join("user_details ud1","ud1.uid=umd.i_receiver_id","left");//left join
            $this->db->select('umd.*,umi.s_subject,ud.s_name AS sender_name,ud1.s_name AS receiver_name');
            
            $rs=$this->db
                ->get_where("user_message_data umd",
                            array("umd.i_id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            //$this->db->join("user_message_index umi","umi.i_id=umd.i_message_index_id","left");//left join
            $this->db->select('umd.*,umi.s_subject');
          
            
            /**
            * this is not result caching if active query caching, 
            * used only for auto pagging countAll statement re-querying. 
            * ex- we dont have to repeat 
            *    $this->db->where($condition); and $this->db->from($table); 
            *    FOR $this->db->count_all_results(); 
            * Because before this $this->db->get()->result(); executes 
            * so it will flush the previous statement. 
            * For this reason the  $this->db->start_cache(); is used.  
            */
            $this->db->start_cache();
                if(!empty($condition))
                    $this->db->where($condition);
                    $this->db->join("user_message_index umi","umi.i_id=umd.i_message_index_id","left");//left join
					$this->db->join("user_details ud","ud.uid=umd.i_sender_id","left");//left join
					$this->db->join("user_details ud1","ud1.uid=umd.i_receiver_id","left");//left join
            		$this->db->select('umd.*,umi.s_subject,ud.s_name AS sender_name,ud1.s_name AS receiver_name');
                    $this->db->from("user_message_data umd");
            $this->db->stop_cache();
            ////Limit the records and get results// 
            if(!empty($limit))           
                $this->db->limit($limit,$offset); 
            $rs=$this->db->get()->result();            
            
            //For auto pagination//
            if(!empty($this->pager))//means auto pager is requested
            {
                $this->pager["per_page"]=$limit;
                $this->pager["total_rows"]=$this->db->count_all_results();//total of the above query including where clause.
            }
            //pr($this->pager);
            //For auto pagination//  
            
            $this->db->flush_cache();///flush the select statement
            
            //pr( $this->db->last_query() );       
            //pr($this->pager);
            
            /**
            * Query cache
            * 
            * @var Admin_model
            * TODO: implement this in every where,
            * after insert,update,delete operation caching must be removed
            */
            /*$this->db->cache_on();
            ///this worked//
            $rs=$this->db
                ->get_where("admin",
                            $condition,
                            $limit,
                            $offset
                )->result();  
            //$rs=$this->db->query("SELECT * FROM admin")->result();///worked           
            $this->db->cache_off();
            //$this->db->cache_delete('admin', 'home');
            //pr($rs);*/           
        }
        return $rs;
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_message($values)
    {
        return $this->add_("user_message_data",$values);
    }
	
	 /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_table_data($table,$values)
    {
        return $this->add_($table,$values);
    }
	
	/**
    * Update 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function edit_table_data($table,$values,$where)
    {
        return $this->update_($table,$values,where);
    }
    
    /**
    * Update 
    * @param mixed $values, array("admin_type_id"=>3,"s_admin_name"=>"test user"...); OR 
    *              $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    * @param mixed $where,  array('name' => $name, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function update_user_message($values,$where)
    {
        return $this->update_("user_message_data",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_message($where)
    {
        return $this->delete_("user_message_data",$where);
    }    
	
	
	 /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function soft_delete_user_messag($where,$values='')
    {
       return $this->update_("user_message_index",$values,$where);
    }  
	
	/**
    * Fetch 
    * @param $s_where, $i_start, $i_limit
    * @returns an array     
    */
    public function get_all_message($s_where= null,$i_start=null,$i_limit=null)
    {
        $ret_=array();
		$s_qry = "SELECT umd.*,umi.s_subject,ud.s_name AS sender_name,ud1.s_name AS receiver_name
					 FROM user_message_data AS umd "
					." LEFT JOIN user_message_index umi ON umi.i_id=umd.i_message_index_id "
					." LEFT JOIN user_details ud ON ud.uid=umd.i_sender_id "
					." LEFT JOIN user_details ud1 ON ud1.uid=umd.i_receiver_id "
                .($s_where!=""?$s_where:"" ). " ORDER BY umd.dt_created_on DESC" .(is_numeric($i_start) && is_numeric($i_limit)?" Limit ".intval($i_start).",".intval($i_limit):"" );
        $rs=$this->db->query($s_qry);
		
		return $rs->result_array();
    }
	
	public function gettotal_msg($s_where= null,$i_start=null,$i_limit=null)
    {
        $ret_=0;
		$s_qry = " SELECT COUNT(umd.i_id) AS i_total FROM user_message_data AS umd "
					." LEFT JOIN user_message_index umi ON umi.i_id=umd.i_message_index_id "
					." LEFT JOIN user_details ud ON ud.uid=umd.i_sender_id "
					." LEFT JOIN user_details ud1 ON ud1.uid=umd.i_receiver_id "
                	.($s_where!=""?$s_where:"" );
        $rs=$this->db->query($s_qry);		
		$i_cnt=0;
		if($rs->num_rows()>0)
		{
		  foreach($rs->result() as $row)
		  {
			  $ret_=intval($row->i_total); 
		  }    
		  $rs->free_result();          
		}
		unset($s_qry,$rs,$row,$i_cnt,$s_where,$i_start,$i_limit);
		return $ret_;
    }
    
    
    public function __destruct(){}
    
}

?>
