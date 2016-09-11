<?php
/* 
 * COMMON FUNCTIONS
 *
 * fnDropDown - Single function for filling drop downs for wine and vintage fields
 *

 * date_uk_to_us
 * date_us_to_uk
 * producer_exists
 * producer_add - [req Authed] checks for existence of name and then adds producer to tblProducer
 * country_exists
 * region_exists
 * subregion_exists
 * region_parent
 * subregion_parent - returns parent region key for given subregion
 * wine_name_exists - checks wine_name & producer combination for pre-existence
 * valid_year - checks that year is a valid 4 digit year, false if not
 * get_wine_producer - returns array of response, producer_id and producer for given wine_id, false if no match
 * delete_temp_image - delete file based on session temp image name and provided path
 * get_wine - array, accepts wine_id or vintage_id, returns array or false if no match
 * wine_exists - bool, accepts wine_id, returns true if exists
 * put_wine - bool, accepts var_array for wine, returns true if update successful, inserts if new key, updates if exists
 * put_vintage - bool, accepts var_array for vintage, returns true if update successful, inserts if new key, updates if exists
 * fnImageResize - resize image dynamically
 */


function date_uk_to_american($date, $replace_separator = FALSE) {
  $days  = '0?[1-9]|[12][0-9]|3[01]';
  $months= '0?[1-9]|1[0-2]';
  $year  = '\d{2}|\d{4}';
  $non_alpha = '[^0-9a-zA-Z]+';
  return preg_replace( "/^\s*($days)($non_alpha)($months)($non_alpha)($year)/", $replace_separator === FALSE ? '$3$2$1$4$5' : '$3'.$replace_separator.'$1'.$replace_separator.'$5', $date);
}

function date_uk_to_us ($date, $format_string='Y-m-d') {
    return date($format_string, strtotime(date_uk_to_american($date, '-')));
}

function date_us_to_uk ($date, $format_string='d-m-Y'){
    return date($format_string, strtotime($date));
}

