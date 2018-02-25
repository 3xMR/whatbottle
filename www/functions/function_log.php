<?php

// Log Functions

// LogWrite - writes warnings, errors and transactions to Log file

//switch logging on or off
$write_to_file = false;

function log_write($desc, $type='1',$function='unspecified',$dur='n/a') {
    //writes warnings, errors and transactions to Log file

    //Type
        //1 - debug
        //2 - timing
        //3 - warning
        //4 - error
        //5 - critical

    global $write_to_file;


    if($write_to_file){

        $email = 'magnus@mcdonaldruden.co.uk';
        $user = $_SESSION['user_id'];
        $script = $_SERVER["SCRIPT_NAME"];
        $root = $_SERVER['DOCUMENT_ROOT'];
        $path = $root."/logs/";
        $today = date('Y-m-d');
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $file = "_whatbottle_log.csv";
        $fullpathname = $path.$today.$file;

        if (file_exists($fullpathname)){
            //open new file in w rite mode
            $mode = "a";
        } else {
            $mode = "w";
        }

        $fh = fopen($fullpathname, $mode) or die("can't open log file");
        //$stringData = "<log>\n\t<timestamp>$date $time</timestamp>\n\t<type>$type</type>\n\t<script>$script</script>\n\t<description>$desc</description>\n\t<duration>$dur</duration>\n\t<user_id>$user</user_id>\n</log>\n";

        //convert array into csv to write to file
        if(is_array($desc)){
            $desc = implode(',', $desc);
        }

        $stringData = "[$date $time],[$type],[$script],[$function],[$desc],[$dur],[$user]\n";
        fwrite($fh, $stringData);
        fclose($fh);

        if ($type > 3){
            //email log file on error or critical error
            $to = $email;
            //subject as above
            $subject = "Whatbottle Log Manager: Error!";
            $body = "An error or critical error has been logged:\r\n\r\n$stringData.";
            $headers = "From: admin@whatbottle.co.uk\r\n";
            mail($to, $subject, $body, $headers);
        }
    }

}

function logRead(){
    $file = "20090707_sharemyfilms_log.csv";
    $root = $_SERVER['DOCUMENT_ROOT'];
    $path = $root."/Log/";
    $myFile = $path.$file;
    $fh = fopen($myFile, 'r');
    $theData = fread($fh, filesize($myFile));
    fclose($fh);
}

?>
