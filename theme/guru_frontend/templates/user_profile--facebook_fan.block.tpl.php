<?/*
<script type="text/javascript">
var view_all_show_row = 3; 
var per_row_element = 3;
$(document).ready(function(){
   $.post('<?=site_url('fconnect/get_user_fb_friends')?>',{uid:<?=$uid?>},function(data){
        dataObj = $.parseJSON(data);
        fanHtml = '';
        $(dataObj).each(function(key,val){
            
            if($.isEmptyObject( val.error ))
            {
                fanHtml+='<img style="padding:3px" src="'+val.pic_square+'" alt="'+val.name+'" title="'+val.name+'" >';
                if((key%per_row_element)>view_all_show_row){
                   $('#fb_fan_div').parent().append('<a href="javascript:void(0)" id="view_hide_btn" class="short_grey_button botmar leftmar" onclick="view_hide_toggle('+"'hide'"+');">View All</a>');
                }                
            }
        });
       
        $('#fb_fan_div').html(fanHtml);
    })
    
});
function view_hide_toggle(param){
if(param=="hide"){
    $("#view_hide_btn").html('Show Less');
    $("#fb_fan_div").css('max-height','160px');
    $("#view_hide_btn").attr('onclick','view_hide_toggle("show")');
}
if(param=="show"){
    $("#view_hide_btn").html('View All');
    $("#fb_fan_div").css('max-height','100%');
     $("#view_hide_btn").attr('onclick','view_hide_toggle("hide")');
}

}
</script>


<div class="panel_info facebook_fans">
        <p class="name">Facebook Fans</p>
        <div class="facebook" id="fb_fan_div" style="overflow: hidden;max-height: 173px">
            <img style='margin-left:40%'  src="<?= site_url(get_theme_path().'images/wait_icon.gif')?>"/>
        </div>
<?/*     <a href="#" class="short_grey_button botmar leftmar">View All</a>
        <a href="#" class="short_grey_button botmar grey leftmar" >Change Fan page link</a>* /?>
 </div>

*/?>


<?php
/**
* on 4 Oct 2013, as per client request, 
*  this section has changed to fetch 
* Facebook friends who are listed in guru.
* code was commented before 10 dec 2013 but opened as per client required and else part is newly added
*/

if(!empty($friends)) {
    
    //$html ='<div class="panel_info facebook_fans"><p class="name">Facebook Connections</p>';
	$html ='<div class="panel_info facebook_fans"><p class="name">Facebook Fans</p>';
    $html.='<div class="facebook" id="fb_fan_div" >';
    foreach($friends as $val)
    {        
        $html.=theme_user_thumb_picture($val->id,'','style="padding:3px;width:50px;height:50px;"');
    }
    
    $html.='</div></div>';
}
else
{
	$html ='<div class="panel_info facebook_fans"><p class="name">Facebook Fans</p>';
    $html.='<div class="facebook" id="fb_fan_div" >';    
    $html.='</div></div>';
}

echo $html;

?>

