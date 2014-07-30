<?php
/**
* Author : Sahinul Haque
* Date : 29Mar2013  
* 
* Additional features to Model.
* Provides a common way to fetch,insert, update, delete records from here. 
*/

class MY_Model extends CI_Model
{
    public $pager;
    public function __construct()
    {
        parent::__construct();
        $this->pager=array();//pagination config
        /**
        * Setting the group concat 
        * max values
        */
        //$this->db->query("SET @@group_concat_max_len = 4294967294");
        
    }
    
    
    /**
    * *Here no joining is possiable. 
    * This can be used to fetch all or single record 
    * from a table.
    * 
    * @param string $table, the db table name
    * @param mixed $condition, 
    *               "id" can be passed
    *               array("field1"=>value1,"field2"=>value2..)
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of admin db table.
    */
    public function load_($table,$condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
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
            
            //a new form of writing query
            /*$rs=$this->db
                ->get_where($table,
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
    * generates and return the 
    * pagination with respect to the query load_() 
    * autometically. 
    * 
    * $options is the pagination config array except 
    * "per_page" and "total_rows". 
    * "base_url" and "uri_segment" can be supplied
    * 
    * @see, controllers/user_role.php for auto pager using load_() method.
    */
    public function get_pager()
    {
        $this->load->library('pagination');
        if(empty($this->pager["per_page"]))
            $this->pager["per_page"]=20;
            
        if(empty($this->pager["base_url"]))
            $this->pager["base_url"]=base_url();//mut supply this
            
        if(empty($this->pager["uri_segment"]))
            $this->pager["uri_segment"]=2;//must supply this
            
        /**
        * Apply the necessary html changes for
        * admin theme "aquincum"
        */
        $current_theme=get_theme_path();
        if(strpos($current_theme,"aquincum")===FALSE)///fe theme, pagination wrapper, TODO::
        {
            /**
            * Additional configuration of pager for admin templates
            */
            $this->user_pager_theme();            
        }
        else
        {
            /**
            * Additional configuration of pager for admin templates
            */
            $this->admin_pager_theme();
        }
            
        
        $this->pagination->initialize($this->pager); 
        
        return $this->pagination->create_links();
                                
    }
    
    private function admin_pager_theme()
    {
        $this->pager["full_tag_open"]='<div class="tPages"><ul class="pages">';
        $this->pager["full_tag_close"]='</ul></div>';
        
        $this->pager['first_tag_open'] = '<li>';
        $this->pager['first_tag_close'] = '</li>';
        $this->pager['first_link'] = 'First';
        
        $this->pager['last_link'] = 'Last';
        $this->pager['last_tag_open'] = '<li>';
        $this->pager['last_tag_close'] = '<li>';
        
        $this->pager['next_link'] = '<span class="icon-arrow-17"></span>';
        $this->pager['next_tag_open'] = '<li class="next">';
        $this->pager['next_tag_close'] = '</li>';   
        
        $this->pager['prev_link'] = '<span class="icon-arrow-14"></span>';
        $this->pager['prev_tag_open'] = '<li class="prev">';
        $this->pager['prev_tag_close'] = '</li>';   
        
        $this->pager['cur_tag_open'] = '<li><a href="javascript:void(0);" title="" class="active">';
        $this->pager['cur_tag_close'] = '</a></li>';  
        
        $this->pager['num_tag_open'] = '<li>';
        $this->pager['num_tag_close'] = '</li>';                     
                
    }
    
    
    private function user_pager_theme()
    {
        $this->pager["full_tag_open"]='<div class="tPages listing"><ul class="pages pagination">';
        $this->pager["full_tag_close"]='</ul></div>';
        
        $this->pager['first_tag_open'] = '<li>';
        $this->pager['first_tag_close'] = '</li>';
        $this->pager['first_link'] = 'First';
        
        $this->pager['last_link'] = 'Last';
        $this->pager['last_tag_open'] = '<li>';
        $this->pager['last_tag_close'] = '<li>';
        
        $this->pager['next_link'] = '<span class="icon-arrow-17"></span>';
        $this->pager['next_tag_open'] = '<li class="next">';
        $this->pager['next_tag_close'] = '</li>';   
        
        $this->pager['prev_link'] = '<span class="icon-arrow-14"></span>';
        $this->pager['prev_tag_open'] = '<li class="prev">';
        $this->pager['prev_tag_close'] = '</li>';   
        
        $this->pager['cur_tag_open'] = '<li><a href="javascript:void(0);" title="" class="active">';
        $this->pager['cur_tag_close'] = '</a></li>';  
        
        $this->pager['num_tag_open'] = '<li>';
        $this->pager['num_tag_close'] = '</li>';                     
                
    }    
    
    /**
    * Insert new record into a table.
    * 
    * @param string $table, the db table name
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_($table,$values)
    {
        $ret=FALSE;
        if(!empty($values)
           && (is_array($values) || is_a($values,"stdClass"))
        )
        {
            $this->db->insert($table,$values);
            $ret=$this->db->insert_id();
        }
        return $ret;
    }
    
    /**
    * Update a table.
    * 
    * @param string $table, the db table name
    * @param mixed $values, array("admin_type_id"=>3,"s_admin_name"=>"test user"...); OR 
    *              $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    * @param mixed $where,  array('name' => $name, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
	* @param $flag, is send by default false and true for return the affected rows needed
							for update user_service_extended @see service_profile.php
    */
    public function update_($table,$values,$where,$flag = false)
    {
        $ret=FALSE;
        if(!empty($values) 
           && !empty($where) 
           && (is_array($values) || is_a($values,"stdClass"))
           
        )        
        {
            $this->db->where($where);
            $ret = $this->db->update($table,$values);
			if($flag)
             	return  $this->db->affected_rows();
			
        }
        //pr($this->db->last_query());
        return $ret;
    }    
    
    /**
    * Delete a record.
    * 
    * @param string $table, the db table name
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_($table,$where)
    {
        $ret=FALSE;
        if( !empty($where) )        
        {
            $this->db->where($where);
            $this->db->delete($table);
            $ret=$this->db->affected_rows();
        }
        return $ret;
    }    
        
    
    
    public function __destruct(){}
}
?>
