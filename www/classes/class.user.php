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


    public function userNameExists($userName){
        //returns user_id | false if userId exists

        if(!isset($userName)){
            $this->lastError = 'userExists(): no username provided, cannot continue';
            return false;
        }

        $query = "Select user_id FROM tblUser WHERE username = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute($userName);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC); //fetch all records in recordset as assoc. array
        $this->count = count($rst); //count results in array

        if($this->count < 1){
            return false; //no records to return
        }

        return $rst['user_id']; //return dataset

    }


}
