<?php
/**
* Author: Mrinmoy Mondal
* Date : 29 Nov 2013
* 
* Purpose : To provide resize of an image
*   
*/

function upload_image_file($s_img_path = '',$s_new_path = '',	$s_file_name = '', $i_new_height = '', $i_new_width = '',$mix_configArr = array())

{
	try
        {

			$CI = & get_instance();
			$CI->load->library('image_lib');
			$i_new_height = (!empty($i_new_height))?$i_new_height:768;
			$i_new_width  = (!empty($i_new_width))?$i_new_width:1024;		

			$config['image_library'] 	= 'gd2';
			$config['source_image']  	= $s_img_path;  // $_FILES['f_image']['tmp_name']
			//$config['create_thumb']  	= TRUE;
			$config['maintain_ratio'] 	= TRUE;
		    $config['canvas_color'] 	= array('red'=>255,'green'=>255,'blue'=>255);
		    //$config['do_not_upsize'] 	= "false";
		    $config['master_dim'] 		= "auto";
			$config['width'] 			= $i_new_width;
			$config['height'] 			= $i_new_height;
			$config['thumb_marker'] 	= '';
			$config['new_image'] 		= $s_new_path.$s_file_name;		// upload_path.upload_file_name	

			if(is_array($mix_configArr) && count($mix_configArr)>0)
			{
				foreach($mix_configArr as $s_key=>$mix_val)
					$config[$s_key] = $mix_val;
			}	

			$CI->image_lib->initialize($config); 
			unset($s_img_path, $s_new_path, $s_file_name, $i_new_height, $i_new_width ,$mix_configArr, $config);
			$b_res = $CI->image_lib->resize();
			$CI->image_lib->clear();
			if( !$b_res )
			{
				unset($b_res);
				return $msg	= $CI->image_lib->display_errors('<div class="err">','</div>');
			}
			else
			{
				unset($b_res);
				return 'ok';
			}
        }
        catch(Exception $err_obj)
		{
			show_error($err_obj->getMessage());
		}
}
