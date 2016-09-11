<?php

require_once("$root/classes/class.db.php");


class vintage extends db {


    protected  $table = 'tblVintage';

    public $vintage_id = null;
    public $note_count = null;
    public $grape_count = null;
    public $award_count = null;
    public $acquisition_count = null;

    protected $fieldlist = array(
        'vintage_id' => array(
            'map' => 'vintage_id',
            'primary_key' => true,
            'required' => true,
            'autonumber' => true
            ),
        'wine_id' => array(
            'map' => 'wine_id',
            'required' => true
            ),
        'year' => array(
            'map' => 'year',
            'required' => true,
            'validation' => 'year'
            ),
        'image1' => array(
            'map' => 'image1',
            'required' => false,
            'datatype' => 'string'
            ),
        'vintage_quality' => array(
            'map' => 'vintage_quality',
            'required' => false,
            'datatype' => 'double'
            ),
        'vintage_value' => array(
            'map' => 'vintage_value',
            'required' => false,
            'datatype' => 'double'
            ),
        'vintage_notes' => array(
            'map' => 'vintage_notes',
            'required' => false,
            'datatype' => 'string'
            ),
        'alcohol' => array(
            'map' => 'alcohol'
            ),
        'closure_id' => array(
            'map' => 'closure_id'
            ),
        'created' => array(
            'map' => 'created',
            'override' => true,
            'default' => 'NOW()'
            ),
        'modified' => array(
            'map' => 'modified',
            'override' => true,
            'default' => "NOW()"
            ),
        'user_id' => array(
            'map' => 'user_id',
            'required' => true
           )
        );


        public function vintage($vintage_id=false){
            //constructor - checks record exists and sets vintage_id to zero if not found
            if($vintage_id > 0){
                //$result = $this -> get_extended("vintage_id = $vintage_id");
                $this -> vintage_id = $vintage_id;
            }
        }

        
        function get_extended($where=false, $columns=false, $group=false, $sort=false, $limit=false){
     
           if($where==false && $this->vintage_id>0){
               $where = "vintage_id = ".$this->vintage_id;
           }

           $columns="tblVintage.vintage_id,
                     tblVintage.wine_id,
                     tblVintage.year,
                     tblVintage.vintage_notes,
                     tblVintage.image1,
                     tblVintage.vintage_quality,
                     tblVintage.vintage_value,
                     tblVintage.alcohol,
                     tblVintage.closure_id,
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

           $this->table = "tblVintage
                           LEFT JOIN tblWine ON tblVintage.wine_id = tblWine.wine_id
                           LEFT JOIN tlkpWineType ON tblWine.winetype_id = tlkpWineType.winetype_id
                           LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                           LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                           LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
                           LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id";
           
           return db::get($where, $columns, $group, $sort, $limit);

        }


        public function vintage_label($where=false){
           //return standard formated vintage label name

           if($where==false && $this->vintage_id>0){
                $where = "vintage_id = ".$this->vintage_id;
           }

           if($where){
               $var_vintage = $this->get_extended($where);
               $wine = $var_vintage[0]['wine'];
               $year = $var_vintage[0]['year'];
               $producer = $var_vintage[0]['producer'];
               $vintage_label = "$producer, $wine $year";
               return $vintage_label;
           }else{
               return false;
           }
           
        }


        public function set_vintage_id($vintage_id){
            $this->vintage_id=$vintage_id;
        }


        public function get_vintage_id(){
           return $this->vintage_id;
        }

        
        public function get_all(){
            if($this->vintage_id>0){
                
                $var_details = $this -> get_extended();
                
                if($var_details){
                    $var_result = $var_details[0];
                    //$var_result['vintage_label'] = $this -> vintage_label();
                    $var_result['var_grapes'] = $this -> get_grapes();
                    $var_result['var_awards'] = $this -> get_awards();
                    $var_result['var_notes'] = $this -> get_notes();
                    $var_result['var_acquisitions'] = $this -> get_acquisitions();
                    return $var_result;                   
                }else{
                    //get_extended returned empty
                    return false;
                }

           }
        }
        
        
        public function get_grapes($details){

           if($this->vintage_id>0){
                $obj_grapes = new vintage_has_grape;
                $vintage_id = $this -> vintage_id;
                if(!$details){
                    $columns = " trelVintageHasGrape.vintage_id, trelVintageHasGrape.grape_id, tblGrape.grape, tblGrape.colour, trelVintageHasGrape.percent "; 
                }
                $where = "vintage_id = $vintage_id";
                $this -> grape_count = $obj_grapes -> row_count($where);
                $result = $obj_grapes ->get_extended($where, $columns);

                return $result;
           }
           
        }

        
        public function get_awards(){
           if($this->vintage_id>0){
                $obj_awards = new vintage_has_award;
                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";
                $this -> award_count = $obj_awards -> row_count($where);
                return $obj_awards -> get_extended($where);
           }
        }


