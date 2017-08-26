<?php

$root = $_SERVER['DOCUMENT_ROOT'];

require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
require_once("$root/classes/class.wine_search.php");

echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
//include style sheets
require_once("$root/includes/css.inc.php");
//page title
echo "<title>Available Admin</title>";
//include all script libraries
require_once("$root/includes/script_libraries.inc.php");

?>
<script type="text/javascript">

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
    echo "<h1>Available Admin</h1>";
    echo "<hr>";
    
    //add ?action=update to execute
    // will set override records to set available bottles for all vintages to zero

    $action = $_REQUEST['action'];
    $password = $_REQUEST['password'];

    echo "<h2>GET LIST OF VINTAGES WITH AVAILABLE BOTTLES:</h2>";
    if($action == 'update'){
       echo "Progressing with DB Update<br/>";
       $update = true;
    }

    $query = "SELECT
                tblVintage.vintage_id,
                tblVintage.year,
                tblVintage.vintage_notes,
                tblVintage.image1,
                tblVintage.vintage_quality,
                tblVintage.vintage_value,
                tblVintage.alcohol,
                tblVintage.closure_id,
                tblVintage.drink_year_from,
                tblVintage.drink_year_to,
            (
                acquisitions.purchased - IFNULL(notes.opened, 0) - IFNULL(override.override, 0)
            ) available
            FROM
                tblVintage
            LEFT JOIN
                (
                SELECT
                    vintage_id,
                    SUM(trelVintageHasAcquire.qty) purchased
                FROM
                    trelVintageHasAcquire
                GROUP BY
                    vintage_id
            ) AS acquisitions
            ON
                acquisitions.vintage_id = tblVintage.vintage_id
            LEFT JOIN
                (
                SELECT
                    vintage_id,
                    IFNULL(COUNT(tblNotes.note_id),
                    0) opened
                FROM
                    tblNotes
                GROUP BY
                    vintage_id
            ) AS notes
            ON
                notes.vintage_id = tblVintage.vintage_id
            LEFT JOIN
                (
                SELECT
                    vintage_id,
                    override
                FROM
                    tblAvailableOverride
            ) AS override
            ON
                override.vintage_id = tblVintage.vintage_id
            HAVING
                available > 0";

 $qry_result = mysql_query($query);

if($qry_result){
    while ($row = mysql_fetch_assoc($qry_result)) {
        $data_array[] = $row;
        echo "vintage_id : ".$row['vintage_id']." Available : ".$row['available'];
            $available = $row['available'];
            $where = " vintage_id = ".$row['vintage_id']." ";
            //check if record exists
            $obj_available = new available_override();
            $rst = $obj_available ->get($where);
            $override = $rst[0]['override'];
            echo " Override = $override";
            $new_override = $override+$available;
            echo " New Override = $new_override";
            $count = $obj_available ->count();
            if($count >0){
               echo " record exists - update";
               $set = " override = ".$new_override;
               if($update){ //update action
                    $result = $obj_available -> update_custom($set, $where);
                    if(!$result){
                        echo " An error occurred - ".$obj_available ->get_sql_error();
                    }else{
                        echo " Update successful";
                    }
               }
            } else {
                echo " record does not exist - insert ";
                $input_array['vintage_id'] = $row['vintage_id'];
                $input_array['override'] = $row['available'];
                $input_array['user'] = 1;
                if($update){ //update action
                    $result = $obj_available ->insert($input_array);
                    if(!$result){
                        echo " An error occurred - ".$obj_available ->get_sql_error();
                    }else{
                        echo " Insert successful";
                    }
                }
            }

        echo "<br/>";
    }
} else {
    echo "sql_error: ".mysql_error();

}


    


?>

</body>
</html>