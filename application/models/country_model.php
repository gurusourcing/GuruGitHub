<?php
/**
* country Model
* 
*/

class Country_model extends MY_Model
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
    public function country_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("country",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_country($values)
    {
        return $this->add_("country",$values);
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
    public function update_country($values,$where)
    {
        return $this->update_("country",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_country($where)
    {
        return $this->delete_("country",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
