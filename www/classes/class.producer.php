<?php


class producer extends db {

var $table = 'tblProducer';

public $fieldlist = array(
    'producer_id' => array(
        'map' => 'producer_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'producer' => array(
        'map' => 'producer',
        'required' => true,
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


}
?>
