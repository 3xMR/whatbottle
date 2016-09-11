<?php


class user extends db {

var $table = 'tblUser';

public $fieldlist = array(
    'user_id' => array(
        'map' => 'user_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'username' => array(
        'map' => 'username',
        'required' => true,
        'datatype' => 'string'
        ),
    'password' => array(
        'map' => 'password',
        'required' => true
        ),
    'salt' => array(
        'map' => 'salt',
        'required' => true
        ),
    'firstname' => array(
        'map' => 'firstname',
        'required' => true,
        'datatype' => 'string'
        ),
    'lastname' => array(
        'map' => 'lastname',
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
    'lastlogon' => array(
        'map' => 'lastlogon',
        'required' => false
       )
    );

}
?>
