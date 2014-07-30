<?php
/**
* user Model
* 
*/

class Featured_services_model extends MY_Model
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
    public function featured_services_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("users u","u.id=s.uid","left");//left join
            $this->db->join("category c","c.id=s.cat_id","left");
            $this->db->join("sub_category sc","sc.id=s.sub_cat_id","left");
            $this->db->join("user_feature_package ufp","ufp.id=s.feature_id AND s.i_featured=1");
            $this->db->select('u.s_user_name,c.s_category,sc.s_sub_category,ufp.s_package_name,s.*');
            
            $rs=$this->db
                ->get_where("user_service s",
                            array("s.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            $this->db->join("users u","u.id=s.uid","left");//left join
            $this->db->join("category c","c.id=s.cat_id","left");
            $this->db->join("sub_category sc","sc.id=s.sub_cat_id","left");
            $this->db->join("user_feature_package ufp","ufp.id=s.feature_id AND s.i_featured=1");
            $this->db->select('u.s_user_name,c.s_category,sc.s_sub_category,ufp.s_package_name,s.*');
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
                $this->db->from("user_service s");
            $this->db->stop_cache();
            ////Limit the records and get results// 
            if(!empty($limit))           
                $this->db->limit($limit,$offset); 
            $rs=$this->db->get()->result();            
            //echo $this->db->last_query();
            //For auto pagination//
            if(!empty($this->pager))//means auto pager is requested
            {
                $this->pager["per_page"]=$limit;
                $this->pager["total_rows"]=$this->db->count_all_results();//total of the above query including where clause.
            }
            //pr($this->pager);
            //For auto pagination//  
            
            $this->db->flush_cache();///flush the select statement
            
           
        }
        return $rs;
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_featured_services($values)
    {
        return $this->add_("user_feature_package",$values);
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
    public function update_featured_services($values,$where)
    {
        return $this->update_("user_feature_package",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_featured_services($where)
    {
        return $this->delete_("user_feature_package",$where);
    }
    public function update_featured_services_status($id)
    {
        $sql="UPDATE `user_feature_package` SET `i_active` = 1-`i_active` WHERE `id` =  {$id} ";
        return $this->db->query($sql);
    }    
    
    
    public function __destruct(){}
    
}

?>
