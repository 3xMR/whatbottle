<?php

class fullness extends db {

var $table = 'tlkpFullness';

public $fieldlist = array(
    'fullness_id' => array(
        'map' => 'fullness_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'FullnessRating' => array(
        'map' => 'FullnessRating',
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
