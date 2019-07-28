<?php

//handles tblVintageHasGrape

class vintage_has_grape extends db {

var $table = 'trelVintageHasGrape';
protected $vintage_has_grape_id = null;

public $fieldlist = array(
    'vintage_has_grape_id' => array(
        'map' => 'vintage_has_grape_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'grape_id' => array(
        'map' => 'grape_id',
        'required' => true
        ),
    'percent' => array(
        'map' => 'percent',
        'datatype' => 'decimal'
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

    public function vintage_has_grape($vintage_has_grape_id=false){
        //constructor
        if($vintage_has_grape_id > 0){
            $this -> vintage_has_grape_id = $vintage_has_grape_id;
        }
    }

    function update($input_array, $where=false){
       //combine vintage_id and grape_id to create WHERE clause
       $vintage_id = $input_array['vintage_id'];
       $grape_id =  $input_array['grape_id'];
       $where = "vintage_id = $vintage_id AND grape_id = $grape_id";
       return db::update($input_array, $where);
   }


   function add($input_array){
       //create new record (insert)
       return db::insert($input_array);
   }

   
   function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){

       $this->table = "trelVintageHasGrape
                       LEFT JOIN tblGrape ON trelVintageHasGrape.grape_id = tblGrape.grape_id";
       return db::get($where, $columns, $group, $sort, $limit);


   }


   public function delete_grape($vintage_has_grape_id=false){

        if($vintage_has_grape_id==false && $this -> vintage_has_grape_id > 0 ){
            $where = "vintage_has_grape_id = ".$this -> vintage_has_grape_id;
        } else {
            $where = "vintage_has_grape_id = $vintage_has_grape_id";
        }

        return db::delete($where);

    }


}

?>
