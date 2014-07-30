/**
* Author: Sahinul Haque
* Date : 15 Mar 2013
* 
* This Plugin will helps in creating inline forms. 
* That is the form fields will display replacing the 
* content. 
* 
* Requirement, jQuery v1.9.1 or higher
* Depends 
*   js/jquery/ui/jquery-ui-1.8.4.custom.js or highr 
*   js/jquery/ui/jquery.ui.dialog.js 
* 
* ::::HOW TO USE::::
* Call in your html page : 
*  $("#inline_edit").inedit({options});
* 
*  The inedit will be operated upon every elements 
* which are child of "#inline_edit" (ex- <div id="inline_edit"></div>).
* 
* :::Now explaining the options parameter:::
* The options is an javascript object.
* 
*  sections : {
*   0 : {
*       "fieldContainer" : the id of field container or any jquery notation of container. Mandatory
*       "contentContainer" : the id of content container or any jquery notation of container. 
*                            If empty then the previous element of "fieldContainer" will be 
*                            considered as "contentContainer".   
*       "addFieldContainer" : optional, if you want to show the add form in different place. 
*       "showButton"  : the id of show button or any jquery notation. If empty then the next 
*                       element of "fieldContainer" will be considered as "showButton".
*       "hideButton" : the id of hide button or any jquery notation. If empty then the element "fieldContainer" 
*                      will be considered as hideButton.
*                      Not required if globalSaveResetButton=true. 
*                       
*       "saveButton" : the id of hide button or any jquery notation is required. 
*                      Not required if globalSaveResetButton=true
*       "defaultValues" {
*               "input_field1_id" : "value", 
*               ///For add more values only this key must be same as "addMoreButton"///
*               "add_more_language" : {
*                0: {"language":"English","proficent":"expert"}, 
*                1: {"language":"Hindi","proficent":"beginner"}
*              }
*           } //the values to be set in edit mode. 
* 
*       "ajaxSaveUrl" : the absolute path for saving the values using ajax. It is highly recommended to use 
*                       this to save the fields into DB. The ajax must return json data as 
*                       {mode: "success|error","message": "Information saved."}
* 
*       "saveSuccessRedirectUrl" :  the absolute path for redirecting after successfully saving the values. 
*                           Usually this will be called immediately after saving, without showing success
*                           or error message.
* 
*       "cancelRedirectUrl" :  the absolute path for redirecting cancel button or "hideButton" is clicked. 
*                             If this path is provided then without hiding the form the page will redirect.
*                               
* 
* 
*       "privacy" : {"set":true,"value":"Public","fieldName":""} //If this object is found then the 
*                   privacy settings icon will appear by the side of the form. The default value can also be set. 
* 
*       * ADD MORE *
*       "addMoreButton" : the id of addmore button container or any jquery notation of container. 
*                         onclick an empty field set will be added within the fieldContainer.
* 
*       "addMoreContainer" : the id of addmore container or any jquery notation of container 
*                            which will be repeated when displaying the fields in edit or add mode.
* 
*       "addMoreShow" : Show the add more fields at top within the fieldContainer or bottom of the fieldContainer.
*                       ex- top|bottom 
*       * END ADD MORE *
*  
*       //Public Methods//
*       "onSaveCallback" : function(fields,values){
*                           return {mode:"error",message:"You are lucky"};
*                       } You may use this function to save the fields or use "ajaxSaveUrl"(recommended). 
*       "beforeSaveCallback" : function(fields){}, This function is called when "save_button" is clicked. So that 
*                              you can alter the final fields values or perform other calculations before saving.   
*       "afterSaveCallback" :  function(values,contentContainer,ajaxReturn){} 
*                              This function is called after the save process is called. 
*       "beforeShowCallback" : function(fields,values){} This function is called before displaying the form. 
*       "beforeHideCallback" : function(contentContainer,default_values){} 
*                              This function is called before hiding the form and displaying the contentContainer.  
*                              You may perform calculation to display within the content each time form is hidden.
*                              Or alter values to be shown when form is hidden. 
*   }
* },
* 
*
* //Create save and cancel buttons at the end of each sections. True/False 
* "globalSaveResetButton": false,
*
* //Privacy settings, if privacy is set then the privacy will operate based upon these settings.
* "globalPrivacySettings": {
*   "Public" : {"icon":"images/icon19.jpg","css":""},
*   "Private" : {"icon":"images/icon20.jpg","css":""},
* }  
* 
* 
*    
* TODO:::
*  AddMore 
*  File Uploading
*/

///loadjscssfile 
function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script');
  fileref.setAttribute("type","text/javascript");
  fileref.setAttribute("src", filename);
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link");
  fileref.setAttribute("rel", "stylesheet");
  fileref.setAttribute("type", "text/css");
  fileref.setAttribute("href", filename);
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref);
}
///end loadjscssfile 

///loading ui css
//loadjscssfile('js/inedit/ui-lightness/jquery-ui-1.10.2.custom.min.css',"css");
//loadjscssfile('js/inedit/jquery-1.9.1.min.js',"js");
//loadjscssfile('js/inedit/jquery.blockUI.js',"js");

