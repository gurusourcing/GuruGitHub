// JavaScript Document

jQuery(function($){
	
	
	function IEditable(option){
		
		var defaults = {
			"default_value" : Array(), 	
			"fields" : Array(),
			"edit_links" : Array(),
		};
		
        ///merging into default variables//
		$.extend(defaults,option);
		
		/**
		* initialize the edit fields
		*/
		function init(){
			
			$("[rel^='edit-']").each(function(i){
				var field_container_id=$(this).attr("rel").replace("edit-","");
                
                //field_container_id=field_container_id.replace(/rel-/,"");
                
                
				/**
                * by default hide all fields
                */
                $("#"+field_container_id).hide();
                
				defaults.fields[i]="#"+field_container_id;
				defaults.edit_links[i]=$(this);
                set_default_values(i);
                
                //display the form
                $(this).click(function(){
                    show_field(i);
                });
                
                //init the cancel button
                $(defaults.fields[i]+" [cancel]").click(function(){
                    hide_field(i);
                });
                
				
			});
			
		};
		
		function show_field(i)
		{
			$(defaults.fields[i]).prev().hide("slow");//lebel
            $(defaults.edit_links[i]).hide();
            
			$(defaults.fields[i]).show("slow");//form field
		}
        
        function hide_field(i)
        {
            $(defaults.fields[i]).prev().show("slow");//lebel
            $(defaults.edit_links[i]).show();
            
            $(defaults.fields[i]).hide("slow");//form field
        }  
        
        //todo
        function set_default_values(i)      
		{
            
        }
		
		///start the edit functionality
		init();
	};
	
	
	
	/**
	* initializing the profile edit fields
	*/
	$.fn.inline_editable =function(parms){
		
		return $(this).each(function(k){
            
            new IEditable(parms);
            
        });
		
			
	};
	
	
	
});