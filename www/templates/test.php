<?php
/*Master Vintage form for new and editing existing Vintages*/
header('Content-Type: text/html; charset=utf-8');
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


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
            
            
//            class MyDB {
//                protected $db;
//                function __construct() {
//                    $this->db = MyPDO::instance();
//                }
//                
//                public function update(){
//                    $field = 'drink_year_to';
//                    $value = 5;
//                    $value = empty($value) ? null : $value;
//                    $var[$field] = $value;
//                    $param = PDO::PARAM_INT;
//                    print_r($var);
//                    print(key($var));
//                    $sql = "UPDATE tblVintage SET drink_year_to = :drink_year_to WHERE vintage_id = '1713';";
//                    $stmt = $this->db->prepare($sql);
//                    $stmt->bindValue(":$field",$value,$param);
//                    $stmt->execute();
//                }
//            }
//            
//           
//            
//            $obj = new MyDB();
//            $obj ->update();
//            
//            
//            //$stmt->bindvalue
            
    
//    foreach($var_param as $param){
//        print_r($param);
//        $parameter = $param['parameter'];
//        $value = empty($param['value']) ? Null : $param['value'];
//        $stmt->bindValue($parameter, 999, PDO::PARAM_INT);
//    }
    
    //print_r($assocUpdateArray);
    
   
            ?>
            <div style="width:400px;">
                <canvas id="chart_one" width="200" height="200"></canvas>
            </div>

            <div style="width:400px;">
                <canvas id="chart_two" width="400" height="400"></canvas>
            </div>


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
    
    
//    
//    var ctx = document.getElementById('myChart');
//    var myChart = new Chart(ctx, {
//        type: 'bar',
//        data: {
//            labels: ['Jan', 'Feb', 'Yellow', 'Green', 'Purple', 'Orange', 'Dave'],
//            datasets: [{
//                label: '# of Votes',
//                data: [12, 19, 3, 5, 2, 3,25],
//                backgroundColor: [
//                    'rgba(255, 99, 132, 0.2)',
//                    'rgba(54, 162, 235, 0.2)',
//                    'rgba(255, 206, 86, 0.2)',
//                    'rgba(75, 192, 192, 0.2)',
//                    'rgba(153, 102, 255, 0.2)',
//                    'rgba(255, 159, 64, 0.2)'
//                ],
//                borderColor: [
//                    'rgba(255, 99, 132, 1)',
//                    'rgba(54, 162, 235, 1)',
//                    'rgba(255, 206, 86, 1)',
//                    'rgba(75, 192, 192, 1)',
//                    'rgba(153, 102, 255, 1)',
//                    'rgba(255, 159, 64, 1)'
//                ],
//                borderWidth: 1
//            }]
//        },
//        options: {
//            scales: {
//                y: {
//                    beginAtZero: true
//                }
//            }
//        }
//    });
    
    
   
    



    
    
    $.post("/reporting/rpc_report.php", {
        action: 'get_all_stats',
        from_date: null,
        to_date: null
    }, function(data){
        if(data.success){
            //success
            console.log('get_all_stats success');
            console.log(data.data);
        } else {
            console.log('get_all_stats failed with error: '+data.error);
        }
    }, "json");
       
        
    $.post("/reporting/rpc_report.php", {
        action: 'get_wine_count_by_country',
        from_date: null,
        to_date: null
    }, function(data){
        if(data.success){
            chart_one(data.data);
        } else {
            console.log('get_acquisition_value failed with error: '+data.error);
        }
    }, "json");
    
            
//    $.post("/reporting/rpc_report.php", {
//        action: 'get_acquisition_qty_by_country',
//        from_date: null,
//        to_date: null
//    }, function(data){
//        if(data.success){
//            chart_two(data.data);
//        } else {
//            console.log('get_acquisition_qty_by_country() failed with error: '+data.error);
//        }
//    }, "json");
    
    
    
    function chart_one(data){
        console.log(data);
        var ctx = document.getElementById('chart_one');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                    label: 'Top Countries by Wine',
                    data: data
                }]
            },
            options: {
                parsing: {
                    xAxisKey: 'country',
                    yAxisKey: 'qty'
                }
//                scales: {
//                    y: {
//                        beginAtZero: true
//                    }
//                }
            }
        });
    }
    
    
    function chart_two(data){
        console.log(data);
        var ctx = document.getElementById('chart_two');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                    label: 'Top Countries by Acquisition',
                    data: data
                }]
            },
            options: {
                parsing: {
                    xAxisKey: 'country',
                    yAxisKey: 'qty'
                }
//                scales: {
//                    y: {
//                        beginAtZero: true
//                    }
//                }
            }
        });
    }
    

}); //document.ready


</script>

</body>
</html>