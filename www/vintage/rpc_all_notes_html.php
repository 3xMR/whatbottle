<?php
/* 
 * html to display all notes for selected vintage
 */
    header('Content-Type: text/html; charset=utf-8');
    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once("$root/includes/init.inc.php");
    require_once("$root/functions/function.php");
    require_once("$root/classes/class.db.php");
    
    $page = filter_input(INPUT_GET, 'page'); //parameter past as GET to identify host page

    
    if($page == 'vintage'){ //get vintage_id and show all notes on vintage.php
        $vintage_id = $_SESSION['var_vintage_temp']['vintage_id'];
        $note_id = 0; //show all notes
    }else{
        //assume tasting note page
        $vintage_id = $_SESSION['var_note']['vintage_id'];
        
        //do not include current note in listBox
        $note_id = $_SESSION['var_note']['note_id'];
        if(!$note_id){
            $note_id=0;   
        }
    }
    

   
    $obj = new tasting_note();
    $where = "vintage_id = $vintage_id AND note_id<>$note_id";
    $sort = "note_date DESC";
    $var_results = $obj -> get($where, $columns=false, $group=false, $sort, $limit=false);
    //print_r($var_results);

    if(!empty($var_results)){
        foreach($var_results as $var_result){
            //return list of award orgs
            $note_id = $var_result['note_id'];
            $note_date = $var_result['note_date'];
            $note_value = $var_result['note_value'];
            $rating_width_value = $note_value*16;
            $note_quality = $var_result['note_quality'];
            $rating_width_quality = $note_quality*8;

            //convert date
            if($note_date>0){
                $note_date = date_us_to_uk($note_date,'d-M-Y');
            } else {
                $note_date = null;
            }
            
            //echo "<div class=\"note_link listBox_row click\" id=\"$note_id\" style=\"margin-bottom:5px; font-size:11pt; width:100%; cursor:pointer;\">";
            echo "<div class=\"listBox_row click\" id=\"$note_id\" >";
                //echo "<input type=\"hidden\" class=\"listBox_value\" value=\"$note_id\" >";
                echo "<div class=\"vertical-centre\" style=\"height:18px;\" >";
                    echo "<div style=\"float:left; width:115px;\"><p>$note_date</p></div>";
                    echo "<div class=\"quality-static-rating-small\" style=\"float:left; margin-left:15px; width:".$rating_width_quality."px;\" ></div>";
                    echo "<div class=\"value-static-rating-small\" style=\"float:left; margin-left:5px; width:".$rating_width_value."px; \" ></div>";
                echo "</div>";
            echo "</div>";
            //echo "<div class=\"clear\"></div>";

        }//foreach
    } else {
        echo "<p style=\"margin-top:5px; margin-left:5px; font-size:inherit;\">None<p>";
    }


?>
