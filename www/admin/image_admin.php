<?php

$root = $_SERVER['DOCUMENT_ROOT'];

require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
//include style sheets
require_once("$root/includes/css.inc.php");
//page title
echo "<title>Image Admin</title>";
//include all script libraries
require_once("$root/includes/script_libraries.inc.php");

?>
<script type="text/javascript">

//FIX: Leaving page with 'Close' button after saving image, but returning to new unsaved vintage results in image being lost
$(document).ready(function(){

    $("#btn_delete_images").click(function(){
        var var_fields = $(".images").serializeArray();
        console.log(var_fields);
        var json_field = JSON.stringify(var_fields);
        console.log(json_field);

        $.post("/vintage/rpc_vintage.php", {
            action: 'bulk_delete',
            json_field: json_field,
            dir:    "/images/labels/"
        }, function(data){
            if(data.success){
                console.log('bulk_delete OK')
                //reload page
                window.location.reload(true)
            } else {
                console.log('bulk_delete Failed. error='+data.error)

            }
        }, "json");

    });


    $("#btn_correct_records").click(function(){
        var var_fields = $(".files").serializeArray();
        console.log(var_fields);
        var json_field = JSON.stringify(var_fields);
        console.log(json_field);

        $.post("/vintage/rpc_vintage.php", {
            action: 'image_correct_records',
            json_field: json_field
        }, function(data){
            if(data.success){
                console.log('correct_records OK')

            } else {
                console.log('correct_records error='+data.error)

            }
        }, "json");

    });


    $("#btn_select_all").click(function(){
        $('.images').attr('checked','checked')
    });


    $("#btn_select_all_records").click(function(){
        $('.files').attr('checked','checked')
    });


    $(".reduce_image").click(function(){
        console.log($(this).attr('id'));

        var image_name = $(this).attr('id');
        $.post("/vintage/rpc_vintage.php", {
            action: 'reduce_image',
            image_name: image_name
        }, function(data){
            if(data.success){
                console.log('reduce_image OK')

            } else {
                console.log('reduce_image error='+data.error)

            }
        }, "json");
    });

});

</script>

</head>

<body>


<?php


    function reduceImage($img, $imgPath, $suffix, $quality)
    {
        // Open the original image.
        $original = imagecreatefromjpeg("$imgPath/$img") or die("Error Opening original (<em>$imgPath/$img</em>)");
        list($width, $height, $type, $attr) = getimagesize("$imgPath/$img");

        // Resample the image.
        $tempImg = imagecreatetruecolor($width, $height) or die("Cant create temp image");
        imagecopyresized($tempImg, $original, 0, 0, 0, 0, $width, $height, $width, $height) or die("Cant resize copy");

        // Create the new file name.
        $newNameE = explode(".", $img);
        $newName = ''. $newNameE[0] .''. $suffix .'.'. $newNameE[1] .'';

        // Save the image.
        imagejpeg($tempImg, "$imgPath/$newName", $quality) or die("Cant save image");

        // Clean up.
        imagedestroy($original);
        imagedestroy($tempImg);
        return true;
    }


    function resizeImage($img, $imgPath, $suffix, $by, $quality)
    {
        // Open the original image.
        $original = imagecreatefromjpeg("$imgPath/$img") or die("Error Opening original (<em>$imgPath/$img</em>)");
        list($width, $height, $type, $attr) = getimagesize("$imgPath/$img");

        // Determine new width and height.
        $newWidth = ($width/$by);
        $newHeight = ($height/$by);

        // Resample the image.
        $tempImg = imagecreatetruecolor($newWidth, $newHeight) or die("Cant create temp image");
        imagecopyresized($tempImg, $original, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height) or die("Cant resize copy");

        // Create the new file name.
        $newNameE = explode(".", $img);
        $newName = ''. $newNameE[0] .''. $suffix .'.'. $newNameE[1] .'';

        // Save the image.
        imagejpeg($tempImg, "$imgPath/$newName", $quality) or die("Cant save image");

        // Clean up.
        imagedestroy($original);
        imagedestroy($tempImg);
        return true;
    }


    echo "<h1>Image Admin</h1>";

    echo "<hr>";

    $obj_vintage = new vintage();
    $where = "image1 > ''";
    $var_result = $obj_vintage -> get_extended($where);

    echo "<h2>Vintage Records with Missing Image Files:</h2>";
    $n = 0;
    if($var_result){
        foreach($var_result as $vintage){

            $vintage_id = $vintage['vintage_id'];
            $wine = $vintage['wine'];
            $year = $vintage['year'];
            $image = $root.$label_path.$vintage['image1'];
            if(file_exists($image)){
                //echo " >> File Found";//do nothing
                $n += 1;
            }else{
                echo "<input type=\"checkbox\" class=\"files\" name=\"$vintage_id\" value=\"$vintage_id\" id=\"$vintage_id\" />";
                echo "wine=$wine year=$year vintage_id=$vintage_id image1=".$vintage['image1']."<br/>";
                $y += 1;
            }

        }
    }

    If($y < 1){
        echo "No vintages with missing images\n";
    }

    echo "<hr/>";
    echo "$n vintages were matched with their images<br/>";
    echo "<input type=\"button\" id=\"btn_select_all_records\" value=\"Select All\" />";
    echo "<input type=\"button\" id=\"btn_correct_records\" value=\"Correct Records\" />";

    echo "<hr/>";

