<?php


class tasting_note extends db {

public $table = 'tblNotes';
protected $rst = null;
protected $note_id = null;
public $tasting_notes = null;



public $fieldlist = array(
    'note_id' => array(
        'map' => 'note_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'note_date' => array(
        'map' => 'note_date',
        'required' => true
        ),
    'note_general' => array(
        'map' => 'note_general',
        'required' => false,
        'datatype' => 'string'
        ),
    'note_appearance' => array(
        'map' => 'note_appearance',
        'required' => false,
        'datatype' => 'string'
        ),
    'note_aroma' => array(
        'map' => 'note_aroma',
        'required' => false,
        'datatype' => 'string'
        ),
    'note_taste' => array(
        'map' => 'note_taste',
        'required' => false,
        'datatype' => 'string'
        ),
    'note_quality' => array(
        'map' => 'note_quality',
        'required' => false
        ),
    'note_value' => array(
        'map' => 'note_value',
        'required' => false
        ),
    'fullness_id' => array(
        'map' => 'fullness_id',
        'required' => false
        ),
    'sweetness_id' => array(
        'map' => 'sweetness_id',
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

    
    public function tasting_note($note_id=false){
        //constructor
        if($note_id > 0){
            $this -> note_id = $note_id;
        }
    }

    public function get_note_id(){
           return $this->note_id;
    }


    public function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
        //get extended record

        if($where==false && $this->note_id > 0){
            $where = "note_id = ".$this -> note_id;
        }

        $this -> table = "tblNotes
                       LEFT JOIN tlkpSweetness ON tblNotes.sweetness_id = tlkpSweetness.sweetness_id
                       LEFT JOIN tlkpFullness ON tblNotes.fullness_id = tlkpFullness.fullness_id";
        return db::get($where, $columns, $group, $sort, $limit);

    }

    
    public function delete_note($note_id=false){

        if($note_id==false && $this -> note_id > 0 ){
            $where = "note_id = ".$this -> note_id;
        } else {
            $where = "note_id = $note_id";
        }

        return db::delete($where);

    }
    
   
}
?>
