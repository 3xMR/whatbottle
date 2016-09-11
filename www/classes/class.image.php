<?php

/*
 * Image handling class - work in progress
 * Feb 2015
 * 
 */


class image {
//put your code here
    
    private $image_name = null; //filename including extension
    private $image_path = null; //absolute path excluding filename
    private $image_ext = null; //extension
    private $image_saved_db = null; //true/false saved to db
    
    function __construct(){
        //constructor function
        
    }
    
    public function get_image($vintage_id){
        //load image from given vintage_id
        
        if(!$vintage_id){
            return false;
        }
        
        
        
    }

    public function get_name(){
        //return image filename
        return $this -> image_name;
    }
    
    public function upload_image(){
        
    }
    
    
}
