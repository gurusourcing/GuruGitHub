<?php
/**
* search Model
* 
* 
* Distance Search in Kms using latitude and longitude.
* @see: http://gis.stackexchange.com/questions/31628/find-points-within-a-distance-using-mysql 
*  
* on6Jan2014,
* The search algo is change from radius to bounding coordinates.
* @see, http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates
* 
* :: SPHINX :: 
* indexer even_idx   *In evendays 1,2,4,6,8,10 ... dates in every month [worked]
* indexer odd_idx    *In odddays 3,5,7,9 ... dates in every month  [worked]
* indexer main_idx    [worked]
* indexer --merge main_idx even_idx --rotate  *In evendays 1,2,4,6,8,10 ... dates in every month [Worked]
* indexer --merge main_idx odd_idx --rotate  *In odddays 3,5,7,9 ... dates in every month [Worked]
* 
* **While testing on 28Aug2013, 
* indexer even_idx [Doesnot worked]
* indexer even_idx --rotate [Worked]
* indexer odd_idx --rotate [Worked]
* indexer rank_idx --rotate [Worked]
* indexer --merge main_idx even_idx --rotate  *In evendays 1,2,4,6,8,10 ... dates in every month [Worked]
* indexer --merge main_idx odd_idx --rotate  *In odddays 3,5,7,9 ... dates in every month [Worked]
* 
* "vw_search_sphinx" is used in sphinx.conf
* 
* Configuring Sphinx :: 
* 
* Version of sphinx : searchd --help
*       Sphinx 2.2.1-dev (r3865)
* 
* Path : /usr/local/etc/sphinx.conf
* stored paths : /var/data/sphinx/main/main_idx
*               /var/data/sphinx/odd/odd_idx
*               /var/data/sphinx/even/even_idx
*               /var/data/sphinx/rank/rank_idx
* 
* 
* ***HOW to delete indexes for fresh start indexing in sphinx.*** 
* WORKING :: after RnD :- 
* 1>  Login using putty "root|Jira202!" 
* 2>cmd : cd ..
* 3>cmd : killall searchd 
* 4>now goto folder  "/var/data/sphinx/" and remove all files within folders "main","odd","even","rank"
* 5>cmd : indexer --config /usr/local/etc/sphinx.conf --all 
* 6>cmd : searchd --config /usr/local/etc/sphinx.conf  
* 
* 
* APPROACH RnD:- 
* 1> If you want to delete all the sphinx indexes that you currently have stop sphinx 
* and delete all the index files in your sphinx's data folder; then you can run indexer 
* again, which will create new index files.
* Found at : http://stackoverflow.com/questions/3055220/remove-all-data-from-sphinx-database 
* 
* 
* ANOTHER APPROACH :- 
* FOUND At : http://wiki.mailspect.com/index.php?title=Rebuilding_Sphinx_Index&oldid=1340 
* 1> Stop Sphinx Daemon
*    killall searchd
* 3> Remove Existing Index Files 
*    rm -f /usr/local/sphinx/var/data/mpp* 
* 
* 4> drop data from content_index and content_counter tables of MPP Archive DB
* mysql -uroot -p use mppdb; truncate content_counter; truncate content_index;
* 
* 5> Temporarily disable cronjobs
* Use: crontab -e and comment out the following
* #5 * * * * /usr/local/MPP/scripts/fetchdata.pl >/dev/null 2>&1 </dev/null
* #45 * * * * /usr/local/sphinx/bin/indexer --config /usr/local/sphinx/etc/sphinx.conf mppdeltaindex --rotate >/dev/null 
*     2>&1 </dev/null
* 
* 6>  Download and install latest fetchdata.pl
* ftp://ftp.messagepartners.com/pub/mpp4/scripts/fetchdata.pl in /usr/local/MPP/scripts/fetchdata.pl
* cd /usr/local/MPP/scripts/
* mv fetchdata.pl fetchdata.pl.old
* wget -c ftp://ftp.messagepartners.com/pub/mpp4/scripts/fetchdata.pl
* chmod 755 fetchdata.pl
* 
* Note: Edit MySQL credentials and set $metadata = 1 if you are using MySQL only for metadata.
* =Edit MySQL credentials in fetchdata.pl to meet your DB requirements and also set $metadata 
* variable to 0 or 1 depending on your setup 
* 
* 7>  Run fetchdata.pl parser
* This can take some time if there are many messages in DB
* perl /usr/local/MPP/scripts/fetchdata.pl
* 
* 8> Index parsed data
* /usr/local/sphinx/bin/indexer --config /usr/local/sphinx/etc/sphinx.conf --all
* 
* 9> Start Sphinx searchd Daemon
* /usr/local/sphinx/bin/searchd --config /usr/local/sphinx/etc/sphinx.conf
* 
* 10> Enable Cronjobs
* Use: crontab -e and uncomment the following
* 5 * * * * /usr/local/MPP/scripts/fetchdata.pl >/dev/null 2>&1 </dev/null
* 45 * * * * /usr/local/sphinx/bin/indexer --config /usr/local/sphinx/etc/sphinx.conf mppdeltaindex --rotate >/dev/null 
*        2>&1 </dev/null
* 
*/

