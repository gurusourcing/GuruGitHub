<?php
/**
* 
* This template will be used to configure 
* the jquery file uploader. 
* 
* This file uploader needs some special 
* templating. That is why is has made seperate
* template.
* 
* variables used::
* $upload_container: "f_container", *required  the main wrapper which will include the the uploader codes.
* $field : "f_logo" , *required input file id 
* $allow_maxUploadFiles : 10, the numbers of files to upload default is 10
* $previewMaxWidth : 80, the max width in preview window
* $previewMaxHeight : 80,  the max height in preview window
* $acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i, the allowed file types
* $maxFileSize: 5000000, in bytes 
* 
* @see, views/admin/theme/franchisee.tpl.php, see how to use
* @see, theme/default/js/inedit/jquery-fileupload/
* @see, helpers/common_helper.php, generate_fileUploader(); ::TODO
* @see, https://github.com/blueimp/jQuery-File-Upload/wiki/Options
* @see, https://github.com/blueimp/jQuery-File-Upload/wiki/Template-Engine
* 
* TODO :: testing
*/

$upload_container=(!empty($upload_container)?$upload_container:"div_upload_container");
$field=(!empty($field)?$field:"f_logo");
$field_hidden="h_".$field;

$allow_maxUploadFiles=(!empty($allow_maxUploadFiles)?$allow_maxUploadFiles:10);
$previewMaxWidth=(!empty($previewMaxWidth)?$previewMaxWidth:80);
$previewMaxHeight=(!empty($previewMaxHeight)?$previewMaxHeight:80);
$acceptFileTypes=(!empty($acceptFileTypes)?$acceptFileTypes:'/(\.|\/)(gif|jpe?g|png)$/i');
$maxFileSize=(!empty($maxFileSize)?$maxFileSize:"5000000");
?>
<html>
<?/********CSS***********/?>
<link rel="stylesheet" href="<?=site_url(get_theme_path('aquincum')).'/';?>css/bootstrap.min.css">
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="<?=site_url(get_theme_path('aquincum')).'/';?>css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="<?=site_url(get_theme_path('aquincum')).'/';?>css/bootstrap-ie6.min.css"><![endif]-->
<!-- Bootstrap Image Gallery styles -->
<link rel="stylesheet" href="<?=site_url(get_theme_path('aquincum')).'/';?>css/bootstrap-image-gallery.min.css">


<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/css/jquery.fileupload-ui-noscript.css"></noscript>
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?/********CSS***********/?>
<?/********JS***********/?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/vendor/jquery.ui.widget.js"></script>

<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?=site_url(get_theme_path('aquincum')).'/';?>js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?=site_url(get_theme_path('aquincum')).'/';?>js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="<?=site_url(get_theme_path('aquincum')).'/';?>js/bootstrap.min.js"></script>
<script src="<?=site_url(get_theme_path('aquincum')).'/';?>js/bootstrap-image-gallery.min.js"></script>