/*
 * Images NOT associated with a record
 */
    $dir = $root."/images/labels/*";
    echo "<h2>Image Files NOT Associated with a Record:</h2>";
    
    //ignore array - do not show files that match this array
    $var_ignore = array("rpc_image_uploader.php","uploads");
 
    //read all files
    $m = 0;
    foreach(glob($dir) as $file) {
        $path_parts = pathinfo($file);
        $basename = $path_parts['basename'];

        $obj_vintage = new vintage();
        $where = "image1 = '$basename'";
        $var_result = $obj_vintage -> get($where);

        if($var_result){
            //record was matched with image
            $m += 1;
        } else {
            //no matching record found - check for none image files
            $key = array_search($basename, $var_ignore);
            if($key===false){ //file was not in ignore list
                echo "<input type=\"checkbox\" class=\"images\" name=\"$basename\" value=\"$basename\" id=\"$basename\" />";
                echo "filename: $basename : filetype: " . filetype($file) . "<br />";
                $x += 1;
            }

        }

    }

    if($x < 1){
        echo "0 images NOT associated with vintages";
    }


    echo "<hr/>";
    echo "$n image files were associated with vintage records<br/>";
    echo "<input type=\"button\" id=\"btn_select_all\" value=\"Select All\" />";
    echo "<input type=\"button\" id=\"btn_delete_images\" value=\"Delete Unassociated Files\" />";

    echo "<hr/>";
    echo "<h2>All Image Details</h2>";

    $dir = $root."/images/labels/*";

    
/*
 * Display Images
 */    
    //read all files
        $n = 0;
    foreach(glob($dir) as $file) {
        $path_parts = pathinfo($file);
        $basename = $path_parts['basename'];
        $image_path = $new_root.$label_path.$basename;
        $var_fileSize = filesize($image_path);
        $fileSize = round($var_fileSize/1024,0);
        $var_size = getimagesize("$image_path");
        //echo "<br/>basename=".$basename;
        //$filename = $path_parts['filename'];
        //echo "<br/>filename=".$filename;
        $image_name = $label_path.$basename;
        //echo "<br/>image_name=$image_name";
        $obj_vintage = new vintage();
        $where = "image1 = '$filename'";
        $var_result = $obj_vintage -> get($where);

        if($fileSize > 75){
            echo "<div id=\"con_image\" style=\"background-color:lightgray; padding:10px; margin-top:10px; margin-left:15px; \" >";


                echo "<div id=\"div_image\" style=\"width:350px; height:450px; float:left; text-align:center; margin-left:5px; padding:5px; border: solid 2px black;\"  >";
                    echo "<img src=\"$image_name\" height=\"350\" >";


                    echo "<div id=\"div_details\" style=\"width:350px; float:left; background-color:;\">";
                        echo "<br/>$basename";

                        //print_r($var_size);
                        echo "<br/>".$var_size[3];
                        $var_fileSize = filesize($image_path);
                        $fileSize = round($var_fileSize/1024,0);
                        echo "<br/>image type = ".image_type_to_mime_type($var_size[2]);
                        echo "<br/>size = $fileSize kb";
                        if($fileSize > 75){
                            echo "<br/><input type=\"button\" class=\"reduce_image\" id=\"$basename\" value=\"Create Reduced Image\" />";
                        }
                    echo "</div>";

                echo "</div>";


                if($fileSize > 50){
                    $color = 'red';
                }else{
                    $color = 'green';
                }
                echo "<div id=\"div_resized\" style=\"width:200px; height:200px; float:left; text-align:center; margin-left:10px; padding:5px; border: solid 2px black; background-color:$color;\">";

                    echo "<img ".fnImageResize($image_name, $new_root, 200)." />";
                echo "</div>";


                echo "<div id=\"div_reduced\" style=\"width:350px; height:450px; float:left; text-align:center; margin-left:10px; padding:5px; border: solid 2px black; \">";
                    $path = "/images/temp/";
                    //echo $path.$basename;
                    if(file_exists($new_root.$path.$basename)){
                        echo "<img src=\"$path"."$basename\" width=\"200\" />";

                    echo "<div id=\"div_reduced_details\" style=\"text-align:center;\" >";
                        $image_path = $new_root.$path.$basename;
                        $var_size = getimagesize("$image_path");
                        //print_r($var_size);
                        echo "<br/>".$var_size[3];
                        $var_fileSize = filesize($image_path);
                        $fileSize = round($var_fileSize/1024,0);
                        echo "<br/>size = $fileSize kb";
                    echo "</div>";


                    }else{
                        echo "No reduced file<br/>";
                    }





                echo "</div>";



                echo "<div class=\"clear\"></div>";
            echo "</div>";
        }
        //echo "<input type=\"checkbox\" class=\"images\" name=\"$basename\" value=\"$basename\" id=\"$basename\" />";
        //echo "filename: $filename : filetype: " . filetype($file) . "<br />";


    }

?>

</body>
</html>

<img src="" />
