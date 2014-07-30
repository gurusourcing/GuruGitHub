<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Fe ajax autocomplete
* 
* CI caching implemented. to fast accessing the autocomplete.
*/



class Autocomplete extends MY_Controller {
    
    
    public function __construct()
    {   
        parent::__construct();
    }
    
    /**
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/service_profile/add_service.tpl.php
    */
    public function ajax_zipCode()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["country_id"] = intval($this->input->get("country_id"));
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-zipCode-".$posted["term"]."-".$posted["country_id"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        
        //$this->load->model("zip_model");
        $this->load->model('zip_location_mapping_model');
        //$condition="z.s_zip LIKE '%".trim($posted["term"])."%' AND z.country_id=".$posted["country_id"];
        $condition="z.country_id='".$posted["country_id"]."' AND z.s_zip LIKE '".trim($posted["term"])."%'";
        
        $ret=$this->zip_location_mapping_model->zip_location_mapping_load($condition,10,0,"z.s_zip ASC");
        if(!empty($ret))
        {
            foreach($ret as $k=>$v)
            {
                $ajx_ret[]=array(
                    "label"=>$v->s_zip,
                    "value"=>$v->s_zip,                
                    "zip_id"=>$v->zip_id,
                    "city_name"=>$v->s_city,
                    "city_id"=>$v->city_id,
                    "state_id"=>$v->state_id,
                    "state_name"=>$v->s_state,
                    "popular_location_id"=>$v->popular_location_id,
                    "popular_location_name"=>$v->s_location,
                );
                
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    } 
    
    /**
    * We need to fetch city id and zip code as well for 
    * autocomplete.
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/service_profile/add_service.tpl.php
    */
    public function ajax_cityName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["country_id"] = intval($this->input->get("country_id"));
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-cityName-".$posted["term"]."-".$posted["country_id"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        
        
        $this->load->model('zip_location_mapping_model');
        //$this->load->model("zip_model");
        //$condition="cy.s_city LIKE '%".trim($posted["term"])."%' AND z.country_id=".$posted["country_id"];
        $condition="z.country_id='".$posted["country_id"]."' AND cy.s_city LIKE '%".trim($posted["term"])."%'";
        
        $ret=$this->zip_location_mapping_model->zip_location_mapping_load($condition,10,0,"cy.s_city ASC");
        if(!empty($ret))
        {
            foreach($ret as $k=>$v)
            {
                $ajx_ret[]=array(
                    "label"=>$v->s_city.", ".$v->s_location,
                    /*"value"=>$v->s_city.", ".$v->s_location,*/
                    "value"=>$v->s_city,
                    "zip_id"=>$v->zip_id,
                    "zip_code"=>$v->s_zip,
                    "city_id"=>$v->city_id,
                    "state_id"=>$v->state_id,
                    "state_name"=>$v->s_state,
                    "popular_location_id"=>$v->popular_location_id,
                    "popular_location_name"=>$v->s_location,
                );
                
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    } 


    /**
    * We need to fetch popular_location_id, city id and zip code as well for 
    * autocomplete.
    * @see, views/fe/company_profile/index.tpl.php
    */
    public function ajax_stateName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["country_id"] = intval($this->input->get("country_id"));
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-stateName-".$posted["term"]."-".$posted["country_id"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        
        
        $this->load->model('state_model');
        //$this->load->model("zip_model");
        $condition="s.s_state LIKE '%".trim($posted["term"])."%' AND s.country_id=".$posted["country_id"];
        $ret=$this->state_model->state_load($condition,10,0,"s.s_state ASC");
        if(!empty($ret))
        {
            foreach($ret as $k=>$v)
            {
                $ajx_ret[]=array(
                    "label"=>$v->s_state,
                    /*"value"=>$v->s_city.", ".$v->s_location,*/
                    "value"=>$v->s_state,
                    "zip_id"=>0,
                    "zip_code"=>"",
                    "city_id"=>0,
                    "city_name"=>"",
                    "state_id"=>$v->id,
                    "popular_location_id"=>0,
                    "popular_location_name"=>"",
                );
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }     
    
    /**
    * We need to fetch languages
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_language()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-language-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }          
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='language' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    } 
    
    
    /**
    * We need to fetch city id and zip code as well for 
    * autocomplete.
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/service_profile/add_service.tpl.php
    */
    public function ajax_locationName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....   
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));        
        /**
        * from add_service.tpl.php we need to 
        * filter on basis of country id
        */
        $posted["country_id"] = intval($this->input->get("country_id"));   
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-locationName-".$posted["term"]."-".$posted["country_id"];
        $ajx_ret=cache_var($cacheVar);
        //pr($ajx_ret,1);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            exit(0);
        }
        
        
        /**
        * Now, cacheing not found 
        * Start searching from db
        */        
        $this->load->model('zip_location_mapping_model');
        //$this->load->model("zip_model");
     
