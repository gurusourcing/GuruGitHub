document.write('<script type="text/javascript" src="js/tinymce/jscripts/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php" ></script>');

tinyMCE.init({
                mode : "textareas",
                theme : "advanced",
                plugins : "table,save,advhr,advimage,advlink,insertdatetime,searchreplace,contextmenu,paste,directionality,imagemanager,filemanager",
				
				document_base_url : "http://kamalakarthik.co.uk/alma/",
       			relative_urls : false,
				remove_script_host : false,
				convert_urls : false,
				
				
                theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|",
                theme_advanced_buttons2 : "cut,copy,paste,|,undo,redo,|,link,unlink,anchor,image,code,|,forecolor,backcolor,|,fontsizeselect,|,fontselect",
                theme_advanced_buttons3 : "",
                theme_advanced_toolbar_location : "bottom",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                plugi2n_insertdate_dateFormat : "%Y-%m-%d",
                plugi2n_insertdate_timeFormat : "%H:%M:%S",
                paste_use_dialog : false,
                file_browser_callback : "tinyBrowser",
                theme_advanced_resizing : true,
                theme_advanced_resize_horizontal : false,
                theme_advanced_link_targets : "_something=My somthing;_something2=My somthing2;_something3=My somthing3;",
                paste_auto_cleanup_on_paste : true,
                paste_convert_headers_to_strong : false,
                paste_strip_class_attributes : "all",
                paste_remove_spans : false,
                paste_remove_styles : false,
                convert_urls : false
				
//                convert_fonts_to_spans : false,
//                theme_advanced_font_sizes : 'Size 1=1,Size 2=2,Size 6=7'
        });