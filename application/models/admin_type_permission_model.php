<?php
/**
* User Permission Model
* 
*/

class Admin_type_permission_model extends MY_Model
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
    public function admin_type_permission_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("admin_type_permission",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_admin_type_permission($values)
    {
        return $this->add_("admin_type_permission",$values);
    }
    
    /**
    * Bulk insert,
    * 
    * @param array $values=>array(
    *                       array("admin_type_id"=>3,"s_admin_name"=>"test user"...),
    *                       array("admin_type_id"=>3,"s_admin_name"=>"test user"...),
    *                   );
    * @see, controllers/user_permission.php, acl_update();
    *               
    */
    public function acl_permission($values)
    {
        $this->db->truncate('admin_type_permission'); 
        return $this->db->insert_batch('admin_type_permission', $values);
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
    public function update_admin_type_permission($values,$where)
    {
        return $this->update_("admin_type_permission",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_admin_type_permission($where)
    {
        return $this->delete_("admin_type_permission",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
