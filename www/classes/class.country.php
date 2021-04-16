<?php


class country extends db {

var $table = 'tblCountry';

public $fieldlist = array(
    'country_id' => array(
        'map' => 'country_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'country' => array(
        'map' => 'country',
        'required' => true,
        'datatype' => 'string'
        ),
    'flag_image' => array(
        'map' => 'flag_image',
        'override' => true,
        'default' => 'n/a'
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


}
?>