        public function get_notes(){
           if($this->vintage_id>0){
                $obj_notes = new tasting_note();
                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";
                $this -> note_count = $obj_notes -> row_count($where);
                return $obj_notes -> get_extended($where);
           }
        }
        
        
        public function get_acquisitions(){
           if($this->vintage_id>0){
                $obj_acquire = new vintage_has_acquire();
                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";
                $this -> acquisition_count = $obj_acquire -> row_count($where);
                return $obj_acquire -> get_extended($where);
           }
        }
        
                
        public function delete_vintage(){
            //deletes vintage and all associations
            global $new_root, $label_path;
            
            if($this->vintage_id>0){
                
                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";

                $obj_notes = new tasting_note();
                $obj_notes -> delete($where);
                $obj_notes =null;
                
                $obj_awards = new vintage_has_award;
                $obj_awards -> delete($where);
                $obj_awards = null;

                $obj_grapes = new vintage_has_grape;
                $obj_grapes -> delete($where);
                $obj_grapes = null;

                $var_record = $this->get_all($where);
                $image_name = $var_record['image1'];
                if($image_name){
                    $image_path = $new_root.$label_path.$image_name;
                    if(file_exists($image_path)){
                        if(!unlink($image_path)){ //failed to delete image
                            return false;
                        }
                    }
                }

                $this -> table = 'tblVintage';
                return db::delete($where);

            } else {
                //nothing to delete
                return false;
            }

        }

        public function delete_grapes(){
            if($this->vintage_id>0){

                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";

                $obj_grapes = new vintage_has_grape;
                $obj_grapes -> delete($where);
                $obj_grapes = null;

            } else {
                //nothing to delete
                return false;
            }
        }

        public function delete_vintage_tasting_note($note_id){
            //delete specific note_id
            if($this->vintage_id>0 && $note_id>0){
                $obj_note = new tasting_note($note_id);
                $obj_note -> delete_note();
                $this->recalc_ratings();
            }
        }

        public function recalc_ratings(){
            //recalculate average ratings and update vintage
           
            if($this->vintage_id>0){
                $vintage_id = $this -> vintage_id;


               $var_fields[] = "'note_quality','note_value";
               
               if($rating=='value'){
                   $note_field = "note_value";
                   $vintage_field = "vintage_value";
               } else {
                   $note_field = "note_quality";
                   $vintage_field = "vintage_quality";
               }

               //average quality rating for all notes and update vintage record
               $note_obj = new tasting_note();
               $where = " vintage_id = $vintage_id ";
               $columns = " AVG(note_quality), AVG(note_value)";
               $rst = $note_obj -> get($where, $columns, $group=false, $sort=false, $limit=false);

               if($rst){
                   $result_quality = round($rst[0]["AVG(note_quality)"],0);
                   $result_value = round($rst[0]["AVG(note_value)"],0);
               }

               //update vintage
               $vintage_obj = new vintage();
               $where = " vintage_id = $vintage_id";
               $set = " vintage_quality = $result_quality, vintage_value = $result_value, modified = Now() ";
               return $vintage_obj -> update_custom($set, $where);
             
            }

        }

        
       public function row_count($where){
           if($where){
                $row_count = parent::row_count($where);
           } elseif ($this->vintage_id>0){
                $where = "vintage_id = ".$this -> vintage_id;
                $row_count = parent::row_count($where);
           } else {
               //return error
               $row_count = -1;
           }
            return $row_count;
       }


}
?>
