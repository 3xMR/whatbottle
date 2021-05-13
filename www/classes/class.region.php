<?php


class region extends db {

var $table = 'tblRegion';

public $fieldlist = array(
    'region_id' => array(
        'map' => 'region_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'region' => array(
        'map' => 'region',
        'required' => true,
        'datatype' => 'string'
        ),
    'country_id' => array(
        'map' => 'country_id',
        'required' => true
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

    function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
        $this->table = "tblCountry
                       LEFT JOIN tblRegion ON tblRegion.country_id = tblCountry.country_id";
        return db::get($where, $columns, $group, $sort, $limit);
    }


}
?>
