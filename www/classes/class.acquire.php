<?php


class acquire extends db {

var $table = 'tblAcquire';

public $fieldlist = array(
    'acquire_id' => array(
        'map' => 'acquire_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'acquire_date' => array(
        'map' => 'acquire_date',
        'required' => true
        ),
    'acquire_type_id' => array(
        'map' => 'acquire_type_id',
        'required' => true
        ),
    'merchant_id' => array(
        'map' => 'merchant_id',
        'required' => false
        ),
    'acquire_notes' => array(
        'map' => 'acquire_notes',
        'required' => false,
        'datatype' => 'string'
        ),
    'acquire_discount_percentage' => array(
        'map' => 'acquire_discount_percentage',
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


   function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
       //remove ambiguity in column usage
       $replacement = $this->table.'.acquire_id';
       $where = str_replace('acquire_id', $replacement, $where);
       $this->table = "tblAcquire
                       RIGHT JOIN tblMerchant ON tblAcquire.merchant_id = tblMerchant.merchant_id";
       return db::get($where, $columns, $group, $sort, $limit);

   }

}
?>
