<?php
/**
* User Model
* 
*/

class User_model extends MY_Model
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
    public function user_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    { 
        $rs=new stdClass();
        if(is_integer($condition))
        {
            $this->db->join("users u","u.id=d.uid","left");//left join
            //$this->db->select('d.s_profile_photo,u.s_user_name,u.dt_last_login,u.id');
            $this->db->select('*');
            
            $rs=$this->db
                ->get_where("user_details d",
                            array("u.id"=>$condition)
                )
                ->row();
                        
        }
        else
        {
            if(!empty($order_by))  
            {
                $this->db->order_by($order_by);
            }  
            
            //$this->db->join("users u","u.id=d.uid","left");//left join
            //$this->db->select('d.s_profile_photo,u.s_user_name,u.dt_last_login,u.id');
            $this->db->select('*');
            
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
                $this->db->join("users u","u.id=d.uid","left");//left join 
                if(!empty($condition))
                {
                    //$this->db->where($condition);//changed to
                    /**
                    * you must protect your values with ''
                    * ex= $condition="z.country_id=".$posted["country_id"]." 
                    *       AND cy.s_city LIKE '%".trim($posted["term"])."%'"
                    * see the string values at cy.s_city are placed within " ' " single quote
                    */
                    if(is_string($condition))
                        $this->db->where($condition,NULL,FALSE);
                    else
                        $this->db->where($condition);                    
                }
                    
                    
                    
                $this->db->from("user_details d");
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
            
           
        }
        return $rs;
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user($values)
    {
        
        //return $this->add_("users",$values);
        //$this->db->insert('', $data); 
        $data = array(      's_user_name'=>@$values['s_user_name'],
                            's_password'=>md5(@$values['s_password']),
                            's_ip'      => @$values['s_ip'],
                            'dt_registration'=> @$values['dt_registration'],
                            'dt_last_login'=> @$values['dt_last_login'],
                            's_facebook_credential'=>trim(@$values['s_facebook_credential']),
                            'comp_id'=>intval(@$values['comp_id']),
                            'i_is_company_emp'=>intval(@$values['i_is_company_emp']),
                            'e_status'=>'active',
                            's_verification_code'=>@$values['s_verification_code'],
                            'i_email_verified'=>intval(@$values['i_email_verified'])
                        );
        $this->db->insert('users', $data); 
        $id = $this->db->insert_id();
        unset($data);
        $data = array(      'uid'=>$id,
                            's_name'=>@$values['s_name'],
                            's_email'=>@$values['s_email'],
                            's_display_name'=>@$values['s_display_name'],
                            'dt_dob'=>format_date(@$values['dt_dob'],'Y-m-d H:i') ,
                            'e_gender'=>@$values['e_gender']
                        );
        $this->add_("user_details",$data);
        //return $this->add_("user_details",$data);//changed by kallol
        return $id;
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
    public function update_user($values,$where)
    {
        return $this->update_("users",$values,$where);
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
    public function update_user_details($values,$where)
    {
        return $this->update_("user_details",$values,$where);
    }        
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user($where)
    {
        $res = $this->delete_("users",$where);
        if($res)
        {
            $uid = array('uid'=>$where['id']);
            return $this->delete_("user_details",$uid);    
        }
        return false;
    }    
    
    /**
     * Fetch Sort Description
     * @param int $uid
     */
    public function user_short_description($uid) {
        
        $this->db->select('count(user_service.uid) AS total_services,AVG(user_service.i_profile_complete_percent) AS i_avg_profile_complete_percent'); 
        $this->db->where(array('user_service.uid'=>$uid,'i_active'=>1));
        $this->db->group_by("user_service.uid"); 
        $query = $this->db->get('user_service');
        return $query->result_object();
        
    }
    
    /**
     * Fetch Sort Description
     * @param int $uid
     */
    public function user_designation($uid) {
        
        $query=$this->db->query("Select * From 
                            (Select * From user_profession ORDER BY dt_from DESC) m
                            WHERE m.uid=".intval($uid)."
                            GROUP BY m.uid  
                                ");
        return $query->row();  
    }     

    
     /**
    * fetch only the s_dummy field
    * @param user id      
    */
    public function fetch_dummy($s_id)
    {
        $this->db->select('u.s_dummy');
        $rs=$this->db
                ->get_where("users u",
                            array("u.id"=>$s_id)
                )
                ->row();
        return $rs;
    }

    public function __destruct(){}
    
}

?>
