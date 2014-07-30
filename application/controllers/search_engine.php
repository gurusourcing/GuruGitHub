<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* search engine
*  
* Common search fields from all pages////
*  params["global_country_id"]
   params["search_type"]
   params["search_type_value"]
   params["location_type"]
   params["location_type_value"]
   params["distance"]
   params["keep_filter"]
* 
* 
* examples:
*   filter_search_classes:XII,M.tech,B.Tech;
*   filter_search_specialization:Ear, nose, throats,cardiac,neurologist;
*   filter_search_subjects:Mathematics,Chemistry,Physics,English;
*   s_name:Test,Employee 1;  to search service provider name use 's_name'
* 
* ON 18Oct2013, 
* Extended Services are now fetched country specific.
* 
* Extended columns at table "user_service_extended"
* "s_specialization_ids", s_qualification_ids, d_experience, 
* s_classes_ids, s_medium_ids, d_tution_fee, s_tution_mode_ids, 
* s_other_subject_ids, d_rate, s_employment_type_id, 
* s_availability_ids, s_tools_ids, s_designation_ids
* 
* on 13Dec13, as per client request
* If user chooses a particular category then that category 
* specific advanced filters are displayed below common filters. 
* These filters are related to the service that user provide as 
* expert/guru under a particular category. Example : When search 
* with ( dentist ) In the Specialization boxes  auto suggest  will 
* show the specialization under that particular category. And will 
* check all other right panel boxes with proper options.
* >>a new column "cat_id" added in db "option" table. 
*/

class Search_engine extends MY_Controller {
    
    public function __construct()
    {   
        parent::__construct();
    }
    
