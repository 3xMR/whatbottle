<?php

require_once("$root/classes/class.db.php");


class vintage extends db {


    protected  $table = 'tblVintage';

    public $vintage_id = null;
    public $note_count = null;
    public $grape_count = null;
    public $award_count = null;
    public $acquisition_count = null;
    public $last_error = null;
   

    protected $fieldlist = [
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
        'blnIgnore' => array(
            'map' => 'blnIgnore',
            'override' => true,
            'default' => '0'
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
            'map' => 'alcohol',
            'datatype' => 'double'
            ),
        'closure_id' => array(
            'map' => 'closure_id',
            'datatype' => 'integer'
            ),
        'drink_year_from' => array(
            'map' => 'drink_year_from',
            'datatype' => 'integer'
            ),
        'drink_year_to' => array(
            'map' => 'drink_year_to',
            'datatype' => 'integer'
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
           ];
             
    
        function __construct($vintage_id=false){
            //constructor
      
            if($vintage_id > 0){
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

           $this->table = "tblVintage
                           LEFT JOIN tblWine ON tblVintage.wine_id = tblWine.wine_id
                           LEFT JOIN tlkpWineType ON tblWine.winetype_id = tlkpWineType.winetype_id
                           LEFT JOIN tblCountry ON tblWine.country_id = tblCountry.country_id
                           LEFT JOIN tblRegion ON tblWine.region_id = tblRegion.region_id
                           LEFT JOIN tblSubRegion ON tblWine.subregion_id = tblSubRegion.subregion_id
                           LEFT JOIN tblProducer ON tblWine.producer_id = tblProducer.producer_id";
           
           return db::get($where, $columns, $group, $sort, $limit);

        }

        
        public function get_last_error(){
            //returns last recorded error
            
            if($this->last_error){
                return $this->last_error;
            }else{
                return false;
            }
            
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
            
            if($this->vintage_id == false){
                return false;
            }
                
            $var_details = $this -> get_extended();

            if(!isset($var_details)){
                return false;
            }
            
            $var_result = $var_details[0];
            $var_result['var_grapes'] = $this -> get_grapes();
            $var_result['var_awards'] = $this -> get_awards();
            $var_result['var_notes'] = $this -> get_notes();
            $var_result['var_acquisitions'] = $this -> get_acquisitions();
            return $var_result;                   
            
        }
        
        
        public function get_grapes($details=false){

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
        
        
        public function get_note_count(){
            //return number of tasting notes for this vintage
            
            if($this->vintage_id>0){
                $obj_notes = new tasting_note();
                $vintage_id = $this -> vintage_id;
                $where = "vintage_id = $vintage_id";
                $note_count = $obj_notes -> row_count($where);
                if($note_count > 0){
                    $this -> note_count = $note_count;
                    return $note_count;
                }
            }
            
            return false;
           
        }
        
        
        public function get_available_bottle_count(){
            /* Calculate and return the number of bottle available to consume
             * Each tasting note against vintage will deduct from the total acquired number
             * acquisition_bottle_count, note_count, available_adjust_count, bottle_in_storage_count
             */
            
            $this->last_error = null;
            $var_available = null;
            
            $acquisition_bottle_count = $this->get_acquisition_bottle_count();
            if($acquisition_bottle_count <= 0){
                return false; //no acquisitions return false - nothing more to calculate
            }
            

            /* Storage: not used today but need to support capability that certain number
             * of bottles are not available because they are in storage
             * set to zero until function added
             */
            
            $storage_bottle_count = 0;
 
            $note_count = $this->get_note_count() ?: 0; //if no notes set to zero 
            
            $gross_available_bottle_count = $acquisition_bottle_count - $storage_bottle_count;
            $net_available_bottle_count = $gross_available_bottle_count - $note_count;
            
            //$override_min = -abs($note_count); //minimum override value is the negative of notes added so they can be net off
            //$override_max = $net_available_bottle_count; //max ovveride value is the number available
            
            //FIX: This function is causing an error
            $override = $this->get_available_override() ?: 0; //if returns an error set to zero
    
            $available_bottles = (($net_available_bottle_count - $override)<0) ? 0 : ($net_available_bottle_count - $override); //if a negative number set to zero
            
            $var_available['acquisition_bottle_count'] = $acquisition_bottle_count;
            $var_available['storage_bottle_count'] = $storage_bottle_count;
            $var_available['gross_available_bottle_count'] = $gross_available_bottle_count;
            $var_available['note_count'] = $note_count;
            $var_available['net_available_bottle_count'] = $net_available_bottle_count;
            $var_available['override'] = $override;
            //$var_available['override_min'] = $override_min;
            //$var_available['override_max'] = $override_max;
            //$var_available['available_max'] = $gross_available_bottle_count;
            $var_available['available_bottles'] = $available_bottles;

            return $var_available;
            
        }
        
        
        public function set_available_override($override_value){
            //set manual override in tblAvailableOverride to compensate for not putting in Notes
            $this->last_error = null;
            $input_array = [];
            
            if(!$this->vintage_id > 0){
                $this->last_error = 'No vintage_id set';
                return false; //can't continue
            }
            
            if(!is_int($override_value)){
                $this->last_error = "class.vintage:set_available_override() override_value is not an integer and is not zero value = $override_value";
                return false;
            }
            
            //check if update or insert using 'vintage_exists'
            $where = " vintage_id = ".$this->vintage_id;
            $obj_available_override = new available_override();
            $available_override_count = $obj_available_override->row_count($where);
            if($available_override_count > 0){
                //record exists for vintage UPDATE
                $input_array['vintage_id']=$this->vintage_id;
                $input_array['override']=$override_value;
                $input_array['user_id']= $_SESSION['user_id'];
                $update_result = $obj_available_override->update($input_array,$where);
                if(!$update_result){
                    $this->last_error = $obj_available_override->get_sql_error();
                    return false;
                }
                return true;
            }else{
                //record does not exist for vintage
                $input_array['vintage_id']=$this->vintage_id;
                $input_array['override']=$override_value;
                $input_array['user_id']= $_SESSION['user_id'];
                $insert_result = $obj_available_override->insert($input_array);
                if(!$insert_result){
                    $this->last_error = $obj_available_override->get_sql_error();
                    return false;
                }
                return true;
            }

        }
        
        
        public function get_available_override(){
            //get manual override from tblAvailableOverride
            $this->last_error = null;
            
            if(!$this->vintage_id > 0){
                $this->last_error = 'No vintage_id set';
                return false; //can't continue
            }
            
            $where = " vintage_id = ".$this->vintage_id;
            $obj_available_override = new available_override();
            $available_override_count = $obj_available_override -> row_count($where);
            
            if($available_override_count <> 1){
                $this->last_error = "query returned more than one row for vintage row_count = $available_override_count";
                return false; 
            }
            
            $rst_override = $obj_available_override->get($where);
            if(!$rst_override){
                $this->last_error = $obj_available_override->get_sql_error();
                return false;
            }
            
            $override = $rst_override[0]['override'];
            
            return $override;

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
        
        
        public function get_acquisition_bottle_count(){
            //returns total number of bottles from all acquisitions
            if($this->vintage_id>0){
                $obj_acquire = new vintage_has_acquire();
                $vintage_id = $this -> vintage_id;
                $where = " vintage_id = $vintage_id ";
                $columns = " SUM(qty) ";
                $rst_result = $obj_acquire -> get($where, $columns);
                $acquisition_bottle_count = $rst_result[0]['SUM(qty)'];
                if($acquisition_bottle_count > 0){
                    $this -> acquisition_count = $acquisition_bottle_count;
                    return $acquisition_bottle_count;
                }
                
                return false;
            }
            
        }
        
                
        public function delete_vintage(){
            //deletes vintage and all associations

            $user = new UserObj();
            $user_id = $user ->isAuthed();
            if(!$user_id){
                $this->set_sql_error('user not authenticated cannot continue');
                return false;
            }
            
            if($this->vintage_id<=0){
                $this->set_sql_error("delete_vintage(): no vintage_id nothing to delete");
                return false; //nothing to delete
            }
                
            $vintage_id = $this -> vintage_id;
            $where = "vintage_id = $vintage_id";

            $obj_notes = new tasting_note();
            $obj_notes -> delete($where);
            $obj_notes = null;

            $obj_awards = new vintage_has_award;
            $obj_awards -> delete($where);
            $obj_awards = null;

            $obj_grapes = new vintage_has_grape;
            $obj_grapes -> delete($where);
            $obj_grapes = null;
            
            $obj_list = new list_has_vintage(); //delete from basket if it exists
            $where_list = " trelListHasVintage.vintage_id = $vintage_id AND trelListHasVintage.list_id = 0 AND trelListHasVintage.user_id = $user_id ";
            $obj_list -> delete($where_list);
            $obj_list = null;

            $var_record = $this->get_all($where);
            $image_name = $var_record['image1']; 
            if($image_name){
                if(!$this->delete_image($image_name, $vintage_id)){
                    $this->last_error = 'delete_vintage(): failed to delete image file';
                };
            }

            $this -> table = 'tblVintage';
            return db::delete($where);

        }
        
        
        private function delete_image($file_name, $vintage_id){
            //delete image file if it isn't associated with another vintage
            global $new_root, $label_path;
            
            if(!$file_name){
                $this->last_error = "delete_image(): no file_name provided $file_name";
                return false;
            }
            
            if(!$vintage_id){
                $this->last_error = "delete_image(): no vintage_id nothing to delete";
                return false; //nothing to delete
            }
            
            $image_path = $new_root.$label_path.$file_name;
            if(!file_exists($image_path)){
                return true; //nothing to delete, file doesn't exist
            }
            
            //check if file name is associated with any other vintage
            $where = " image1 = '$file_name' AND vintage_id <> $vintage_id ";
            $count = parent::row_count($where);
            if($count >= 1){ //image is associated with more than one vintage so do not delete the file
                return true;
            }
            
            if(!unlink($image_path)){ //failed to delete image
                $this->last_error = 'delete_image(): failed to delete image file';
                return false;
            }
            
            return true;
            
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

        
       public function row_count($where=false){
           
           if($where){
                $row_count = parent::row_count($where);
           } elseif ($this->vintage_id>0){
                $where = "vintage_id = ".$this -> vintage_id;
                $row_count = parent::row_count($where);
           } else {
               $row_count = parent::row_count();
           }
           
           return $row_count;
       }


}

