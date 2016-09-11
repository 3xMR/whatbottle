<?php


echo "<div class=\"left_column\" >";

    //Tasting Notes
    $obj = new tasting_note();
    $where = "vintage_id = $vintage_id";
    $sort = "note_date DESC";
    $var_results = $obj -> get($where, $columns=false, $group=false, $sort, $limit=false);

    if(!empty($var_results)){
        foreach($var_results as $var_result){
            //return list of award orgs
            $note_id = $var_result['note_id'];
            $note_date = $var_result['note_date'];
            $note_quality = $var_result['note_quality'];
            $rating_width = ($note_quality*8)."px";

            //convert date
            if($note_date>0){
                $note_date = date_us_to_uk($note_date,'d-M-Y');
            } else {
                $note_date = null;
            }

            echo "<div class=\"note_link\" id=\"$note_id\" style=\"float:left; clear:left; width:200px; margin-bottom:5px; cursor:pointer;\">";
                echo "<div  style=\"float:left; clear:left; width:100px; cursor:pointer;\" >";
                    echo "<span style=\"font-size:14px;\">$note_date </span><br/>";
                echo "</div>";
                echo "<div class=\"static-rating\" style=\"width:$rating_width; float:left;\"></div>";
            echo "</div>";
        }
    } else {
        echo "<span>None</span>";
    }

    echo "<div class=\"clear\"></div>";

echo "</div>"; //left_column

?>
