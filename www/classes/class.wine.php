<?php

class wine extends db {

   public $table = 'tblWine';
   private $wine_id = null;

   public $fieldlist = array(
    'wine_id' => array(
        'map' => 'wine_id',
        'primary_key' => true,
        'required' => true,
        'autonumber' => true
        ),
    'wine' => array(
        'map' => 'wine',
        'required' => true,
        'datatype' => 'string'
        ),
    'fkClassification' => array(
        'map' => 'fkClassification',
        'datatype' => 'int'
        ),
    'winetype_id' => array(
        'map' => 'winetype_id',
        'required' => true,
        'datatype' => 'int'
        ),
    'producer_id' => array(
        'map' => 'producer_id',
        'required' => true,
        'datatype' => 'int'
        ),
    'country_id' => array(
        'map' => 'country_id',
        'required' => true,
        'datatype' => 'int'
        ),
    'region_id' => array(
        'map' => 'region_id',
        'required' => true,
        'datatype' => 'int'
        ),
    'subregion_id' => array(
        'map' => 'subregion_id',
        'datatype' => 'int'
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


    function __construct($wine_id=false){
        
        if(isset($wine_id)){
           $this -> wine_id = $wine_id;
        }
        
    }

    function update($input_array, $where=false){
       return db::update($input_array, $where);
    }


    function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){

        if($where==false && $this->wine_id>0){
                $where = "wine_id = $this->wine_id";
        }

        $this->table = "tblWine
                        LEFT JOIN tlkpWineType ON tblWine.winetype_id = tlkpWineType.winetype_id
                        LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                        LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                        LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
                        LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id";
        if(!$columns>""){
            $columns = "tblWine.wine_id, tblWine.wine, tblWine.winetype_id, tlkpWineType.winetype,tblWine.producer_id, tblProducer.producer,
                tblWine.region_id, tblWine.country_id, tblCountry.country, tblWine.region_id, tblRegion.region, tblWine.subregion_id, tblSubRegion.subregion,
                tblWine.created, tblWine.modified, tblWine.user_id, tblCountry.flag_image";
        }
        return db::get($where, $columns, $group, $sort, $limit);
    }
   

   
}
