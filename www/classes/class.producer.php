<?php


class producer extends db {

    var $table = 'tblProducer';

    public $fieldlist = array(
        'producer_id' => array(
            'map' => 'producer_id',
            'primary_key' => true,
            'required' => false,
            'autonumber' => true
            ),
        'producer' => array(
            'map' => 'producer',
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
        'user_id' => array(
            'map' => 'user_id',
            'required' => true
           )
        );

     function __construct($producer_id=false){
        //constructor

        if($producer_id > 0){
            $this -> producer_id = $producer_id;
        }

    }

    
    public function getWineCount($producerId){
        //get count of wines associated with Producer
        
        $this->db = MyPDO::instance(); //return a static instance of the PDO class for db connectivity
        
        $producerId = $producerId > 0 ? $producerId : $this->producerId;
        
        if(!isset($producerId)){
            $this->last_error = "getWineCount(): No producer id provided";
            return false;
        }
        
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM tblWine WHERE producer_id = ?');
        $stmt->execute([$producerId]);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($rst){
            return $rst['COUNT(*)'];
        }else{
            $this->last_error = "getWineCount(): No record or count returned";
            return false;
        }
        
    }


}
?>
