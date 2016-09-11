<?php

class acquire_type extends db {

var $table = 'tlkpAcquireType';

public $fieldlist = array(
    'acquire_type_id' => array(
        'map' => 'acquire_type_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'award_type' => array(
        'map' => 'award_org',
        'required' => true
        ),
    'created' => array(
        'map' => 'created',
        'override' => true,
        'default' => 'NOW()'
        ),
    'modified' => array(
        'map' => 'modified',
        'override' => true,
        'default' => 'NOW()'
        ),
    'user_id' => array(
        'map' => 'user_id',
        'required' => true
       )
    );


}
?>
