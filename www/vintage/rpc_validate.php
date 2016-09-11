<?php
//validates whether selected inputValue corresponds with a unique database entry and returns key

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");

$field = $_POST['field'];
$value = $_POST['value'];

switch ($field){
            
    case 'producer':
       $query =   sprintf("SELECT * FROM tblProducer WHERE producer = '%s' ",mysql_real_escape_string($value));
       $key_field='producer_id';
    break;

    case 'key_country':
        $query =   sprintf("SELECT * FROM tblCountry WHERE country = '%s'",mysql_real_escape_string($value));
        $key_field='country_id';
    break;

    case 'key_region':
        $query =   sprintf("SELECT * FROM tblRegion WHERE region = '%s'",mysql_real_escape_string($value));
        $key_field='region_id';
    break;

    case 'key_subregion':
        $query =   sprintf("SELECT * FROM tblSubRegion WHERE subregion = '%s'",mysql_real_escape_string($value));
        $key_field='subregion_id';
    break;

}//switch

$result = mysql_query($query);
$num_rows = mysql_num_rows($result);

if ($num_rows>=1){
    $status_code='True';
    while($row = mysql_fetch_array($result)){
        $key_value = $row[$key_field];
    }     
}else{
    $status_code='False';
}

//Output XML
header('Content-type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
echo "<response>\n";
echo "\t<status>$status_code</status>\n";
echo "\t<key_value>$key_value</key_value>\n";
echo "\t<producer_key>$key_value</producer_key>\n";
echo "</response>";

?>
