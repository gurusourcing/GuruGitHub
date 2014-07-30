<?php
/**
* option Model
* 
* on 13Dec13, as per client request
* If user chooses a particular category then that category 
* specific advanced filters are displayed below common filters. 
* These filters are related to the service that user provide as 
* expert/guru under a particular category. Example : When search 
* with ( dentist ) In the Specialization boxes  auto suggest  will 
* show the specialization under that particular category. And will 
* check all other right panel boxes with proper options.
*   >>A new column "cat_id" added in db "option" and "user_suggestion" tables.
*/

class Option_model extends MY_Model
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
    public function option_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        //return $this->load_("option",$condition,$limit,$offset,$order_by);
        
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("category c","c.id=o.cat_id","left");//left join
            $this->db->select('o.*,c.s_category');
            
            $rs=$this->db
                ->get_where("option o",
                            array("o.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            $this->db->select('o.*,c.s_category');
            /*$rs=$this->db
                ->get_where("admin a",
                            $condition,
                            $limit,
                            $offset
                )->result();*/
            
            
            /**
            * this is not result caching if active query caching, 
            * used only for auto pagging countAll cityment re-querying. 
            * ex- we dont have to repeat 
            *    $this->db->where($condition); and $this->db->from($table); 
            *    FOR $this->db->count_all_results(); 
            * Because before this $this->db->get()->result(); executes 
            * so it will flush the previous cityment. 
            * For this reason the  $this->db->start_cache(); is used.  
            */
            $this->db->start_cache();
                $this->db->join("category c","c.id=o.cat_id","left");//left join
                if(!empty($condition))
                    $this->db->where($condition);
                $this->db->from("option o");
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
            
            $this->db->flush_cache();///flush the select cityment
            
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
    public function add_option($values)
    {
        return $this->add_("option",$values);
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
    public function update_option($values,$where)
    {
        return $this->update_("option",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_option($where)
    {
        return $this->delete_("option",$where);
    }  
    
    public function fetch_option_type()
    {
         ////Now return only the enum col 'e_doc_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM `option` LIKE 'e_type' ";
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