<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/jquery.fileupload-ui.js"></script>
<?/*
<!-- The main application script -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/main.js"></script>
*/?>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/js/cors/jquery.xdr-transport.js"></script><![endif]-->
<?/********end JS***********/?>
<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
    
    /////file uploader///
    var file_uploader_dir="<?=base_url(get_theme_path('aquincum')."js/inedit/jquery-fileupload");?>/";
    $("#<?=$upload_container;?>").fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},        
        url: file_uploader_dir+'server/php/index.php',
        previewAsCanvas : false,
        fileInput : $("input#<?=$field;?>"),
        dropZone : $("#<?=$upload_container;?>"),
        maxNumberOfFiles : "<?=$allow_maxUploadFiles;?>",
        previewMaxWidth : "<?=$previewMaxWidth;?>",
        previewMaxHeight : "<?=$previewMaxHeight;?>",
        acceptFileTypes : "<?=$acceptFileTypes;?>",
        maxFileSize : "<?=$maxFileSize;?>",
        
        /*By default, files are appended to the files container. Set this option to true, to prepend files instead.*/
        prependFiles : true,
        
        /*is triggered after successful uploads after the download template has been rendered and the transition effects have completed.*/
        completed    :   function(e,data){
            
            var h_vals=$("#<?=$field_hidden;?>").attr("value") || "";
            $.each(data.result.files,function(i,f){
                
                h_vals+=$.param(f)+"###";
                
            });
            
            $("#<?=$field_hidden;?>").attr("value",h_vals);
            //console.log("@completed",$("#<?=$field_hidden;?>").attr("value") );
            // data.result
            // data.textStatus;
            // data.jqXHR;      
            
            //return data;      
        },
        /*is triggered after files have been deleted,*/
        destroyed    :   function(e,data){
            
            ////REMOVE from hidden field///
            var h_vals=$("#<?=$field_hidden;?>").attr("value") || "";
            var h_arr=new Array();
            
            var temp=h_vals.split(/###/);
            $.each(temp,function(i,f){
                if( $.trim(f)=="" )
                    return true;
                    
                var temp_u=data.url.substr( data.url.search('file=') ).replace('file=','name=');
                var temp_f=decodeURIComponent(f);
                if(temp_f.search(temp_u )<0)
                {
                    h_arr.push(f);
                }
            });
            
            $("#<?=$field_hidden;?>").attr("value",h_arr.join('###'));
        },        
        
        uploadTemplateId: null,
        downloadTemplateId: null,
        
        uploadTemplate: function (o) {
            var rows = $();
            $.each(o.files, function (index, file) {
                /*var row = $('<tr class="template-upload fade">' +
                    '<td class="preview"><span class="fade"></span></td>' +
                    '<td class="name"></td>' +
                    '<td class="size"></td>' +
                    (file.error ? '<td class="error" colspan="2"></td>' :
                            '<td><div class="progress">' +
                                '<div class="bar" style="width:0%;"></div></div></td>' +
                                '<td class="start"><button>Start</button></td>'
                    ) + '<td class="cancel"><button>Cancel</button></td></tr>');
                row.find('.name').text(file.name);
                row.find('.size').text(o.formatFileSize(file.size));
                */
                
                /*console.log(o.options.maxNumberOfFiles,index);
                if(index==o.options.maxNumberOfFiles)
                    return false;*/
                
                var row = $('<tr class="template-upload fade">' +
                    '<td width="auto">'+
                        '<div class="preview" style="clear:both;"><span class="fade"></span></div>'+
                        '<div class="name" style="clear:both;"></div>'+
                        '<div class="size" style="clear:both;"></div>'+
                        (file.error ? 
                            '<div class="error" style="clear:both;"></div>'
                        : 
                            '<div class="progress" style="clear:both;width:auto;">'+
                            '<div class="bar" style="width:0%;"></div>'+
                            '</div>'+
                            '<div style="clear:both;float:left;">'+
                                '<button class="btn btn-primary start">'+
                                '<i class="icon-upload icon-white"></i>'+
                                '<span>Start</span>'+
                                '</button>'+
                            '</div>'
                        )+
                        '<div style="clear:right;float:left;">'+
                            '<button class="btn btn-warning cancel">'+
                            '<i class="icon-ban-circle icon-white"></i>'+
                            '<span>Cancel</span>'+
                            '</button>'+                        
                        '</div>'+
                    '</td></tr>'
                    );
                row.find('.name').text(file.name);
                row.find('.size').text(o.formatFileSize(file.size));
                
                if (file.error) {
                    row.find('.error').text(
                        locale.fileupload.errors[file.error] || file.error
                    );
                }
                rows = rows.add(row);
                
            });
            return rows;
        },
        downloadTemplate: function (o) {
            var rows = $();
            $.each(o.files, function (index, file) {
                /*var row = $('<tr class="template-download fade">' +
                    (file.error ? '<td></td><td class="name"></td>' +
                        '<td class="size"></td><td class="error" colspan="2"></td>' :
                            '<td class="preview"></td>' +
                                '<td class="name"><a></a></td>' +
                                '<td class="size"></td><td colspan="2"></td>'
                    ) + '<td class="delete"><button>Delete</button> ' +
                        '<input type="checkbox" name="delete" value="1"></td></tr>');
                row.find('.size').text(o.formatFileSize(file.size));
                */
                                    
                var row = $('<tr class="template-download fade">' +
                    '<td width="auto">'+
                    (file.error ? 
                        '<div class="error" style="clear:both;"></div>'
                        :
                        '<div class="preview" style="clear:both;"></div>'
                    ) + 
                    '<div class="name" style="clear:both;"></div>'+
                    '<div class="size" style="clear:both;"></div>'+                    
                    '<div style="clear:right;float:left;">'+
                        '<button class="btn btn-danger delete" >'+
                        '<i class="icon-trash icon-white"></i>'+
                        '<span>Delete</span>'+
                        '</button>'+     
                        /*'<input type="checkbox" name="delete" value="1">'+ */                  
                    '</div>'+
                    '</td></tr>'
                    );
                row.find('.size').text(o.formatFileSize(file.size));
                                                
                if (file.error) {
                    row.find('.name').text(file.name);
                    row.find('.error').text(
                        locale.fileupload.errors[file.error] || file.error
                    );
                } else {
                    row.find('.name a').text(file.name);
                    if (file.thumbnail_url) {
                        row.find('.preview').append('<a><img></a>')
                            .find('img').prop('src', file.thumbnail_url);
                        row.find('a').prop('rel', 'gallery');
                    }
                    row.find('a').prop('href', file.url);
                    row.find('.delete')
                        .attr('data-type', file.delete_type)
                        .attr('data-url', file.delete_url);
                        
                    if(file.delete_with_credentials)
                    {
                        row.find('.delete')
                            .attr("data-xhr-fields",'{"withCredentials":true}');
                    }
                        
                }
                rows = rows.add(row);
            });
            return rows;
        }
        
        
        
        /*
        add : function(e, data){
            console.log(data);
        }*/
        
    });
    
    // Enable iframe cross-domain access via redirect option:
    /*$('#frm_franchisee_theme').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );  */
    
    // Load existing files:
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#<?=$upload_container;?>').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#<?=$upload_container;?>')[0]
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {result: result});
    });    
     
    /////end file uploader///
    
});    
});
</script>




<div id="<?=$upload_container;?>">
<input id="<?=$field;?>" type="file" name="files[]" multiple>
<input type="hidden" id="<?=$field_hidden;?>" name="<?=$field_hidden;?>">

<div id="field_<?=$upload_container;?>">
    
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped">
        <tbody class="files"></tbody>
        </table> 
                      
</div>
</div>
</html>