jQuery(function($){
    
    //$.getScript('js/inedit/jquery.blockUI.js');//
    
    $.extend($.ui, {inedit: { version: "1.0.0" } });
	//console.log($.ui);
    
	function IEditable(option){
     try{   
        //console.log(option);
        /**
        * Private,
        * keeping the obj clone 
        * self will be available as public 
        */
        var self =this;         
        
        self.version=$.ui.inedit.version;
        
        ///public dialog
        self.msgBox="";        
        

        //private dom who is invoking this plugin//
        var dom = option.dom;// 
        //removing this from the option//
        option.dom= "";
        
        //////fetching the project folder////
        var sourcefolder="";
        var tmp =location.pathname.split("/");
        var cnt=tmp.length-1;
        for(i=0;i<cnt;i++)
        {
          if(tmp[i]!="")
          {
              sourcefolder+="/"+tmp[i];
          }
        }
        //////end fetching the project folder////        
        
        ///private, current working sections with jQuery Obj
        var wSections={};

		var defaults = {
			"sections": {
                0 : {
                    /**
                    * the id of field container or any jquery notation of container.
                    * Within which the empty fields will remain.
                    */
                    "fieldContainer" : "#field_id1", 
                    /**
                    * the id of content container or any jquery notation of container.
                    * Within which the value will be displayed. 
                    * If empty then the previous element of #field_id1 
                    * will be considered as contentContainer.
                    */
                    "contentContainer" : "#content_id1",
                    /**
                    * optional, if you want to show the add form 
                    * in different place. please mention the container id. 
                    * Otherwise empty form will be opened within the  
                    * fieldContainer. 
                    */
                    "addFieldContainer" : "#addfield_id1",
                    /**
                    * the id of show button or any jquery notation.
                    * onclick the fields will be diplayed. 
                    * If empty then the next element of #field_id1 
                    * will be considered as showButton.
                    */
                    "showButton" : "#show_id1", 
                    /**
                    * the id of hide button or any jquery notation.
                    * onclick the fields will hide and contentContainer will be shown.
                    * Or cancel button. 
                    * If empty then the element #fieldContainer 
                    * will be considered as hideButton.
                    * Not required if globalSaveResetButton=true. 
                    */                    
                    "hideButton" : "#hide_id1",
                    /**
                    * the id of save button container or any jquery notation of container.
                    * onclick the fields are prepared to save, onSaveCallback() is called 
                    * and fields will hide and contentContainer will be shown.
                    * Not required if globalSaveResetButton=true. 
                    */                    
                    "saveButton" : "#save_id1",                    
                    
                    /**
                    * the values to be set in edit mode.
                    * For add mode this will be empty.
                    */
                    "defaultValues" : {
                        /**
                        * #input_field1_id must be same as given 
                        * in the input field tag. ex- <input type="text" id="input_field1_id" /> 
                        * For <select id="input_field2_id"><option value="23" ></option></select>
                        * to select 23 by default use "input_field2_id":23  
                        * The html value of the select,checkbox,radios and files will be stored
                        * in "defaultValuesDisplay".
                        */
                        "input_field1_id" : "hello world",
                        "input_field2_id" : "hello world",  
                        "input_field3_id" : "hello world"
                        /**
                        * For addmore pass default values like this. The key("add_more_language") 
                        * must be same as "addMoreButton".
                        * Only for addmore an object can be allowed here. 
                        * 
                        */
                        /*"add_more_language" : {
                            0: {"language":"English","proficent":"expert"}, 
                            1: {"language":"Hindi","proficent":"beginner"}
                        }*/                        

                        
                    },               
                    
                    /**
                    * the absolute path for saving the values using 
                    * ajax. The ajax must return json data as 
                    * {mode: "success|error","message": "Information saved."}
                    * ex- http://localhost/guru/php/home/ajax_save
                    */                    
                    "ajaxSaveUrl" : "",       
                    
                    /**
                    * added on 1Aug2013
                    * the absolute path for redirecting after successfully saving the values. 
                    * Usually this will be called immediately after saving, without showing success
                    * or error message.
                    */
                    "saveSuccessRedirectUrl" : "", 

                    /**
                    * added on 1Aug2013
                    * the absolute path for redirecting cancel button or "hideButton" is clicked.
                    * If this path is provided then without hiding the form the page will redirect.
                    */
                    "cancelRedirectUrl" :  "",                                 
                    
                    /**
                    * If this object is found then 
                    * the privacy settings icon will appear
                    * by the side of the form. 
                    * The default value can also be set. 
                    */
                    "privacy" : {"set":true,"value":"Public","fieldName":""},
                    
                    /**
                    * ADD MORE
                    * the id of addmore button container or any jquery notation of container.
                    * onclick an empty field set will be added within the fieldContainer.  
                    * ex- "addMoreButton" : "#add_more_language", if the fieldContainer contains only one add more. 
                    * If fieldContainer contains only two add mores. 
                    * ex-  "addMoreButton" : {"#add_more_language1","#add_more_language2", ...},
                    * 
                    */                      
                    "addMoreButton" : "",
                    
                    /**
                    * the id of addmore remove button container or any jquery notation of container.
                    * onclick an existing row will be removed within the fieldContainer.
                    */
                    "addMoreRemoveButton" : "", //the button to click for remove row
                    
                    /**
                    * the id of addmore container or any jquery notation of container 
                    * which will be repeated when displaying the fields in edit or add mode.  
                    * ex- "addMoreContainer" : "#add_more_wrapper", if the fieldContainer contains only one add more. 
                    * If fieldContainer contains only two add mores. 
                    * ex-  "addMoreContainer" : {"#add_more_wrapper1","#add_more_wrapper2", ...},
                    */                      
                    "addMoreContainer" : "",   
                    
                    /**
                    * Show the add more fields at top within the fieldContainer 
                    * or bottom of the fieldContainer.
                    * ex- top|bottom
                    */                      
                    "addMoreShow" : "top",  
                    /**
                    * Limit the maximum number of rows to add.
                    * Leave blank for unlimited. 
                    */
                    "addMoreMax"  : "",
                    ///end addmore///                                     
                    
                    /*Public Methods*/
                    /**
                    * It is highly recommended to use this function to save the fields.
                    * This function is called when "save_button" is clicked and after the 
                    * beforeSaveCallback() is called. 
                    * You may use ajax to save values into database or perform 
                    * other calculations. 
                    * If ajaxSaveUrl is also provided with this, then this callback will 
                    * be called first then the ajaxSaveUrl will called using ajax.
                    * 
                    * The return type of this function is object 
                    * {mode: "success|error","message": "Information saved."}
                    *  
                    * fields : obj of all input fields within fieldContainer 
                    * values : obj of new values ready to save into DB.
                    */
                    "onSaveCallback" : function(fields,values){},
                    
                    /**
                    * This function is called when "save_button" is clicked. 
                    * You may use ajax to alter the final fields values or perform 
                    * other calculations. 
                    *  
                    * fields : obj of all input fields within fieldContainer 
                    */
                    "beforeSaveCallback" : function(fields){},                    
                    
                    /**
                    * This function is called after the save process is called.
                    * You may call some ajax or perform calculation 
                    * to display the values within the contentContainer
                    * values : obj of new values saved into DB.
                    * contentContainer : obj of contentContainer 
                    * ajaxReturn : json Parsed data return from ajax 
                    */
                    "afterSaveCallback" : function(values,contentContainer,ajaxReturn){},                     
                    
                    /**
                    * This function is called before displaying the form.
                    * You may call some ajax or perform calculation 
                    * to display within the fields.
                    * Or alter values to be shown.
                    * fields : obj of all input fields within fieldContainer 
                    * values : obj of old values.                    
                    */
                    "beforeShowCallback" : function(fields,default_values){},
                    
                    /**
                    * This function is called after displaying the form.
                    * You may call some ajax or bind additional jquery events, 
                    * such as autocomplete, datepickers 
                    * to display within the fields.
                    * fields : obj of all input fields within fieldContainer 
                    */
                    "afterShowCallback" : function(fields){},                    
                    
                    /**
                    * This function is called before hiding the form and displaying
                    * the contentContainer. 
                    * You may call some ajax or perform calculation 
                    * to display within the content each time form is hidden.
                    * Or alter values to be shown when form is hidden.
                    * contentContainer : obj of contentContainer 
                    * values : obj of new values.                    
                    */
                    "beforeHideCallback" : function(contentContainer,default_values){}                    
                }
            },
            /**
            * Create save and cancel buttons at the end of each scetions.
            * True/False 
            */
            "globalSaveResetButton": false,
            /**
            * Privacy settings, if privacy is set then 
            * the privacy will operate based upon these settings.
            */
            "globalPrivacySettings": {
                "Public" : {"icon":"images/icon19.jpg","css":""},
                "Private" : {"icon":"images/icon20.jpg","css":""},
            }    
		};
        
        /**
        * Private Settings
        */
        var settings ={
            'base_url': location.protocol+"//"+location.host+sourcefolder+"/",
            "globalPrivacySettings": {
                "Public" : {"icon":"images/icon19.jpg","css":""},
                "Private" : {"icon":"images/icon20.jpg","css":""},
            },
            /**
            * Private use only
            * For select,checkbox,radios and files we have to use different 
            * value for displaying to user. So we use this. 
            * The "defaultValuesDisplay" will be filled up autometically.
            * So no need to setup from option. 
            */
            "defaultValuesDisplay" : {
                /**
                * #input_field1_id must be same as given 
                * in the input field tag. ex- <input type="text" id="input_field1_id" /> 
                * For <select id="input_field2_id"><option value="23" >hello world</option></select>
                * to display when 23 is selected by default use "input_field2_id":"hello world"  
                * The html value of the select,checkbox,radios and files will be stored
                * in "defaultValuesDisplay".
                * For checkbox and radio, the next textnode will be selected.
                * 
                * "input_field1_id" : "hello world",
                " input_field2_id" : "hello world",  
                " input_field3_id" : "hello world"
                */
            },                 
             
            /**
            * Show the add more fields at top within the fieldContainer 
            * or bottom of the fieldContainer.
            * ex- top|bottom
            */                      
            "addMoreShow" : "top",                         
        };
		
        ///merging into default variables//
		$.extend(defaults,option);              
        
        function commonSaveResetButton()
        {
            var str= '<input type="submit" value="Save" class="leftmar short" />'+
                     '<input type="reset" value="Cancel" class="short" /> ';
            return str;         
        }
        
		/**
        * private,  
		* initialize the edit fields
		*/
		function init() {

            ///crawling each fields///
            $.each(defaults.sections,function(i,v){
                wSections={};
                
                if($(dom).has(v.fieldContainer).length)
                {
                   var temp={}; 
                   var tid="";
                   ///assigning the globalsettings///
                   if(defaults.globalSaveResetButton)
                   {
                       $(dom).find(v.fieldContainer).append(commonSaveResetButton());
                       v.saveButton= v.fieldContainer+" [type='submit']";
                       v.hideButton= v.fieldContainer+" [type='reset']";
                   }
                   ///end assigning the globalsettings///
                                        
                   //assigning the jQuery Objs with current working sections/// 
                   $.extend(wSections,{
                       "dom" : $(dom),
                       "fieldContainer":$(dom).find(v.fieldContainer),
                   });
                   
                   ////if fieldContainer doesnot have any id then assign it//
                   tid=($.trim(wSections.fieldContainer.attr("id"))!=""
                            ? wSections.fieldContainer.attr("id")
                            : "fieldContainer_"+i
                          );
                   wSections.fieldContainer.attr("id",tid);                  
                   ////end if fieldContainer doesnot have any id then assign it// 
                   
                   /**
                   * If empty then the previous element of #field_id1 
                   * will be considered as contentContainer.
                   */
                   temp={};
                   if($(dom).has(v.contentContainer).length)
                       temp=$(dom).find(v.contentContainer); 
                   else///auto mode
                   {
                       temp=wSections.fieldContainer.prev();
                       tid=($.trim(temp.attr("id"))!=""
                                ? temp.attr("id")
                                : "contentContainer_"+i
                              );
                              
                       temp.attr("id",tid);
                       v.contentContainer=tid;
                   }
                   $.extend(wSections,{"contentContainer":temp});
                   
                   
                   /**
                   * If empty then the next element of #field_id1 
                   * will be considered as contentContainer.
                   */
                   temp={};
                   if($(dom).has(v.showButton).length)
                       temp=$(dom).find(v.showButton); 
                   else///auto mode
                   {
                       temp=wSections.fieldContainer.next();
                       tid=($.trim(temp.attr("id"))!=""
                                ? temp.attr("id")
                                : "contentContainer_"+i
                              );
                              
                       temp.attr("id",tid);
                       v.showButton=tid;
                   }             
                   $.extend(wSections,{"showButton":temp});

                   /**
                   * If empty then the element #fieldContainer 
                   * will be considered as hideButton.
                   */   
                   temp={};
                   if($(dom).has(v.hideButton).length)
                       temp=$(dom).find(v.hideButton); 
                   else///auto mode
                   {
                       temp=wSections.fieldContainer;
                       v.hideButton=temp.attr("id");
                   }             
                   $.extend(wSections,{"hideButton":temp});                                                     
                   
                   /**
                   * The save button must be configured.
                   * Either v.saveButton must not empty 
                   * Or globalSaveResetButton=true.  
                   * It is not possiable to have 
                   * saveButton = "" and globalSaveResetButton=false.
                   */
                   temp={};
                   if($(dom).has(v.saveButton).length)
                       temp=$(dom).find(v.saveButton); 
                   
                   $.extend(wSections,{"saveButton":temp});
                   
                   /**
                   * the absolute path of saving the values using ajax. 
                   * ex- http://localhost/guru/php/home/ajax_save
                   */                    
                   $.extend(wSections,{"ajaxSaveUrl":v.ajaxSaveUrl});  
                   
                   /**
                   * the absolute path of redirecting to listing page after saving. 
                   * ex- http://localhost/guru/php/home/listing
                   */                    
                   $.extend(wSections,{"saveSuccessRedirectUrl":v.saveSuccessRedirectUrl});  
                    
                   /**
                   * the absolute path of redirecting to listing page when cancel is clicked. 
                   * ex- http://localhost/guru/php/home/listing
                   */                    
                   $.extend(wSections,{"cancelRedirectUrl":v.cancelRedirectUrl});                             
                   
                   /**
                   * Privacy settings
                   */
                   $.extend(wSections,{"privacy":setGlobalPrivacy(v)});
                                     
                   /**
                   * Add more init, 
                   * We have to configure the addmore ids and 
                   * fields, Then we can assignDefaultValue 
                   */
                   $.extend(wSections,{"addMoreButton":v["addMoreButton"]}); 
                   $.extend(wSections,{"addMoreContainer":v["addMoreContainer"]}); 
                   $.extend(wSections,{"addMoreShow":v["addMoreShow"]}); 
                   $.extend(wSections,{"addMoreRemoveButton":v["addMoreRemoveButton"]});
                   $.extend(wSections,{"addMoreMax":v["addMoreMax"]});
                   $.extend(wSections,{"addMore":addMoreSection(v)});  

                   /**
                   * assigning the callback functions
                   * into wSections
                   */
                   $.each(v,function(k,l){
                      if($.isFunction(l))
                      {
                          eval('$.extend(wSections,{'+k+':'+l+'});');
                      } 
                   });
                                                         
                   
                   //Assigning the default values to this section//
                   assignDefaultValue(v);
                   //end Assigning the default values to this section//
                   
                   ////bind events////
                   bindEvt(wSections);
                   ////end bind events////
                   
                   ///set the default values into input, for defaultDisplay////
                   setFormValues(wSections);
                   ///set the default values into input, for defaultDisplay////                   
                                      
                   ///onPage load hide all sectionForm//
                   hideSectionForm(wSections);
                                      
                   //end assigning the jQuery Objs with current working sections/// 
                }
            });
            ///end crawling each fields///       
			
            ////Creating the jQuery Dialog box/////
            createDialog();
            ////end Creating the jQuery Dialog box/////
		};
        ///end init 
        
        ///obj= each section
        function setGlobalPrivacy(obj)
        {
            if($.isEmptyObject( obj.privacy ))
                return false;
            
            /*
            obj.privacy= {"set":true,"value":"","fieldName":""};
            settings ={
            "globalPrivacySettings": {
                "Public" : {"icon":"","css":""},
                "Private" : {"icon":"","css":""},
                }            
            }*/
            
            if(obj.privacy.set)
            {
                var fieldContainer=$(dom).find(obj.fieldContainer);
                var field_name=($.trim( obj.privacy.fieldName )==""
                                    ? fieldContainer.attr("id")+"_privacy" 
                                    : $.trim( obj.privacy.fieldName ) );
                                    
                var setPriv = $.extend(true,{},
                                settings.globalPrivacySettings,
                                defaults.globalPrivacySettings) ;
                
                //console.log(setPriv,defaults);
                var def_val = ($.trim(obj.privacy.value)==""
                                ?"Public"
                                : $.trim(obj.privacy.value));
                
                var def_icon=settings.base_url+($.trim(setPriv[ def_val ].icon)==""
                                                ?"images/icon19.jpg"
                                                : $.trim(setPriv[ def_val ].icon));                                    
                                    
                var priv_html='<div rel="privacy" class="right-top eye_panel">'+
                                '<a href="javascript:void(0);" class="eye_icon">'+
                                    '<img src="'+def_icon+'" width="16" height="10" alt="icon" />'+
                                    '<input type="hidden" name="'+field_name+'" id="'+field_name+'" value="'+def_val+'" />'+
                                '</a>'+
                                '<ul class="link">';
                var c=0; 
                $.each(setPriv,function(m,l){
                  
                  priv_html+='<li '+(c!=0?'class="nodevider"':'')+' >'+
                            '<a href="javascript:void(0);" rel="'+m+'" >'+
                                '<img src="'+settings.base_url+l.icon+'" width="16" height="10" alt="'+m+'" /> '+m+
                            '</a></li>';
                });            
                                    
                priv_html+='</ul>'+
                          '</div>';
                                
                fieldContainer.append(priv_html);
                
                var ret={
                    "set":true,
                    "value":def_val,
                    "fieldName":field_name,
                    "container": fieldContainer.find("div[rel='privacy']") 
                };
                
                ////assigning the obj.privacy.value into obj.defaultValues////
                /**
                * Here obj.privacy.value is set but 
                * obj.defaultValues is empty. 
                * Then we have to assign the obj.privacy.value into 
                * obj.defaultValues for the 1st time it is loaded. 
                */
                if(!$.isEmptyObject(obj.defaultValues))
                    obj.defaultValues[ret.fieldName]=$.trim(def_val);  
                else
                {
                    obj.defaultValues={};
                    obj.defaultValues[ret.fieldName]=$.trim(def_val);  
                }                     
                ////end assigning the obj.privacy.value into obj.defaultValues////                
                
                return ret;
            }///end set privacy            
            return false;
        }        
        
        //obj= each section
        function assignDefaultValue(obj)
        {
            //console.log(obj);
            //Assigning the default values to this section//
            if($.isEmptyObject(obj.defaultValues))
                $.extend(wSections,{"defaultValues":{}});
            else
                $.extend(wSections,{"defaultValues":obj.defaultValues});
            
            
            $.extend(wSections,{"fields":new Array()});
            
            /////Registering each fields into wSections///
            //TODO :: File field to be added///
            wSections.fieldContainer.find("input,select,textarea")
            .each(function(i,m){
                /** 
                * @see, showSectionForm()
                * The defaultValues are assigned into the form fields 
                * at showSectionForm()
                */
                wSections.fields.push($(this));
                
            });
            /////end Registering each fields into wSections///
        }
        
        //obj=wSections
        function bindEvt(obj)
        {
            ///bind showFields////
            obj.showButton.click(function(){
                /**
                * on,23 sep2013, 
                * when edit button is clicked then hide 
                * all other edit buttons from all other sections 
                */
                $.each(defaults.sections,function(i,v){
                    if($(dom).has(v.fieldContainer).length)
                    {
                        if($(dom).find(v.showButton).length)
                            $(dom).find(v.showButton).hide();
                        else
                            $(dom).find("#"+v.showButton).hide();
                        
                        //console.log(v.showButton);
                    }
                });
                
                //obj.showButton.show();
                showSectionForm(obj);
            });            
            ///end bind showFields////
            ///bind hideFields////
            obj.hideButton.click(function(){
                
                /**
                * added on 1Aug2013
                * the absolute path for redirecting cancel button or "hideButton" is clicked.
                * If this path is provided then without hiding the form the page will redirect.
                */              
                if($.trim(obj.cancelRedirectUrl)!="")
                    window.location.href=obj.cancelRedirectUrl;
                else
                    hideSectionForm(obj);
                    
                /**
                * on,23 sep2013, 
                * when edit button is clicked then hide 
                * all other edit buttons from all other sections 
                */
                $.each(defaults.sections,function(i,v){
                    if($(dom).has(v.fieldContainer).length)
                    {
                        if($(dom).find(v.showButton).length)
                            $(dom).find(v.showButton).show();
                        else
                            $(dom).find("#"+v.showButton).show();
                        
                        //console.log(v.showButton);
                    }
                });                
                
            });            
            ///end bind hideFields////  
            ///bind saveFields////
            obj.saveButton.click(function(){
                saveSectionForm(obj);
            });            
            ///end bind saveFields////              
            ///bind Privacy////
            if(!$.isEmptyObject( obj.privacy ))
            {
                obj.privacy.container.find("ul.link li a").each(function(k,l){
                   $(this).click(function(){
                        setPrivacy(obj.privacy,$(this).attr("rel"));
                    });                 
                });
            }
            ///end bind Privacy////                      
            
        }
        
        //obj=wSections
        function showSectionForm(obj)
        {
            ///Re-Set the addmore///          
            if( !$.isPlainObject(obj.addMoreButton) 
                && $.trim(obj.addMoreButton)!=""
            )///only one addmore
            {
                var values = {};
                var valueIndex=obj.addMoreButton.replace('#','');
                if( !$.isEmptyObject(obj.defaultValues[ valueIndex ]) )
                    values=obj.defaultValues[ valueIndex ];   
                    
                obj.addMore.reSetValues(values); 
                //pr("@showSectionForm",values);               
            }
            else if( !$.isEmptyObject( obj.addMore ) )//more of addmore's
            {
                $.each(obj.addMore,function(v,ad){
                    var valueIndex=ad.replace('#','');    
                    var values = {};
                    if( !$.isEmptyObject(obj.defaultValues[ valueIndex ]) )
                        values=obj.defaultValues[ valueIndex ];                    
                    
                    obj.addMore.reSetValues(values);
                });
            }
            ///end getting all fields within addmore/// 
            
            //assign the default values into form input//
            setFormValues(obj);
            
            /**
            * Making the, defaultValues into private. 
            * So that is not passed by reference of obj.defaultValues. 
            * Otherwise obj.defaultValues will be modified within callback
            * obj.beforeShowCallback.  
            */
            var dvals=$.extend(true,{},obj.defaultValues);
            
            ///callback beforeShowCallback///
            if($.isFunction(obj.beforeShowCallback))
                obj.beforeShowCallback(obj.fields,dvals);
            
            
            obj.showButton.hide();
            obj.contentContainer.effect("slide",{"mode":"hide","direction":"up","duration":"fast"});
            obj.fieldContainer.effect("slide",{"mode":"show","direction":"down","duration":800});
            
            ///callback afterShowCallback///
            if($.isFunction(obj.afterShowCallback))
                obj.afterShowCallback(obj.fields);            
            
        }
        
        //obj=wSections
        function hideSectionForm(obj)
        {

            /**
            * Making the, defaultValues into private. 
            * So that is not passed by reference of obj.defaultValues. 
            * Otherwise obj.defaultValues will be modified within callback
            * obj.beforeHideCallback.  
            * For select,checkbox,radios,files the values are ready to display.
            */
            var dvals=$.extend(true,{},obj.defaultValues,settings.defaultValuesDisplay);            
            //console.log("@hideSectionForm",settings.defaultValuesDisplay);
            
            ///callback beforeHideCallback///
            if($.isFunction(obj.beforeHideCallback))
                obj.beforeHideCallback(obj.contentContainer,dvals);            
            
            obj.fieldContainer.effect("slide",{"mode":"hide","direction":"up","duration":"fast"});
            obj.contentContainer.effect("slide",{"mode":"show","direction":"down","duration":800});
            obj.showButton.show(900);
        }        
        
        //obj=wSections
        function saveSectionForm(obj)
        {
            //{mode: "success|error","message": "Information saved."}
            var saveStatus={
                "mode" : "",
                "message" : ""
            };
            var callbackReturn={};
            
            /**
            * Making the, defaultValues into private. 
            * So that is not passed by reference of obj.defaultValues. 
            * Otherwise obj.defaultValues will be modified within callback
            * obj.beforeShowCallback.  
            */
            var dvals=$.extend(true,{},obj.defaultValues);
            
            $.blockUI({message: 'Saving please wait...' });
            
            ///callback beforeSaveCallback///
            if($.isFunction(obj.beforeSaveCallback))
                obj.beforeSaveCallback(obj.fields);            
            
            
            /**
            * Now getting the values of the fields.
            * There may be some alteration in input fields
            * in onSaveCallback
            */
            var fvals=getFormValues(obj.fields,obj);
            
            ///callback onSaveCallback///
            if($.isFunction(obj.onSaveCallback))
                callbackReturn=obj.onSaveCallback(obj.fields,fvals);            
            
            $.extend(saveStatus,callbackReturn);            
            
            ////Post the form using ajax///
            if($.trim(obj.ajaxSaveUrl)!="")
            {
                $.post(obj.ajaxSaveUrl,
                    fvals,
                    function(data)
                    {
                        if(data)
                        {
                            data=$.parseJSON(data);
                            $.extend(saveStatus,data);
                        }
                        afterSave(obj,saveStatus,fvals,data);   
                    }
                );
            }////end Post the form using ajax///
            else
            {
                afterSave(obj,saveStatus,fvals);                    
            }
        }
        
        //fields=wSections.fields
        //obj=wSections
        function getFormValues(fields,obj)
        {
            var vals={};
            //init settings.defaultValuesDisplay
            settings.defaultValuesDisplay={};
            
            $.each(fields,function(n,fl){
               
               if(fl.is("input[type='text'],input[type='hidden'],input[type='password']"))
                   vals[fl.attr("name")]=fl.attr("value");
               else if( fl.is("input[type='radio'],input[type='checkbox']")  
                  /*&& fl.is(":checked")*/
               )
               {
                   if(fl.is(":checked"))
                   {
                        ///text next to checkbox or radios//
                        var str=$($(this)[0].nextSibling).text();//getting textnode 
                        if($.trim(str)=="")
                            str=$(this).next().text();    
                            
                        str=$.trim(str);    
                        ///text next to checkbox or radios//                                   
                        
                        /**
                        * For checkboxes, radios, we have multiple selection option.
                        * So we must check the values in loop
                        */
                        var fld_name= fl.attr("name");
                        
                        if($.trim(vals[fld_name])!="" //value already inserted into vals[fld_name]
                           && !$.isPlainObject(vals[fld_name]) ///string 
                        )///value already exists
                        {
                            vals[fld_name]=$.makeArray(vals[fld_name]);
                            vals[fld_name].push(fl.attr("value"));
                            
                            ///assigning value of displaying//
                            settings.defaultValuesDisplay[fld_name]=$.makeArray(settings.defaultValuesDisplay[fld_name]);
                            settings.defaultValuesDisplay[fld_name].push(str);
                        }
                        else if($.isArray( vals[fld_name] ) )//another value as multivalue
                        {
                            vals[fld_name].push(fl.attr("value"));
                            ///assigning value of displaying///
                            settings.defaultValuesDisplay[fld_name].push(str);
                        }
                        else//firsttime or single valued checkbox or radio
                        {
                            vals[fld_name] = fl.attr("value");
                            ///assigning value of displaying//
                            settings.defaultValuesDisplay[fld_name]=str;
                        }                        
                       
                   }
                   else///unchecked
                   {
                       var fld_name= fl.attr("name");
                       
                       if( !$.isArray( vals[fld_name] ) 
                        && $.trim(vals[fld_name])==""
                       )
                       {
                           vals[fld_name] = "";
                           settings.defaultValuesDisplay[fld_name]="";
                       }
                   }

               }
               else if(fl.is("textarea"))
               {
                   //vals[fl.attr("name")]=fl.html();//not worked in jq-1.7, but worked in 1.9
                   vals[fl.attr("name")]=fl.val();
                   
                   /**
                   * FOR \n and br issues                   
                   */
                   var tmp_ta=fl.val();
                   tmp_ta=tmp_ta.replace(/\n/,'<br/>');
                   
                   settings.defaultValuesDisplay[fl.attr("name")]=tmp_ta;
               }                  
               else if(fl.is("select"))
               {
                   var tmp=fl.find("option:selected");
                   if(tmp.length>1)//multiple 
                   {
                       vals[fl.attr("name")]=new Array();
                       settings.defaultValuesDisplay[fl.attr("name")]=new Array();
                       
                       $.each(tmp,function(x,y){
                           vals[fl.attr("name")][x]=$(y).attr("value");
                           ///assigning value of displaying//
                           var str=$(y).text();
                           settings.defaultValuesDisplay[fl.attr("name")][x]=$.trim(str);
                           ///assigning value of displaying//                             
                       });
                   }
                   else if(tmp.length>0)///
                   {
                       vals[fl.attr("name")]=tmp.attr("value");
                       ///assigning value of displaying//
                       var str=tmp.text();
                       settings.defaultValuesDisplay[fl.attr("name")]=$.trim(str);
                       ///assigning value of displaying//                        
                   }
               }               
            });
                       
            ////Add More Values////
            var addMoreValues=new Array();
            if( !$.isPlainObject(obj.addMoreButton) 
             && $.trim(obj.addMoreButton)!=""
            )///only one addmore
            {
                ///getting the addMoreButton's id  
                //var tmp=$.trim(obj.addMoreButton).replace("#","");
                var tmp=$.trim(obj.fieldContainer.find(obj.addMoreButton).attr("id"));
                
                addMoreValues=obj.addMore.getAllValues();
                if(!$.isEmptyObject( addMoreValues ))
                {
                    vals[tmp]=addMoreValues["dbReady"];
                    vals[tmp+"_vld"]=addMoreValues["civalidate"];//For CI Validation using array
                }
                    
            }
            else if( !$.isEmptyObject( obj.addMore ) )//more of addmore's
            {
                $.each(obj.addMore,function(v,am){
                    //$.merge(addMoreValues, am.getAllValues() );
                    
                    //var tmp=am.getOption("addMoreButton");
                    var tmp="";
                    addMoreValues=am.getAllValues();
                    if(!$.isEmptyObject( addMoreValues ))
                    {
                        vals[tmp]=addMoreValues["dbReady"];
                        vals[tmp+"_vld"]=addMoreValues["civalidate"];//For CI Validation using array
                    }
                                            
                    
                });
            }            
            //pr("@getForm",addMoreValues,vals);
            
            ////end Add More Values////
            
            
            return vals;
        }
        
        //obj=wSections
        function setFormValues(obj)
        {
            //init settings.defaultValuesDisplay
            settings.defaultValuesDisplay={};
            
            //obj.fields,obj.defaultValues
            var values=obj.defaultValues;
            
            ///getting all fields within addmore//
            var addMoreFields=new Array();
            if( !$.isPlainObject(obj.addMoreButton) 
             && $.trim(obj.addMoreButton)!=""
            )///only one addmore
                addMoreFields=obj.addMore.getAllFields("fieldId");
            else if( !$.isEmptyObject( obj.addMore ) )//more of addmore's
            {
                $.each(obj.addMore,function(v,am){
                    $.merge(addMoreFields, am.getAllFields("fieldId") );
                });
            }
            ///end getting all fields within addmore//    

            //pr("@setFormValues",addMoreFields);            
            
            //TODO :: File field to be added///
            $.each(obj.fields,function(i,m){
                
                //accepting name or id
                var id=$(this).attr("name");//1st prefrence is name then id.
                id=($.trim(id)==""?$(this).attr("id"):id);
                
                /**
                * If the field "m" belongs to addMore then
                * goto next iteration. 
                * Because we have already set the value 
                * within the field.
                */
                if($.inArray(id,addMoreFields)>=0 )
                    return true;                
                
                
                if($(this).is("input[type='text'],input[type='hidden']"))
                {
                    //$(this).attr("value", $.trim(values[id]));
                    
                    var tmp_ta=$.trim(values[id]);
                    tmp_ta=tmp_ta.replace(/&#034;/g,'\'');//double quote issue
                    tmp_ta=tmp_ta.replace(/&#039;/g,'\'');//single quote issue
                    tmp_ta=tmp_ta.replace(/&amp;/g,'&');//&amp; issue
                    $(this).attr("value",$.trim(tmp_ta));
                    //$(this).html(tmp_ta);
                    
                    ///assigning value of displaying//
                    values[id]=$.trim(values[id]).replace(/&#034;/g,'\'');//double quote issue
                    values[id]=$.trim(values[id]).replace(/&#039;/g,'\'');//single quote issue
                    values[id]=$.trim(values[id]).replace(/&amp;/g,'&');//&amp; issue
                    settings.defaultValuesDisplay[id]=values[id];
                    ///assigning value of displaying//                    
                    
                    //console.log(tmp_ta,values[id]);
                    //pr($(this));//obj.addMore.getOption(idx);
                    //console.log("@hidd",$.trim(values[id]));
                }
                else if($(this).is("input[type='radio'],input[type='checkbox']"))
                {
                    ///text next to checkbox or radios//
                    var str=$($(this)[0].nextSibling).text();//getting textnode 
                    if($.trim(str)=="")
                        str=$(this).next().text();    
                    ///text next to checkbox or radios//                                   
                    
                    
                    /**
                    * For checkboxes, radios, we have multiple selection option.
                    * So we must check the values in loop
                    */
                    //else if( !$.isEmptyObject(values[id]) )
                    //!$.isPlainObject(values[id])
                    if( typeof values[id]=="string" ///supplied defaultValue is string
                        && $(this).attr("value") == $.trim(values[id]) 
                    )//string value has passed
                    {
                        $(this).attr("checked",true);
                        ///assigning value of displaying///
                        settings.defaultValuesDisplay[id]=str;
                    }    
                    else if(!$.isEmptyObject(values[id])
                        && typeof values[id]!="string"
                    ) //object value has passed for multivalue
                    {
                        settings.defaultValuesDisplay[id]=new Array();
                        
                        $.each(values[id],function(k,g){
                            if( $(this).attr("value") == $.trim(g) )
                            {
                                $(this).attr("checked",true);
                                ///assigning value of displaying///
                                settings.defaultValuesDisplay[id].push(str);
                                ///end assigning value of displaying///                          
                            }
                        }); 
                    }                    
                }
                else if($(this).is("select"))
                {
                    $(this).find("option[value='"+$.trim(values[id])+"']").attr("selected",true);
                    ///assigning value of displaying//
                    var str=$(this).find("option[value='"+$.trim(values[id])+"']").text();
                    settings.defaultValuesDisplay[id]=str;
                    ///assigning value of displaying//                   
                }
                else if($(this).is("textarea"))
                {
                    //$(this).text($.trim(values[id]));
                    /**
                    * For \n or Br issue
                    */
                    var tmp_ta=$.trim(values[id]);
                    tmp_ta=tmp_ta.replace(/<br\/>/g,'\n');
                    tmp_ta=tmp_ta.replace(/&#034;/g,'\'');//double quote issue
                    tmp_ta=tmp_ta.replace(/&#039;/g,'\'');//single quote issue
                    tmp_ta=tmp_ta.replace(/&amp;/g,'&');//&amp; issue
                    $(this).attr("value",$.trim(tmp_ta));
                    //$(this).html(tmp_ta);
                    
                    ///assigning value of displaying//
                    values[id]=$.trim(values[id]).replace(/&#034;/g,'\'');//double quote issue
                    values[id]=$.trim(values[id]).replace(/&#039;/g,'\'');//single quote issue
                    values[id]=$.trim(values[id]).replace(/&amp;/g,'&');//&amp; issue
                    settings.defaultValuesDisplay[id]=values[id];
                    ///assigning value of displaying//
                    //console.log("@texta-",$(this).is("textarea"),$.trim(values[id]));                    
                }                
            });
            
            ////setting Privacy////
            if(!$.isEmptyObject( obj.privacy ))
                setPrivacy(obj.privacy,values[obj.privacy.fieldName]);
            ////end setting Privacy////          
            
            //console.log("@setval",values,settings.defaultValuesDisplay);      
        }
        
        /**
        * obj = wSections 
        * saveStatus={mode: "success|error","message": "Information saved."}
        * fvals = object of input fields values {inputName : value, ...}
        */
        function afterSave(obj,saveStatus,fvals)
        {
            
            /**
            * on 1Aug2013,
            * We can redirect to listing pages, 
            * after saving successfully. So we will 
            * not show the dialog button.
            * But when ok is clicked it will redirect to 
            * the listing page.  
            * 
            * on 23Sep, 
            * Do not show the success popup in FE. 
            * So, for admin we will show popup.
            *  
            */
            //showing the message
            self.showMessage(saveStatus.mode,
                        saveStatus.message,
                        obj);
            
            if(saveStatus.mode=="success")
            {
                ///overwriting the new saved values into// 
                $.extend(obj.defaultValues,fvals);
                hideSectionForm(obj);
            }
            else if($.trim(saveStatus.message)=="")
                self.showMessage("error","There is an server error. Please try again.");
            
            ///callback afterSaveCallback, obj.fields///
            if($.isFunction(obj.afterSaveCallback))
            {
                var ajaxReturn=(arguments[3]?arguments[3]:"");
                obj.afterSaveCallback(fvals,obj.contentContainer,ajaxReturn);
            }
                                    
            /**
            * on,23 sep2013, 
            * when edit button is clicked then hide 
            * all other edit buttons from all other sections 
            */
            $.each(defaults.sections,function(i,v){
                if($(dom).has(v.fieldContainer).length)
                {
                    if($(dom).find(v.showButton).length)
                        $(dom).find(v.showButton).show();
                    else
                        $(dom).find("#"+v.showButton).show();
                    
                    //console.log(v.showButton);
                }
            });               
              
                
            $.unblockUI(); 
            //console.log("#afterSave",obj.defaultValues,fvals);      
        }
        
        function createDialog()
        {
            var dialogFrame='<div id="dialog-message" title="Information">'+
                '<p>'+
                '<span id="dialog-message-icon" class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;">'+
                 '</span>'+
                '<div id="dialog-message-content"></div></p></div>';
                
            $("body").append(dialogFrame);
            self.msgBox=$( "#dialog-message" );
            self.msgBox.dialog({
                modal: true,
                autoOpen: false,
                buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                },
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    //effect: "explode",
                    duration: 1000
                }                
            });               
            
        }
        
        ///public method
        self.showMessage=function(status,message)
        {
            if($.trim(message)=="")
                return false;
            
            //mode: "success|error"
            var stat_={
                "error" : {"ico":'ui-icon ui-icon-alert',"title":"Error"},
                "success" :{"ico":'ui-icon ui-icon-check',"title":"Success"} ,
                "others" : {"ico":'ui-icon ui-icon-info',"title":"Information"}
            }
            
            status=($.trim(stat_[status])==""?"others":status);   
            
            self.msgBox.find("#dialog-message-icon")
            .removeClass().addClass(stat_[status]["ico"]);
            
            self.msgBox.find("#dialog-message-content").html(message);
            
            self.msgBox.prev().find(".ui-dialog-title").text(stat_[status]["title"]);
            
            //self.msgBox.dialog( "open" ); 
            /**
            * Will Redirect after saving success
            * and ok button is clicked
            * 
            * on 23Sep, 
            * Do not show the success popup in FE. 
            * So, for admin we will show popup.             
            */
            //console.log(arguments[2] , status,self.msgBox.dialog("option","buttons"));
            //console.log(self.msgBox.find(".ui-dialog-titlebar-close")); 
            if(arguments[2] && status=="success")
            {
              var redirectUrl= arguments[2].saveSuccessRedirectUrl; 
              if($.trim(redirectUrl)!="")
              {
                  self.msgBox.dialog("option","buttons",{
                      "Ok": function(){
                         //console.log("Ready to redirect");
                         window.location.href=redirectUrl;
                      }
                  });
                                    
                  self.msgBox.prev().find(".ui-dialog-titlebar-close").on("click",function(){
                        window.location.href=redirectUrl;    
                  });  
                  
                  self.msgBox.dialog( "open" );                                  
              }              
            }
            ////end redirect
            
            /**
            * on 23Sep, 
            * Do not show the success popup in FE. 
            * So, for admin and errors we will show popup.
            */
            if(status!="success")
            {
                self.msgBox.dialog( "open" );
            }            
        }
        
        /**
        * objPrivacy=wSections.privacy
        * value=Public|Private
        */
        function setPrivacy(objPrivacy,value)
        {
			//console.log(objPrivacy);
            objPrivacy.container.find("#"+objPrivacy.fieldName).attr("value",value);
            //changing the icons
            var setPriv = $.extend(true,{},
                                settings.globalPrivacySettings,
                                defaults.globalPrivacySettings) ;      
              
            objPrivacy.container.find("img[alt='icon']").attr("src",setPriv[value].icon);
        }
        
        /********ADD MORE******/
        ///obj= each section
        function addMoreSection(obj)
        {
            if($.isEmptyObject(obj.addMoreButton)
                && $.isEmptyObject(obj.addMoreContainer)
            )//addmore not applied
                return false;
            else if($.isEmptyObject(obj.addMoreButton)
                || $.isEmptyObject(obj.addMoreContainer)
            )//something wrong happened
                throw new Error("Please check addMoreButton and addMoreContainer.");
                
            //Here every thing is fine, we can start addmore//
            
            /**
            * There may be a situation in which, the "fieldContainer" 
            * contains two addmores,   
            */
            if( !$.isPlainObject(obj.addMoreButton) )///only one addmore
            {
                var values = {};
                //var valueIndex=obj.addMoreButton.replace('#','');
                var valueIndex=$.trim($(obj.fieldContainer).find(obj.addMoreButton).attr("id"));

                if( !$.isEmptyObject(obj.defaultValues[ valueIndex ]) )
                    values=obj.defaultValues[ valueIndex ];
                    
                //pr(obj,valueIndex,values);
                 
                return new AddMore({
                    "mainWrapper" : obj.fieldContainer,
                    "addMoreButton" : obj.addMoreButton,
                    "addMoreRemoveButton" : obj.addMoreRemoveButton,
                    "addMoreContainer" : obj.addMoreContainer,
                    "addMoreShow" :  obj.addMoreShow,
                    "addMoreMax"  : obj.addMoreMax, 
                    "values" : values
                });
            }
            else ///there are more than 1 addmore's within the container
            {
                var ret={};
                
                $.each(obj.addMoreButton,function(i,ad){
                
                    //var valueIndex=ad.replace('#','');   
                    var valueIndex=$.trim(obj.fieldContainer.find(ad).attr("id"));
                     
                    var values = {};
                    if( !$.isEmptyObject(obj.defaultValues[ valueIndex ]) )
                        values=obj.defaultValues[ valueIndex ];
                    
                    var addMoreRemoveButton="";
                    /*if( $.isPlainObject( obj["addMoreRemoveButton"] ) )
                        addMoreRemoveButton=obj.addMoreRemoveButton;
                    else if( $.trim( obj["addMoreRemoveButton"][i] )!="")//varies from different addmores
                        addMoreRemoveButton=$.trim( obj.addMoreRemoveButton[i] );*/    
                    
                    if( !$.isPlainObject( obj.addMoreRemoveButton ) )//Button is common to all addmore's
                        addMoreRemoveButton=obj.addMoreRemoveButton;
                    else if(!$.isEmptyObject( obj.addMoreRemoveButton ))    
                        addMoreRemoveButton=$.trim( obj.addMoreRemoveButton[i] );
                                                        
                    
                    var addMoreShow="";
                    if( !$.isPlainObject( obj.addMoreShow ) )//this is common to all addmore's
                        addMoreShow=obj.addMoreShow;
                    else if(!$.isEmptyObject( obj.addMoreRemoveButton ))//varies from different addmores
                        addMoreShow=$.trim( obj.addMoreShow[i] ); 
                    
                        
                    var addMoreMax="";
                    if( !$.isPlainObject( obj.addMoreMax ) )//this is common to all addmore's
                        addMoreMax=obj.addMoreMax;
                    else if(!$.isEmptyObject( obj.addMoreMax ))//varies from different addmores
                        addMoreMax=$.trim( obj.addMoreMax[i] );   
                    
                    var addMore= new AddMore({
                        "mainWrapper" : obj.fieldContainer,
                        "addMoreButton" : obj.addMoreButton[i],
                        "addMoreRemoveButton" : addMoreRemoveButton,
                        "addMoreContainer" : obj.addMoreContainer[i],
                        "addMoreShow" :  addMoreShow,
                        "addMoreMax"  : addMoreMax, 
                        "values" : values
                    }); 
                    
                    ret[valueIndex]=addMore;
                                       
                });
                
                return ret;
            }
        }             
        /********end ADD MORE******/
        
        ///start the functionality///////
        init();          
		
     }//end try
     catch(err)
     {                    
        //throw new Error("Error condition in X");
        console.error(err);
        //$.error(err.message);
     }
	};
	
	
	/**
	* initializing the inedit
	*/
	$.fn.inedit =function(parms){          
        
		return $(this).each(function(k){
            
            parms=$.extend(parms,{"dom":this});//the parent is dom 
            var ob=new IEditable(parms);
            ///binding the object into selector//
            $(this).data("inE",ob);
            //console.log(ob );
            //return this;            
        });	    
	};
	///end initializing
        
    /**
    * ADD MORE PLUGIN
    */
    function AddMore(option)
    {
        try{
         
            var self=this;
            var defaults = {
                "mainWrapper" : "",//mainwrapper within which the addmore will reside, *mandatory
                "addMoreButton" : "",//the button to click for add row
                "addMoreRemoveButton" : "", //the button to click for remove row
                "addMoreContainer" : "",//the container to repeat in add more
                "addMoreShow" : "top", //top|bottom, default position for showing the container 
                "addMoreMax"  : "", //5, Limit the maximum number of rows to display. 
                                    //   blank for unlimited.  
                /*"values" : {
                    0 : {
                        "input_field1_id" : "hello world",
                        "input_field2_id" : "hello world",  
                        "input_field3_id" : "hello world"                        
                    },
                    1 : {
                        "input_field1_id" : "hello world",
                        "input_field2_id" : "hello world",  
                        "input_field3_id" : "hello world"                        
                    }
                }*/  
                "values" : {},///prefilled values within the fields   
                "removeButtonHtml" : '<a id="removeRow" class="top_mar" href="javascript:void(0);">'+
                                     '<img width="7" height="7" alt="Remove" src="theme/default/images/admin/cross3.png"></a>', 
                                     //the button to click for remove row       
            };
            
            var wSetting= $.extend(true,{},defaults,option);//copied and merged all values passed
            
            
            function init(){
                try{
                
                if( !$(wSetting.mainWrapper).length )//the main wrapper doesnot exists
                    throw ("The main Wrapper does not exists.");
                else if(!$(wSetting.addMoreButton).length
                    || !$(wSetting.addMoreContainer).length
                )
                    throw ("The addMore Button or Container does not exists.");
                    
                ///every thing is fine we can now start 
                
                ////Register the jQbjects///
                $.extend(wSetting,{"obj_mainWrapper":$(wSetting.mainWrapper).first()});//unique
                $.extend(wSetting,{"obj_addMoreButton":$(wSetting.addMoreButton).first()});//unique
                $.extend(wSetting,{"obj_addMoreContainer":$(wSetting.addMoreContainer).first()});//unique
                ////end Register the jQbjects///    
                
                //console.log(wSetting.addMoreShow);
                wSetting.addMoreShow=($.trim(wSetting.addMoreShow)==""?"top"
                                        :$.trim(wSetting.addMoreShow).toLowerCase()
                                        );
                
                
                ////Assign prefilled values///getFLObject(wSetting.mainWrapper)
                setAllValues();   
                ////end Assign prefilled values///
                
                ///bind add and remove row events//
                bindEvents();
                
                //console.log("@AddMore",wSetting);
                }
                catch(err)
                {
                    //throw new Error("Error condition in X");
                    //console.error(err);
                    console.info(err);
                    //$.error(err.message);            
                }                
            }
            
            /**
            * Renuilding the 1st row
            */
            function setAllValues(){
                //rewriting the 1st Row
                wSetting.obj_mainWrapper.attr("lastIdx",0);
                
                wSetting.obj_addMoreContainer
                .attr("rel","0");
                wSetting.obj_addMoreContainer.find("input,select,textarea")///TODO, add files
                .each(function(i,field){
                    //id and names of filed must remains same
                    setFieldId(wSetting.obj_addMoreContainer,$(this));
                });
                //remove button//
                //if(!$(wSetting.obj_addMoreContainer).find(wSetting.addMoreRemoveButton).length)\
                if(!$(wSetting.addMoreRemoveButton).parents(wSetting.mainWrapper).length)
                {
                    //$(wSetting.obj_addMoreContainer).append(wSetting.removeButtonHtml);
                    wSetting.obj_addMoreContainer.append(wSetting.removeButtonHtml);
                    wSetting.addMoreRemoveButton="#removeRow";
                }
                //end remove button// 
                bindEvents(wSetting.obj_addMoreContainer);                
                //end rewriting the 1st Row//
            }
            
            
            /**
            * arguments[0]= {"input_field1_id" : "hello world", "input_field2_id" : "hello world"} single row
            */
            function addRow()
            {
                //max limit reached
                //var totalRows=parseInt($(wSetting.mainWrapper).find(wSetting.addMoreContainer).length);
                var totalRows=getFLObject(wSetting.addMoreContainer,'length');
                if( totalRows==parseInt(wSetting.addMoreMax)
                    && $.trim(wSetting.addMoreMax)!=""
                )
                    return false; 
                //end max limit reached
                
                var newRow= parseInt(wSetting.obj_mainWrapper.attr("lastIdx"));
                newRow=newRow+1;
                
                var value = (arguments[0]?arguments[0]:{});
                var container={};

                container = wSetting.obj_addMoreContainer.clone();
                //adding the new Row// 
                //all fields are having ids with suffix "_row"+lastindex //     
                container.attr("rel",newRow);              
                
                container.find("input,select,textarea")///TODO, add files
                .each(function(i,field){
                    
                    //id and names of filed must remains same
                    setFieldId(container,$(this));                    
                    
                });                 
                
                setValues(container,value);
                
                /**
                * Display the new row
                */
                if( wSetting.addMoreShow=="top" )//add new row at top
                    getFLObject(wSetting.addMoreContainer,"first").before(container);
                else //add new row at bottom
                    getFLObject(wSetting.addMoreContainer,"last").after(container);                   
                
                //console.log("@addRow",getFLObject(wSetting.addMoreContainer,"total","last"),wSetting.addMoreContainer);
                
                bindEvents(container);
                
                wSetting.obj_mainWrapper.attr("lastIdx",newRow);
                
            }     
            
            /**
            * repeater= (clone) wSetting.obj_addMoreContainer
            * values = {"input_field1_id" : "hello world", "input_field2_id" : "hello world"} single row
            */
            function setValues(repeater,values)
            {
                //clear fields//                  
                self.clear_fields(repeater);                
                
                if($.isEmptyObject(values))
                    return true;
                    
                ///textbox and hidden fields
                repeater.find("input[type='text'],input[type='hidden'],input[type='file']").each(function(){
                   var id = getFieldId(repeater,$(this));
                   
                    var tmp_ta=$.trim(values[id]);
                    tmp_ta=tmp_ta.replace(/&#034;/g,'\'');//double quote issue
                    tmp_ta=tmp_ta.replace(/&#039;/g,'\'');//single quote issue
                    tmp_ta=tmp_ta.replace(/&amp;/g,'&');//&amp; issue   
                   
                   if($.trim(values[id])!="")
                    $(this).attr("value",$.trim(tmp_ta)); 
                });
                ///textarea
                repeater.find("textarea").each(function(){
                   var id = getFieldId(repeater,$(this)); 
                   
                   if($.trim(values[id])!="") 
                   {
                       //$(this).html($.trim(values[id]));///worked
                        /**
                        * For \n or Br issue
                        */
                        var tmp_ta=$.trim(values[id]);
                        tmp_ta=tmp_ta.replace(/<br\/>/g,'\n');
                        tmp_ta=tmp_ta.replace(/&#034;/g,'\'');//double quote issue
                        tmp_ta=tmp_ta.replace(/&#039;/g,'\'');//single quote issue
                        tmp_ta=tmp_ta.replace(/&amp;/g,'&');//&amp; issue                        
                        
                        $(this).attr("value",$.trim(tmp_ta));                       
                       
                   }
                     
                });            
                ///dropdown 
                repeater.find("select").each(function(){
                   var id = getFieldId(repeater,$(this)); 
                    
                   if($.trim(values[id])!="")  
                    $(this).find("option[value='"+$.trim(values[id])+"']").attr("selected",true); 
                    
                });
                
                ///checkbox and radios fields
                repeater.find("input[type='radio'],input[type='checkbox']").each(function(){
                    var id = getFieldId(repeater,$(this));
                    
                    /*if($.trim(values[id])!="")  
                        $(this).attr("checked",true);*/
                      
                    if($.trim(values[id])==$(this).attr("value"))  
                        $(this).attr("checked",true);    
                    
                });                 
            }
            
            /**
            * repeater= (clone) wSetting.obj_addMoreContainer
            * field = $("input,select,textarea,file") 
            */            
            function getFieldId(repeater,field)
            {
                var idSuffix="_row"+repeater.attr("rel");
                return field.attr("id").replace(idSuffix,"");
            }
            
            /**
            * repeater= (clone) wSetting.obj_addMoreContainer
            * field = $("input,select,textarea,file") 
            */            
            function setFieldId(repeater,field)
            {
                var currentIdx=parseInt(repeater.attr("rel"));
                var idSuffix="_row"+currentIdx;     
                
                var fixedId="_row"+wSetting.obj_addMoreContainer.attr("rel");//clone repeater
                var id=field.attr("id").replace( fixedId, "" );    
                //pr("@setFieldId",field,currentIdx,idSuffix,id);       
                field.attr("id",id+idSuffix);
                field.attr("name",id+idSuffix);//id and names must remains same
            }            
            
            /**
            * Binding add and remove events,
            * arguments[0]= (clone) wSetting.obj_addMoreContainer
            */
            function bindEvents()
            {
                var repeater=(arguments[0]?arguments[0]:{});
                
                /**
                * Add row
                * Must bind add event only once
                */
                if($.isEmptyObject(repeater))
                {
                    wSetting.obj_addMoreButton.off("click")
                    .on("click",function(){
                        addRow();
                    });                    
                }
                else
                {
                    //remove row
                    repeater.find(wSetting.addMoreRemoveButton).off("click")
                    .on("click",function(){
                        removeRow(repeater);
                    });                    
                }
            }
            
            /**
            * repeater= (clone) wSetting.obj_addMoreContainer
            */
            function removeRow(repeater)
            {
                var totalRows= getFLObject(wSetting.addMoreContainer,"total");
                if(totalRows==1)
                {
                    self.clear_fields(repeater);
                    return true;
                }
                
                repeater.remove();
            }
            
            /**
            * Since we are accepting query, 
            * so in case of repeating elements, 
            * We cannot get the exact 
            * total element count, 
            * first object
            * last object.
            * Because the id is not changed for wSetting.addMoreContainer
            * arguments[1]= total|first|last|all 
            */
            function getFLObject(query)
            {
                //console.log($(query),query);
                if(!$(query).length)
                    return false;
                
                
                var retType= (arguments[1]?arguments[1]:"first");
                var tag = $(query).prop("tagName").toLowerCase();
                var obj = $(tag).find(query);
                
                switch(retType)
                {
                    case 'total':
                        return parseInt(obj.length);
                    break;
                    case 'last':
                        return obj.last();
                    break;              
                    case 'all':
                        return obj;
                    break;                             
                    default :
                        return obj.first();
                    break;                    
                }
            }
            
            /**
            * public, Clearing fields
            * repeater= (clone) wSetting.obj_addMoreContainer
            */
            self.clear_fields=function(repeater)
            {
                try{
                    ///textbox and hidden fields
                    /*repeater.find("input[type='text'],input[type='hidden'],input[type='password'],input[type='file']")*/
                    repeater.find("input[type='text'],input[type='hidden'],input[type='file']")
                    .each(function(){
                        $(this).attr("value",""); 
                    });
                    ///textarea
                    repeater.find("textarea").each(function(){
                       $(this).html(""); 
                    });            
                    ///dropdown 
                    repeater.find("select").each(function(){
                       $(this).find("option:first").attr("selected",true); 
                    });
                    
                    ///checkbox and radios fields
                    repeater.find("input[type='radio'],input[type='checkbox']").each(function(){
                        $(this).attr("checked",false);
                    });                        
                    
                }
                catch(err){}          
            }         
            
            ///public
            self.getOption=function(idx)
            {
                return $.extend({},wSetting[idx]);
            }
               
            
            
            /**
            * public, returns all inputs within this addmore 
            * arguments[0] = "fieldId"|"fields"
            */
            self.getAllFields=function()
            {
                var allRows=getFLObject(wSetting.addMoreContainer,"all");
                if($.isEmptyObject( allRows ))
                    return new Array();
                    
                var ret=(arguments[0]?arguments[0]:"fields");    
                if(ret=="fieldId")
                {
                    var ret_v=new Array();
                    allRows.find("input,select,textarea,file").each(function(){
                        ret_v.push($(this).attr("id"));
                    });
                    return ret_v;
                }
                else
                    return $.makeArray(allRows.find("input,select,textarea,file"));
            }
            
            /**
            * Test getAllValues(), return values in array format for each fields. 
            */            
            self.getAllValues=function()
            {
                var allRows=getFLObject(wSetting.addMoreContainer,"all");
                if($.isEmptyObject( allRows ))
                    return new Array();
                
                var ret_={
                    "dbReady":{},
                    "civalidate":{},//For CI Validation using array
                    "inputs": {}
                };
                allRows.each(function(k,repeater){
                    ret_.dbReady[k]={};
                    var val="";
                    
                    //pr("@getAllValues",repeater);
                    
                    $(this).find("input[type='text'],input[type='hidden'],input[type='password']").each(function(m,field){
                        val=$.trim($(this).attr("value"));
                        var fid=getFieldId($(repeater),$(this));
                        
                        //ret_.dbReady[k][ getFieldId($(repeater),$(this))  ]=val;
                        ret_.dbReady[k][ fid  ]=val;
                        
                        ////For CI Validation using array////
                        if( typeof (ret_.civalidate[ fid ])=="undefined")
                            ret_.civalidate[ fid ]={};
                        ret_.civalidate[ fid ][k]=val;
                        ////end For CI Validation using array////
                        
                        ret_.inputs[ $(this).attr("id") ]=val;
                    });
                    
                    $(this).find("input[type='radio']:checked,input[type='checkbox']:checked").each(function(m,field){ 
                        
                        var id=$(this).attr("id");
                        var fid=getFieldId($(repeater),$(this));
                        val = $(this).attr("value");
                        
                        /**
                        * For checkboxes, radios, we have multiple selection option.
                        * So we must check the values in loop
                        */       
                        if($.trim(ret_.inputs[ id ])!="" //value already inserted into vals[fld_name]
                           && !$.isPlainObject(ret_.inputs[ id ]) ///string 
                        )///value already exists
                        {
                            ret_.dbReady[k][ fid  ]=$.makeArray(ret_.dbReady[k][ fid  ]);
                            ret_.dbReady[k][ fid  ].push(val);
                            
                            ////For CI Validation using array////
                            if( typeof (ret_.civalidate[ fid ])=="undefined")
                                ret_.civalidate[ fid ]={};                            
                            ret_.civalidate[ fid  ][k]=$.makeArray(ret_.civalidate[ fid  ][k]);
                            ret_.civalidate[ fid  ][k].push(val);                            
                            ////end For CI Validation using array////
                            
                            ret_.inputs[ id  ]=$.makeArray(ret_.inputs[ id  ]);
                            ret_.inputs[ id  ].push(val); 
                            
                        }
                        else if($.isArray( ret_.inputs[ id ] ) )//another value as multivalue
                        {
                            ret_.dbReady[k][ fid  ].push(val);
                            
                            ////For CI Validation using array////
                            if( typeof (ret_.civalidate[ fid ])=="undefined")
                                ret_.civalidate[ fid ]={};                            
                            ret_.civalidate[ fid  ][k].push(val);
                            ////end For CI Validation using array////
                            
                            ret_.inputs[ id  ].push(val); 
                        }
                        else//firsttime or single valued checkbox or radio
                        {
                            ret_.dbReady[k][ fid ]=val;
                            
                            ////For CI Validation using array////
                            if( typeof (ret_.civalidate[ fid ])=="undefined")
                                ret_.civalidate[ fid ]={};                            
                            ret_.civalidate[ fid  ][k]=val;
                            ////end For CI Validation using array////                
                                        
                            ret_.inputs[ id ]=val;
                        }                                          
                        
                    });
                    
                    $(this).find("select").each(function(m,field){
                             
                        var tmp=$(this).find("option:selected");
                        if(tmp.length>1)//multiple 
                        {
                            val=new Array();
                           
                            $.each(tmp,function(x,y){
                               val[x]=$(y).attr("value");                             
                               });
                        }
                        else if(tmp.length>0)///
                        {
                           val=tmp.attr("value");                       
                        }                        
                        
                        var fid=getFieldId($(repeater),$(this));
                        //ret_.dbReady[k][ getFieldId($(repeater),$(this))  ]=val;
                        ret_.dbReady[k][ fid ]=val;
                        
                        ////For CI Validation using array////
                        if( typeof (ret_.civalidate[ fid ])=="undefined")
                            ret_.civalidate[ fid ]={};                        
                        ret_.civalidate[ fid ][k]=val;
                        ////end For CI Validation using array////                          
                        
                        ret_.inputs[ $(this).attr("id") ]=val;                                        
                        
                    });                                        
                    
                    $(this).find("textarea").each(function(m,field){
                        val=$.trim($(this).attr("value"));
                        var fid=getFieldId($(repeater),$(this));
                        
                        //ret_.dbReady[k][ getFieldId($(repeater),$(this))  ]=val;
                        ret_.dbReady[k][ fid ]=val;

                        ////For CI Validation using array////
                        if( typeof (ret_.civalidate[ fid ])=="undefined")
                            ret_.civalidate[ fid ]={};                                               
                        ret_.civalidate[ fid ][k]=val;
                        ////end For CI Validation using array////       
                                          
                        ret_.inputs[ $(this).attr("id") ]=val;
                    });
                                        
                });
                
                return $.extend({},ret_);//private
            }            
            
            /**
            * TODO RE-set the values within the fields.
            */
            self.reSetValues=function()
            {
                var allRows=getFLObject(wSetting.addMoreContainer,"all");
                //values tobe set may be supplied by arguments, @see, IEditable::showSectionForm(obj)
                var values=(arguments[0]?arguments[0]:wSetting.values);
                
                ///remove all rows except 1st one//
                $.each(allRows,function(i){
                    if(i==0)
                    {
                        $.extend(wSetting,{"obj_addMoreContainer":$(this)});
                        self.clear_fields($(this));
                        //setValues($(this),{});//set empty value
                    }
                    else
                        removeRow($(this));
                });
                //pr("@reSetValues",wSetting.obj_addMoreContainer);
                ///end remove all rows except 1st one//
                
                ///////Set the values////
                ///no value supplied///
                if( $.isEmptyObject(values) )
                    return true;
                else//values supplied
                {
                    $.each(values,function(i,v){
                        if(i==0)
                            setValues(wSetting.obj_addMoreContainer,v);
                        else
                            addRow(v);                            
                    });
                }//end else, setting rows with default values
                
                ///////end Set the values////
            }
            
            
            ///start
            init();
        }
        catch(err)
        {
            //throw new Error("Error condition in X");
            console.error(err);
            //$.error(err.message);            
        }
    }
    
    
    function pr()
    {
        var objs=arguments;
        console.log(arguments);
    }
    

});///end jQuery