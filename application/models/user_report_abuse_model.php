<?php
/**
* user_report_abuse Model
* 
*/

class User_report_abuse_model extends MY_Model
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
    public function user_report_abuse_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $rs=new stdClass();
              if(is_integer($condition))
              {
                $this->db->join("users u","u.id=ura.uid","left");//left join
                $this->db->join("users uby","uby.id=ura.uid_abuse_by","left");//left join
                $this->db->select('u.s_user_name,ura.*,uby.s_user_name AS s_abuse_by_user_name');
                $this->db->select(
                /**
                * on 31July2013, as per Mr. Ashim request
                * if e_abuse_for = user the s_absue_for=user   
                */
                /*'(CASE WHEN
                    ura.e_absue_for="service" THEN 
                       (SELECT s_service_name FROM user_service AS s WHERE s.id=ura.i_absue_for_id)
                    WHEN ura.e_absue_for="user" THEN 
                        (SELECT s_user_name FROM users AS u WHERE u.id=ura.i_absue_for_id)
                    WHEN ura.e_absue_for="company" THEN
                        (SELECT s_company FROM user_company AS c WHERE c.id=ura.i_absue_for_id)
                  END) 
                AS s_absue_for',*/
                '(CASE WHEN
                    ura.e_absue_for="service" THEN 
                       (SELECT CONCAT("Service : ",s_service_name)  FROM user_service AS s WHERE s.id=ura.i_absue_for_id)
                    WHEN ura.e_absue_for="user" THEN 
                        "User"
                    WHEN ura.e_absue_for="company" THEN
                        (SELECT CONCAT("Company : ",s_company) FROM user_company AS c WHERE c.id=ura.i_absue_for_id)
                  END) 
                AS s_absue_for',
                FALSE
                );
                $rs=$this->db
                ->get_where("user_report_abuse ura",
                           array("ura.id"=>$condition)
                )
                ->row();
              }
              else
              {
                  if(!empty($order_by))  
                  {
                      $this->db->order_by($order_by);
                  }  

                    $this->db->join("users u","u.id=ura.uid","left");//left join
                    $this->db->join("users uby","uby.id=ura.uid_abuse_by","left");//left join
                    $this->db->select('u.s_user_name,u.e_status AS e_user_status,ura.*,uby.s_user_name AS s_abuse_by_user_name');
                    
                    ///this is shifted below within cache section
                    /*$this->db->select(
                    '(CASE WHEN
                        ura.e_absue_for="service" THEN 
                           (SELECT s_service_name FROM user_service AS s WHERE s.id=ura.i_absue_for_id)
                        WHEN ura.e_absue_for="user" THEN 
                            (SELECT s_user_name FROM users AS u WHERE u.id=ura.i_absue_for_id)
                        WHEN ura.e_absue_for="company" THEN
                            (SELECT s_company FROM user_company AS c WHERE c.id=ura.i_absue_for_id)
                      END) 
                    AS s_absue_for',
                    FALSE
                    );*/
       /*$rs=$this->db
                      ->get_where("admin a",
                                  $condition,
                                  $limit,
                                  $offset
                      )->result();*/


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
                    $this->db->join("users usr","usr.id=ura.uid","left");//left join
                    $this->db->select('usr.s_user_name,ura.*');
                    $this->db->select(
                     /**
                    * on 31July2013, as per Mr. Ashim request
                    * if e_abuse_for = user the s_absue_for=user   
                    */
                    /*'(CASE WHEN
                        ura.e_absue_for="service" THEN 
                           (SELECT s_service_name FROM user_service AS s WHERE s.id=ura.i_absue_for_id)
                        WHEN ura.e_absue_for="user" THEN 
                            (SELECT s_user_name FROM users AS u WHERE u.id=ura.i_absue_for_id)
                        WHEN ura.e_absue_for="company" THEN
                            (SELECT s_company FROM user_company AS c WHERE c.id=ura.i_absue_for_id)
                      END) 
                    AS s_absue_for',*/
                    '(CASE WHEN
                        ura.e_absue_for="service" THEN 
                           (SELECT CONCAT("Service : ",s_service_name)  FROM user_service AS s WHERE s.id=ura.i_absue_for_id)
                        WHEN ura.e_absue_for="user" THEN 
                            "User"
                        WHEN ura.e_absue_for="company" THEN
                            (SELECT CONCAT("Company : ",s_company) FROM user_company AS c WHERE c.id=ura.i_absue_for_id)
                      END) 
                    AS s_absue_for',
                    FALSE
                    );
                    
                    $this->db->from("user_report_abuse ura");
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
    public function add_user_report_abuse($values)
    {
        return $this->add_("user_report_abuse",$values);
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
    public function update_user_report_abuse($values,$where)
    {
        return $this->update_("user_report_abuse",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_report_abuse($where)
    {
        return $this->delete_("user_report_abuse",$where);
    }    
    
    /**
    * Fetching enum field values for creating dropdown
    */
    public function fetch_abuse_for_status()
    {
    ////Now return only the enum col 'e_doc_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM user_report_abuse LIKE 'e_absue_for' ";
            $query  =   $this->db->query($sql);
            $rslt   =   $query->row_array();
            $query->free_result();
            /**
            * gathering the enum type keys, and assign as array,
            * then fill the array with value 0 
            * @var $arr_keys
            */
            $arr_keys=array();
            $temp=str_replace("enum","array",$rslt["Type"]);
            eval("\$arr_keys=$temp;");
            //$ret_matrix_flip=array_fill_keys($arr_keys,0);
            //pr($arr_keys);
            unset($sql,$query,$rslt,$temp);                        
            
            return $arr_keys;   
    }   
    
    /**
    * Fetching enum field values for creating dropdown
    */    
    public function fetch_abuse_action_status()
    {
    ////Now return only the enum col 'e_doc_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM user_report_abuse LIKE 'e_action_taken' ";
            $query  =   $this->db->query($sql);
            $rslt   =   $query->row_array();
            $query->free_result();
            /**
            * gathering the enum type keys, and assign as array,
            * then fill the array with value 0 
            * @var $arr_keys
            */
            $arr_keys=array();
            $temp=str_replace("enum","array",$rslt["Type"]);
            eval("\$arr_keys=$temp;");
            //$ret_matrix_flip=array_fill_keys($arr_keys,0);
            //pr($arr_keys);
            unset($sql,$query,$rslt,$temp);                        
            
            return $arr_keys;   
    }             
    
    public function __destruct(){}
    
}

?>