    public function index()
    {
        $this->data['page_title'] = 'Search';
        
        /**
        * Gathering and rewriting the posted values
        * the fields cat_id, uid,city_id,zip_id from get_post are encrypted values
        */
        $posted=$this->input->post();///collect all requests 
        //pr($posted);
        $posted["global_country_id"]=get_globalCountry();
        $posted["cat_id"]=intval(decrypt(@$posted["search_cat_id"]));
        $posted["uid"]=intval(decrypt(@$posted["search_uid"]));
        $posted["city_id"]=intval(decrypt(@$posted["search_city_id"]));
        $posted["zip_id"]=intval(decrypt(@$posted["search_zip_id"]));
        //pr($posted);
		
		
		
        //$this->data["posted"]=$posted;
        
        ////Fetching category service extended fields////
        $this->load->model('category_service_extended_defination_model','service_extended_def_model');
        $temp=$this->service_extended_def_model
                             ->category_service_extended_defination_load(
                                array("country_id"=>$posted["global_country_id"],"cat_id"=>$posted["cat_id"],'i_active'=>1),
                                null,
                                null,
                                "s_search_page_order ASC"
                             );
        $service_extended_def=array();
        if(!empty($temp))
        {
            foreach($temp as $k=>$extended)
            {
                $service_extended_def[$extended->s_column_name]=$extended;
            }
        }
        //$this->data['service_extended_def']=$service_extended_def;
        //pr($this->data['service_extended_def']);
        ////end Fetching category service extended fields////
		//pr($service_extended_def);
		/************ to get all default values for first time search 14 feb 2014 **************
		* modified on 25 feb 2014 for keep filter to work
		*/
		
			
		if(empty($posted["filter_search_fb_circle"]))
			$posted["filter_search_fb_circle"] = array('None','1st Circle','2nd Circle','3rd Circle');
		
		if($posted["keep_filter"]!=1)
		{
			/*$this->session->unset_userdata("arr_session");
			$this->session->unset_userdata("arr_session_posted");*/
		}
		else
		{
			$arr_session_data    	=    $this->session->userdata("arr_session");
			$arr_session_posted    	=    $this->session->userdata("arr_session_posted");
		}
		
		$srch_cat = decrypt($posted["search_cat_id"]);
		$this->load->model('option_model');
		
		if(empty($posted["filter_search_services"]) && (empty($arr_session_posted["filter_search_services"])))
			$posted["filter_search_services"] = array('Individual','Company');
		else
		{
			$posted["filter_search_services"] = $posted["filter_search_services"]?$posted["filter_search_services"]:$arr_session_posted["filter_search_services"];
		}
			
		if(empty($posted["filter_search_gender"]) && (empty($arr_session_posted["filter_search_gender"])))
			$posted["filter_search_gender"] = array('Male','Female');
		else
		{
			$posted["filter_search_gender"] = $posted["filter_search_gender"]?$posted["filter_search_gender"]:$arr_session_posted["filter_search_gender"];
		}
		
		if(!empty($arr_session_posted["filter_search_fb_circle"]))
		{
			$posted["filter_search_fb_circle"] = $posted["filter_search_fb_circle"]?$posted["filter_search_fb_circle"]:$arr_session_posted["filter_search_fb_circle"];
		}
			
		
		if(!empty($service_extended_def["s_specialization_ids"]) || (!empty($arr_session_data["s_specialization_ids"])))
		//if(!empty($service_extended_def["s_specialization_ids"]))
		{	
			$service_extended_def["s_specialization_ids"] = $service_extended_def["s_specialization_ids"]?$service_extended_def["s_specialization_ids"]:$arr_session_data["s_specialization_ids"];	
			$posted["filter_search_specialization"] = $posted["filter_search_specialization"]?$posted["filter_search_specialization"]:$arr_session_posted["filter_search_specialization"];
				
			if(empty($posted["filter_search_specialization"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='specilization' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				$posted["filter_search_specialization"] = $ajx_ret;
			}
		}	
		
		if(!empty($service_extended_def["d_experience"]) || (!empty($arr_session_data["d_experience"])))
		//if(!empty($service_extended_def["d_experience"]))
		{
			
			$service_extended_def["d_experience"] = $service_extended_def["d_experience"]?$service_extended_def["d_experience"]:$arr_session_data["d_experience"];
			$posted["filter_search_experience"] = $posted["filter_search_experience"]?$posted["filter_search_experience"]:$arr_session_posted["filter_search_experience"];
			
			if(empty($posted["filter_search_experience"]))
				$posted["filter_search_experience"] = array( "0 to 2 years", "2 to 4 years","4 to 6 years"
															,"6 to 8 years","8 to 10 years","more than 10 years"
														);
		}		
		
		if(!empty($service_extended_def["s_other_subject_ids"]) || (!empty($arr_session_data["s_other_subject_ids"])))
		{	
			$service_extended_def["s_other_subject_ids"] = $service_extended_def["s_other_subject_ids"]?$service_extended_def["s_other_subject_ids"]:$arr_session_data["s_other_subject_ids"];	
			$posted["filter_search_subjects"] = $posted["filter_search_subjects"]?$posted["filter_search_subjects"]:$arr_session_posted["filter_search_subjects"];
			
			if(empty($posted["filter_search_subjects"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='subject' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				
				$posted["filter_search_subjects"] = $ajx_ret;
			}		
		}
		
		if(!empty($service_extended_def["s_classes_ids"]) || (!empty($arr_session_data["s_classes_ids"])))
		{	
			$service_extended_def["s_classes_ids"] = $service_extended_def["s_classes_ids"]?$service_extended_def["s_classes_ids"]:$arr_session_data["s_classes_ids"];	
			$posted["filter_search_classes"] = $posted["filter_search_classes"]?$posted["filter_search_classes"]:$arr_session_posted["filter_search_classes"];
			
			if(empty($posted["filter_search_classes"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='classes' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				
				$posted["filter_search_classes"] = $ajx_ret;
			}	
		}
		
		if(!empty($service_extended_def["s_medium_ids"]) || (!empty($arr_session_data["s_medium_ids"])))
		{	
			$service_extended_def["s_medium_ids"] = $service_extended_def["s_medium_ids"]?$service_extended_def["s_medium_ids"]:$arr_session_data["s_medium_ids"];	
			$posted["filter_search_medium"] = $posted["filter_search_medium"]?$posted["filter_search_medium"]:$arr_session_posted["filter_search_medium"];
			
			if(empty($posted["filter_search_medium"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='language' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				
				$posted["filter_search_medium"] = $ajx_ret;
			}
		}
		
		if(!empty($service_extended_def["s_tution_mode_ids"]) || (!empty($arr_session_data["s_tution_mode_ids"])))
		{
			$service_extended_def["s_tution_mode_ids"] = $service_extended_def["s_tution_mode_ids"]?$service_extended_def["s_tution_mode_ids"]:$arr_session_data["s_tution_mode_ids"];	
			$posted["filter_search_tution_mode"] = $posted["filter_search_tution_mode"]?$posted["filter_search_tution_mode"]:$arr_session_posted["filter_search_tution_mode"];
				
			if(empty($posted["filter_search_tution_mode"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='tution_mode' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				
				$posted["filter_search_tution_mode"] = $ajx_ret;
			}
		}
		
		if(!empty($service_extended_def["d_rate"]) || (!empty($arr_session_data["d_rate"])))
		{
			$service_extended_def["d_rate"] = $service_extended_def["d_rate"]?$service_extended_def["d_rate"]:$arr_session_data["d_rate"];	
			$posted["filter_search_rate"] = $posted["filter_search_rate"]?$posted["filter_search_rate"]:$arr_session_posted["filter_search_rate"];
					
			if(empty($posted["filter_search_rate"]))
				$posted["filter_search_rate"] = array( "1 to 100", "101 to 200","201 to 300","301 to 400","401 to 500",
												"501 to 600","601 to 700","701 to 800"
												);
		}
		
		if(!empty($service_extended_def["s_availability_ids"]) || (!empty($arr_session_data["s_availability_ids"])))
		{
			$service_extended_def["s_availability_ids"] = $service_extended_def["s_availability_ids"]?$service_extended_def["s_availability_ids"]:$arr_session_data["s_availability_ids"];	
			$posted["filter_search_availability"] = $posted["filter_search_availability"]?$posted["filter_search_availability"]:$arr_session_posted["filter_search_availability"];
					
			if(empty($posted["filter_search_availability"]))
			{
				$ajx_ret = array();
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='availability' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						//for options category mapping, there may be same option for all category
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;                        
						}
					}
				}
				
				$posted["filter_search_availability"] = $ajx_ret;
			}			
		}		
		
		if(!empty($service_extended_def["s_qualification_ids"]) || (!empty($arr_session_data["s_qualification_ids"])))
		{
			$service_extended_def["s_qualification_ids"] = $service_extended_def["s_qualification_ids"]?$service_extended_def["s_qualification_ids"]:$arr_session_data["s_qualification_ids"];	
			$posted["filter_search_qualification"] = $posted["filter_search_qualification"]?$posted["filter_search_qualification"]:$arr_session_posted["filter_search_qualification"];
			
			if(empty($posted["filter_search_qualification"]))
			{
				$ajx_ret = array();			
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='degree' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;						
						}
					}
				}				
				$posted["filter_search_qualification"] = $ajx_ret;
			}
		}	
		
		if(!empty($service_extended_def["s_tools_ids"]) || (!empty($arr_session_data["s_tools_ids"])))
		{
			$service_extended_def["s_tools_ids"] = $service_extended_def["s_tools_ids"]?$service_extended_def["s_tools_ids"]:$arr_session_data["s_tools_ids"];	
			$posted["filter_search_tools"] = $posted["filter_search_tools"]?$posted["filter_search_tools"]:$arr_session_posted["filter_search_tools"];
			
			if(empty($posted["filter_search_tools"]))
			{
				$ajx_ret = array();			
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='tools' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;						
						}
					}
				}				
				$posted["filter_search_tools"] = $ajx_ret;
			}
		}
		
		if(!empty($service_extended_def["s_designation_ids"]) || (!empty($arr_session_data["s_designation_ids"])))
		{
			$service_extended_def["s_designation_ids"] = $service_extended_def["s_designation_ids"]?$service_extended_def["s_designation_ids"]:$arr_session_data["s_designation_ids"];	
			$posted["filter_search_designation"] = $posted["filter_search_designation"]?$posted["filter_search_designation"]:$arr_session_posted["filter_search_designation"];
			
			if(empty($posted["filter_search_designation"]))
			{
				$ajx_ret = array();			
				$condition = '';
				if(!empty($posted["search_cat_id"]))
					$condition="cat_id ='".$srch_cat."' AND ";
				
				$condition.="e_type ='designation' ";
				$ret=$this->option_model->option_load($condition,NULL,0,"s_suggestion ASC");
				
				if(!empty($ret))
				{
					$unique=array();
					foreach($ret as $k=>$v)
					{
						if(!in_array($v->s_suggestion,$unique))
						{
							$ajx_ret[]=$v->s_suggestion;						
						}
					}
				}				
				$posted["filter_search_designation"] = $ajx_ret;
			}
		}	
			
		$prev_sess = $this->session->userdata("arr_session");
		//if($posted["keep_filter"]==1 && empty($prev_sess))
		if($posted["keep_filter"]==1)
		{
			$arr_session = array();
			$arr_service_extended_def = $service_extended_def;
			$this->session->set_userdata("arr_session",$arr_service_extended_def);
			$this->session->set_userdata("arr_session_posted",$posted);
		}
		
		/*********** to get by default values for first time search feb 2014 end *************/
		
		$this->data['service_extended_def']=$service_extended_def;
		$this->data["posted"]=$posted;
		
		$this->session->set_userdata(array('posted_session'=>$posted));
		
        $this->data['search_result']=$this->process_search($posted); 
        $this->render();
    }
    
    /**
    * This function process the search results
    * From sphinx or caches and returns the 
    * array of resultset.
    */
    private function process_search($parm=array())
    {
        /*if(empty($parm))//after debug uncomment this
            return FALSE;*/
        
        $this->load->model("search_model");
        //$ret=$this->search_model->search_service("");
        //pr($parm,1);
        ////Auto Pagination
        //$this->search_model->pager["base_url"]=admin_base_url("search_engine/index");
		$this->search_model->pager["base_url"]=site_url("search_engine/index");
        $this->search_model->pager["uri_segment"]=3;        
        
       /* $ret=$this->search_model->search_load(
             $parm,
             $limit=30,
             $offset=0
        );*/
		
		$ret=$this->search_model->search_load(
             $parm,
             $limit=200,
        	 $this->uri->segment(3,0)
        );
        $this->data['search_result_pager']=$this->search_model->get_pager();
        
        if($this->search_model->pager["total_rows"]>0
            && $this->search_model->pager["total_rows"]< $this->search_model->pager["per_page"]
        )
            $outof=$this->search_model->pager["total_rows"];
        elseif($this->search_model->pager["total_rows"]>0
            && $this->search_model->pager["total_rows"]>$this->search_model->pager["per_page"]
        )
            $outof=$this->search_model->pager["per_page"];
        else
            $outof=0;
            
        //pr($this->search_model->pager);
        //$this->data["pager_heading"]=$outof." out of ".format_plural($this->search_model->pager["total_rows"],"profile");
		$this->data["pager_heading"]="<span class='res_cnt'>".$outof."</span> out of ".format_plural($this->search_model->pager["total_rows"],"profile");
        //pr($this->search_model->pager);
        
        return $ret;
    }
    
}

/* End of file search_engine.php */
/* Location: ./application/controllers/search_engine.php */