        if(!empty($posted["country_id"]))
            $condition.="z.country_id='".$posted["country_id"]."' AND ";
        
        $condition="cy.s_city LIKE '%".trim($posted["term"])."%' OR pl.s_location LIKE '%".trim($posted["term"])."%'";
                
        $ret=$this->zip_location_mapping_model->zip_location_mapping_load($condition,10,0,"cy.s_city ASC");
        if(!empty($ret))
        {
            foreach($ret as $k=>$v)
            {
                $ajx_ret[]=array(
                    "label"=>$v->s_location.", ".$v->s_city,
                    "value"=>$v->s_location.", ".$v->s_city,
                    /*"value"=>$v->s_city,*/
                    "zip_id"=>$v->zip_id,
                    "zip_code"=>$v->s_zip,
                    "city_name"=>$v->s_city,
                    "city_id"=>$v->city_id,
                    "state_id"=>$v->state_id,
                    "popular_location_id"=>$v->popular_location_id,
                    "popular_location_name"=>$v->s_location,
                );
            }
        }
        
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);
        echo json_encode($ajx_ret);
    }     
    
    
    
    
    /**
    * We need to fetch instituteName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_instituteName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-instituteName-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }         
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='institute' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }      
    
    /**
    * We need to fetch specilizationName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_specilizationName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-specilizationName-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }         
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='specilization' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }    

    /**
    * We need to fetch degreeName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    * 
    */
    public function ajax_degreeName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-degreeName-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }            
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='degree' AND s_suggestion LIKE '%".trim($posted["term"])."%'";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    } 
    
    /**
    * We need to fetch className for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_className()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-className-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }                    
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='classes' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }     
    
    /**
    * send email to share with friends
    *        
    */
    public function ajaxSendEmail()
    {
        $data=$this->input->post();
        
        foreach($data as $k=>$v)
            $this->form_validation->set_rules('email[]','email','valid_email');
       
		if(empty($data['email']))
		{
			echo '</br>Please provide email</br>';
		}
        else if($this->form_validation->run()==false)
        {
            echo validation_errors();   
        }
		else if(count($data['email'])>10)
		{
			echo '</br>Maximum 10 email can be send at a time.</br>';
		}
        else
        {
            //$link=site_url(short_url_code(get_userLoggedIn('id')));
            $link=$data['link'];
            $u=set_user_profile_name(get_userLoggedIn());
            
            $msg=sprintf(message_line('email share message'),$link,$link,$u->s_name);
            $from=get_userLoggedIn('s_email');
            $subject=site_name().', share profile';
            //echo set_user_profile_name(get_userLoggedIn())->s_name;
            foreach($data['email'] as $k=>$v)
            {
               	// sendBulkMail($v,$from,$subject,$msg);  // open when you want to send mail through cron
				
				// changed on feb 2014 , maximum 10 email can be send at a time
			   	$mailData['to'] =  $v;
				$mailData['from'] =  $from;
				$mailData['subject'] =  $subject;
				$mailData['message'] =  $msg;
				sendMail($mailData);
            }
            echo 'success'; 
           
        } 
    }
    
    public function ajaxSetGlobalCountry()
    {
        $data=$this->input->post();
        
        foreach($data as $k=>$v)
            $this->form_validation->set_rules('global_country_id','country','required');
        
        if($this->form_validation->run()==false)
        {
            echo validation_errors();   
        }
        else
        {
            $this->session->set_userdata(array("global_country_id"=>$data["global_country_id"]));
            echo 'success'; 
        } 
    }    
    
    /**
    * We need to fetch service categories Or User Full names for 
    * autocomplete.
    * @see, theme/guru_frontend/main.tpl.php
    * @see, theme/guru_frontend/templates/home--index.tpl.php
    */
    public function ajax_searchTypeValue()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_type"] = trim($this->input->get("search_type"));
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-searchTypeValue-".$posted["term"]."-".$posted["search_type"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        
        $this->load->model('category_model');
        $this->load->model('user_model');
        
        if(trim($posted["search_type"])=="service")///searching service
        {
            $condition="s_category LIKE '%".trim($posted["term"])."%' 
                        OR s_alias_names LIKE '%".trim($posted["term"])."|%' 
                        OR s_alias_names LIKE '%###".trim($posted["term"])."|%'
                        ";
            $ret=$this->category_model->category_load($condition,10,0,"s_category ASC");     
            
            if(!empty($ret))
            {
                foreach($ret as $k=>$v)
                {                   
                    $ajx_ret[]=array(
                        "label"=>$v->s_category,
                        "value"=>$v->s_category,
                        "id"=>encrypt($v->id) ,
                    );
                    ////for alias names///
                    $alias=dbHashSeperateDec($v->s_alias_names);
                    $s_alias_names="";
                    if(!empty($alias))
                    {
                        foreach($alias as $a)
                        {
                            $li=explode("|",$a);
                            //$s_alias_names.=$li[0]."(".get_countryName($li[1]).')<br/>';
                            $ajx_ret[]=array(
                                "label"=>$li[0],
                                "value"=>$li[0],
                                "id"=>encrypt($v->id),
                            );                            
                            
                        }
                    }
                    ////end for alias names///                
                }///end for
            }//end if            
            
            /**
            * on 13Dec13, as per client request,
            * In search  'Type in Keyword / Name / Category' is an auto suggest box. 
            * When user types in, it searches the category list  and all the meaning 
            * full keywords suggested by frontend users (like specialization, degree etc. 
            * from the "option" table of database) to find if there is any match. 
            * If it finds the match it list that as suggestion dropdown. 
            * *The Left panel advanced filter is only visible if category is selected 
            * from autocomplete. Otherwise common filter boxes will be displayed.  
            */
            $this->load->model('option_model');
            $condition="s_suggestion LIKE '".trim($posted["term"])."%'";
            $ret=array();//reinitialize
            $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
            if(!empty($ret))
            {
                foreach($ret as $k=>$v)
                {
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>"",
                    );
                }
            }            
            //Now sorting alphabatically
            usort($ajx_ret, 
                create_function('$a, $b','return strnatcasecmp($a["label"] , $b["label"] );')
            );
                   
        }
        else///searching user
        {
            $condition="d.s_name LIKE '%".trim($posted["term"])."%' AND e_status='active'";
            $ret=$this->user_model->user_load($condition,10,0,"s_name ASC");  
            
            if(!empty($ret))
            {
                foreach($ret as $k=>$v)
                {                   
                    $ajx_ret[]=array(
                        "label"=>$v->s_name,
                        "value"=>$v->s_name,
                        "id"=>encrypt($v->uid),
                    );            
                }///end for
            }//end if                       
        }
                
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }    
    
    /**
    * We need to fetch zip or city for 
    * autocomplete. CI caching implemented.
    * @see, theme/guru_frontend/main.tpl.php
    * @see, theme/guru_frontend/templates/home--index.tpl.php
    */
    public function ajax_locationTypeValue()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["location_type"] = trim($this->input->get("location_type"));
        $posted["country_id"] = intval(get_globalCountry());
        
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-locationTypeValue-".$posted["term"]."-".$posted["location_type"]."-".$posted["country_id"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        
        $this->load->model('zip_location_mapping_model');
        
        if(trim($posted["location_type"])=="zip")///searching service
        {
            //$condition="z.s_zip LIKE '%".trim($posted["term"])."%' AND z.country_id=".$posted["country_id"];
            $condition="z.country_id='".intval($posted["country_id"])."' AND z.s_zip LIKE '".trim($posted["term"])."%'";
            $ret=$this->zip_location_mapping_model->zip_location_mapping_load($condition,10,0,"z.s_zip ASC");
            if(!empty($ret))
            {
                foreach($ret as $k=>$v)
                {
                    $ajx_ret[]=array(
                        "label"=>$v->s_zip.', '.$v->s_location.', '.$v->s_city,
                        "value"=>$v->s_zip,                
                        "zip_id"=>encrypt($v->zip_id) ,
                        "city_name"=>$v->s_city,
                        "city_id"=>encrypt($v->city_id) ,
                        "state_id"=>encrypt($v->state_id) ,
                        "state_name"=>$v->s_state,
                        "popular_location_id"=>encrypt($v->popular_location_id) ,
                        "popular_location_name"=>$v->s_location,
                    );
                    
                }
            }          
                   
        }
        else///searching user
        {
			
			$this->load->model('city_model');
			$cond_city="cy.country_id =".$posted["country_id"]." AND cy.s_city LIKE '%".trim($posted["term"])."%'";
            $ret_city=$this->city_model->city_load($cond_city,4,0,"cy.s_city ASC");
            if(!empty($ret_city))
            {
				$ajx_ret[]=array("label"=>'City',
									"header_row"=>'Y');
                foreach($ret_city as $k=>$v)
                {
                    $ajx_ret[]=array(
                        "label"=>$v->s_city,
                        "value"=>$v->s_city,
						"header_row"=>'N',
                        "city_id"=>encrypt($v->id) ,
                        "state_id"=>encrypt($v->state_id) ,
                        "state_name"=>$v->s_state
                    );
                    
                }
            }///end if 
			
			//$condition="cy.s_city LIKE '%".trim($posted["term"])."%' AND z.country_id=".$posted["country_id"];
            $condition="z.country_id=".$posted["country_id"]." AND cy.s_city LIKE '%".trim($posted["term"])."%'";
            $ret=$this->zip_location_mapping_model->zip_location_mapping_load($condition,8,0,"cy.s_city ASC");
            if(!empty($ret))
            {
				$ajx_ret[]=array("label"=>'Landmarks',
									"header_row"=>'Y');
                foreach($ret as $k=>$v)
                {
                    $ajx_ret[]=array(
                        //"label"=>$v->s_city.", ".$v->s_location,
						"label"=>$v->s_location.", ".$v->s_city,
                        "value"=>$v->s_city,
                        "zip_id"=>encrypt($v->zip_id),
						
						"header_row"=>'N',
                        "zip_code"=>$v->s_zip,
                        "city_id"=>encrypt($v->city_id) ,
                        "state_id"=>encrypt($v->state_id) ,
                        "state_name"=>$v->s_state,
                        "popular_location_id"=>encrypt($v->popular_location_id) ,
                        "popular_location_name"=>$v->s_location
                    );
                    
                }
            }///end if  
			
			
        }
            
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }
    
    /**
    * We need to fetch subjects
    * autocomplete.
    * @see, views/fe/user_profile/index.tpl.php
    * 
    */
    public function ajax_subjects_bkp()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        
        $this->load->model('user_service_extended_model');
        $condition="s_other_subject_ids LIKE '%".trim($posted["term"])."%'";
        $ret=$this->user_service_extended_model->user_service_extended_load($condition,10,0);
        array_walk_recursive($ret,"modifyUnSerialCallback","s_other_subject_ids");
        //pr($ret);
        if(!empty($ret))
        {
            $temp=array();
            foreach($ret as $k=>$v)
            {
                //s_other_subject_ids
                if(!empty($v->s_other_subject_ids))
                    $temp=array_merge($temp,$v->s_other_subject_ids);
            }
            $temp=array_unique($temp);
            sort($temp);
            
            if(!empty($temp))
            {
                foreach($temp as $m=>$n)
                {
                    if(!empty($n))
                        $ajx_ret[]=array(
                        "label"=>$n ,
                        "value"=>$n ,
                        );
                }///end for
            }//end if
            
        }///end if
        
        echo json_encode($ajx_ret);
    }    
            
    /**
    * We need to fetch subjectsName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_subjects()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-subjects-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='subject' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }     
    
    /**
    * We need to fetch mediumName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/user_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_medium()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-medium-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='language' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }         
    
    /**
    * We need to fetch tution_modeName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_tution_mode()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-tution-mode-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='tution_mode' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }      
    
    /**
    * We need to fetch availabilityName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_availability()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-availability-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='availability' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
                
            }
        }
        
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }      
    
    /**
    * We need to fetch toolsName for 
    * autocomplete.
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * 
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_tools()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-tools-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='tools' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";        
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }      
    
    /**
    * We need to fetch designationName for 
    * autocomplete
    * on 13Dec13 as per client request, 
    * search_cat_id is added
    * .
    * @see, views/fe/sevice_profile/index.tpl.php
    * @see, views/fe/search_engine/index.tpl.php
    */
    public function ajax_designation()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        $posted["search_cat_id"] = trim(decrypt($this->input->get("search_cat_id")));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-designation-".$posted["term"];
        if(!empty($posted["search_cat_id"]))
            $cacheVar.="-".$posted["search_cat_id"];
        
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }
        
        $this->load->model('option_model');
        if(!empty($posted["search_cat_id"]))
            $condition="cat_id ='".trim($posted["search_cat_id"])."' AND ";
            
        $condition.="e_type ='designation' AND s_suggestion LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->option_model->option_load($condition,10,0,"s_suggestion ASC");
        if(!empty($ret))
        {
            $unique=array();
            foreach($ret as $k=>$v)
            {
                //for options category mapping, there may be same option for all category
                if(!in_array($v->s_suggestion,$unique))
                {
                    $unique[]=$v->s_suggestion;
                    $ajx_ret[]=array(
                        "label"=>$v->s_suggestion,
                        "value"=>$v->s_suggestion,
                        "id"=>$v->id,
                    );
                }
            }
        }
        
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);  
        echo json_encode($ajx_ret);
    }      
    
    
	/** 30 Nov 2013
    * We need to fetch skills from user skill table
    * autocomplete.
    * @see, views/fe/user_profile/index.tpl.php
    * 
    */
    public function ajax_skillName()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
        
        /**
        * To fasten the autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-skill-".$posted["term"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }          
        
        
        
        $this->load->model('user_skill_model');
        $condition="s_skill_name LIKE '%".trim($posted["term"])."%' ";
        $ret=$this->user_skill_model->user_skill_fetch($condition,10,0,"s_skill_name ASC");
		//pr($ret); exit;
        if(!empty($ret))
        {
            foreach($ret as $k=>$v)
            {
                $ajx_ret[]=array(
                    "label"=>$v->s_skill_name,
                    "value"=>$v->s_skill_name,
                );
            }
        }
        
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    } 
    
	
	/**
    * We need to fetch  User Full names except the login user id for 
    * autocomplete.
    * @see, message/index.tpl.php
    */
    public function ajax_searchUserList()
    {
        $ajx_ret=array();//aray("id"=>"","label"=>"","value"=>""),....    
        
        $posted=array();
        $posted["term"] = trim($this->input->get("term"));
		
		$uid=get_userLoggedIn("id");
       	
        /**
        * To fasten the location in autocomplete
        * We will use caching.
        * 1st look at the cache if not found then 
        * fetch from query.
        */
        $cacheVar="ajax-searchUserList-".$posted["term"];
        $ajx_ret=cache_var($cacheVar);
        if(!empty($ajx_ret))
        {
            echo json_encode($ajx_ret);
            return true;
        }        
        
        $this->load->model('user_model');
        
        
		$condition="d.s_name LIKE '%".trim($posted["term"])."%' AND e_status='active'";
		if($uid>0)
			$condition.=" AND u.id!='".$uid."' ";
		
		$ret=$this->user_model->user_load($condition,10,0,"s_name ASC");  
		
		if(!empty($ret))
		{
			foreach($ret as $k=>$v)
			{                   
				$ajx_ret[]=array(
					"label"=>$v->s_name,
					"value"=>$v->s_name,
					"id"=>encrypt($v->uid),
				);            
			}///end for
		}//end if                       
        
                
        //Cache the results found
        cache_var($cacheVar,$ajx_ret);        
        echo json_encode($ajx_ret);
    }  
    
    
    public function __destruct(){}
  
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */