<?php

//handles tblVintageHasAward

class vintage_has_award extends db {

var $table = 'trelVintageHasAward';
protected $vintage_has_award_id = null;

public $fieldlist = array(
    'vintage_has_award_id' => array(
        'map' => 'vintage_has_award_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'award_id' => array(
        'map' => 'award_id',
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


    public function vintage_has_award($vintage_has_award_id=false){
        //constructor
        if($vintage_has_award_id > 0){
            $this -> vintage_has_award_id = $vintage_has_award_id;
        }
    }

    function update($input_array){

       //combine vintage_id and award_id to create WHERE clause
       $vintage_id = $input_array['vintage_id'];
       $award_id =  $input_array['award_id'];
       $where = "vintage_id = $vintage_id AND award_id = $award_id";
       return db::update($input_array, $where);

   }



   function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
       //remove ambiguity in column usage
       $replacement = $this->table.'.award_id';
       $where = str_replace('award_id', $replacement, $where);
       $this->table = "trelVintageHasAward
                       LEFT JOIN tblAward ON trelVintageHasAward.award_id = tblAward.award_id
                       LEFT JOIN tlkpAwardOrg on tblAward.award_org_id = tlkpAwardOrg.award_org_id";
       return db::get($where, $columns, $group, $sort, $limit);
       
   }


   public function delete_award($vintage_has_award_id=false){

        if($vintage_has_award_id==false && $this -> vintage_has_award_id > 0 ){
            $where = "vintage_has_award_id = ".$this -> vintage_has_award_id;
        } else {
            $where = "vintage_has_award_id = $vintage_has_award_id";
        }

        return db::delete($where);

   }

}
?>
