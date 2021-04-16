<?php

//require_once("$root/classes/class.db.php");
require_once("$root/classes/MyPDO.php");


class TestVintage {
    
    //set variables for this class
    private $table = "tblVintage";
    private $primaryKeyName = "vintage_id";
    
    protected $db; //db connection object
    public $lastErrorMessage = ''; //used to store last errorMessage for external recall
    
    protected $primaryKey = null;
   
    
    public $year;
    

    function __construct($primaryKey  = null) {
        //constructor - sets object based on parameter if valid
        
        $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
        
        if($primaryKey > 0){
            $this -> primaryKey  = $primaryKey ;
            $this->getRecordFromDb();
        }
    }


    public function getPrimaryKey(){
       return $this -> primaryKey;
    }


    public function setPrimaryKey($primaryKey){
        $this -> primaryKey = $primaryKey;
        $this->getRecordFromDb();
    }
    
    
    private function getRecordFromDb(){
        //Get vintage record from database using vintageId
        
        if(!$this->primaryKey >0){
            $this->lastErrorMessage = "getRecordFromDb: primaryKey not set";
            return false;
        }
        
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKeyName = 294";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);

        if($rst){
            //** set properties **//
            $this->year = $rst['year'];
        }else{
            $this->lastErrorMessage = "getRecordFromDb: No record returned";
            return false;
        }
    
        
    }
    
        
    private function setProperties(){

    }
     
    
    

    
}
?>
