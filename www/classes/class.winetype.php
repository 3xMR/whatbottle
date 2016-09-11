<?php


class winetype extends db {

var $table = 'tlkpWineType';

public $fieldlist = array(
    'winetype_id' => array(
        'map' => 'winetype_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'winetype' => array(
        'map' => 'winetype',
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

}
?>
