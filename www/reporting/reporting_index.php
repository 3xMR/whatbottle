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


echo "<title>Reports</title>";

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
                    echo "<img src=\"/images/report_grey_256.png\" height=\"36px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:none; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Reporting</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left;\"  >";
                    //echo "<img id=\"process_indicator\" src=\"/images/ajax_loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";
        echo "</div>"; //con_title_bar
        
        //Left Column
        echo "<div id=\"vintage_form_content\" >";
        
            echo "<div style=\"background-color:#fafafa; display:block; width:100%; float:left; padding:3px 0px 3px 3px; border-bottom:solid 1px lightgray;\" >";
                echo "<p>Filters</p>";
                echo "<img class=\"expand_button click\" style=\"position:absolute; right:10px; margin-top:-9px; margin-right:12px; background-color:; \" src=\"/images/plus_black_256.png\" height=\"24px\" data-panel=\"panel_filter\">";
            echo "</div>";
            echo "<div style=\"background-color:lightgreen; display:none; width:100%; float:left;\" id=\"panel_filter\">";
                echo "hello moto<br/><br/><br/><br/><br/><br/><br/>";
            echo "</div>";
            
            echo "<div style=\"background-color:#fafafa; display:block; width:100%; float:left; padding:3px 0px 3px 3px; border-bottom:solid 1px lightgray;\" >";
                echo "<p>Key Statistics</p>";
                echo "<img class=\"expand_button click\" style=\"position:absolute; right:10px; margin-top:-9px; margin-right:12px; background-color:; z-index:100; \" src=\"/images/plus_black_256.png\" height=\"24px\" data-panel=\"panel_stats\">";
            echo "</div>";
            
            ?> 
            <div style="display:none; width:100%; float:left; padding:8px 0px 3px 3px;" id="panel_stats">
                <div class="rwd-con-33" style="">
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/021-wine.png" style="display:block; float:left; margin-right:5px;" height=50px >
                        <div style="float:left;">
                            <span id="stat_total_wine" style="display:block; line-height:28px; font-size:26px;" > - </span>
                            <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">wines</span>
                        </div>
                    </div>
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/021-wine-10.png" style="display:block; float:left; margin-right:5px;" height=50px >
                        <div style="float:left;">
                            <span id="stat_total_vintage" style="display:block; line-height:28px; font-size:26px; "> - </span>
                            <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">vintages</span>
                        </div>
                    </div>
                </div>
                <div class="rwd-con-33" style="">
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/009-wine-3.png" style="display:block; float:left; margin-right:5px;" height=48px >
                        <div style="float:left;">
                            <span id='stat_total_bottle' style="display:block; line-height:28px; font-size:26px;"> - </span>
                            <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">bottles</span>
                        </div>
                    </div>
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/008-wine-2.png" style="display:block; float:left; margin-right:5px;" height=50px >
                        <span id="stat_total_note" style="display:block; line-height:28px; font-size:26px;"> - </span>
                        <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">notes</span>
                    </div>
                </div>
                <div class="rwd-con-33" style="">
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/032-wine-14.png" style="display:block; float:left; margin-right:7px;" height=50px >
                        <div style="float:left;">
                            <span id="stat_total_acquisition" style="display:block; line-height:28px; font-size:26px;"> - </span>
                            <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">acquisitions</span>
                        </div>
                    </div>
                    <div class="rwd-con-50" style="position:relative;">
                        <img src="/images/040-corkscrew-1.png" style="display:block; float:left; margin-right:5px; " height=50px >
                        <div style="float:left;">
                            <span id="stat_total_available" style="display:block; line-height:28px; font-size:26px;"> - </span>
                            <span style="display:block; line-height:12px; font-size:12px; padding-left:3px;">available</span>
                        </div>
                    </div>
                </div>
            </div>
         
            
            <div style="float:left; margin-top:10px" >
                <p>Wines</P><br>

                <div style="width:400px;">
                    <canvas id="chart_one" width="200" height="200"></canvas>
                </div>

                <div style="width:400px;">
                    <canvas id="chart_two" width="400" height="400"></canvas>
                </div>
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
    
    
    $.post("/reporting/rpc_report.php", {
        action: 'get_all_stats',
        from_date: null,
        to_date: null
    }, function(data){
        if(data.success){
            console.log(data);
            load_stats(data);
        } else {
            console.log('get_all_stats failed with error: '+data.error);
        }
    }, "json");
    
    
    function load_stats(data){
        console.log(data);
        $('#stat_total_wine').text(number_with_commas(data.wine_count));
        $('#stat_total_vintage').text(number_with_commas(data.vintage_count));
        $('#stat_total_bottle').text(number_with_commas(data.bottle_count));
        $('#stat_total_note').text(number_with_commas(data.note_count));
        $('#stat_total_acquisition').text(number_with_commas(data.acquisition_count));
        $('#stat_total_available').text(number_with_commas(data.available_count));
    }
    
    
    function chart_one(data){
        console.log(data);
        var ctx = document.getElementById('chart_one');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                    label: 'Top Countries by # of Wines',
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
    
    
    $(document).on('click', '.expand_button', function(event){
        toggle_panel($(this));
    });
    
    function toggle_panel(el){
        var panel = $(el).data('panel');
        if($(el).hasClass('open')){ //close panel
            $(el).removeClass('open').removeClass('closed').addClass('closed');
            $(el).attr('src','/images/plus_black_256.png');
            $('#'+panel).slideUp();
        }else{ //open panel
            $(el).removeClass('open').removeClass('closed').addClass('open');
            $(el).attr('src','/images/minus_black_256.png');
            $('#'+panel).slideDown();
        }
    }
    
    
    function number_with_commas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

}); //document.ready


</script>

</body>
</html>