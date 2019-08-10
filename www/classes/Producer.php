<?php
/* 
 * Class Producer
 * 
 * 
 */



require_once("$root/classes/MyPDO.php"); //include PDO db class

class wbProducer{
    
    public $lastErrorMessage = ''; //used to store last errorMessage for external recall
    protected $db; //db connection object
    private $producerId;
            
    
    function __construct() {
        //constructor function
        
        $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
    }
    
    
    public function getWineCount($producerId){
        //get count of wines associated with Producer
        
        $producerId = $producerId > 0 ? $producerId : $this->producerId;
        
        if(empty($producerId)){
            $this->lastErrorMessage = "No producer id provided";
            return false;
        }
        
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tblWine WHERE producer_id = ?');
        $stmt->execute([$producerId]);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($rst){
            return $rst['COUNT(*)'];
        }else{
            $this->lastErrorMessage = "No record returned";
            return false;
        }
        
    }
    
}