<?php

for ($i = 1; $i <= 15; $i++) {
    echo "<div class=\"level_1 listBox_row\" id=\"row_$i\" >";
        echo "<div id=\"$i+.1\" >";
            $width = $i*16*0.5;
            $width = $width."px";
            echo "<div style=\"float:left; width:100px;\">List item $i</div>";
            echo "<div  class=\"value-static-rating\" style=\" width:$width; float:left;\" ></div>";
            echo "<div class=\"clear\" ></div>";
        echo "</div>";
    echo "</div>";
}


?>