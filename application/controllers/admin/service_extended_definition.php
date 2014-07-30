<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Admin Service_extended_definition
* Admin 
*  View list, 
*  Add, Edit , Delete. 
* 
*/

class Service_extended_definition extends MY_Controller {
    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model("category_service_extended_defination_model");
        $this->load->model("user_service_extended_model");
    }
    
    public function index() {
        user_access("administer country");//access check
        if($posted = $this->input->post()){
             $this->data["stage"] = 'step 2';
        } else {
            $this->data["stage"] = 'step 1';
        }

        $this->data["page_title"]="Service Extended Definition";
        $this->render();
    }

    /*ajax sub cat populate
     */
     public function ajax_sub_cat_list()
    {
        $id=intval($this->input->get('cat_id'));
        $ret="";
        if($id)
        {
            $ret=dd_sub_category(array("cat_id"=>$id));
            unset($ret[""]);
        }

        echo json_encode($ret);
    }
    /*ajax service populate
     */
     public function ajax_service_list()
    {   
        $cat_id=intval($this->input->get('cat_id')); 
        $sub_cat_id=intval($this->input->get('sub_cat_id'));
        $ret="";
        if($sub_cat_id)
        {
            $ret=dd_service(array("sub_cat_id"=>$sub_cat_id));
            unset($ret[""]);
        }

        echo json_encode($ret);
    }
    /**
    * Assigning permisions available 
    */
    public function service_extended_definition_permission()
    {
        return array(
            "administer service extended definition"=>array(
                "title"=>"Administer service extended definition",
                "description"=>"Can view, add, edit, delete service extended definition.".message_line("security concern"),
            ),
            
        );
    }//end welcome_permission
}

