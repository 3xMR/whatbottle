<?php

class sweetness extends db {

var $table = 'tlkpSweetness';

public $fieldlist = array(
    'sweetness_id' => array(
        'map' => 'sweetness_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'SweetnessRating' => array(
        'map' => 'SweetnessRating',
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
