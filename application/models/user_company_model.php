<?php
/**
* country Model
* 
*/

class User_company_model extends MY_Model
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
    public function user_company_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
       $rs=new stdClass();
       //debug_print_backtrace();
        if(is_integer($condition))
        {
            $this->db->join("users u","u.id=uc.uid","left");//left join
            $this->db->select('u.s_user_name,uc.*');
            
            $rs=$this->db
                ->get_where("user_company uc",
                            array("uc.id"=>$condition)
                )
                ->row();
                       
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            //$this->db->join("users u","u.id=uc.uid","left");//left join
            $this->db->select('u.s_user_name,uc.*');
            //$this->db->select('u.s_user_name,uc.*');
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
                $this->db->join("users u","u.id=uc.uid","left");//left join
                if(!empty($condition))
                    $this->db->where($condition);
                $this->db->from("user_company uc");
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
    public function add_user_company($values)
    {
        $newId=$this->add_("user_company",$values);
        
        //if add successful then update user tbl and user_service tbl
        if($newId)
        {
            $CI=&get_instance();
            $CI->load->model('user_model'); // load user_model
            $CI->load->model('user_service_model'); // load user_service_model
            
            $uid=$values["uid"]; // user id
            
            /**
            * updating  i_is_company_service = 1, comp_id = newId, i_is_company_default = 1, s_dummy
            * into user_service tbl
            */
            $service= $CI->user_service_model->user_service_load(array('uid'=>$uid));
            if(!empty($service))  
            {
                $service=$service[0];
                //pr($s_dummy);
                /* setting up the s_dummy field value */
                $val = decodeFromDummyField($service->s_dummy);
                $val['i_is_company_service'] =1;
                $temp_s_dummy = encodeArrayToDummyField($val);                
                
                $CI->user_service_model->update_user_service(
                        array('i_is_company_service'=>1,
                            'comp_id'=>$newId,
                            'i_is_company_default'=>1,
                            "s_dummy"=>$temp_s_dummy,
                            ),
                        array('uid'=>$uid));
            }

                
            


            // updating  i_is_company_owner=1, comp_id=newId  into user tbl
            $CI->user_model->update_user(array('i_is_company_owner'=>1,'comp_id'=>$newId), array('id'=>$uid));
                                   
        }
        return $newId;
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
    public function update_user_company($values,$where)
    {
        return $this->update_("user_company",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_company($where)
    {
        return $this->delete_("user_company",$where);
    } 
    
    public function update_user_company_status($id)
    {
        $sql="UPDATE `user_company` SET `i_active` = 1-`i_active` WHERE `id` =  {$id} ";
        return $this->db->query($sql);
    }
    
    
    /**
    * user company skills
    * @param company id
    * @see company profile       
    */
    public function company_employee_skill($comp_id=NULL)
    {
        $this->db->select('uce.uid');
        $this->db->where(array("uce.comp_id"=>intval($comp_id),"uce.i_active"=>1));
        $this->db->from("user_company_employee uce");
        $rs=$this->db->get()->result_array();
        
        $uid='';
        if(!empty($rs))
            foreach($rs as $k=>$v)
                 $uid=(!empty($uid) ? $uid.','.$v["uid"] : $v["uid"]);
         
        $obj_skill=array(); 
        if(!empty($uid))
        {
            $CI=&get_instance();
            $CI->load->model('user_skill_model');
            $obj_skill=$CI->user_skill_model->user_skill_load("uid IN (".$uid.")");
        }
         
         return $obj_skill;
           
    }
    
    
    
     /**
    * fetch only the s_dummy field
    * @param company id      
    */
    public function fetch_dummy($s_id)
    {
        $this->db->select('c.s_dummy');
        $rs=$this->db
                ->get_where("user_company c",
                            array("c.id"=>$s_id)
                )
                ->row();
        return $rs;
    }
    
    public function fetch_company_status($comp_id)
    {
        $this->db->select('c.i_active');
        $rs=$this->db
                ->get_where("user_company c",
                            array("c.id"=>$comp_id)
                )
                ->row();
        return $rs;
    }

    
    public function __destruct(){}
    
}

?>
