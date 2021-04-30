<?php
//start php session
session_start();

/*
 * rpc for reporting and stats
 *
 */


//$root = $_SERVER['DOCUMENT_ROOT'];
//$new_root = rtrim($root, '/\\');
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/includes/init.inc.php');
require_once(__ROOT__.'/functions/function.php');
require_once(__ROOT__.'/classes/class.db.php');


if($_REQUEST['rpc_action'] || $_REQUEST['action']){
    //convert action to function call
    if($_REQUEST['rpc_action']){
        $fnc = $_REQUEST['rpc_action'];
    }else{
        $fnc = $_REQUEST['action'];
    }
    if(is_callable($fnc)){
        //call action as function
        $var_result = call_user_func($fnc);
        echo json_encode($var_result);
    }else{
        $var_result['error'] = "function [$fnc] not found on server page [".$_SERVER['PHP_SELF']."]";
        $var_result['success'] = false;
        echo json_encode($var_result);
    }
}else{
    $var_result['success'] = false;
    $var_result['error'] = "no rpc_action or action requested - cannot continue";
    echo json_encode($var_result); 
}


function get_acquisition_value(){
    //report on acquisition values

    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];

    $obj = new vintage_has_acquire();
    //$columns = "DATE_FORMAT(acquire_date,'%b') as Month";
    $columns = "DATE_FORMAT(tblAcquire.acquire_date,'%b') as month, sum(trelVintageHasAcquire.total_price) as total";
    //$group = " trelVintageHasAcquire.acquire_id ";
    $group = " Month ";
    $where = " tblAcquire.acquire_date BETWEEN '2014-01-01' AND '2014-12-31'";
    $sort = " acquire_date ASC ";
    $rst = $obj ->get_extended($where,$columns,$group, $sort);
    
    
    if(!$rst){
        $var_result['success']=false;
        $var_result['error']="acquisition report failed";
        return $var_result;
    }

    $var_result['success']=true;
    $var_result['data']=$rst;
    return $var_result;

}


function get_vintage_count(){
    //return count of vintages for period (return total if no period provided)
    
    $obj = new vintage();
    $vintage_count = $obj ->row_count();
    
    if(!$vintage_count){
        $var_result['success']=false;
        return $var_result; 
    }
    
    $var_result['vintage_count'] = $vintage_count;
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result;

}


function get_wine_count(){
    //return count of vintages for period (return total if no period provided)
    
    $wine = new wine();
    $wine_count = $wine ->row_count();
    
    if(!$wine_count){
        $var_result['success']=false;
        return $var_result; 
    }
    
    $var_result['wine_count'] = $wine_count;
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result; 
  
}


function get_note_count(){
    //return count of notes for period (return total if no period provided)
    
    $note = new tasting_note();
    $note_count = $note ->row_count();
    
    if(!$note_count){
        $var_result['success']=false;
        return $var_result; 
    }
    
    $var_result['note_count'] = $note_count;
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result; 
  
}


function get_bottle_count(){
    //return count of acquired bottle for period (return total if no period provided)
    
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];

    $obj = new vintage_has_acquire();
    $columns = " sum(qty) as qty";
    $where = null;
    $rst = $obj ->get($where,$columns);
    
    if(!$rst){
        $var_result['success']=false;
        return $var_result; 
    }

    $var_result['success']=true;
    $var_result['bottle_count']=$rst[0]['qty'];
    return $var_result; 

}


function get_acquisition_count(){
    //return count of acquisitions for period (return total if no period provided)
    
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];

    $acquire = new acquire();
    $acquire_count = $acquire->row_count();
 
    if(!$acquire_count){
        $var_result['success']=false;
        return $var_result; 
    }
    
    $var_result['acquisition_count'] = $acquire_count;
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result; 

}


function get_override_count(){
    //return count of available bottles (return total if no period provided)
    
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];

    $obj= new available_override();
    $columns = " sum(override) as qty";
    $where = null;
    $rst = $obj ->get($where,$columns);
    
    if(!$rst){
        $var_result['success']=false;
        return $var_result; 
    };
    
    $var_result['override_count'] = $rst[0]['qty'];
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result; 
    
}


function get_wine_count_by_country(){
    //return count of vintages for period (return total if no period provided)
    
    $obj = new wine();
    $columns = " tblcountry.country, count(tblwine.wine_id) as qty ";
    $group = " tblcountry.country ";
    $where = null;
    $sort = " qty DESC ";
    $limit = '10';
    $rst = $obj ->get_extended($where,$columns,$group,$sort,$limit);
    
    if(!$rst){
        $var_result['success']=false;
        return $var_result; 
    }
    
    //print_r($rst);
    
    $var_result['success']=true;
    $var_result['data']=$rst;
    return $var_result; 
  
}



function get_acquisition_qty_by_country(){
    //return qty of bottles bought grouped by country
    
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];

    $obj = new vintage_has_acquire();
    //$columns = "DATE_FORMAT(acquire_date,'%b') as Month";
    $columns = " tblCountry.country, sum(trelVintageHasAcquire.qty) as qty";
    //$group = " trelVintageHasAcquire.acquire_id ";
    $group = " tblCountry.country ";
    //$where = " tblAcquire.acquire_date BETWEEN '2014-01-01' AND '2014-12-31'";
    $sort = " qty DESC ";
    $limit = '12';
    $rst = $obj ->get_extended($where,$columns,$group, $sort, $limit);
    
    
    if(!$rst){
        $var_result['success']=false;
        $var_result['error']="get_acquisition_qty_by_country() failed";
        return $var_result;
    }

    $var_result['success']=true;
    $var_result['data']=$rst;
    return $var_result;
  
}


function get_all_stats(){
    //return key stats in one rpc call
     
    $var_result['wine_count'] = get_wine_count()['wine_count'];
    $var_result['vintage_count'] = get_vintage_count()['vintage_count'];
    $var_result['note_count'] = get_note_count()['note_count'];
    $var_result['bottle_count'] = get_bottle_count()['bottle_count'];
    $var_result['acquisition_count'] = get_acquisition_count()['acquisition_count'];
    $var_result['available_count'] = $var_result['bottle_count'] - $var_result['note_count'] - get_override_count()['override_count'];
    $var_result['success']=true;
    $var_result['data']=$var_result;
    return $var_result; 
   
}
