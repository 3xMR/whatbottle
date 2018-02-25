<?php

// debugLog class


class debugLog {

    public $fileName = "debugLog.txt";
    public $path = "/logs/";
    private $date, $time;
    private $root;
    public $debug_buffer;
    private $immediate_commit = true;



    function __construct($file_name=false, $path=false){
        //constructor
            $this -> date = date('Y-m-d');
            $this -> time = date('H:i:s');
            $root = $_SERVER['DOCUMENT_ROOT'];
            $this -> root = rtrim($root, '/\\');

        if($file_name){
            //no file_name provided use default
            $this -> fileName = $this -> date." ".$file_name;
        } else {
            $this -> fileName = $this -> date." ".$this -> fileName;
        }

        if($path){
            $this -> path = $this -> root.$path;
        } else {
            $this -> path = $this -> root.$this -> path;
        }

    }

    public function debug($content, $commit=false){
        //write line to debug buffer

        $script = $_SERVER["SCRIPT_NAME"];

        if($content){
            $line_start = ">> ".$this -> date." ".$this -> time." [$script] ";

            if(is_array($content)){
                $content = implode(',', $content);
            }

            $line_content = $content;
            $line_end = " \n";

            $this -> debug_buffer .= $line_start.$line_content.$line_end;

            if($commit or ($this -> immediate_commit)){
                $this ->debug_write();
                //clear buffer
                $this -> debug_buffer = "";
            }
        }
    }


    public function set_immediate_commit($switch){
        //switch immediate commit on or off
        if($switch){
            $this -> immediate_commit = true;
        } else {
            $this -> immediate_commit = false;
        }
    }

    
    public function clear_log(){
        //delete log file

        $fullpathname = $this -> path.$this -> fileName;

        if (file_exists($fullpathname)){
            //kill
            unlink($fullpathname);
        }
    }


    function debug_write(){
        //writes warnings, errors and transactions to Log file

        $fullpathname = $this -> path.$this -> fileName;
        $stringData = $this -> debug_buffer;

        if (file_exists($fullpathname)){
            //open new file in w rite mode
            $mode = "a";
        } else {
            $mode = "w";
        }

        $fh = fopen($fullpathname, $mode) or die("<br>Error can't open debug log file");

        fwrite($fh, $stringData);
        fclose($fh);

        //clear buffer
        $this -> debug_buffer = "";

    }




} //end debugLog class

?>
