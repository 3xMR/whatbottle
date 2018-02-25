<?php
/* 
 * html for tasting note form
 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");

?>
<style>
    .text_area{
        padding:3px;
        width:90%; 
        height:70px;
        font-family: calibri, arial;
        font-size:19px;
        color:gray;
    }
    
</style>

<script type="text/javascript">

$(document).ready(function(){

//***functions***

    function old_refresh_all_notes(){
        var vintage_id = $('#vintage_id').val();
        $('#all_notes_content').load('/vintage/rpc_all_notes_html.php', {vintage_id: vintage_id}, function(){
 
       });
    }


//***Initialise***

    //refresh html
    //refresh_all_notes();

    //enable or disable delete button
    if($('#note_id').val()>0){
        //enable delete
        $("#btn_delete").removeAttr('disabled');
    } else {
        $("#btn_delete").attr('disabled', 'disabled');
    }


//***form controls & buttons***


    //update hidden fields when star rating selected
    $('.auto-submit-star').rating({
        callback: function(value, link){
            if(!value>0){
                value = 0;
            }
            console.log('note_quality = '+value);
            $("#note_quality").val(value);
        }
    });

    
    $('.auto-submit-pound').rating_pound({
        callback: function(value, link){
            if(!value>0){
                value = 0;
            }
            console.log('note_value = '+value);
            $("#note_value").val(value);
        }
    });
    

   //date picker
   $('#note_date').datepicker({ dateFormat: 'dd-M-yy' });

   //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    })
  
  
    //***validation***
    $("#frm_note").validate({
    });

    //TODO: issue with date being seen as valid in IE
    jQuery.validator.addClassRules("date", {
        required: true,
        date: true
    });

    jQuery.validator.addClassRules("required", {
        required: true
    });

});

</script>

<?php

//debug info
$debug = false;


//authenticated?
if(is_authed()){
    $disabled = null;
}else{
    $disabled = "disabled='disabled'";
}

//default to continue
$bln_continue = true;

//get note_id
$note_id = $_SESSION['var_note']['note_id'];

//get vintage_id
if($note_id>0){ //editing an existing note
    $is_new_note = false;
   //check vintage is identified
    $vintage_id = $_SESSION['var_note']['vintage_id'];
    
    if(!$_SESSION['var_note']['vintage_id']>0){
        //exit
        $bln_continue = false;
    }
} else { //new note - not saved yet
    if($debug){echo "<br/>[tasting_note.php] => note_id not identified - new note";}
    $is_new_note = true;
    if($_SESSION['var_note']['vintage_id']>0){
        //vintage_id provided in session
        $vintage_id = $_SESSION['var_note']['vintage_id'];
        if($debug){echo "<br/>[tasting_note.php] => vintage_id identified in session $vintage_id";}
    } else {
        //check if vintage provided in REQUEST
        if($_REQUEST['vintage_id']>0){
            $vintage_id = $_REQUEST['vintage_id'];
            if($debug){echo "<br/>[tasting_note.php] => vintage_id identified from REQUEST $vintage_id";}
        } else {
            //could not identify vintage
            if($debug){echo "<br/>[tasting_note.php] => vintage_id not identified - critical error";}
            $bln_continue = false;
        }
    }
}


if(!$bln_continue){
    echo "<br/>redirect - insufficient info to continue";
    exit;
}

//get data from session
$note_date = $_SESSION['var_note']['note_date'];
$note_appearance = $_SESSION['var_note']['note_appearance'];
$note_aroma = $_SESSION['var_note']['note_aroma'];
$note_taste = $_SESSION['var_note']['note_taste'];
$note_general = $_SESSION['var_note']['note_general'];
$note_quality = $_SESSION['var_note']['note_quality'];
$note_value = $_SESSION['var_note']['note_value'];
$fullness_id = $_SESSION['var_note']['fullness_id'];
$sweetness_id = $_SESSION['var_note']['sweetness_id'];



//set default date if new note
if($is_new_note){
    $note_date = date("d-M-Y");
}

if($note_quality>0){
    $quality = 'quality_'.($note_quality);
    $$quality = "checked = checked";
}

if($note_value>0){
    $value = 'value_'.($note_value);
    $$value = "checked = checked";
}


echo "<div class=\"rwd-con-100\" >";

    echo "<div class=\"vertical-centre input-main-inline\" style=\"float:left; height:2em; display:block; margin-top:15px; margin-right:30px;\" >";
        echo "<p>Date</p>";
        echo "<input type=\"text\" style=\"width:150px; margin-left:5px;\" class=\"date input-main-input\" name=\"note_date\" id=\"note_date\" value=\"$note_date\" $disabled/>";
    echo "</div>";

    echo "<div class=\"vertical-centre input-main-label\" style=\"margin-right:30px; margin-top:15px; float:left; height:2em; display:block; \" >";//quality rating
        echo "<p style=\"width:45px;\" >Quality</p>";
        echo "<div class=\"rating\" style=\"width:192px; height:32px; display:block; float:left\" $disabled >";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"1\" class=\"auto-submit-star {split:2}\" title=\"Undrinkable\" $quality_1 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"2\" class=\"auto-submit-star {split:2}\" title=\"Terrible\" $quality_2 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"3\" class=\"auto-submit-star {split:2}\" title=\"Very Poor\" $quality_3 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"4\" class=\"auto-submit-star {split:2}\" title=\"Poor\" $quality_4 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"5\" class=\"auto-submit-star {split:2}\" title=\"OK\" $quality_5 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"6\" class=\"auto-submit-star {split:2}\" title=\"Reasonable\" $quality_6 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"7\" class=\"auto-submit-star {split:2}\" title=\"Good\" $quality_7 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"8\" class=\"auto-submit-star {split:2}\" title=\"Very Good\" $quality_8 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"9\" class=\"auto-submit-star {split:2}\" title=\"Excellent\" $quality_9 $disabled/>";
            echo "<input name=\"note_quality\"  type=\"radio\" value=\"10\" class=\"auto-submit-star {split:2}\" title=\"Outstanding\" $quality_10 $disabled/>";
        echo "</div>";
    echo "</div>";

    echo "<div class=\"vertical-centre input-main-label\" style=\"margin-top:15px; float:left; height:2em; display:block; \" >";
        echo "<p style=\"width:45px;\" >Value</p>";
        echo "<div class=\"rating\" style=\"width:192px; height:32px; display:block; float:left;\" >";
            echo "<input name=\"note_value\" type=\"radio\" value=\"1\" class=\"auto-submit-pound \" title=\"Poor\" $value_1 $disabled/>";
            echo "<input name=\"note_value\" type=\"radio\" value=\"2\" class=\"auto-submit-pound \" title=\"OK\" $value_2 $disabled/>";
            echo "<input name=\"note_value\" type=\"radio\" value=\"3\" class=\"auto-submit-pound \" title=\"Good\" $value_3 $disabled/>";
            echo "<input name=\"note_value\" type=\"radio\" value=\"4\" class=\"auto-submit-pound \" title=\"Very Good\" $value_4 $disabled/>";
            echo "<input name=\"note_value\" type=\"radio\" value=\"5\" class=\"auto-submit-pound \" title=\"Excellent\" $value_5 $disabled/>";
        echo "</div>";
    echo "</div>";
    
    echo "<div class=\"clear\"></div>";

echo "</div>"; //top_row

//display tasting note form - star ratings mess up validation so place form after them
echo "<form action=\"#\" method=\"post\" name=\"frm_note\" id=\"frm_note\" >";

//hidden fields
echo "<input type=\"hidden\" name=\"note_id\" id=\"note_id\" value=\"$note_id\" />";
echo "<input type=\"hidden\" name=\"vintage_id\" id=\"vintage_id\" value=\"$vintage_id\" />";
echo "<input type=\"hidden\" name=\"note_quality\" id=\"note_quality\" value=\"$note_quality\" />";
echo "<input type=\"hidden\" name=\"note_value\" id=\"note_value\" value=\"$note_value\" />";

echo "<div id=\"first_column\" class=\"rwd-con-50\" >";

    echo "<div class=\"input-main-label\" style=\"margin-top:20px;\" >"; //margin to align within inline element in column 2
        echo "<label>Appearance</label>";
    echo "</div>";
    echo "<div class=\"input-main\" style=\"overflow:hidden;\" >";
        echo "<textarea class=\"text_area\" style=\"width:95%; height:75px;\" value=\"$note_appearance\" name=\"note_appearance\" id=\"note_appearance\" $disabled >$note_appearance</textarea>";
    echo "</div>";
    
    echo "<div class=\"input-main-label\" style=\"margin-top:10px;\" >";
        echo "<label>Aroma</label>";
    echo "</div>";
    echo "<div class=\"input-main\" style=\"overflow:hidden;\" >";
    echo "<textarea class=\"text_area\" style=\"width:95%; height:75px;\" value=\"$note_aroma\" name=\"note_aroma\" id=\"note_aroma\" $disabled >$note_aroma</textarea>";
    echo "</div>";
    
    echo "<div class=\"input-main-label\" style=\"margin-top:10px;\" >";
        echo "<label>Taste</label>";
    echo "</div>";
    echo "<div class=\"input-main\" style=\"overflow:hidden;\" >";
        echo "<textarea class=\"text_area\" style=\"width:95%; height:75px;\" value=\"$note_taste\" name=\"note_taste\" id=\"note_taste\" $disabled >$note_taste</textarea>";
    echo "</div>";
    
    echo "<div class=\"input-main-label\" style=\"margin-top:10px;\" >";
        echo "<p>General Notes</p>";
    echo "</div>";
    echo "<div class=\"input-main\" style=\"overflow:hidden;\" >";
        echo "<textarea class=\"text_area\" style=\"width:95%; height:75px;\" value=\"$note_general\" name=\"note_general\" id=\"note_general\" $disabled >$note_general</textarea>";
    echo "</div>";
echo "</div>"; //first_column



echo "<div id=\"second_column\" class=\"rwd-con-50\" >";
    
    echo "<div style=\"float:right; width:100%; margin-top:20px;\" >";
        echo "<div class=\"input-main-inline vertical-centre\" >";
            echo "<label style=\"width:100px;\" >Fullness</label>";
            echo "<select class=\"fullness required\" name=\"fullness_id\" id=\"fullness_id\" style=\"width:210px; margin-left:5px;\" $disabled>";
                echo "<option value=\"0\">";
                $obj = new fullness();
                $sort = "fullness_id ASC";
                $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort, $limit=false);
                foreach($var_results as $var_result){
                    //fullness dropdown
                    $item = $var_result['FullnessRating'];
                    $key = $var_result['fullness_id'];
                    if($key==$fullness_id){
                        echo ("<option value=".$key." SELECTED>".$item);
                    } else {
                        echo ("<option value=".$key.">".$item);
                    }

                }
            echo "</select>";
        echo "</div>";

        echo "<div class=\"input-main-inline vertical-centre\" style=\"margin-top:10px;\" >";
            echo "<label style=\"width:100px;\" >Sweetness</label>";
            echo "<select class=\"sweetness\" name=\"sweetness_id\" id=\"sweetness_id\" style=\" width:210px; margin-left:5px;\" $disabled>";
                echo "<option value=\"0\">";
                $obj = new sweetness();
                $sort = "sweetness_id ASC";
                $var_results = $obj -> get($where=false, $columns=false, $group=false, $sort, $limit=false);
                foreach($var_results as $var_result){
                    //sweetness dropdown
                    $item = $var_result['SweetnessRating'];
                    $key = $var_result['sweetness_id'];
                    if($key==$sweetness_id){
                        echo ("<option value=".$key." SELECTED>".$item);
                    } else {
                        echo ("<option value=".$key.">".$item);
                    }
                }
            echo "</select>";
        echo "</div>";

        echo "<div style=\"clear:both; float:left; margin-top:45px;\" id=\"con_all_notes\"  >";
                //listBox populated by /vintage/rpc_all_notes_html.php
                //jquery object on /vintage/rpc_note_form_html.php
        echo "</div>";
    echo "</div>";

echo "</div>"; //second_column

   

    
echo "<div class=\"clear\" ></div>";


echo "</form>";

?>
