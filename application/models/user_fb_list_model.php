<?php
/**
* user_fb_list Model
* 
* As per client request, 17Aug13
*  Remove all SP and convert it into php functions.
* 
* 
*/

class User_fb_list_model extends MY_Model
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
    public function user_fb_list_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        return $this->load_("user_fb_list",$condition,$limit,$offset,$order_by);
    }
    
    /**
    * Insert 
    * @param array $values=>array("admin_type_id"=>3,"s_admin_name"=>"test user"...);
    *        object $values=new stdClass();
    *               $values->admin_type_id=3; $values->s_admin_name="test user"...
    */
    public function add_user_fb_list($values)
    {
        return $this->add_("user_fb_list",$values);
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
    public function update_user_fb_list($values,$where)
    {
        return $this->update_("user_fb_list",$values,$where);
    }    
    
    /**
    * Delete 
    * @param mixed $where,  array('id' => $id, 'title' => $title, 'status' => $status); OR 
    *                       array('name !=' => $name, 'id <' => $id, 'date >' => $date); OR 
    *                       "name='Joe' AND status='boss' OR status='active'";
    */
    public function delete_user_fb_list($where)
    {
        return $this->delete_("user_fb_list",$where);
    }   
	
	
	/**
    * @param mixed $user_id must be integer value 
    * @param mixed $start, $limit (default values as -1,-1)
	* 
    * @s_query,@s_ids_csv,@s_label is OUT parameters of stored procedure
    * @return stdObj of users db table.
    * 
    * As per client request, 17Aug13
    *  Remove all SP and convert it into php functions.
    */ 
    public function get_all_friend($user_id,$start=-1,$limit=-1)
    {
        $rs=new stdClass();
		
		$i_user_id	=	intval($user_id);
		/*$sql		=	"call sp_get_all_friend($i_user_id,$start,$limit,@s_query,@s_ids_csv,@s_label,@parent_ids)";
		$this->db->query($sql);
		
		$sql	=	'SELECT @s_query AS s_query,@s_ids_csv AS s_ids_csv,@s_label AS s_label';
		$query	=	$this->db->query($sql);
		$row	=	$query->row_array();
		$ids	=	($row['s_ids_csv'] == '' ? '0' : $row['s_ids_csv']);		
		$ids 	= 	str_getcsv($ids,",");
		
		$s_label	=	($row['s_label'] == '' ? '0' : $row['s_label']);	
		$s_label 	= 	str_getcsv($s_label,",");*/
        
        //called by ref
        $this->sp_get_all_friend($i_user_id, $start, $limit, $s_query, $s_ids_csv,$s_label,$parent_ids);
        $ids    =    ($s_ids_csv == '' ? '0' : $s_ids_csv);        
        $ids     =     str_getcsv($ids,",");
        
        $s_label  =  ($s_label == '' ? '0' : $s_label);    
        $s_label  =   str_getcsv($s_label,",");        
        
		//pr($ids);
		if(!empty($ids))
	        $this->db->where_in($ids);
	    $this->db->from("users u");
		$rs=$this->db->get()->result();
		
		if(!empty($rs))
		{
			for($i=0;$i<count($rs);$i++)
			{
				$rs[$i]->label = $s_label[$i];
			}
		}
		
		return $rs;
    }
	
    /**
    * Replica of sp_get_all_friend
    */
    private function sp_get_all_friend($i_search_user_id, $i_start, $i_limit, &$s_qry, &$s_csv,&$lbl,&$parent_ids)
    {
        if ( $i_limit <  0 || $i_start < 0 ) 
          $s_limit_part ='';
        else
          $s_limit_part=' LIMIT '.i_start.','.i_limit;
          
        
        $qry = 'SELECT uid_friend AS uid, 1 AS fb_friend_label, uid AS parent_id
                    FROM user_fb_list
                    WHERE uid='.$i_search_user_id .$s_limit_part;

        $s_qry = $qry;

        $qry1    =    'SELECT GROUP_CONCAT(friend.uid SEPARATOR ",") as csv, 
                            GROUP_CONCAT(friend.fb_friend_label SEPARATOR ",") as label ,
                            GROUP_CONCAT(friend.parent_id SEPARATOR ",") as parent_id  
                            FROM('.$qry.') AS friend;';        
        
        $query  =    $this->db->query($qry1);
        $row    =    $query->row_array();
        //pr($row);
        
        if(!empty($row))
        {
             $s_csv = trim($row["csv"]);
             $lbl = trim($row["label"]);
             $parent_ids = trim($row["parent_id"]);            
        }
        return FALSE;  
    }
    
    
	/**
    * @param mixed $user_id must be integer value 
    * @param mixed $start, $limit (N.B - default values as -1,-1)
	* 
    * @s_query,@s_ids_csv,@s_label is OUT parameters of stored procedure
    * @return stdObj of users db table.
    */ 
	
	public function get_all_friend_of_friend($user_id,$start=-1,$limit=-1)
    {
        $rs=new stdClass();
		
		$i_user_id	=	intval($user_id);
		/*$sql		=	"call sp_get_all_friend_of_friend($i_user_id,$start,$limit,@s_query,@s_ids_csv,@s_label,@parent_ids)";
		$this->db->query($sql);
		
		$sql	=	'SELECT @s_query AS s_query,@s_ids_csv AS s_ids_csv,@s_label AS s_label';
		$query	=	$this->db->query($sql);
		$row	=	$query->row_array();
		$ids	=	($row['s_ids_csv'] == '' ? '0' : $row['s_ids_csv']);		
		$ids 	= 	str_getcsv($ids,",");
		
		$s_label	=	($row['s_label'] == '' ? '0' : $row['s_label']);	
		$s_label 	= 	str_getcsv($s_label,",");
        pr("@SP");
        pr(array($ids,$s_label));*/
        
        //called by ref
        $this->sp_get_all_friend_of_friend($i_user_id, $start, $limit, $s_query, $s_ids_csv,$s_label,$parent_ids);
        $ids    =    ($s_ids_csv == '' ? '0' : $s_ids_csv);        
        $ids     =     str_getcsv($ids,",");
        
        $s_label  =  ($s_label == '' ? '0' : $s_label);    
        $s_label  =   str_getcsv($s_label,",");             
		
		if(!empty($ids))
	        $this->db->where_in($ids);
	    $this->db->from("users u");
		$rs=$this->db->get()->result();
		
		if(!empty($rs))
		{
			for($i=0;$i<count($rs);$i++)
			{
				$rs[$i]->label = $s_label[$i];
			}
		}
		
		return $rs;
    } 
	
    /**
    * Replica of sp_get_all_friend_of_friend
    */
    private function sp_get_all_friend_of_friend($i_search_user_id, $i_start, $i_limit, &$s_qry, &$s_csv,&$lbl,&$parent_ids)
    {
        if ( $i_limit <  0 || $i_start < 0 ) 
          $s_limit_part ='';
        else
          $s_limit_part=' LIMIT '.i_start.','.i_limit;
          
         //call br ref 
        $this->sp_get_all_friend($i_search_user_id,'-1','-1',$q,$x,$l,$pid);  
        
        $qry_part='FROM (
                            SELECT uid_friend ,2 AS fb_friend_label, uid AS parent_id
                            FROM user_fb_list
                            WHERE uid IN ('.(empty($x)?0:$x).')
                                AND uid_friend NOT IN ('.(empty($x)?0:$x).')
                        ) AS t_u
                        WHERE t_u.uid_friend <> '.$i_search_user_id
                        .$s_limit_part ;
                        
        $qry = 'SELECT uid_friend AS uid, fb_friend_label, parent_id 
                    '.$qry_part;

        $s_qry = $qry;

        $qry1    =    'SELECT GROUP_CONCAT(ff_rs.uid_friend SEPARATOR ",") as csv, 
                        GROUP_CONCAT(ff_rs.fb_friend_label SEPARATOR ",") as label,
                        GROUP_CONCAT(ff_rs.parent_id SEPARATOR ",") as parent_id 
                        FROM (SELECT *'.$qry_part.') AS ff_rs ';        
        
        $query  =    $this->db->query($qry1);
        $row    =    $query->row_array();
        //pr($row);
        if(!empty($row))
        {
             $s_csv = trim($row["csv"]);
             $lbl = trim($row["label"]);
             $parent_ids = trim($row["parent_id"]);            
        }
        return FALSE;  
    }    
    
    
	/**
    * @param mixed $user_id must be integer value 
    * @param mixed $start, $limit (N.B - default values as -1,-1)
	* 
    * @s_query,@s_ids_csv,@s_label is OUT parameters of stored procedure
    * @return stdObj of users db table.
    */ 
	
	public function get_all_friend_of_friend_of_friend($user_id,$start=-1,$limit=-1)
    {
       
        $rs=new stdClass();
		
		$i_user_id	=	intval($user_id);
		/*$sql		=	"call sp_get_all_friend_of_friend_of_friend($i_user_id,$start,$limit,@s_query,@s_ids_csv,@s_label,@parent_ids)";
		$this->db->query($sql);
		
		$sql	=	'SELECT @s_query AS s_query,@s_ids_csv AS s_ids_csv,@s_label AS s_label';
		$query	=	$this->db->query($sql);
		$row	=	$query->row_array();
		$ids	=	($row['s_ids_csv'] == '' ? '0' : $row['s_ids_csv']);		
		$ids 	= 	str_getcsv($ids,",");
		
		$s_label	=	($row['s_label'] == '' ? '0' : $row['s_label']);	
		$s_label 	= 	str_getcsv($s_label,",");*/
        
        
        //called by ref
        $this->sp_get_all_friend_of_friend_of_friend($i_user_id, $start, $limit, $s_query, $s_ids_csv,$s_label,$parent_ids);
        $ids    =    ($s_ids_csv == '' ? '0' : $s_ids_csv);        
        $ids     =     str_getcsv($ids,",");
        
        $s_label  =  ($s_label == '' ? '0' : $s_label);    
        $s_label  =   str_getcsv($s_label,",");          
		
		if(!empty($ids))
	        $this->db->where_in($ids);
	    $this->db->from("users u");
		$rs=$this->db->get()->result();
		
		if(!empty($rs))
		{
			for($i=0;$i<count($rs);$i++)
			{
				$rs[$i]->label = $s_label[$i];
			}
		}
		
		return $rs;
    } 
	
    /**
    * Replica of sp_get_all_friend_of_friend_of_friend
    */
    private function sp_get_all_friend_of_friend_of_friend($i_search_user_id, $i_start, $i_limit, 
        &$s_qry, &$s_csv,&$lbl,&$parent_ids)
    {
        if ( $i_limit <  0 || $i_start < 0 ) 
          $s_limit_part ='';
        else
          $s_limit_part=' LIMIT '.i_start.','.i_limit;
          
         //call br ref 
        $this->sp_get_all_friend($i_search_user_id,'-1','-1',$q1,$x1,$l1,$pid1);
        $this->sp_get_all_friend_of_friend($i_search_user_id,'-1','-1',$q,$x,$l,$pid);  
        
        $qry_part='FROM
                    (
                        SELECT uid_friend ,3 AS fb_friend_label,uid AS parent_id
                        FROM user_fb_list
                        WHERE uid IN ('.(empty($x)?0:$x).')
                            AND uid_friend NOT IN ('.(empty($x)?0:$x).')
                    ) AS t_u2
                    WHERE t_u2.uid_friend NOT IN ('.(empty($x1)?0:$x1).')'
                    .$s_limit_part ;
                        
        $qry = 'SELECT uid_friend AS uid, fb_friend_label, parent_id 
                    '.$qry_part;

        $s_qry = $qry;

        $qry1    =    'SELECT GROUP_CONCAT(ff_rs2.uid_friend SEPARATOR ",") as csv, 
                        GROUP_CONCAT(ff_rs2.fb_friend_label SEPARATOR ",") as label,
                        GROUP_CONCAT(ff_rs2.parent_id SEPARATOR ",") as parent_id
                        FROM (SELECT *'.$qry_part.') AS ff_rs2';        
        
        $query  =    $this->db->query($qry1);
        $row    =    $query->row_array();
        //pr($row);
        if(!empty($row))
        {
             $s_csv = trim($row["csv"]);
             $lbl = trim($row["label"]);
             $parent_ids = trim($row["parent_id"]);            
        }
        return FALSE;  
    }     
    
    
	/**
    * @param mixed $user_id must be integer value 
    * @param mixed $start, $limit (N.B - default values as -1,-1)
	* @param $unique, true=>friend id will not repeated, 
    *                 false=>friend id may repeat in case common friends  exists
    *                  @see find_connected_chain_within_friends() 
    * @s_query,@s_ids_csv,@s_label is OUT parameters of stored procedure
    * @return stdObj of users db table.
    */ 
	
	public function get_all_friend_and_their_friend($user_id,$start=-1,$limit=-1,$unique=true)
    {
        $rs=new stdClass();
		
		$i_user_id	=	intval($user_id);
		/*$sql		=	"call sp_get_all_friend_and_their_friend($i_user_id,$start,$limit,@s_query,@s_ids_csv,@s_label,@parent_ids)";
		$this->db->query($sql);
		
		$sql	=	'SELECT @s_query AS s_query,@s_ids_csv AS s_ids_csv,@s_label AS s_label,@parent_ids AS parent_ids';
		$query	=	$this->db->query($sql);
		$row	=	$query->row_array();
		$ids	=	($row['s_ids_csv'] == '' ? '0' : $row['s_ids_csv']);		
		$ids 	= 	str_getcsv($ids,",");
		
		$s_label	=	($row['s_label'] == '' ? '0' : $row['s_label']);	
		$s_label 	= 	str_getcsv($s_label,",");
		
		$parent_ids	=	($row['parent_ids'] == '' ? '0' : $row['parent_ids']);	
		$parent_ids 	= 	str_getcsv($parent_ids,",");
        */
        
        //called by ref
        $this->sp_get_all_friend_and_their_friend($i_user_id, $start, $limit, $s_query, $s_ids_csv,$s_label,$parent_ids);
        $ids    =    ($s_ids_csv == '' ? '0' : $s_ids_csv);        
        $ids     =     str_getcsv($ids,",");
        
        $s_label  =  ($s_label == '' ? '0' : $s_label);    
        $s_label  =   str_getcsv($s_label,","); 
        
        $parent_ids    =    ($parent_ids == '' ? '0' : $parent_ids);    
        $parent_ids     =     str_getcsv($parent_ids,",");            
        
        //pr(array($parent_ids,$ids,$s_label),1);  // debug   
		
		if(!empty($ids))
	        $this->db->where_in('id',$ids);
	    $this->db->from("users u");   
		$rs=$this->db->get()->result();
                
        //return false;
		//pr($rs,1);
        
		if(!empty($rs))
		{
            $pks = array();
			foreach($rs as $i=>$val)
			{
				$k = array_search($val->id,$ids);
				
				$val->label = $s_label[$k];
				$val->parent_id = $parent_ids[$k];
                $pks[] = $val->id;
			}
                     
            if(!$unique)
            {
                
				$ret = array();
                /* replacing $rs index with respective pk */
                $temp =  array_combine($pks,$rs);	
                foreach($ids as $ik=>$ival)
                {  
                    $t = new stdClass();
					
					// new patch if the array key counts does not be equal
					if(empty($temp[$ival]))	
					{
						$temp[$ival] = new stdClass();
					}	
					/*if(!empty($temp[$ival]))	
					{*/
						$t = clone $temp[$ival]; 
						$t->parent_id    =   $parent_ids[$ik]; 
						$ret["records"][$ik] =  $t;  
						$ret["ids"][$t->id]    = $ik ; // key stores pk and value is index of  $ret["records"]	
					/*}*/
                } 
               //pr($ret["parent_ids"]);
               //pr($ret,1);			  
                return $ret;
            }
		}
		
		return $rs;
		
    }
     
     
    /**
    * Replica of sp_get_all_friend_and_their_friend
    */
    private function sp_get_all_friend_and_their_friend($i_search_user_id, $i_start, $i_limit, 
        &$s_qry, &$s_csv,&$lbl,&$parent_ids)
    {
        if ( $i_limit <  0 || $i_start < 0 ) 
          $s_limit_part ='';
        else
          $s_limit_part=' LIMIT '.i_start.','.i_limit;
          
         
		 //call br ref 
        $this->sp_get_all_friend($i_search_user_id,'-1','-1',$q,$x,$l,$pid);
        $this->sp_get_all_friend_of_friend($i_search_user_id,'-1','-1',$q1,$x1,$l1,$pid1);  
        $this->sp_get_all_friend_of_friend_of_friend($i_search_user_id,'-1','-1',$q2,$x2,$l2,$pid2);  
        
		//return false; // debug
		
        $qry_part=' FROM
                    (
                        ('.$q.')
                        UNION
                        ('.$q1.')
                        UNION
                        ('.$q2.')
                    ) AS ffatf '.$s_limit_part ;
                        
        $qry = 'SELECT * '.$qry_part;

        $s_qry = $qry;

        $qry1    =    'SELECT GROUP_CONCAT(ffatf_rs.uid SEPARATOR ",") as csv, 
                        GROUP_CONCAT(ffatf_rs.fb_friend_label SEPARATOR ",") as label,
                        GROUP_CONCAT(ffatf_rs.parent_id SEPARATOR ",") as parent_id
                        FROM (SELECT * '.$qry_part.' ) AS ffatf_rs';        
        
        $query  =    $this->db->query($qry1);
        $row    =    $query->row_array();
        //pr(array($row,$qry1));exit;
		//pr($row,1);
        if(!empty($row))
        {
			//pr($row,1);
             $s_csv = trim($row["csv"]);
             $lbl = trim($row["label"]);
             $parent_ids = trim($row["parent_id"]);       
        }
        return FALSE;  
    }          
    
    public function __destruct(){}
    
}

?>
