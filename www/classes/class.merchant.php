<?php


class merchant extends db {

var $table = 'tblMerchant';

public $fieldlist = array(
    'merchant_id' => array(
        'map' => 'merchant_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'merchant' => array(
        'map' => 'merchant',
        'required' => true,
        'datatype' => 'string'
        ),
    'merchant_abbreviation' => array(
        'map' => 'merchant_abbreviation',
        'required' => false
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
