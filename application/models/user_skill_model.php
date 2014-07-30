<?php
/**
* User skill
* 
*/

class User_skill_model extends MY_Model
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
    public function user_skill_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    { 
        return $this->load_("user_skill",$condition,$limit,$offset,$order_by);
    }
	
	/**
    * @param mixed $condition, 
    *               "id" can be passed
    *               array("field1"=>value1,"field2 !="=>value2..)
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of admin db table.
    */
    public function user_skill_fetch($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    { 
        $table = 'user_skill';
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $rs=$this->db
                ->get_where($table,
                            array("id"=>$condition)
                )
                ->row();
                
                        
        }
        else
        {   
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
			$this->db->group_by('s_skill_name');
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
                $this->db->from($table);
            $this->db->stop_cache();
            ////Limit the records and get results//       
            if(!empty($limit))                
                $this->db->limit($limit,$offset); 
            $rs=$this->db->get()->result();            
            //pr( $this->db->last_query() );  
            
            //For auto pagination//
            if(!empty($this->pager))//means auto pager is requested
            {
                $this->pager["per_page"]=$limit;
                //$this->pager["total_rows"]=$this->db->count_all($table);//total of table  
                $this->pager["total_rows"]=$this->db->count_all_results();//total of the above query including where clause.
            }
            //pr($this->pager);
            //For auto pagination//  
            
            $this->db->flush_cache();///flush the select statement
                        //pr( $this->db->last_query() );
        }
        return $rs;
        
    
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_skill($values)
    {
        
        return $this->add_("user_skill",$values);
        //$this->db->insert('', $data); 
        
        
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
    public function update_user_skill($values,$where)
    {
        return $this->update_("user_skill",$values,$where);
    }    
    
       
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_skill($where)
    {
        return  $this->delete_("user_skill",$where);
       
    }    
    

    public function count_endorsement($uid)
    {
        $this->db->select_sum("i_endorse_count");
        $this->db->group_by("uid");
        $rs=$this->db
            ->get_where("user_skill",
                        array("uid"=>$uid)
            )
            ->row();                

        if(!empty($rs))
        {
            return intval($rs->i_endorse_count);
        }
        return 0;
    }
	
	
	public function checking_duplicate_skills($uid,$skill="")
    {
        $rs=$this->db
            ->get_where("user_skill",
                        array("uid"=>$uid,
							"s_skill_name"=>$skill)
            )
            ->row();                

        if(!empty($rs))
        {
            return true;
        }
        return false;
    }    


    public function __destruct(){}
    
}

?>
