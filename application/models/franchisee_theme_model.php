<?php
/**
* Admin Theme Model
* 
*/

class Franchisee_theme_model extends MY_Model
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
    public function franchisee_theme_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        $ftheme= $this->load_("franchisee_theme",$condition,$limit,$offset,$order_by);
        if(!empty($ftheme))
        {
            $this->load->model("theme_model");
            if(is_array($ftheme))///multi rows fetched
            {
                foreach($ftheme as $ft)
                {
                    $temp=$this->theme_model->load_theme($ft->theme_id);
                    $ft->theme_settings=unserialize($r->s_theme_settings);
                }
            }
            else///single record
            {
                $temp=$this->theme_model->load_theme($ftheme->theme_id);
                $ft->theme_settings=unserialize($r->s_theme_settings);
            }
        }
        
        return $ftheme;
    }
    
    /**
    * Insert  
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_franchisee_theme($values)
    {
        return $this->add_("franchisee_theme",$values);
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
    public function update_franchisee_theme($values,$where)
    {
        /*$ret=$this->update_("franchisee_theme",$values,$where);
        pr($this->db->last_query(),1);
        return $ret;*/
        
        return $this->update_("franchisee_theme",$values,$where);
        
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_franchisee_theme($where)
    {
        return $this->delete_("franchisee_theme",$where);
    }    
    
    
    public function __destruct(){}
    
}

?>
