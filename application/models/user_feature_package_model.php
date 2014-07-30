<?php
/**
* user Model
* 
*/

class User_feature_package_model extends MY_Model
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
    public function user_feature_package_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("user_feature_package",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_feature_package($values)
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
    public function update_user_feature_package($values,$where)
    {
        return $this->update_("user_feature_package",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_feature_package($where)
    {
        return $this->delete_("user_feature_package",$where);
    }
    public function update_user_feature_package_status($id)
    {
        $sql="UPDATE `user_feature_package` SET `i_active` = 1-`i_active` WHERE `id` =  {$id} ";
        return $this->db->query($sql);
    }    
    
    
    public function __destruct(){}
    
}

?>
