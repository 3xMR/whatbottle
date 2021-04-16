<?php

class award extends db {

public $table = 'tblAward';

private $award_id = null;

public $fieldlist = array(
    'award_id' => array(
        'map' => 'award_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'award_org_id' => array(
        'map' => 'award_org_id',
        'required' => true
        ),
    'award' => array(
        'map' => 'award',
        'required' => true,
        'datatype' => 'string'
        ),
    'list_position' => array(
        'map' => 'list_position',
        'required' => false
        ),
    'award_notes' => array(
        'map' => 'award_notes',
        'required' => false,
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
    'user_id' => array(
        'map' => 'user_id',
        'required' => true
       )
    );


    function __construct($award_id=false){
        $this -> award_id = $award_id;
        log_write("award_id: $award_id",1,'class.award: award');
    }

    function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){

        if($where==false && $this->award_id>0){
            $where = "award_id = $this->award_id";
        }

        //remove ambiguity in column usage
        $replacement = $this->table.'.award_org_id';
        $where = str_replace('award_org_id', $replacement, $where);
        $this->table = "tblAward
                       RIGHT JOIN tlkpAwardOrg ON tlkpAwardOrg.award_org_id = tblAward.award_org_id";
        return db::get($where, $columns, $group, $sort, $limit);
    }

}

?>
