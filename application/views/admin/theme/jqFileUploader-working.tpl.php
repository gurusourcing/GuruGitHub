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
* $upload_container: "f_container", the main wrapper which will include the the uploader codes.
* $field : "f_logo" , input file id 
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

$allow_maxUploadFiles=(!empty($allow_maxUploadFiles)?$allow_maxUploadFiles:10);
$previewMaxWidth=(!empty($previewMaxWidth)?$previewMaxWidth:80);
$previewMaxHeight=(!empty($previewMaxHeight)?$previewMaxHeight:80);
$acceptFileTypes=(!empty($acceptFileTypes)?$acceptFileTypes:'/(\.|\/)(gif|jpe?g|png)$/i');
$maxFileSize=(!empty($maxFileSize)?$maxFileSize:"5000000");
?>

<div id="<?=$upload_container;?>">
    <input id="<?=$field;?>" type="file" name="files[]" multiple>
    <div id="field_<?=$upload_container;?>">
    
<!-- The loading indicator is shown during file processing -->
<div class="fileupload-loading"></div>
<br>
<!-- The table listing the files available for upload/download -->
<table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table> 
                      
    </div>
</div>



<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd" tabindex="-1">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn modal-download" target="_blank">
            <i class="icon-download"></i>
            <span>Download</span>
        </a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
            <i class="icon-play icon-white"></i>
            <span>Slideshow</span>
        </a>
        <a class="btn btn-info modal-prev">
            <i class="icon-arrow-left icon-white"></i>
            <span>Previous</span>
        </a>
        <a class="btn btn-primary modal-next">
            <span>Next</span>
            <i class="icon-arrow-right icon-white"></i>
        </a>
    </div>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td>{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td>{% if (!i) { %}
            <button class="btn btn-warning cancel">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>    
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td>
            <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>
    </tr>
{% } %}
</script>

<? /*Js for for file uploader */?> 
<!-- Bootstrap CSS Toolkit styles -->
<link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap.min.css">
<!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
<link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css">
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!-- Bootstrap Image Gallery styles -->
<link rel="stylesheet" href="http://blueimp.github.com/Bootstrap-Image-Gallery/css/bootstrap-image-gallery.min.css">

<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/css/jquery.fileupload-ui-noscript.css"></noscript>
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/vendor/jquery.ui.widget.js"></script>


<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="http://blueimp.github.com/cdn/js/bootstrap.min.js"></script>
<script src="http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js"></script>

<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/jquery.fileupload-ui.js"></script>
<?/*
<!-- The main application script -->
<script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/main.js"></script>
*/?>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/js/cors/jquery.xdr-transport.js"></script><![endif]-->
<? /*Js for for file uploader */?>

<script type="text/javascript">
jQuery(function($){
$(document).ready(function(){
    
    
    /////file uploader///
    var file_uploader_dir="<?=base_url("theme/default/js/inedit/jquery-fileupload");?>/";
    $("#<?=$upload_container;?>").fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},        
        url: file_uploader_dir+'server/php/index.php',
        fileInput : $("input#<?=$field;?>"),
        dropZone : $("#<?=$upload_container;?>"),
        maxNumberOfFiles : "<?=$allow_maxUploadFiles;?>",
        previewMaxWidth : "<?=$previewMaxWidth;?>",
        previewMaxHeight : "<?=$previewMaxHeight;?>",
        acceptFileTypes : "<?=$acceptFileTypes;?>",
        maxFileSize : "<?=$maxFileSize;?>",
        
        /*dropZone : $("#logo_field"),
        fileInput : $("#logo"),
        maxNumberOfFiles : 10,
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
    /*$.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#<?=$upload_container;?>').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#<?=$upload_container;?>')[0]
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {result: result});
    });*/    
     
    /////end file uploader///
    
});    
});
</script>
