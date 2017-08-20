<?php


class available_override extends db {

var $table = 'tblAvailableOverride';

public $fieldlist = array(
    'available_override_id' => array(
        'map' => 'available_override_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'override' => array(
        'map' => 'override'
        ),
    'created' => array(
        'map' => 'created',
        'override' => true,
        'default' => 'Now()'
        ),
    'modified' => array(
        'map' => 'modified',
        'override' => true,
        'default' => 'Now()'
        ),
    'user_id' => array(
        'map' => 'user_id',
        'required' => true
       )
    );


    public function vintage_exists($vintage_id){
        //check to see if vintage record exists
        if(!$vintage_id > 0){
            return false;
        }
        $where = " vintage_id = ".$vintage_id;
        $rst = $this -> get($where);
        if(!$rst){
            return false;
        }else{
            return $rst;
        }

    }


}

?>