function fnFillDropDown($name,$selected) {
    Switch ($name){
            // select dropdown query based on $name
            case 'winetype':
                    $query ="SELECT DISTINCT winetype_id, winetype FROM tlkpWineType";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=''> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['winetype'];
                        $key = $row['winetype_id'];
                        if($key==$selected){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
            break;

            case 'producer':
                echo "<select name=\"producer\" id=\"producer\" onkeyup=\"lookup(this.value);\" class=\"$style\">";
                    $query ="SELECT DISTINCT producer_id, producer FROM tblproducer
                        ORDER BY producer ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['Name'];
                        $key = $row['producer_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'merchant':
                echo "<select name=\"merchant\" class=\"$style\">";
                    $query ="SELECT DISTINCT pkMerchant, Merchant FROM tblmerchant
                        ORDER BY Merchant ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['Merchant'];
                        $key = $row['pkMerchant'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'country':
                echo "<select name=\"country\" class=\"$style\" onchange=\"resubmit()\">";
                    $query ="SELECT DISTINCT country_id, country FROM tblcountry
                        ORDER BY country ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['country'];
                        $key = $row['country_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'region':
                if ($filter>0)
                {
                    $where = " WHERE country_id = $filter";
                } else {
                    $where = "";
                }

                echo "<select name=\"region\" class=\"$style\">";
                    $query ="SELECT DISTINCT region_id, region, country_id
                             FROM tblRegion
                             $where
                            ORDER BY region ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['region'];
                        $key = $row['region_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

             case 'subregion':
                if ($filter>0)
                {
                    $where = " WHERE region_id = $filter";
                } else {
                    $where = "";
                }

                echo "<select name=\"subregion\" class=\"$style\">";
                    $query ="SELECT DISTINCT subregion_id, subregion, region_id
                             FROM tblSubRegion
                             $where
                            ORDER BY subregion ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['subregion'];
                        $key = $row['subregion_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'acquisition':
                echo "<select name=\"acquisition\" class=\"$style\">";
                    $query ="SELECT tblAcquire.acquire_id, tblAcquire.Date, tblMerchant.Merchant
                             FROM tblAcquire
                             LEFT JOIN tblMerchant ON tblMerchant.pkMerchant = tblAcquire.merchant_id
                             ORDER BY Date DESC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['Merchant'].", ".$row['Date'];
                        $key = $row['acquire_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

        }//end switch

}

function fnDropDown($name, $selected_item, $style, $filter){
   
        Switch ($name){
            // select dropdown query based on $name
            case 'winetype':
                echo "<select name=\"winetype\" class=\"$style\">";
                    $query ="SELECT DISTINCT winetype_id, winetype FROM tlkpWineType";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['winetype'];
                        $key = $row['winetype_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'producer':
                echo "<select name=\"producer\" id=\"producer\" onkeyup=\"lookup(this.value);\" class=\"$style\">";
                    $query ="SELECT DISTINCT producer_id, producer FROM tblproducer
                        ORDER BY producer ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['producer'];
                        $key = $row['producer_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'merchant':
                echo "<select name=\"merchant\" class=\"$style\">";
                    $query ="SELECT DISTINCT pkMerchant, Merchant FROM tblmerchant
                        ORDER BY Merchant ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['Merchant'];
                        $key = $row['pkMerchant'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'country':
                echo "<select name=\"country\" class=\"$style\" onchange=\"resubmit()\">";
                    $query ="SELECT DISTINCT country_id, country FROM tblcountry
                        ORDER BY country ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['country'];
                        $key = $row['country_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'region':
                if ($filter>0)
                {
                    $where = " WHERE country_id = $filter";
                } else {
                    $where = "";
                }

                echo "<select name=\"region\" class=\"$style\">";
                    $query ="SELECT DISTINCT region_id, region, country_id
                             FROM tblRegion
                             $where
                            ORDER BY region ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['region'];
                        $key = $row['region_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

             case 'subregion':
                if ($filter>0)
                {
                    $where = " WHERE region_id = $filter";
                } else {
                    $where = "";
                }

                echo "<select name=\"subregion\" class=\"$style\">";
                    $query ="SELECT DISTINCT subregion_id, subregion, region_id
                             FROM tblSubRegion
                             $where
                            ORDER BY subregion ASC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['subregion'];
                        $key = $row['subregion_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

            case 'acquisition':
                echo "<select name=\"acquisition\" class=\"$style\">";
                    $query ="SELECT tblAcquire.acquire_id, tblAcquire.Date, tblMerchant.Merchant
                             FROM tblAcquire
                             LEFT JOIN tblMerchant ON tblMerchant.pkMerchant = tblAcquire.merchant_id
                             ORDER BY Date DESC";
                    $result = mysql_query($query) or die(mysql_error());
                    echo "<option value=0> ";
                    while($row = mysql_fetch_array($result))
                    {
                        $item = $row['Merchant'].", ".$row['Date'];
                        $key = $row['acquire_id'];
                        if($key==$selected_item){
                            echo ("<option selected value=".$key.">".$item);
                        }else{
                            echo ("<option value=".$key.">".$item);
                        }
                    }
                echo "</select>";
            break;

        }//end switch
        
    } //end function




function producer_exists($producer){
    //returns true if producer already exists

    $query = "SELECT *
    FROM tblProducer
    WHERE producer = '$producer'";
       
    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows>=1){
        while($row = mysql_fetch_array($result)){
            $producer_key = $row['producer_id'];
        }
        return $producer_key;
    }else{
        return False;
    }     
}


function producer_add($producer){
    //adds producer to tblProducer
    $response = false;
    
    //must be authed to make changes
    if(is_authed()){
        $user_id = $_SESSION['user_id'];
        //confirm that producer does not already exist
        $producer_exists = producer_exists($producer);
        if($producer_exists>0){
            //producer already exists - return existing key
            $response = $producer_exists;
        }else{
            //add new producer
            mysql_query("INSERT INTO tblProducer
            (producer,dtmCreated,user_id)
            VALUES ('$producer',Now(),$user_id)")
            or die(mysql_error());
            $response = mysql_insert_id();
            //return with key for new producer
            return $response;
        }
    }  
}


function country_exists($country){
    //returns country key if name exists - false if not
 
    //echo "<br/>function: country_exists - $country";
    $query = "SELECT *
    FROM tblCountry
    WHERE country = '$country'";

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        while($row = mysql_fetch_array($result)){
            $country_key = $row['country_id'];
        }
        return $country_key;
    } else {
        return 'False';
    }

}

function region_exists($region){
    //returns key if name exists - false if not

    $query = "SELECT *
    FROM tblRegion
    WHERE region = '$region'";

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        while($row = mysql_fetch_array($result)){
            $region_key = $row['region_id'];
        }
        return $region_key;
    } else {
        return 'False';
    }
}

function subregion_exists($subregion){
    //returns key if name exists - false if not

    $query = "SELECT *
    FROM tblSubRegion
    WHERE subregion = '$subregion'";

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        while($row = mysql_fetch_array($result)){
            $subregion_key = $row['subregion_id'];
        }
        return $subregion_key;
    } else {
        return 'False';
    }
}


function wine_name_exists($wine_name, $producer, $producer_id){
    //returns key if name and producer combination already exists - false if not

    if ($producer_id>0){
        //lookup using key
        $query = "SELECT wine_id, producer_id
        FROM tblWine
        WHERE Wine = '$wine_name'
        AND producer_id = $producer_id";
    }else{
        //lookup using name
        $query = "SELECT tblWine.wine_id, tblWine.producer_id, tblProducer.producer
        FROM tblWine
        LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id
        WHERE Wine = '$wine_name'
        AND producer = '$producer'";
    }

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows>=1){
        while($row = mysql_fetch_array($result)){
            $wine_key = $row['wine_id'];
        }
        return $wine_key;
    } else {
        return False;
    }
}

function region_parent($region_id){
    //returns country key for provided region key

    $query = "SELECT country_id
    FROM tblRegion
    WHERE region_id = '$region_id'";

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        while($row = mysql_fetch_array($result)){
            $country_key = $row['country_id'];
        }
        return $country_key;
    } else {
        return 'False';
    }
}

function subregion_parent($subregion_id){
    //returns country key for provided region key

    $query = "SELECT region_id
    FROM tblSubRegion
    WHERE subregion_id = '$subregion_id'";

    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        while($row = mysql_fetch_array($result)){
            $region_key = $row['region_id'];
        }
        return $region_key;
    } else {
        return 'False';
    }
}

function valid_year($year){
    //retuns true or false
    $min = 1000;
    $max = date("Y") + 100;

    $valid = false;

    if(strlen($year)==4){
        //confirm 4 digits are numbers
        if(preg_match("/[0-9]{4}/",$year)){
            //valid year
            $valid = true;
        }
    }
   
   return $valid;

}

function get_wine_producer($wine_id)
{
    //retrieve producer name for a given wine key
    $query = "SELECT tblProducer.producer, tblProducer.producer_id
                    FROM tblWine
                    LEFT JOIN tblProducer ON tblProducer.producer_id = tblWine.producer_id
                    WHERE tblWine.wine_id = $wine_id";
    $result = mysql_query($query) or die(mysql_error());
    
    if(mysql_num_rows($result)==1){
        $row = mysql_fetch_array($result) or die(mysql_error());
        //create return array
        $return = array('response'=>'True' );
        $return['producer']=$row['producer'];
        $return['producer_id']=$row['producer_id'];
        return $return;
    }else{
        return false;
    }
    
}


function save_wine($wine,$action='update'){
    //save wine record from array - default to update
    //!!! Assumes Validation Completed
    //action = 'update' or 'add'
    //return = wine_id or False

    //Require authentication to make changes
    if(is_authed()){
        //expand array
        $wine_name = $wine['wine_name'];
        $wine_type = $wine['winetype'];
        $key_producer = $wine['key_producer'];
        $key_country = $wine['key_country'];
        $key_region = $wine['key_region'];
        $key_subregion = $wine['key_subregion'];
        $user_id = $_SESSION['user_id'];

        if ($action=='Add'){
            //Create new record
            $query = "INSERT INTO tblWine (
                    wine,
                    winetype_id,
                    producer_id,
                    country_id,
                    region_id,
                    subregion_id,
                    created,
                    modified,
                    user_id
                    )
            VALUES (
                    '$wine_name',
                    '$wine_type',
                    '$key_producer',
                    '$key_country',
                    '$key_region',
                    '$key_subregion',
                     Now(),
                     Now(),
                    '$user_id'
                    )";


        if ($action=='Update'){

        }

        mysql_query($query) or die(mysql_error());
        $wine_id = mysql_insert_id();
        return $wine_id;
        }
    }else{
        return False;
    }
}

function get_wine($key,$key_type){
    //return wine details based on wine or vintage key
    //Usage: $key=vintage,wine
    //TODO: Need to handle get from vintage_id by looking up vintage first to return wine_id
    //then lookup wine
    
    echo "<br/>get_wine = $key, $key_type";

    if($key_type=='wine' && $key > 0){


        $query_old="SELECT *
        FROM tblWine
        LEFT JOIN tblVintage ON tblVintage.wine_id = tblWine.wine_id
        LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id
        LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
        LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
        LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
        WHERE tblWine.wine_id=$key";

        $query="SELECT *
        FROM tblWine
        LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id
        LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
        LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
        LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
        WHERE tblWine.wine_id='$key'";

        //echo $query;

    }else if($key_type=='vintage' && $key > 0){

    }else{
        $blnFailed=True;
    }

    $result = mysql_query($query) or die(mysql_error());
    $num_rows = mysql_num_rows($result);

    if ($num_rows==1){
        $row = mysql_fetch_array($result) or die(mysql_error());
        return $row;
    } else if ($num_rows > 1){
        $blnFailed = True;
        $debug = true;
        if ($debug){echo "<p class=\"error\">get_wine: duplcate wine_id key: $key</p>";}
        while($row = mysql_fetch_array($result)){
            echo "wine_id: ".$row['wine_id'];
        }
    } else {
        $blnFailed = True;
        echo '<br/>get_wine = no rows';

    }
    
}

function wine_exists($wine_id){
    //bool - true if wine_id exists - false if not
    
    $return = False;
    if ($wine_id>0){
        $query = "SELECT * FROM tblwine
            WHERE wine_id=$wine_id";
        $result = mysql_query($query) or die(mysql_error());
        $num_rows = mysql_num_rows($result);

        if ($num_rows==1){
            $return = True;
        } 
    }
    
    return $return;
}

function vintage_exists($vintage_id){
    //bool - true if vintage_id exists - false if not

    $return = False;
    if ($vintage_id > 0){
        $query = "SELECT * FROM tblvintage
            WHERE vintage_id = $vintage_id";
        $result = mysql_query($query) or die(mysql_error());
        $num_rows = mysql_num_rows($result);

        if ($num_rows==1){
            $return = True;
        }
    }

    return $return;
}

function put_vintage($var_vintage){
    //assumes validation have been completed
    //will insert if key does not exist and update if it does
    $debug = True;
    $return = False;

    //var_vintage defintion - use these names to map to fields
    // vintage_id - int, primary key
    // wine_id - int, parent wine
    // year - int, year of vintage
    // image1 - varchar, filename for main images (provides for additional images in the future)
    // alcohol - decimal, percentage of alcohol
    // grapes - plural is array of grapes containing arrays 'grape'; grape_id, percent
    // awards - plural is array of awards containing array 'award'
    // closure - int, closure type from tlkpClosureType
    // comments - varchar, general comments
    // modified - timestamp, date and time record was modified
    // created - date and time, record was created
    // user_id - int, user that owns/created record

    //table field mapping
    $vintage_table_fieldlist = array(
    'vintage_id' => 'vintage_id',
    'wine_id' => 'fkWinw',
    'year' => 'year',
    'image1' => 'image1',
    'vintage_notes' => 'comments',
    'image1' => 'alcohol',
    'closure_id' => 'closure',
    'created' => 'created',
    'modified' => 'modified',
    'user_id' => 'user_id');
    echo "<br/> vintage field mapping: ";
    print_r($vintage_table_fieldlist);

    //TODO: test for array
    if (is_array($var_vintage) && is_authed()){
        if($debug){echo "<br/>=>function: put_vintage: var_vintage: ";
            print_r($var_vintage);
        }

        if ( $var_vintage['vintage_id']>0) {$vintage_id = $var_vintage['vintage_id'];} //required
        if ( $var_vintage['wine_id']>0) {$wine_id = $var_vintage['wine_id'];} //required
        //$wine_id = $var_vintage['wine_id']; //optional for update
        $year = $var_vintage['year']; //required
        if ( isset($var_vintage['comments'])) {$comments = $var_vintage['comments'];}
        $var_grapes = $var_vintage['grapes'];
        $var_awards = $var_vintage['awards'];
        $alcohol = $var_vintage['alcohol'];
        $image1 = $var_vintage['image1'];
        $closure = $var_vintage['closure'];
        $user_id = $_SESSION['user_id'];
        
        
        if(isset($vintage_id) && vintage_exists($vintage_id)){
            //update record
            echo "<br/>=>put_vintage: vintage_id: $vintage_id";
            foreach($var_vintage as $value){
                echo "<br/> value: ".$value;
                $field = array_search($value, $vintage_table_fieldlist);
                echo "<br/> field: $field";
             }
            
            $query = "UPDATE tblvintage SET
                year = \"$year\",
                vintage_notes = \"$comments\",
                image1 = \"$image1\",
                image1 = \"$alcohol\",
                closure_id = \"$closure\",
                modified = Now(),
                WHERE vintage_id = $vintage_id";

            if($debug){echo "<br/>=>function: put_vintage: query: ".$query;}

            
        } else {
            //check parent wine_id is provided
            if($wine_id>0){
                //insert vintage
                $query = "INSERT INTO tblVintage (
                    wine_id,
                    year,
                    vintage_notes,
                    image1,
                    created,
                    modified,
                    image1,
                    closure_id,
                    user_id
                    ) VALUES (
                    \"$wine_id\",
                    \"$year\",
                    \"$comments\",
                    \"$image1\",
                    Now(),
                    Now(),
                    \"$alcohol\",
                    \"$closure\",
                    \"$user_id\"
                    )";

                $result = mysql_query($query);
                $vintage_id = mysql_insert_id();
                if($result){
                    //new vintage added successfully - add grapes and awards

                    //add grapes
                    //echo "<br/>=> fn put_wine add grapes: ";
                    $var_grapes = $var_vintage['grapes'];
                    print_r($var_grapes);
                    foreach($var_grapes as $grape){
                        //echo "<br/>";
                        //print_r($grape);
                        $grape_id = $grape['grape_id'];
                        $percent = $grape['Percent'];
                        $grape_qry = "INSERT INTO trelvintagehasgrape (
                                vintage_id,
                                grape_id,
                                Percent,
                                dtmCreated,
                                modified,
                                user_id )
                                VALUES (
                               \"$vintage_id\",
                               \"$grape_id\",
                               \"$percent\",
                               Now(),
                               Now(),
                               $user_id
                                )";
                        //echo "<br/>=> grape insert: $grape_qry";
                        mysql_query($grape_qry) or die(mysql_error());
                    }
                } else {
                        die(mysql_error());
                }

                echo "<br/>=> function put_vintage insert query: $query";
            }
        }
    }

    return $return;
}

function delete_temp_image($path){
    //echo "<br/>=>function delete_temp_image ";
    //delete existing temp file - unless it points towards existing image
    if($_SESSION['var_vintage_temp']['tmp_image']>""){
       //delete existing tmp image
        echo "<br/>=> delete existing temp: ".$_SESSION['var_vintage_temp']['tmp_image'];
        $file = $path.$_SESSION['var_vintage_temp']['tmp_image'];
        echo "<br/>=> delete file: $file";
        fclose($file);
        unlink($file);
    }
}


function fnImageResize($imagepath, $root, $targetsize){

	$origSize = getimagesize($root.$imagepath);
	$width = $origSize[0];
	$height = $origSize[1];

	//takes the larger size of the width and height and applies the
	//formula accordingly...this is so this script will work
	//dynamically with any size image

	if ($width > $height)
	{
	$percentage = ($targetsize / $width);
	} else {
	$percentage = ($targetsize / $height);
	}

	//gets the new value and applies the percentage, then rounds the value
	$width = round($width * $percentage);
	$height = round($height * $percentage);

	//returns the new sizes in html image tag format...this is so you can plug this function inside an image tag and just get the image

	return "src=\"$imagepath\" width=\"$width\" height=\"$height\"";

}


?>
