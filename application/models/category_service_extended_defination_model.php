<?php
/**
* category_service_extended_defination Model
* 
* ON 7Oct2013, 
*  A "country_id" field/column is added into "category_service_extended_defination" table. 
* So that USA and India have different categories. 
* @see, controllers/admin/category_service_extended_definition.php 
* @see, controllers/search_engine.php
* @see, controllers/service_profile.php
* 
* Extended columns at table "user_service_extended"
* "s_specialization_ids", s_qualification_ids, d_experience, 
* s_classes_ids, s_medium_ids, d_tution_fee, s_tution_mode_ids, 
* s_other_subject_ids, d_rate, s_employment_type_id, 
* s_availability_ids, s_tools_ids, s_designation_ids
* 
* new columns added in table "user_service_extended" , 
* "s_availability_ids" , "s_tools_ids", "s_designation_ids"
* 
* Also new enum value "available" , "tool" , "classes" , "subject"
* added at "option" and "user_suggestion" tables
* 
*/

class Category_service_extended_defination_model extends MY_Model
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
    public function category_service_extended_defination_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("category_service_extended_defination",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_category_service_extended_defination($values)
    {
        return $this->add_("category_service_extended_defination",$values);
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
    public function update_category_service_extended_definition($values,$where)
    {
        return $this->update_("category_service_extended_defination",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_category_service_extended_defination($where)
    {
        return $this->delete_("category_service_extended_defination",$where);
    } 
	
	
	
	
	public function get_fields_name($table)
	{
		$data = array();
		$fields = $this->db->list_fields($table);
		foreach($fields as $field)
		{
			$data[] = $field;			
		}
		return $data;
	}   
    
	
	public function count_records($table,$arr_where = array())
	{
		$count = 0;
		if(!empty($arr_where))
		{
			$query = $this->db->get_where($table, $arr_where);
			$count = $query->num_rows();	
		}		 
		return $count; 
	}
    
    public function __destruct(){}
    
}

?>
