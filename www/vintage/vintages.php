<html>
<head>
<title>Vintages</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/Styles/whatbottle.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/includes/nav/topheader.php");

//Respond with Vintage details for Wine
$wine_id = $_GET['ID'];

//Run query
$query ="SELECT * FROM tblVintage WHERE wine_id=".$wine_id." ORDER BY year ASC";
$result = mysql_query($query) or die(mysql_error());	 
//$row = mysql_fetch_array($result) or die(mysql_error());

//Fill drop down
$Wine = $_GET['Name'];
$Wine = stripslashes(rawurldecode($Wine));
echo "<span class=\"FieldLabel\">".$Wine."</span>";
echo "<br/>";
while($row = mysql_fetch_array($result)){
	$year = $row['year'];
	$Index = $row['vintage_id'];
	if($year==0){
			$year = "n/a";
		}
	print "<span class =\"URLResult\"><A href='vintage.php?ID=$Index&$year'>$year</a></span><br/>";
}
include 'footer.php';
?>
</body>
</html>
