<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body style="width:550px !important;">
        <form id="frm_profilepic_upload" action="" method="post" enctype="multipart/form-data" >
        <?
                theme_jqUploader(array(
                    "upload_container"=> "upload_picture",
                    "field" =>  "user_pic",
                    "allow_maxUploadFiles" => 1,
                ));
             ?>
            <input type="submit" value="Apply">
    </form>   
    </body>
</html>
