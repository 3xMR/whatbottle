<?php
/*Master Vintage form for new and editing existing Vintages*/
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");
//require_once("$root/classes/Vintage.php");



echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
require_once("$root/includes/css.inc.php");//include style sheets


echo "<title>test?</title>";

echo "</head>";

echo "<body>";

//page
echo "<div class=\"page_container\">";

    //header
    require_once("$root/includes/nav/topheader.php");

    //wine_form
    echo "<div class=\"con_single_form rounded\" >";
        
        //Title bar
        echo "<div class=\"con_title_bar\" >";
            //wine name
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px;\" >";
                echo "<div style=\"float:left; width:48px; margin-right:10px;\" >";
                    echo "<img src=\"/images/vintage_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:none; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Test</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left;\"  >";
                    //echo "<img id=\"process_indicator\" src=\"/images/ajax_loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar
        
        //Left Column
        echo "<div id=\"vintage_form_content\" >";
            echo "Test Charts<br>";
  
            //$obj = new list_has_vintage();
            
//            $vintage_id = 354;
//            $list_id = 0;
//            $rst = $obj->add_vintage_to_list($vintage_id);
//               $vintage_id = 423;
//            $list_id = 0;
//            $rst = $obj->add_vintage_to_list($vintage_id);
//               $vintage_id = 609;
//            $list_id = 0;
//            $rst = $obj->add_vintage_to_list($vintage_id);
//               $vintage_id = 665;
//            $list_id = 0;
//            $rst = $obj->add_vintage_to_list($vintage_id);
//               $vintage_id = 1225;
//            $list_id = 0;
//            $rst = $obj->add_vintage_to_list($vintage_id);
            //$rst = $obj->remove_vintage_from_list($index, $vintage_id, $list_id);
            //$rst = $obj->clear_list($list_id);
//            $rst = $obj->get_list_contents();
//            $count = $obj->count_in_list();
//            if($rst===false){
//                echo "Error: ".$obj->get_sql_error()."<br>";
//            }else{
//                echo 'Success!<br/>';
//                echo "Count=$count<br/>";
//                print_r($rst);
//            }
//            echo "<br>";
        ?>
        
        <canvas id="myChart" width="400" height="400"></canvas>
        
        <?php
            
        echo "</div>";
        
        //Button Bar
        echo "<div class=\"con_button_bar\" >";
            if( is_authed() ){
                echo "<input type=\"button\" id=\"btn_save\" value=\"Save\" />";
                echo "<input type=\"button\" id=\"btn_delete\" value=\"Delete\" />";
            }  
            echo "<input type=\"button\" id=\"btn_close\" value=\"Close\" class=\"btn_close\" />";
        echo "</div>";

        //clear page_container
        echo "<div class=\"clear\"></div>";
    
    echo "</div>";


echo "</div>"; //page_container
    
//include all script libraries
require_once("$root/includes/script_libraries.inc.php"); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.0/chart.min.js"></script>
<script type="text/javascript" src="/libraries/jquery.page_control_1.4.js"></script>
<script type="text/javascript">


$(document).ready(function(){
    
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Yellow', 'Green', 'Purple', 'Orange', 'Dave'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3,25],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    

}); //document.ready


</script>

</body>
</html>