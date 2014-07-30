<?php
/**
* Admin Theme Model
* 
*/

class Theme_model extends MY_Model
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
    * @return stdObj of theme db table.
    */
    public function theme_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("theme",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_theme($values)
    {
        return $this->add_("theme",$values);
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
    public function update_theme($values,$where)
    {
        return $this->update_("theme",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_theme($where)
    {
        return $this->delete_("theme",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