class Search_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }



    /**
    * 1> fetch from CI cache variable if exists then return
    * 2> fetch from search cache if exists then return
    * 3> use sphinx fulltext search then mysql then return
    *    insert into search cache and return. 
    * 
    * @param array $condition, 
    *               array("field1"=>value1,"field2"=>value2..)
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of db table.
    */
    public function search_load($condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        global $search_condition;
        //pr($condition);
        /**
        * We need to alter the $condition array
        * for creating the $sphinx_condition 
        */
        $search_condition=array("original_condition"=>$condition,"sphinx_condition"=>"");
        array_walk($condition,"Search_model::refine_search");
        //pr($search_condition["sphinx_condition"]);


        /**
        * First check the cache if any one already
        * searched earlier. 
        * Parse the $condition into sphinx search query.
        */
        $sphinx_condition=$search_condition["sphinx_condition"];

        //pr($condition);
        //pr($sphinx_condition);
        
        ///end parsing
        /**
        * The file caching is not working because 
        * $sphinx_condition is a very large string
        * so md5 is always returning the same value 
        * for different filters
        */
        /*$cache_=md5($sphinx_condition."-".$limit."-".$offset."-".$order_by);
        $ret=cache_var($cache_);//TODO : remove comment  after testing service
        if(!empty($ret))//found from file caching system, now return
            return $ret;*/
        /////End step1//////


        /**
        * Second we are trying to get the results from the search cache table
        */
        $cache_condition=array(
        "s_search_field_value"=>serialize($condition),
        "s_sess_id"=>$this->session->userdata('session_id'),
        "i_page_number"=>$offset,
        );
        $ret=$this->load_("search_cache",$cache_condition);
        if(!empty($ret))//result found in cache
        {
            $ret=unserialize($ret[0]->s_search_result);
            //cache_var($cache_,$ret);
            //For auto pagination//
            if(!empty($this->pager))//means auto pager is requested
            {
                $this->pager["per_page"]=$limit;
                $this->pager["total_rows"]=count($ret);//total of the above query including where clause.
            }
            //pr($this->pager);
            //For auto pagination//                      
            return $ret;
        }
        /*elseif(trim($condition["search_type"])=="user")///search into user
            $ret=$this->search_user($sphinx_condition,$limit,$offset,$order_by);*/
        else//search into services, only
            $ret=$this->search_service($sphinx_condition,$limit,$offset,$order_by);            

        if(!empty($ret))
        {
            /**
            * Inserting into search cache
            */
            $cache_condition["dt_cache_time"]=date("Y-m-d H:i");
            $cache_condition["s_search_result"]=serialize($ret);
            $this->add_("search_cache",$cache_condition);
            //pr($cache_condition);
            /**
            * Inserting into file caching system 
            */
            //cache_var($cache_,$ret);            
        }    

        return $ret;
    }  

    /**
    * 1> Search from sphinx
    * 2> Get the ids
    * 3> Search from user 
    * 
    * @param string $sphinx_condition, @see, search_load()
    * @param mixed $order_by,  ex='title desc, name asc'
    * 
    * @return stdObj of db table.
    * @ignore, FOR this time this function is not in use.
    */
    public function search_user($sphinx_condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        /**
        * First loading the user ids from sphinx
        * then we will run the query from mysql
        */
        $CI=&get_instance();
        $CI->load->add_package_path(APPPATH.'third_party/sphinx-master/');
        $CI->load->library('sphinxsearch');
        $status=$this->sphinxsearch->status();


        //ex-select title_1 from test1 match('demo') 
        /* WORKING 
        $this->sphinxsearch->set_select("title_1");///select columns
        $this->sphinxsearch->add_query("demo ::: ","test1");///match and index 
        $result = $this->sphinxsearch->run_queries();*/

        //ex-select title_1 from test1 match('demo') 
        /* WORKING        
        $this->sphinxsearch->set_select("title_1");///select columns
        $result = $this->sphinxsearch->query("demo ::: ","test1");///match and index 
        */


        //ex-select title_1 from test1 match('@(title,content) \'demo ::: 1\'') 
        $this->sphinxsearch->SetMatchMode(SPH_MATCH_EXTENDED2);//required for this type of search
        //$this->sphinxsearch->SetFieldWeights(array("title"=>0,"content"=>1)); //working 
        /**
        * operators => 
        * & AND , ex- hello & world
        * | OR ,  ex- hello | world
        * -, ! NOT , ex- hello -world, hello !world
        * () GROUP,  ex- ( hello world )
        */
        //$result = $this->sphinxsearch->query("@(title,content) \"demo ::: 1\" | \"demo ::: 22\"","test1");///working
        //$result = $this->sphinxsearch->query("@(title) \"demo ::: 1\" | \"demo ::: 22\"","test1");///working


        //using exact match words
        //$result = $this->sphinxsearch->query("demo ::: 1","test1");///working


        //using limit , working 
        //$this->sphinxsearch->set_limits(100, 70);//SetLimits(offset, limit, max, cutoff)
        //$result = $this->sphinxsearch->query("demo ::: ","test1");        

        ///After testing uncomment these, 
        /**
        * To optimize the sphinx result output.
        * Sphinx config : searchd program configuration options :: 
        * max_matches = 1000 ; 
        * 
        * We assume that if we showing 20 records in a page then 
        * for 1000 results it will get 50 pages into pagination.
        * So user will never crawl each 50 pages. Hence we are not changing
        * max_matches of sphinx configuration. But retriving 
        * 50,000 records each time     
        */
        $this->sphinxsearch->set_limits(0, 50000,50000);//SetLimits(offset, limit, max, cutoff)
        $result = $this->sphinxsearch->query("@(s_user_dummy) ".$sphinx_condition,"main_idx");
        ///end After testing uncomment these, TODO//// 
        
        $this->sphinxsearch->close();
        $CI->load->remove_package_path(APPPATH.'third_party/sphinx-master/');
        //pr($result);

        if(empty($result))
            return FALSE;

        $id=array();
        if(!empty($result["matches"]))
            $id=array_keys($result["matches"]);//retriving th PK

        //pr($id);
        ////////End search from sphinx///////
        ////////search from service///////
        /**
        * Now use the PKS from $ret_ into mysql query
        * into service table. 
        * Then return the resultset. 
        * 
        * Only fetching those fields which are required
        * within the search result.  
        */
        $sql="SELECT s.s_service_name, 
        (
        CASE 
        WHEN ISNULL(u.comp_id) OR u.comp_id=0 THEN 
        ud.s_name
        ELSE 
        c.s_company
        END 
        ) AS s_provided_by,

        (
        CASE 
        WHEN ISNULL(u.comp_id) OR u.comp_id=0 THEN 
            ud.s_profile_photo
        ELSE 
            c.s_logo
        END 
        ) AS s_profile_image,

        (
        CASE 
        WHEN s.i_is_company_service=0 THEN 
        (SELECT GROUP_CONCAT(DISTINCT uk.s_skill_name SEPARATOR ' ') as s_skill
        FROM user_skill uk WHERE uk.uid=s.uid  GROUP BY uk.uid) 
        ELSE 
        (SELECT GROUP_CONCAT(DISTINCT uk.s_skill_name SEPARATOR ' ') as s_skill
        FROM user_skill uk LEFT JOIN users u1 ON uk.uid=u1.id 
        WHERE u1.comp_id=s.comp_id GROUP BY u1.comp_id)
        END 
        ) AS s_skill,    

        (
        CASE 
        WHEN s.i_is_company_service=0 THEN 
        (SELECT SUM(i_endorse_count) 
        FROM user_skill uk WHERE uk.uid=s.uid GROUP BY uk.uid) 
        ELSE 
        (SELECT SUM(i_endorse_count)
        FROM user_skill uk LEFT JOIN users u1 ON uk.uid=u1.id 
        WHERE u1.comp_id=s.comp_id GROUP BY u1.comp_id)
        END 
        ) AS i_endorse_count,                   

        (
        SELECT COUNT(id) as i_recommendation
        FROM user_service_recommendation usr WHERE usr.service_id=s.id
        ) AS i_recommendation_count,                 

        s.id as service_id,s.comp_id,s.uid,
        s.i_view_count as i_service_view_count,
        u.i_view_count as i_user_view_count,
        c.i_view_count as i_company_view_count,

        ud.s_email,s.s_skype,s.s_yahoo,s.s_gtalk, 
        u.s_facebook_credential, u.s_linkedin_credential, 
        s.s_service_desc, 
        s.s_short_url as s_service_short_url, 
        u.s_short_url as s_user_short_url, 
        c.s_short_url as s_company_short_url, 

        s.i_online,s.i_featured, s.i_is_company_service,
        s.i_is_company_default, 
        u.e_status,  u.i_email_verified, u.i_doc_verified, 
        u.i_mobile_verified, u.i_is_company_owner, u.i_is_company_emp, 
        c.i_is_registered, c.i_active as i_company_active

        FROM 
        user_service s 
        LEFT JOIN users u ON s.uid=u.id AND u.e_status='active' 
        LEFT JOIN user_details ud ON ud.uid=u.id  AND u.e_status='active' 
        LEFT JOIN user_company c ON c.id=u.comp_id AND u.e_status='active' AND c.i_active=1 
        ";

        /**
        * Search Conditions 
        * check if service is active 
        * and user (service owner) is active  
        * and if is company then company is active
        */
        $search_condition=" WHERE s.i_active=1 
        AND (NOT ISNULL(u.id) OR NOT ISNULL(c.id))                                  
        ";
        if(!(empty($id)))
        {
            $search_condition.=" AND u.id IN (".implode(",",$id).") ";
        }
        $sql.=$search_condition;

        /**
        * Order by featured services first
        */
        $sql.=" ORDER BY s.i_featured DESC ";
        if(!empty($order_by))  
            $sql.=",".$order_by;


        $qry_p=$sql;
        if(!empty($limit))                
            $qry_p.=" LIMIT ".intval($offset).",".intval($limit)." ";


        /**
        * Setting the group concat 
        * max values
        */
        $this->db->query("SET @@group_concat_max_len = 4294967294");/**imp for concate**/       
        $rs=$this->db->query($qry_p);
        $ret_=$rs->result();
        $rs->free_result();

        //For auto pagination//
        if(!empty($this->pager))//means auto pager is requested
        {
            $this->pager["per_page"]=$limit;
            $rs=$this->db->query($sql);
            $this->pager["total_rows"]=$rs->num_rows();//total of the above query including where clause.
            $rs->free_result();
        }
        //pr($this->pager);
        //For auto pagination//          

        //pr( $this->db->last_query() );        

        return $ret_;        
    }   

    /**
    * 1> Search from sphinx
    * 2> Get the ids
    * 3> Search from service 
    * 
    * @param string $sphinx_condition, @see, search_load()
    * @param mixed $order_by,  ex='title desc, name asc' 
    * @return stdObj of db table.
    */
    public function search_service($sphinx_condition,$limit=NULL,$offset=NULL,$order_by=NULL)
    {
        global $search_condition;//array("original_condition"=>$condition,"sphinx_condition"=>"");
        /**
        * First loading the service ids from sphinx
        * then we will run the query from mysql
        */
        $CI=&get_instance();
        $CI->load->add_package_path(APPPATH.'third_party/sphinx-master/');
        $CI->load->library('sphinxsearch');
        $status=$this->sphinxsearch->status();


        //ex-select title_1 from test1 match('demo') 
        /* WORKING 
        $this->sphinxsearch->set_select("title_1");///select columns
        $this->sphinxsearch->add_query("demo ::: ","test1");///match and index 
        $result = $this->sphinxsearch->run_queries();*/

        //ex-select title_1 from test1 match('demo') 
        /* WORKING        
        $this->sphinxsearch->set_select("title_1");///select columns
        $result = $this->sphinxsearch->query("demo ::: ","test1");///match and index 
        */


        //ex-select title_1 from test1 match('@(title,content) \'demo ::: 1\'') 
        $this->sphinxsearch->SetMatchMode(SPH_MATCH_EXTENDED2);//required for this type of search
        //$this->sphinxsearch->SetFieldWeights(array("title"=>0,"content"=>1)); //working 
        /**
        * operators => 
        * & AND , ex- hello & world
        * | OR ,  ex- hello | world
        * -, ! NOT , ex- hello -world, hello !world
        * () GROUP,  ex- ( hello world )
        * \\\@  escaping , ex-  ( hello\\\@ world )
        */
        //$result = $this->sphinxsearch->query("@(title,content) \"demo ::: 1\" | \"demo ::: 22\"","test1");///working
        //$result = $this->sphinxsearch->query("@(title) \"demo ::: 1\" | \"demo ::: 22\"","test1");///working


        //using exact match words
        //$result = $this->sphinxsearch->query("demo ::: 1","test1");///working


        //using limit , working 
        //$this->sphinxsearch->set_limits(100, 70);//SetLimits(offset, limit, max, cutoff)
        //$result = $this->sphinxsearch->query("demo ::: ","test1");  
        
        //$result = $this->sphinxsearch->query("@(s_service_dummy) Venture","main_idx");//worked
        /*$result = $this->sphinxsearch->query(
            "@(s_service_dummy) \"s_service_name\\\@:\\\@Venture\""
            ,"main_idx");//worked
        */        

        ///After testing uncomment these, 
        /**
        * To optimize the sphinx result output.
        * Sphinx config : searchd program configuration options :: 
        * max_matches = 1000     
        * 
        * We assume that if we showing 20 records in a page then 
        * for 1000 results it will get 50 pages into pagination.
        * So user will never crawl each 50 pages. Hence we are not changing
        * max_matches of sphinx configuration. But retriving 
        * 50,000 records each time        
        */
        //$this->sphinxsearch->set_limits(0, 50000);//SetLimits(offset, limit, max, cutoff)//testing
        $this->sphinxsearch->set_limits(0, 10000);//SetLimits(offset, limit, max, cutoff)//working
        $result = $this->sphinxsearch->query("@(s_service_dummy) ".$sphinx_condition,"main_idx");//working 

        /*pr($result);
        pr($sphinx_condition) ;    
        pr($search_condition["original_condition"]); */
        /** added by Mainak, **/  
              
        if(empty($result))
            return FALSE;

        $id=array();
        if(!empty($result["matches"]))
            $id=array_keys($result["matches"]);//retriving the PK 
        elseif($result["total_found"]<=0)
            $id=array(0);
            
        /**
        * Implementing the rank index 
        * for order by ids
        * @see, GuruSearchRankCalculation.doc
        */
        $rank_based_services = implode(' | ',$id); 
        $this->sphinxsearch->set_filter("uid",array(intval(get_userLoggedIn("id")),0));
        $this->sphinxsearch->set_select("sid");///select columns
        /*$this->sphinxsearch->set_sort_mode(SPH_SORT_EXTENDED,
            "i_featured_value DESC, i_fb_level_value DESC, i_active_level_value DESC,
             i_end_recommended_value DESC, i_profile_completion_value DESC, i_verified_value DESC "
            );*/
        $this->sphinxsearch->set_sort_mode(SPH_SORT_EXTENDED,
            "i_featured_value DESC, i_fb_level_value DESC, i_active_level_value DESC,
             i_end_recommended_value DESC, i_profile_completion_value DESC"
            );//working
        $rs_order_by = $this->sphinxsearch->query("@(service_id) " .$rank_based_services ,"rank_idx");///
        
        //testing, not working need more rnd
        /*$this->sphinxsearch->close();
        $this->sphinxsearch->open();
        $this->sphinxsearch->SetMatchMode(SPH_MATCH_EXTENDED2);//required for this type of search
        $this->sphinxsearch->set_limits(0, 10000);//SetLimits(offset, limit, max, cutoff)//testing*/
        //search --index rank_idx @service_id 9 //this works but below code does not
        //$rs_order_by = $this->sphinxsearch->query("@(service_id) 9" ,"rank_idx");//working
        //$this->sphinxsearch->add_query("@(service_id) 9","rank_idx");///match and index 
        //$this->sphinxsearch->SetFilter("service_id",array(intval(9)));
        //$rs_order_by = $this->sphinxsearch->run_queries();
        
        $order_by=array();
        if(!empty($rs_order_by["matches"]))
        {
            foreach($rs_order_by["matches"] as $oid=>$match)
            {
                $order_by[]=intval($match["attrs"]["sid"]);
            }
        }
        
        //pr($this->sphinxsearch->status());
        /*pr($order_by);
        pr($rs_order_by,1);*/
        /** added by Mainak,**/
        
        ///end After testing uncomment these, ////        

        $this->sphinxsearch->close();
        $CI->load->remove_package_path(APPPATH.'third_party/sphinx-master/');
        

        //pr($id);
        ////////End search from sphinx///////
        ////////search from service///////
        /**
        * Now use the PKS from $ret_ into mysql query
        * into service table. 
        * Then return the resultset. 
        * 
        * Only fetching those fields which are required
        * within the search result. 
		* i_verified_value added feb  
        */
        $sql="SELECT s.s_service_name, 
        (
        CASE 
        WHEN ISNULL(u.comp_id) OR u.comp_id=0 THEN 
        ud.s_name
        ELSE 
        c.s_company
        END 
        ) AS s_provided_by,

        (
        CASE 
        WHEN ISNULL(u.comp_id) OR u.comp_id=0 THEN 
            ud.s_profile_photo
        ELSE 
            c.s_logo
        END 
        ) AS s_profile_image,

        (
        CASE 
        WHEN s.i_is_company_service=0 THEN 
        (SELECT GROUP_CONCAT(DISTINCT uk.s_skill_name SEPARATOR ' ') as s_skill
        FROM user_skill uk WHERE uk.uid=s.uid  GROUP BY uk.uid) 
        ELSE 
        (SELECT GROUP_CONCAT(DISTINCT uk.s_skill_name SEPARATOR ' ') as s_skill
        FROM user_skill uk LEFT JOIN users u1 ON uk.uid=u1.id 
        WHERE u1.comp_id=s.comp_id GROUP BY u1.comp_id)
        END 
        ) AS s_skill,    

        (
        CASE 
        WHEN s.i_is_company_service=0 THEN 
        (SELECT SUM(i_endorse_count) 
        FROM user_skill uk WHERE uk.uid=s.uid GROUP BY uk.uid) 
        ELSE 
        (SELECT SUM(i_endorse_count)
        FROM user_skill uk LEFT JOIN users u1 ON uk.uid=u1.id 
        WHERE u1.comp_id=s.comp_id GROUP BY u1.comp_id)
        END 
        ) AS i_endorse_count,   

        (
        SELECT COUNT(id) as i_recommendation
        FROM user_service_recommendation usr WHERE usr.service_id=s.id
        ) AS i_recommendation_count,                 

        (
        SELECT (i_featured_value+i_fb_level_value+i_active_level_value+i_end_recommended_value
                +i_profile_completion_value+i_verified_value) as i_user_rank 
        FROM user_rank urank WHERE urank.service_id=s.id AND urank.uid=".intval(get_userLoggedIn("id"))."
        ) AS i_user_rank,
        
        (
        SELECT i_fb_level 
        FROM user_rank urank WHERE urank.service_id=s.id AND urank.uid=".intval(get_userLoggedIn("id"))."
        ) AS i_user_fb_level,        

        s.id as service_id,s.comp_id,s.uid,
        s.i_view_count as i_service_view_count,
        u.i_view_count as i_user_view_count,
        c.i_view_count as i_company_view_count,

        ud.s_email,s.s_skype,s.s_yahoo,s.s_gtalk, 
        u.s_facebook_credential, u.s_linkedin_credential, 
        s.s_service_desc, 
        s.s_short_url as s_service_short_url, 
        u.s_short_url as s_user_short_url, 
        c.s_short_url as s_company_short_url, 

        s.i_online,s.i_featured, s.i_is_company_service,
        s.i_is_company_default, 
        u.e_status,  u.i_email_verified, u.i_doc_verified, 
        u.i_mobile_verified, u.i_is_company_owner, u.i_is_company_emp, 
        c.i_is_registered, c.i_active as i_company_active

        FROM 
        user_service s 
        LEFT JOIN users u ON s.uid=u.id AND u.e_status='active' 
        LEFT JOIN user_details ud ON ud.uid=u.id  AND u.e_status='active' 
        LEFT JOIN user_company c ON c.id=u.comp_id AND u.e_status='active' AND c.i_active=1 
        ";

        /**
        * Search Conditions 
        * check if service is active 
        * and user (service owner) is active  
        * and if is company then company is active
        */
        $sql_search_condition=" WHERE s.i_active=1 
        AND (NOT ISNULL(u.id) OR NOT ISNULL(c.id))                                  
        ";
        
        /**
        * Distance search, in Kms using latitude and longitude of "zip" table or "city" table, 
        * @see, above comment section for reference site .
        * To search by kilometers use 6371.
        * To search by miles use replace 3959 .  
        * * This search is only possiable if and only if "search_zip_id" 
        * or "search_city_id" value is found. Because we need to search 
        * by distance with respect to "search_zip_id" or "search_city_id" 
        * 
        * on23Dec13, 
        * if searched by city or zip then distance searched, now
        * if city or zip doesnot returns any record from sphinx, then 
        * distance search fails. So in this case we will filter city
        * or zip from here.
        * 
        * on6Jan2014,
        * The search algo is change from radius to bounding coordinates.
        * @see, above comment section for reference site . 
        */
        if(!empty($search_condition["original_condition"]["distance"])
            && (!empty($search_condition["original_condition"]["search_zip_id"])
                || !empty($search_condition["original_condition"]["search_city_id"])
            )
        )
        {
            $dFactor=array("miles"=>3959,"kms"=>6371);
            $distance=explode("-",trim($search_condition["original_condition"]["distance"]));
            $distance=array_combine(array("min","max"),$distance);//now we can get $distance["min"]
            
            //Distance Search Using Radius Algo//
            /*
            $cityOrZip=array(
                "zip"=>array(
                    "selectCzLat"=>" SELECT zp.s_latitude FROM zip zp WHERE zp.id="
                        .intval($search_condition["original_condition"]["zip_id"])." ",
                    "selectCzLon"=>" SELECT zp.s_longitude FROM zip zp WHERE zp.id="
                        .intval($search_condition["original_condition"]["zip_id"])." ",
                        
                    "selectSLat"=>" SELECT zp1.s_latitude FROM zip zp1 WHERE zp1.id=s.zip_ids ",
                    "selectSLon"=>" SELECT zp1.s_longitude FROM zip zp1 WHERE zp1.id=s.zip_ids ",
                ),
                "city"=>array(
                    "selectCzLat"=>" SELECT ct.s_latitude FROM city ct WHERE ct.id="
                        .intval($search_condition["original_condition"]["city_id"])." ",
                    "selectCzLon"=>" SELECT ct. s_longitude FROM city ct WHERE ct.id="
                        .intval($search_condition["original_condition"]["city_id"])." ",
                        
                    "selectSLat"=>" SELECT ct1.s_latitude FROM city ct1 WHERE ct1.id=s.city_ids ",
                    "selectSLon"=>" SELECT ct1.s_longitude FROM city ct1 WHERE ct1.id=s.city_ids ",
                ),
            );
            
            $location_type=trim($search_condition["original_condition"]["location_type"]);
            
            $mainQuery=(!empty($result["matches"])?" WHERE s.id IN(".implode(",",$id).") ":"");
            
            $distance_query="
                SELECT
                    id, (
                      ".$dFactor["miles"]." * acos (
                      cos ( radians((".$cityOrZip[$location_type]["selectCzLat"].")) )
                      * cos( radians( (".$cityOrZip[$location_type]["selectSLat"].") ) )
                      * cos( radians( (".$cityOrZip[$location_type]["selectSLon"].") ) 
                            - radians((".$cityOrZip[$location_type]["selectCzLon"].")) 
                            )
                      + sin ( radians((".$cityOrZip[$location_type]["selectCzLat"].")) )
                      * sin( radians( (".$cityOrZip[$location_type]["selectSLat"].") ) )
                    )
                ) AS distance
                FROM user_service s "
                /*." WHERE s.id IN(".implode(",",$id).") "* /
                .$mainQuery
                ." HAVING  distance > ".intval($distance["min"])." AND distance < ".intval($distance["max"])."
                ORDER BY distance 
            ";
            */
            
            //Distance Search using Bounding Coordinates//
            $location_type=trim($search_condition["original_condition"]["location_type"]);
            
            $cityOrZip=array(
                "zip"=>array(
                    "selectCzLat"=>" SELECT zp.s_latitude FROM zip zp WHERE zp.id="
                        .intval($search_condition["original_condition"]["zip_id"])." ",
                    "selectCzLon"=>" SELECT zp.s_longitude FROM zip zp WHERE zp.id="
                        .intval($search_condition["original_condition"]["zip_id"])." ",
                    "selectCzLatLon"=>" SELECT zp.s_latitude,zp.s_longitude FROM zip zp WHERE zp.id="
                        .intval($search_condition["original_condition"]["zip_id"])." ",
                        
                    "selectSLat"=>" SELECT zp1.s_latitude FROM zip zp1 WHERE zp1.id=s.zip_ids ",
                    "selectSLon"=>" SELECT zp1.s_longitude FROM zip zp1 WHERE zp1.id=s.zip_ids ",
                ),
                "city"=>array(
                    "selectCzLat"=>" SELECT ct.s_latitude FROM city ct WHERE ct.id="
                        .intval($search_condition["original_condition"]["city_id"])." ",
                    "selectCzLon"=>" SELECT ct.s_longitude FROM city ct WHERE ct.id="
                        .intval($search_condition["original_condition"]["city_id"])." ",
                    "selectCzLatLon"=>" SELECT ct.s_latitude,ct.s_longitude FROM city ct WHERE ct.id="
                        .intval($search_condition["original_condition"]["city_id"])." ",
                        
                    "selectSLat"=>" SELECT ct1.s_latitude FROM city ct1 WHERE ct1.id=s.city_ids ",
                    "selectSLon"=>" SELECT ct1.s_longitude FROM city ct1 WHERE ct1.id=s.city_ids ",
                ),
            );                
            
            
            $rs_CzLatLon=$this->db->query($cityOrZip[$location_type]["selectCzLatLon"]);
            $row_czLatLon=$rs_CzLatLon->row();
            $rs_CzLatLon->free_result();
            if(!empty($row_czLatLon))
            {
                ///for max distance, $distMax["bound_qry"]
                $distMax=$this->bounding_coordinates(
                    $row_czLatLon->s_latitude,
                    $row_czLatLon->s_longitude,
                    $distance["max"] 
                );
                ///replace LAT and LON from "bound_qry"
                $srch_rep=array(
                    "search"=>array("LAT","LON"),
                    "replace"=>array( 
                            "(".$cityOrZip[$location_type]["selectSLat"].")",
                            "(".$cityOrZip[$location_type]["selectSLon"].")"
                            )     
                );
                $distMax["bound_qry"] = str_replace($srch_rep["search"], $srch_rep["replace"],$distMax["bound_qry"]);
                $distMax["bound_qry"]="(".$distMax["bound_qry"].")";
                //pr($distMax);
                
                $distMin=$this->bounding_coordinates(
                    $row_czLatLon->s_latitude,
                    $row_czLatLon->s_longitude,
                    $distance["min"] 
                );
                $distMin["bound_qry"] = str_replace($srch_rep["search"], $srch_rep["replace"],$distMin["bound_qry"]);
                $distMin["bound_qry"]="(".$distMin["bound_qry"].")";
                
                
                $mainQuery=(!empty($result["matches"])?" WHERE s.id IN(".implode(",",$id).") ":"");
            
                $distance_query="
                    SELECT
                        id
                    FROM user_service s "
                    .$mainQuery
                    ."   HAVING   ".$distMax["bound_qry"]." OR ".$distMin["bound_qry"]." 
                    
                ";
                
                  
            }
            //end Distance Search using Bounding Coordinates//
            
            
            ///@final query
            $rs_dis=$this->db->query($distance_query);
            $ret_dis=$rs_dis->result();
            $rs_dis->free_result();
            /*pr($ret_dis);
            pr($distance_query);*/
            
            $id=array(0);
            if(!empty($ret_dis))
            {
                //$id=array(0);
                foreach($ret_dis as $m=>$d)
                {
                    $id[]=intval($d->id);
                }
                unset($m,$d);
            }
            unset($ret_dis);
        }//end if distance search
        
        
        
        /**
        * filter_search_fb_circle, 
        * Here fb lebels are filtered out, from 
        * search results of sphinx. 
        */
        if(!empty($search_condition["original_condition"]["filter_search_fb_circle"]))
        {
            $fb_circle=array("1"=>"1st Circle","2"=>"2nd Circle","3"=>"3rd Circle");
			/* modified on 7mar 2014 for default selecting circle
			* if none is selected then this does not run means all result will be shown
			*/
			if(!in_array("None",$search_condition["original_condition"]["filter_search_fb_circle"]))
			{
				$fb_query="SELECT DISTINCT service_id FROM user_rank ";
				//exclude the visitors from viewing these servces
				//$temp="0";
				$temp="";
				foreach($search_condition["original_condition"]["filter_search_fb_circle"] as $v)
				{
					$val=array_search($v,$fb_circle);
					$temp.=(!empty($temp)?",":",").intval($val);
				}      
				$temp = ltrim($temp,',');      
				$fb_query.=" WHERE i_fb_level IN (".(!empty($temp)?$temp:0).") 
						AND uid=".intval(get_userLoggedIn("id"))."  
						AND service_id IN (".implode(",",$id).") ";
				
				$rs_fbt=$this->db->query($fb_query);
				$ret_fb=$rs_fbt->result();
				$rs_fbt->free_result();
				//pr(array($fb_query,$ret_fb));//debug
				$id=array(0);
				if(!empty($ret_fb))
				{
					//$id=array(0);
					foreach($ret_fb as $m=>$fv)
					{
						$id[]=intval($fv->service_id);
					}
					unset($m,$fv);
				}
				unset($ret_fb);
			
			}
        }
        
        /**
        * Now we have filtered by fb level if there is any.
        * otherwise fetch directly from ids
        */
        if(!(empty($id)))
        {
            $sql_search_condition.=" AND s.id IN (".implode(",",$id).") ";
        }
        
        $sql.=$sql_search_condition;

        /**
        * Order by as per rank indexer, 23Aug2013
        */
        //$sql.=" ORDER BY s.i_featured DESC ";
        if(!empty($order_by))
        {
            //$sql.=" ORDER BY s.id IN (".implode(",",$order_by).") DESC ";
            $tmp="";
            foreach($order_by as $ord)
            {
                $tmp.=(!empty($tmp)?",":"").' s.id='.intval($ord)." DESC ";
            }
            $sql.=" ORDER BY ".$tmp." ";
            unset($tmp);
        }  
        //pr($order_by);    
            
        $qry_p=$sql;
        if(!empty($limit))                
            $qry_p.=" LIMIT ".intval($offset).",".intval($limit)." ";

        /**
        * Setting the group concat 
        * max values
        */
        $this->db->query("SET @@group_concat_max_len = 4294967294");/**imp for concate**/       
        $rs=$this->db->query($qry_p);
        $ret_=$rs->result();
        $rs->free_result();
        
        //pr($qry_p);
        //pr($ret_,1);
        
        //For auto pagination//
        if(!empty($this->pager))//means auto pager is requested
        {
            $this->pager["per_page"]=$limit;
            $rs=$this->db->query($sql);
            $this->pager["total_rows"]=$rs->num_rows();//total of the above query including where clause.
            $rs->free_result();
        }
        //pr($this->pager);
        //For auto pagination//          

        //pr( $this->db->last_query() );        

        return $ret_;
    }     

    /**
    * Complex decision making for search conditions
    * 
    * @param mixed $item
    * @param mixed $key
    * @param mixed $search_condition
    */
    public function refine_search(&$item,$key){
        global $search_condition;
        //pr($item);
        /**
        * these items are checked within the conditionss below so,
        * we will return from here.
        * Also if any value is empty then also return from here.
        * Search condition for "filter_search_fb_circle" is done at search_service() 
        * Search distance(Kms) for "distance" is done at search_service() 
        */
        $exclude_columns=array("search_type_value","cat_id","uid","location_type_value",
                                "city_id","zip_id","filter_search_fb_circle", "distance");
        
        if(in_array($key,$exclude_columns) || empty($item)) 
            return TRUE;
            
        //pr($key);////debug
        if($key=="global_country_id" && !empty($item))
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .searchSphinxRule("country_id",$item);
        elseif($key=="search_type")
        {
            //When search type is service but category not found in autocomplete
            if($item=="service" && empty($search_condition["original_condition"]["cat_id"])
                && trim($search_condition["original_condition"]["search_type_value"])!="Keyword"//auto filled text
            )
            {
                //ex= "s_service_name@:@Venture Service@;@" 
                /*$cond= searchSphinxRule("s_service_name",$search_condition["original_condition"]["search_type_value"]); 
                $cond=str_replace(escapeSphinxQL(getDummyFieldPrefixSuffix("suffix")),"",$cond);                
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .$cond;*/
                                
                //ex= ("s_service_name@:@Venture Service" | Venture) , searchign for service//
                $cond= searchSphinxRule("s_service_name",$search_condition["original_condition"]["search_type_value"]); 
                $cond=str_replace(escapeSphinxQL(getDummyFieldPrefixSuffix("suffix")),"",$cond);    
                $cond="(".$cond." | ".searchSphinxRule("",$search_condition["original_condition"]["search_type_value"]).")";
            
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .$cond;
                                
            }                
            elseif($item=="service" && !empty($search_condition["original_condition"]["cat_id"]))///category found in autocomplete
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .searchSphinxRule("cat_id",$search_condition["original_condition"]["cat_id"]);
            //When search type is user but uid not found in autocomplete
            elseif($item=="user" && empty($search_condition["original_condition"]["uid"])
                 && trim($search_condition["original_condition"]["search_type_value"])!="Keyword"//auto filled text
            )
            {
                /**
                * For alphabet search, we will consider if the search string length is one.
                * Otherwise it is a text search in sphinx. 
                */
                if(strlen($search_condition["original_condition"]["search_type_value"])==1)//alphabet search
                {
                    //ex- ("s_name@:@Test") //not worked
                    /*$cond= searchSphinxRule("s_name",$search_condition["original_condition"]["search_type_value"]); 
                    $cond=str_replace(escapeSphinxQL(getDummyFieldPrefixSuffix("suffix")),"",$cond);    
                    */
                    
                    //ex- ("s_name@:@" << ("S")), not worked
                    /*$cond="(".searchSphinxRule("","s_name".getDummyFieldPrefixSuffix("prefix"))
                    ." << ".searchSphinxRule("",$search_condition["original_condition"]["search_type_value"]).""
                    .")";*/                    
                    
                    
                    $rs=$this->db->query("SELECT uid FROM user_details 
                                WHERE s_name LIKE '".trim($search_condition["original_condition"]["search_type_value"])."%' ");
                    $ret_=$rs->result();
                    $rs->free_result();
                    $cond="";
                    if(!empty($ret_))
                    {
                        $condT="";
                        foreach($ret_ as $ri=>$uu)
                        {
                            //ex= ("uid@:@1234@;@" | "uid@:@1235@;@") , searching for service providers or service owner//
                            $condT.= (!empty($condT)?" | ":"").searchSphinxRule("uid",$uu->uid);                             
                        }
                        $cond.="(".$condT.")";
                        unset($condT);
                    }
                    else
                    {
                        $cond.= searchSphinxRule("uid",0); 
                    }
                    
                    $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .$cond;
                    //pr($cond);//dubug
                                    
                }
                else//name search
                {
                    //ex= ("s_name@:@Test" | TEST) , searching for service providers//
                    $cond= searchSphinxRule("s_name",$search_condition["original_condition"]["search_type_value"]); 
                    $cond=str_replace(escapeSphinxQL(getDummyFieldPrefixSuffix("suffix")),"",$cond);    
                    $cond="(".$cond." | ".searchSphinxRule("",$search_condition["original_condition"]["search_type_value"]).")";
                    $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                    .$cond;
                }                
            }                
            elseif($item=="user" && !empty($search_condition["original_condition"]["uid"]))///user found in autocomplete
            {
                //ex= ("uid@:@1234@;@" | TEST) , searching for service providers or service owner//
                $cond= searchSphinxRule("uid",$search_condition["original_condition"]["uid"]); 
                $cond="(".$cond." | ".searchSphinxRule("",$search_condition["original_condition"]["search_type_value"]).")";
                
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .$cond; 
                                
            }
           
        }
        elseif($key=="location_type"){
            
            if(trim($search_condition["original_condition"]["location_type"])=="zip"
                && !empty($search_condition["original_condition"]["zip_id"])) ///zip, selected from auto complete  
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .searchSphinxRule("zip_ids",$search_condition["original_condition"]["zip_id"]);            
            
            elseif(!empty($search_condition["original_condition"]["city_id"])) ///city, selected from auto complete
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .searchSphinxRule("city_ids",$search_condition["original_condition"]["city_id"]);
                                                                
            elseif(!empty($search_condition["original_condition"]["location_type_value"])
                && trim($search_condition["original_condition"]["location_type_value"])!="City/ Zip"//auto filled text
            )
                $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"")
                                .searchSphinxRule("",$search_condition["original_condition"]["location_type_value"]);
        }
        /*
        //This distance search is done at search_service()  
        elseif($key=="distance" && !empty($item))
        {
            /**
            * Distance radius in kms
            * /             
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" | ":"")
                                .searchSphinxRule("",$item);           
        }*/
        elseif($key=="filter_search_services" && !empty($item))
        { 
            $temp="";
            foreach($item as $v)
            {
                $temp.=(!empty($temp)?" | ":"").searchSphinxRule("i_is_company_service",(trim($v)=="Individual"?0:1));
            }
            $temp="(".$temp.")";
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"").$temp;
        }
        elseif($key=="filter_search_locality" && !empty($item))
        {
            //get_cityId(), get_popularLocationId() 
            /**
            * (("city_ids@:@2@;@" & "zip_ids@:@2@;@") | ("city_ids@:@5@;@" & "zip_ids@:@22@;@"))
            */            
            $temp="";
            foreach($item as $v)
            {
                if(!empty($v))
                {
                    $locs=get_locationId(explode(",",$v));
                    $temp.=(!empty($temp)?" | ":"")
                            ."("
                            .searchSphinxRule("city_ids",$locs->city_id)
                            ." & ".searchSphinxRule("zip_ids",$locs->zip_id)
                            .")";                    
                }
            }
            $temp="(".$temp.")";
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"").$temp;           
        }        
        elseif($key=="filter_search_experience" && !empty($item))
        { 
            $expr=dd_experience_range();
            $temp="";
            foreach($item as $v)
            {
                $val=array_search($v,$expr);
                $temp.=(!empty($temp)?" | ":"").searchSphinxRule("filter_search_experience",trim($val));
            }
            $temp="(".$temp.")";
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"").$temp;
        }
        elseif(($key=="filter_search_tution_fee"||$key=="filter_search_rate") && !empty($item))
        { 
            $temp="";
            foreach($item as $v)
            {
                $val=explode("to",$v);
                $temp.=(!empty($temp)?" | ":"").searchSphinxRule($key,trim($val[0]));
                $temp.=(!empty($temp)?" | ":"").searchSphinxRule($key,trim($val[1]));
            }
            $temp="(".$temp.")";
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"").$temp;
        }        
        elseif(strpos($key,"filter_")!==FALSE && !empty($item))
        {             
			/**
            * All extended fields are prefixed with "filter_". 
            * As filter_search_specialization, filter_search_institution ...
            * SPQuery ::  
            * ex- ("filter_search_specialization@:@" << ("Computer Science" | "Heart Specialist") << "@;@" )
            */            
            $temp="";
            foreach($item as $v)
            {
                $temp.=(!empty($temp)?" | ":"").searchSphinxRule("",$v);
            }
            $temp="(".searchSphinxRule("",$key.getDummyFieldPrefixSuffix("prefix"))
                    ." << (".$temp.")"
                    ." << ".searchSphinxRule("",getDummyFieldPrefixSuffix("suffix"))
                    .")";
            $search_condition["sphinx_condition"].=(!empty($search_condition["sphinx_condition"])?" & ":"").$temp;           
        }                 
                
        /*exclude these array("search_type_value","cat_id","uid","location_type_value","city_id","zip_id",
        ,"global_country_id","filter_search_locality_circle");
        */

    }
    
    /**
    * 1> r = d/R = (1000 km)/(6371 km) = 0.1570 as the angular radius of the query circle.
    * 2> Computing the Minimum and Maximum Latitude, also check the south/north pole issue 
    *    latmin = lat - r
    *    latmax = lat + r
    * 3> Computing the Minimum and Maximum Longitude – the Correct Way
    *    latT = arcsin(sin(lat)/cos(r)) = 1.4942 (5)
    *    lonmin = lonT1 = lon - delta_lon = -1.8184 (6)
    *    lonmax = lonT2 = lon + delta_lon = 0.4221 (7)
    *    where
    *    delta_lon = arccos( ( cos(r) - sin(latT) * sin(lat) ) / ( cos(latT) * cos(lat) )
    *    = arcsin(sin(r)/cos(lat)) = 1.1202 
    * 4> Dealing with Poles and the 180th Meridian
    *    If latmax > pi()/2 Then the bounding coordinates are (latmin, -pi()) and (pi()/2, pi())
    *    If latmin < - pi()/2 Then the bounding coordinates are (-pi()/2, -pi()) and (latmax, pi()).
    *    
    *    If lonmin < -pi() Then the two sets of bounding coordinates are 
    *       (latmin, lonmin + 2pi()), (latmax, pi()) and (latmin, -pi()), (latmax, lonmax). 
    *    If lonmax > pi() Then the two sets of bounding coordinates are 
    *       (latmin, lonmin), (latmax, pi()) and (latmin, -pi()), (latmax, lonmax - 2pi()).
    * 
    * 5> dist=arccos(sin(lat1) * sin(lat2) + cos(lat1) * cos(lat2) * cos(lon1 - lon2)) 
    *    Ex- SELECT * FROM Places WHERE
    *    (Lat => 1.2393 AND Lat <= 1.5532) AND (Lon >= -1.8184 AND Lon <= 0.4221)
    *    AND
    *    acos(sin(1.3963) * sin(Lat) + cos(1.3963) * cos(Lat) * cos(Lon - (-0.6981))) <= 0.1570;
    * 
    * @param mixed $lat in deg 
    * @param mixed $lon in deg 
    * @param mixed $distance in miles/kms 
    * @param mixed $distance_unit => miles or kms
    */
    public function bounding_coordinates($lat,$lon,$distance,$distance_unit="miles")
    {
        
        $dFactor=array("miles"=>3959,"kms"=>6371); 
        $bound_cord=array(     
            "r"=>floatval(floatval($distance)/$dFactor[$distance_unit]),
            "lat" =>  deg2rad($lat) ,//rad
            "lon" =>  deg2rad($lon),//rad   
            "latmin" => 0.0,//rad
            "latmax" => 0.0,//rad 
            "lonmin" => 0.0,//rad
            "lonmax" => 0.0,//rad
            "latT" => 0.0,
            "delta_lon" => 0.0,       
            "coordinates" => array(
                array(0.0,0.0),//(latmin,lonmin)
                array(0.0,0.0),//(latmax,lonmax)
            ),
            "bound_qry"=>"",//sql where clause for latmin, lonmin, latmax, lonmax
        );
        
        //validate here
        if(empty($lat)||empty($lon)||empty($distance))
            return $bound_cord;
            
        
        /*
        * 2> Computing the Minimum and Maximum Latitude of the selected zip or city, 
        *    also check the south/north pole issue. 
        *    latmin = lat - r
        *    latmax = lat + r
        */
        $bound_cord["latmin"]=floatval($bound_cord["lat"] - $bound_cord["r"]) ;
        $bound_cord["latmax"]=floatval($bound_cord["lat"] + $bound_cord["r"]) ;  
        
        /*
        * 3> Computing the Minimum and Maximum Longitude – the Correct Way
        *    latT = arcsin(sin(lat)/cos(r)) = 1.4942 (5)
        *    lonmin = lonT1 = lon - delta_lon = -1.8184 (6)
        *    lonmax = lonT2 = lon + delta_lon = 0.4221 (7)
        *    where
        *    delta_lon = arccos( ( cos(r) - sin(latT) * sin(lat) ) / ( cos(latT) * cos(lat) )
        *    = arcsin(sin(r)/cos(lat)) = 1.1202 
        */
        $bound_cord["latT"]=asin(sin($bound_cord["lat"])/cos($bound_cord["r"]));
        $bound_cord["delta_lon"]=  asin(sin($bound_cord["r"])/cos($bound_cord["lat"]));
        $bound_cord["lonmin"]= floatval($bound_cord["lon"] - $bound_cord["delta_lon"]); 
        $bound_cord["lonmax"]= floatval($bound_cord["lon"] + $bound_cord["delta_lon"]); 
        
        $bound_cord["coordinates"][0]=array($bound_cord["latmin"],$bound_cord["lonmin"]);//{latmin,lonmin}
        $bound_cord["coordinates"][1]=array($bound_cord["latmax"],$bound_cord["lonmax"]);//{latmax,lonmax}    
            
        $bound_cord["bound_qry"]=" (radians(LAT) >= ".$bound_cord["coordinates"][0][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][1][0].") "; 
        $bound_cord["bound_qry"].=" AND (radians(LON) >= ".$bound_cord["coordinates"][0][1]." AND radians(LON) <= ".$bound_cord["coordinates"][1][1].") "; 
        
        
        /*
        * 4> Dealing with Poles and the 180th Meridian
        *    If latmax > pi()/2 Then the bounding coordinates are (latmin, -pi()) and (pi()/2, pi())
        *    If latmin < - pi()/2 Then the bounding coordinates are (-pi()/2, -pi()) and (latmax, pi()).
        *    
        *    If lonmin < -pi() Then the two sets of bounding coordinates are 
        *       (latmin, lonmin + 2pi()), (latmax, pi()) and (latmin, -pi()), (latmax, lonmax). 
        *    If lonmax > pi() Then the two sets of bounding coordinates are 
        *       (latmin, lonmin), (latmax, pi()) and (latmin, -pi()), (latmax, lonmax - 2pi()).
        */ 
        $pi_2= pi()/2;
        $pi2 =2*pi();
        if($bound_cord["latmax"] > $pi_2 ) 
        {
            $bound_cord["coordinates"][0]=array($bound_cord["latmin"],-pi());//{latmin,lonmin}
            $bound_cord["coordinates"][1]=array($pi_2,pi());//{latmax,lonmax}    
            
            $bound_cord["bound_qry"]=" (radians(LAT) >= ".$bound_cord["coordinates"][0][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][1][0].") "; 
            $bound_cord["bound_qry"].=" AND (radians(LON) >= ".$bound_cord["coordinates"][0][1]." AND radians(LON) <= ".$bound_cord["coordinates"][1][1].") "; 
            
        }
        elseif($bound_cord["latmin"] < -$pi_2 )
        {
            $bound_cord["coordinates"][0]=array($bound_cord["latmin"],-pi());
            $bound_cord["coordinates"][1]=array($pi_2,pi());
            
            $bound_cord["bound_qry"]=" (radians(LAT) >= ".$bound_cord["coordinates"][0][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][1][0].") "; 
            $bound_cord["bound_qry"].=" AND (radians(LON) >= ".$bound_cord["coordinates"][0][1]." AND radians(LON) <= ".$bound_cord["coordinates"][1][1].") ";                 
        }
        elseif($bound_cord["lonmin"] < -pi() )
        {
            $bound_cord["coordinates"][0]=array($bound_cord["latmin"],($bound_cord["lonmin"]+$pi2));
            $bound_cord["coordinates"][1]=array($bound_cord["latmax"],pi());  
            $bound_cord["coordinates"][2]=array($bound_cord["latmin"],-pi());
            $bound_cord["coordinates"][3]=array($bound_cord["latmax"],$bound_cord["lonmax"]);  
            
            $bound_cord["bound_qry"]=" (radians(LAT) >= ".$bound_cord["coordinates"][0][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][1][0]
                ." AND radians(LAT) >= ".$bound_cord["coordinates"][2][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][3][0]
            .") "; 
            
            $bound_cord["bound_qry"].=" AND (radians(LON) >= ".$bound_cord["coordinates"][0][1]." AND radians(LON) <= ".$bound_cord["coordinates"][1][1]
                ." AND radians(LON) >= ".$bound_cord["coordinates"][2][1]." AND radians(LON) <= ".$bound_cord["coordinates"][3][1]
            .") "; 
            
            
        }
        elseif($bound_cord["lonmax"] > pi() )
        {
            $bound_cord["coordinates"][0]=array($bound_cord["latmin"],$bound_cord["lonmin"]);
            $bound_cord["coordinates"][1]=array($bound_cord["latmax"],pi());  
            $bound_cord["coordinates"][2]=array($bound_cord["latmin"],-pi());
            $bound_cord["coordinates"][3]=array($bound_cord["latmax"],($bound_cord["lonmax"]-$pi2));  
            
            $bound_cord["bound_qry"]=" (radians(LAT) >= ".$bound_cord["coordinates"][0][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][1][0]
                ." AND radians(LAT) >= ".$bound_cord["coordinates"][2][0]." AND radians(LAT) <= ".$bound_cord["coordinates"][3][0]
            .") "; 
            
            $bound_cord["bound_qry"].=" AND (radians(LON) >= ".$bound_cord["coordinates"][0][1]." AND radians(LON) <= ".$bound_cord["coordinates"][1][1]
                ." AND radians(LON) >= ".$bound_cord["coordinates"][2][1]." AND radians(LON) <= ".$bound_cord["coordinates"][3][1]
            .") "; 
        }
        
        //5> dist=arccos(sin(lat1) * sin(lat2) + cos(lat1) * cos(lat2) * cos(lon1 - lon2)) 
        $bound_cord["bound_qry"].=(!empty($bound_cord["bound_qry"])?" AND ":"")
            ." acos (
                      (cos (".$bound_cord["lat"]."  )
                      * cos( radians( LAT ) )
                      * cos( radians( LON ) 
                            - (".$bound_cord["lon"].") 
                            )
                      )
                      + (sin ( radians(LAT) )
                      * sin( ".$bound_cord["lat"]." )
                      )
                    )<=".$bound_cord["r"]
            ;    
               
        return $bound_cord; 
    }

    public function __destruct(){}

}

?>
