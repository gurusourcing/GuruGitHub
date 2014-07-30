<?php
/**
* user_service_extended Model
* 
*/

class User_service_extended_model extends MY_Model
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
    public function user_service_extended_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("user_service_extended",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_service_extended($values)
    {
        return $this->add_("user_service_extended",$values);
    }
    
    /**
    * Update 
    * @param mixed $values, array("admin_type_id"=>3,"s_admin_name"=>"test user"...); OR 
    *              $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    * @param mixed $where,  array('name' => $name, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
	* @param $flag, is send by default false and true for return the affected rows needed
							for update user_service_extended @see service_profile.php
    */
    public function update_user_service_extended($values,$where, $flag = false)
    {
        return $this->update_("user_service_extended",$values,$where, $flag);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_service_extended($where)
    {
        return $this->delete_("user_service_extended",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
