<?php
/**
* country Model
* 
*/

class Payment_model extends MY_Model
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
    public function payment_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
       $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("users u","u.id=p.uid","left");//left join
            $this->db->select('u.s_user_name,p.*');
            $this->db->select(
                '(CASE WHEN
                     p.e_type="document verification" THEN 
                        (SELECT s_document_required FROM doc_verification AS d WHERE d.id=p.i_type_id)
                     WHEN p.e_type="advertisement" THEN 
                        (SELECT CONCAT_WS("\n Count :", a.s_url,CAST(a.i_displayed_count AS CHAR)) AS ads FROM 
                            advertisement a WHERE a.id=p.i_type_id)
                     WHEN p.e_type="franchisee" THEN
                        (SELECT f.s_url FROM franchisee_domain AS f WHERE f.id=p.i_type_id)
                     WHEN p.e_type="feature service" THEN
                        (SELECT us.s_service_name FROM user_service AS us WHERE us.id=p.i_type_id)
                END) 
              AS s_paid_for',
              FALSE
             );
            $rs=$this->db
                ->get_where("payment p",
                            array("p.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            //$this->db->join("users u","u.id=p.uid","left");//left join
            $this->db->select('u.s_user_name,p.*');
            $this->db->select(
                '(CASE WHEN
                     p.e_type="document verification" THEN 
                        (SELECT s_document_required FROM doc_verification AS d WHERE d.id=p.i_type_id)
                     WHEN p.e_type="advertisement" THEN 
                        (SELECT CONCAT_WS("\n Count :", a.s_url,CAST(a.i_displayed_count AS CHAR)) AS ads FROM 
                            advertisement a WHERE a.id=p.i_type_id)
                     WHEN p.e_type="franchisee" THEN
                        (SELECT f.s_url FROM franchisee_domain AS f WHERE f.id=p.i_type_id)
                     WHEN p.e_type="feature service" THEN
                        (SELECT us.s_service_name FROM user_service AS us WHERE us.id=p.i_type_id)
                END) 
              AS s_paid_for',
              FALSE
             );
            
           
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
            $this->db->join("users u","u.id=p.uid","left");//left join
                if(!empty($condition))
                    $this->db->where($condition);
                $this->db->from("payment p");
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
    public function add_payment($values)
    {
        return $this->add_("payment",$values);
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
    public function update_payment($values,$where)
    {
        return $this->update_("payment",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_payment($where)
    {
        return $this->delete_("payment",$where);
    } 
    
    /*public function update_payment_status($id,$val)
    {
        $sql="UPDATE `payment` SET `e_status` ='".trim($val)."' WHERE `id` =".intval($id)."";
        return $this->db->query($sql);
    } */
    
    /**
    * Fetching enum field values for creating dropdown
    */      
    public function fetch_status()
    {
    ////Now return only the enum col 'e_doc_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM payment LIKE 'e_status' ";
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
