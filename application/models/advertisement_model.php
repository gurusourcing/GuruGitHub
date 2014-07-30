<?php
/**
* country Model
* 
*/

class Advertisement_model extends MY_Model
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
    public function advertisement_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("advertisement",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_advertisement($values)
    {
        return $this->add_("advertisement",$values);
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
    public function update_advertisement($values,$where)
    {
        return $this->update_("advertisement",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_advertisement($where)
    {
        return $this->delete_("advertisement",$where);
    }    
    
    /**
    * selecting the advertisement type for creating dropdownlist
    * 
    */
    
    public function fetch_advertisement_type()
    {
        ////Now return only the enum col 'e_ads_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM advertisement LIKE 'e_ads_type' ";
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
    
    /**
    * chnging the i_active status
    * 
    */
    public function update_advertisement_status($id=null)
    {
        $sql="UPDATE `advertisement` SET `i_active` = 1-`i_active` WHERE `id` =  {$id} ";
        return $this->db->query($sql);
    }
    
    public function __destruct(){}
    
}

?>
