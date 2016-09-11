<?php

class grape extends db {

var $table = 'tblGrape';

public $fieldlist = array(
    'grape_id' => array(
        'map' => 'grape_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'grape' => array(
        'map' => 'grape',
        'required' => true,
        'datatype' => 'string'
        ),
    'colour' => array(
        'map' => 'colour',
        'required' => true,
        'datatype' => 'string'
        ),
    'comments' => array(
        'map' => 'comments',
        'required' => false,
        'datatype' => 'string'
        ),
    'parent' => array(
        'map' => 'parent',
        'required' => false,
        'datatype' => 'int'
        ),
    'created' => array(
        'map' => 'created'
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
