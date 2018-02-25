
<!--Common CSS sheets - add to here to appear in all pages-->

<link type="text/css" href="/css/reset.css" rel="stylesheet" />
<!--<link type="text/css" href="/css/smoothness/jquery-ui-1.8.custom.css" rel="stylesheet" />-->
<link type="text/css" href="/css/jquery-ui.css" rel="stylesheet">
<link type="text/css" href="/css/jquery.jcrop.css" rel="stylesheet" />
<link type="text/css" href="/css/whatbottle_rd.css" rel="stylesheet" />
<?php

//detect iPad and add overide style sheet
if(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')){
    echo "<link type=\"text/css\" href=\"/css/whatbottle_ipad.css\" rel=\"stylesheet\" />";
}

?>