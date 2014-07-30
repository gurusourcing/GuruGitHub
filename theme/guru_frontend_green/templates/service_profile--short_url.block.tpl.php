<?
if(intval(is_short_url_editable($service_id,'service'))==1):?>
<script id="short_url_script" type="text/javascript">
    $(document).ready(function(){
        $('#short_url_display').bind('click',function(){            
            $('#show_full_url').hide();
            $('#show_edit_url').show(function(){
                $('#show_edit_url input#shorturl_val').select().blur(function(){
                    $('#show_full_url').show();
                    $('#show_edit_url').hide();

                    /*$.ajax({
                            type: "POST",
                            url: "<?=  site_url('service_profile/change_short_url')?>",
                            data: { s_short_url: $('#shorturl_val').val() , service_id : '<?=$service_id?>' }
                            }).done(function( msg ) {
                                if($.trim(msg)!=""){
                                    window.location.reload();
                                } 

                     });*/
                     
                     $.post(
                        "<?=  site_url('service_profile/change_short_url')?>",
                        { s_short_url: $('#shorturl_val').val(), service_id :'<?=$service_id;?>'},
                        function(msg)
                        {
                            if($.trim(msg)!=""){
                                window.location.reload();
                            } 
                        }
                    ); 
                    
                     
                });
        
            });
            })

        
    });
    
</script>
<div class="panel_info">
        <h3 align="center" class="botmar">Short URL</h3>
        <div id="show_full_url">        
            <input id="short_url_display" type="text" readonly="true" class="profile_name" style="width: 157px; padding: 0px 0px 0px 18px; text-align: left;" value="http://<?=current_domain();?>/<?=short_url_code($service_id,'service');?>">
        </div>
        <div id="show_edit_url" style="display:none">        
            <form>
                <input type="text"  readonly="true" class="profile_name" style="width: 109px; float: left; margin-right: 0px; padding-left: 18px;" value="http://<?=current_domain();?>/">
                <input id="shorturl_val" maxlength="6" name="shorturl" type="text" style="width: 50px; padding: 0px; float: left; margin-left: -2px; text-align: left; margin-right: 0px;" class="profile_name" value="<?=short_url_code($service_id,'service');?>">
            </form>
        </div>
</div>
<?else:?>

<script id="short_url_script" type="text/javascript">
    $(document).ready(function(){
        $('#show_full_url').bind('click',function(){
            $("#short_url_display").select();    
        });
        
    });
    
</script>

    <div class="panel_info">
            <h3 align="center" class="botmar">Short URL</h3>
            <div id="show_full_url" rel="xx">        
                <input id="short_url_display" type="text" readonly="true" class="profile_name" style="width: 157px; padding: 0px 0px 0px 18px; text-align: left;" value="http://<?=current_domain();?>/<?=short_url_code($service_id,'service');?>">
            </div>
    </div>
<? endif; ?>