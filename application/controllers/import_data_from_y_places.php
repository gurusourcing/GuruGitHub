<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Import data from y_places into
* country, city,state, zip, zip_location_mapping, popular_location  tablles 
* 
*/
class Import_data_from_y_places extends MY_Controller {

    public function __construct()
    {   
        parent::__construct();
       
    }

    public function index()
    {
       //checks if admin is logged-in
        if(is_adminLoggedIn())
          $this->import_into_state_tbl();
       else
            goto_accessDeny();
    }
    
    //Inserting data into state table from y_places table
    public function import_into_state_tbl()
    {
        $sql = "INSERT INTO state (country_id,s_state,WOE_ID) 
                    SELECT c.id,y.Name,y.WOE_ID FROM country AS c JOIN y_places AS y 
                        ON y.Parent_ID =c.WOE_ID AND y.Placetype='State'";
        
        $res= $this->db->query($sql);
        //if(!empty($res))
            //$this->import_into_city_tbl();
    }
    
    //Inserting data into city table from y_places table
    public function import_into_city_tbl()
    {
        $sql = "INSERT INTO city (country_id,state_id,s_city,s_latitude,s_longitude,WOE_ID)
                    SELECT s.country_id,s.id,y.Name,y.latitude,y.longitude,y.WOE_ID
                        FROM state AS s JOIN y_places AS Y
                        ON s.WOE_ID=y.Parent_ID AND (y.Placetype='County' OR y.Placetype='Town')";
        
        $this->db->query($sql);
    }
    
    //Inserting data into zip table from y_places table
    public function import_into_zip_tbl()
    {
        $sql = "INSERT INTO zip (city_id,state_id,country_id,s_zip,s_latitude,s_longitude,WOE_ID)
                    SELECT c.id,c.state_id,c.country_id,y.zip,y.latitude,y.longitude,y.WOE_ID
                        FROM city AS c JOIN y_places AS y 
                            ON c.WOE_ID=y.Parent_ID AND y.zip!='' AND (y.Placetype='County' OR y.Placetype='Town')";
        
        $this->db->query($sql);
    }

    //Inserting data into popular location table from y_places table
    public function import_into_popular_location_tbl()
    {
        $sql = "INSERT INTO popular_location (city_id,state_id,country_id,s_location,s_latitude,s_longitude,WOE_ID)
                    SELECT c.id,c.state_id,c.country_id,c.s_city,c.s_latitude,c.s_longitude,c.WOE_ID
                      FROM city AS c LEFT JOIN y_places AS y 
                       ON c.WOE_ID=y.Parent_ID AND y.Placetype IN ('Airport','Suburb','Estate','Historical','LandFeatur','POI','Sport') ";
                      
        
        $this->db->query($sql);
    }
    
    //Inserting data zip location mapping table from y_places table
    public function import_into_zip_location_mapping_tbl()
    {
        $sql = "INSERT INTO zip_location_mapping (popular_location_id,zip_id,WOE_ID)
                    SELECT p.id, z.id,p.WOE_ID FROM popular_location AS p JOIN zip AS 
                    ON p.WOE_ID=z.WOE_ID";
        
        $this->db->query($sql);
    }
    
    public function __destruct(){}

}

/* End of file import_data_from_y_places.php */
/* Location: ./application/controllers/import_data_from_y_places.php */