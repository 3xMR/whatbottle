<?php

class award_org extends db {

var $table = 'tlkpAwardOrg';

public $fieldlist = array(
    'award_org_id' => array(
        'map' => 'award_org_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'award_org' => array(
        'map' => 'award_org',
        'required' => true
        ),
    'award_org_description' => array(
        'map' => 'award_org_description',
        'required' => false
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
