<?php


class vintage_has_acquire extends db {

var $table = 'trelVintageHasAcquire';

public $fieldlist = array(
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'acquire_id' => array(
        'map' => 'acquire_id',
        'required' => true
        ),
    'vintage_has_acquire_id' => array(
        'map' => 'vintage_has_acquire_id',
        'primary_key' => true,
        'required' => true
        ),
    'qty' => array(
        'map' => 'qty',
        'required' => true
        ),
    'unit_price' => array(
        'map' => 'unit_price',
        'required' => false
        ),
    'discounted_price' => array( //price_paid
        'map' => 'discounted_price',
        'required' => false
        ),
    'discount_percentage' => array(
        'map' => 'discount_percentage',
        'required' => false
        ),
    'total_discount' => array( //discount
        'map' => 'total_discount',
        'required' => false
        ),
    'total_price' => array(
        'map' => 'total_price',
        'required' => false
        ),
    'price_band' => array(
        'map' => 'price_band',
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
       $this->table = "trelVintageHasAcquire
                       LEFT JOIN tblAcquire ON trelVintageHasAcquire.acquire_id = tblAcquire.acquire_id";
//                       LEFT JOIN tblMerchant ON tblAcquire.merchant_id = tblMerchant.merchant_id
//                       LEFT JOIN tblVintage ON trelVintageHasAcquire.vintage_id = tblVintage.vintage_id
//                       LEFT JOIN tblWine ON tblVintage.wine_id = tblWine.wine_id
//                       LEFT JOIN tblCountry on tblWine.country_id = tblCountry.country_id";
       
       return db::get($where, $columns, $group, $sort, $limit);
       
    }

    
}
?>
