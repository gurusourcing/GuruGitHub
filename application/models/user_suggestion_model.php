<?php
/**
* User_suggestion Model
* 
* on 13Dec13, as per client request
* If user chooses a particular category then that category 
* specific advanced filters are displayed below common filters. 
* These filters are related to the service that user provide as 
* expert/guru under a particular category. Example : When search 
* with ( dentist ) In the Specialization boxes  auto suggest  will 
* show the specialization under that particular category. And will 
* check all other right panel boxes with proper options.
* >>a new column "cat_id" added in db "option","user_suggestion" table. 
*/

class User_suggestion_model extends MY_Model
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
    public function user_suggestion_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("user_suggestion",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_suggestion($values)
    {
        return $this->add_("user_suggestion",$values);
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
    public function update_user_suggestion($values,$where)
    {
        return $this->update_("user_suggestion",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_suggestion($where)
    {
        return $this->delete_("user_suggestion",$where);
    }  
	
	
	
	public function check_duplicate_suggestion($params)
	{
		if(!empty($params))
		{
			$sql    =   "SELECT * FROM user_suggestion WHERE BINARY s_suggestion='".$params['s_suggestion']."' AND e_type='".$params['e_type']."' ";
            $query  =   $this->db->query($sql);
            $rslt   =   $query->row_array();	
			
			return count($rslt)?count($rslt):0;		
		}	
	}  
    
    public function approve_user_suggestion($id)
    {
        //$this->db->join("option o","o.s_suggestion=s.s_suggestion AND o.e_type=s.e_type");//left join
		$this->db->join("option o","o.s_suggestion=s.s_suggestion AND o.e_type=s.e_type AND o.cat_id=s.cat_id ");//left join
        $this->db->select('o.*');

       /* $rs=$this->db
        ->get_where("user_suggestion s",
                    array("s.id"=>$id)
        )
        ->row();*/
        $this->db->where('s.id',$id);
        $this->db->from("user_suggestion s");
        $rs=$this->db->count_all_results();
                    
        if($rs)
           return false;
        else
        {
            //$this->db->select('s_suggestion, e_type');
			$this->db->select('s_suggestion, e_type, cat_id');
            $query = $this->db->get_where('user_suggestion',array('id'=>$id))->row();
			
            $this->db->insert('option', $query);
            $ret=$this->db->insert_id();
            
            if($ret)
                $this->db->delete('user_suggestion', array('id' => $id)); 
            return $ret; 
        }
        
    }
    
    public function fetch_user_suggestion_type()
    {
         ////Now return only the enum col 'e_doc_type' identifiers////
            $sql    =   "SHOW COLUMNS FROM user_suggestion LIKE 'e_type' ";
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
    
    public function __destruct(){}
    
}

?>
