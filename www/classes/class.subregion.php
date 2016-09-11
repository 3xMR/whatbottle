<?php


class subregion extends db {

var $table = 'tblSubRegion';

public $fieldlist = array(
    'subregion_id' => array(
        'map' => 'subregion_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'subregion' => array(
        'map' => 'subregion',
        'required' => true,
        'datatype' => 'string'
        ),
    'region_id' => array(
        'map' => 'region_id',
        'required' => true
        ),
    'classification' => array(
        'map' => 'classification',
        'required' => false,
        'datatype' => 'string'
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
                       LEFT JOIN tblRegion ON tblRegion.country_id = tblCountry.country_id
                       LEFT JOIN tblSubRegion ON tblSubRegion.region_id = tblRegion.region_id";
       return db::get($where, $columns, $group, $sort, $limit);
    }
    
}
?>
