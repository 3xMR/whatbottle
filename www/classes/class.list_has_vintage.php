<?php

//handles tblVintageHasGrape
require_once("$root/classes/User.php");

class list_has_vintage extends db {

protected $table = 'trelListHasVintage';
protected $list_has_vintage_id = null;
protected $db;

protected $fieldlist = array(
    'list_has_vintage_id' => array(
        'map' => 'list_has_vintage_id',
        'primary_key' => true,
        'required' => false,
        'autonumber' => true
        ),
    'vintage_id' => array(
        'map' => 'vintage_id',
        'required' => true
        ),
    'list_id' => array(
        'map' => 'list_id',
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
    

    function __construct($list_has_vintage_id=false){

        parent::__construct(); //call class.db __construct to set db connection

        if($list_has_vintage_id > 0){
            $this -> list_has_vintage_id = $list_has_vintage_id;
        }
    }
    
   
    public function add_vintage_to_list($vintage_id, $list_id=false){
       //add a vintage to a list
       //list_id is optional, no list_id adds vintage to basket
       
       if(!$vintage_id){
           $this->set_sql_error('no vintage_id provided cannot continue');
           return false;
       }
       
       $user = new UserObj();
       $user_id = $user ->isAuthed();
       if(!$user_id){
           $this->set_sql_error('user not authenticated cannot continue');
           return false;
       }
       
       $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
       
       if($this->vintage_in_list($vintage_id, $list_id)){ //check for existence to avoid duplicates
            $this->set_sql_error('vintage already added to list');
            return false;
       }
       
       $input_array['vintage_id'] = $vintage_id;
       $input_array['list_id'] = $list_id;
       $input_array['user_id'] = $user_id;
       
       return db::insert($input_array);
       
    }
   
   
    public function remove_vintage_from_list($list_has_vintage_id, $vintage_id=false, $list_id=false){
       //remove a vintage from a list
       //list_id is optional, no list_id assumes list_id = 0 which is the basket
       
       if(!$list_has_vintage_id && !$vintage_id){
           $this->set_sql_error('no list_has_vintage_id and no vintage_id provided cannot continue');
           return false;
       }
       
       $user = new UserObj();
       $user_id = $user ->isAuthed();
       if(!$user_id){
           $this->set_sql_error('user not authenticated cannot continue');
           return false;
       }
       
       $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
       
       if($list_has_vintage_id){
           $where = " list_has_vintage_id = $list_has_vintage_id AND user_id = $user_id ";
       } else {
           $where = " vintage_id = $vintage_id AND list_id = $list_id AND user_id = $user_id";
       }

       return db::delete($where);
       
    }
    
    
    public function clear_list($list_id=false){
       //clear all vintages from a list
       //list_id is optional, no list_id assumes list_id = 0 which is the basket
       
       $user = new UserObj();
       $user_id = $user ->isAuthed();
       if(!$user_id){
           $this->set_sql_error('user not authenticated cannot continue');
           return false;
       }
       
       $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
       
       $where = " list_id = $list_id AND user_id = $user_id ";
     
       return db::delete($where);
       
    }
   
   
    function vintage_in_list($vintage_id, $list_id=false){
        //check if vintage is already in list
       
        if(!$vintage_id){
           $this->set_sql_error('no vintage_id provided cannot continue');
           return false;
        }
       
        $user = new UserObj();
        $user_id = $user ->isAuthed();
        if(!$user_id){
            $this->set_sql_error('user not authenticated cannot continue');
            return false;
        }

        $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
        
        $where = " vintage_id = $vintage_id AND list_id = $list_id AND user_id = $user_id ";
        
        return db::row_count($where);

   }
   
   
    function count_in_list($list_id=false){
       //return number of vintages in a given list for an authenticated user
       //list_id optional - will return count of basket if ommitted
      
        $user = new UserObj();
        $user_id = $user ->isAuthed();
        if(!$user_id){
            $this->set_sql_error('user not authenticated cannot continue');
            return false;
        }
        
        $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
        
        $where = " trelListHasVintage.list_id = $list_id AND trelListHasVintage.user_id = $user_id ";
        
        return db::row_count($where);
       
    }
   
    
    function get_list_contents($list_id=false){
       //return array of vintages in a given list for an authenticated user
       //list_id optional - will return count of basket if ommitted
       
        $user = new UserObj();
        $user_id = $user ->isAuthed();
        if(!$user_id){
            $this->set_sql_error('user not authenticated cannot continue');
            return false;
        }
        
        $list_id = $list_id == false ? 0 : $list_id; //if list_id not set, set it to zero;
        
        $where = " trelListHasVintage.list_id = $list_id AND trelListHasVintage.user_id = $user_id ";
        
        //return db::get($where);
        
        return $this->get_extended($where);
       
   }
   
   
    function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
        
        $columns = "trelListHasVintage.list_has_vintage_id,
                    trelListHasVintage.vintage_id,
                    trelListHasVintage.list_id,
                    tblList.list,
                    tblVintage.vintage_id,
                    tblVintage.wine_id,
                    tblVintage.year,
                    tblVintage.vintage_notes,
                    tblVintage.image1,
                    tblVintage.vintage_quality,
                    tblVintage.vintage_value,
                    tblVintage.alcohol,
                    tblVintage.closure_id,
                    tblVintage.drink_year_from,
                    tblVintage.drink_year_to,
                    tblVintage.created,
                    tblVintage.modified,
                    tblVintage.user_id,
                    tblWine.wine,
                    tblWine.winetype_id,
                    tblWine.producer_id,
                    tblWine.country_id,
                    tblWine.region_id,
                    tblWine.subregion_id,
                    tlkpWineType.winetype,
                    tblCountry.country,
                    tblCountry.flag_image,
                    tblRegion.region,
                    tblSubRegion.subregion,
                    tblSubRegion.classification,
                    tblProducer.producer";

        $this->table = "trelListHasVintage
                        LEFT JOIN tblList ON trelListHasVintage.list_id = tblList.list_id
                        LEFT JOIN tblVintage ON trelListHasVintage.vintage_id = tblVintage.vintage_id
                        LEFT JOIN tblWine ON tblVintage.wine_id = tblWine.wine_id
                        LEFT JOIN tlkpWineType ON tblWine.winetype_id = tlkpWineType.winetype_id
                        LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                        LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                        LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
                        LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id";

        $result = db::get($where, $columns, $group, $sort, $limit);
        
        $this->table = 'trelListHasVintage'; //return table to default
                
        return $result;

    }



}

