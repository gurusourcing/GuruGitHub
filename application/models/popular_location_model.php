<?php
/**
* popular_location Model
* 
*/

class Popular_location_model extends MY_Model
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

    public function popular_location_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("country cn","cn.id=z.country_id","left");//left join
            $this->db->join("city cy","cy.id=z.city_id","left");//left join
            $this->db->join("state s","s.id=z.state_id","left");//left join
            $this->db->select('z.*,cn.s_country,s.s_state,cy.s_city');
            
            $rs=$this->db
                ->get_where("popular_location z",
                            array("z.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            $this->db->join("country cn","cn.id=z.country_id","left");//left join
            $this->db->join("city cy","cy.id=z.city_id","left");//left join
            $this->db->join("state s","s.id=z.state_id","left");//left join
            $this->db->select('z.*,cn.s_country,s.s_state,cy.s_city');
            /*$rs=$this->db
                ->get_where("admin a",
                            $condition,
                            $limit,
                            $offset
                )->result();*/
            
            
            /**
            * this is not result caching if active query caching, 
            * used only for auto pagging countAll popular_locationment re-querying. 
            * ex- we dont have to repeat 
            *    $this->db->where($condition); and $this->db->from($table); 
            *    FOR $this->db->count_all_results(); 
            * Because before this $this->db->get()->result(); executes 
            * so it will flush the previous popular_locationment. 
            * For this reason the  $this->db->start_cache(); is used.  
            */
            $this->db->start_cache();
                if(!empty($condition))
                    $this->db->where($condition);
                $this->db->from("popular_location z");
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
            
            $this->db->flush_cache();///flush the select popular_locationment
            
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
    public function add_popular_location($values)
    {
        return $this->add_("popular_location",$values);
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
    public function update_popular_location($values,$where)
    {
        return $this->update_("popular_location",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_popular_location($where)
    {
        return $this->delete_("popular_location",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
