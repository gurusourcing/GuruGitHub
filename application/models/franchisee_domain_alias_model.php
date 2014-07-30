<?php
/**
* Franchisee Domain Alias Model
* A franchisee can assign many domain names for a single 
* subdomain in franchisee_domain record. 
* 
* Ex- doctors.guru.com is the url, now “doctors” is the 
* sub domain franchisee choose at the time of subscription. 
* Now he wants to  add multiple domains under this account. 
* Say  doctors.kolkata.in, doctors.india.in are the new urls 
* he want. 
* So he need to add, Kolkata.in and india.in as new domains. 
* These domains will be stored into this table which will reflect 
* same results as doctors.guru.com  
* 
* 
*/

class Franchisee_domain_alias_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
    * @param mixed $condition, 
    *               "id" can be passed
    *               array("field1"=>value1,"field2"=>value2..)
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of admin db table.
    */
    public function franchisee_domain_alias_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("admin a","a.id=fda.aid","left");//left join
            $this->db->join("franchisee_domain fd","fd.id=fda.fdomain_id","left");//left join
            $this->db->select('fda.*,a.admin_type_id, a.s_admin_name, fd.s_sub_domain as s_sub_fdomain, 
                                fd.s_domain as s_fdomain, fd.s_url as s_furl ');
            
            
            $rs=$this->db
                ->get_where("franchisee_domain_alias fda",
                            array("fd.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            $this->db->join("admin a","a.id=fda.aid","left");//left join
            $this->db->join("franchisee_domain fd","fd.id=fda.fdomain_id","left");//left join
            $this->db->select('fda.*,a.admin_type_id, a.s_admin_name, fd.s_sub_domain as s_sub_fdomain, 
                                fd.s_domain as s_fdomain, fd.s_url as s_furl ');
                                
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
                $this->db->where($condition);
                $this->db->from("franchisee_domain fda");
            $this->db->stop_cache();
            ////Limit the records and get results//            
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
    public function add_franchisee_domain_alias($values)
    {
        return $this->add_("franchisee_domain_alias",$values);
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
    public function update_franchisee_domain_alias($values,$where)
    {
        return $this->update_("franchisee_domain_alias",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_franchisee_domain_alias($where)
    {
        return $this->delete_("franchisee_domain_alias",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
