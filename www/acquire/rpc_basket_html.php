<?php
/* 
 * Called by Load method on basket panel

 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

//initialise
//$var_basket = array();

//create html for basket array
//$basket_count = isset($_SESSION['var_basket']) ? count($_SESSION['var_basket']) : 0;

$obj = new list_has_vintage();
$rst = $obj->get_list_contents();
$count = $obj->count_in_list();
//print_r($rst);
if($count > 0){
    
    //$var_basket = $_SESSION['var_basket'];
    
    ?>
    <table class="basket_table">
        <tbody>
    <?php

    echo "<input type=\"hidden\" id=\"basket_count\" value=\"$count\" />"; //hidden input to tack basket_count
    foreach($rst as $item){
        //$obj_vintage = new vintage($vintage);
        //$vintage_label = $obj_vintage -> vintage_label();
        $vintage_label = $item['producer'].", ".$item['wine']." ".$item['year'];
        $vintage_id = $item['vintage_id'];
        $index = $item['list_has_vintage_id'];
    ?>
       
        <tr>
            <td>
               <?php echo "<input type=\"checkbox\" class=\"basket_vintage_check\" id=\"basket_check=$vintage_id\" data-index=$index />"; ?>
            </td>
            <td>
                <p style="font-size:14px;"><?php echo $vintage_label; ?></p>
            </td>
        </tr>
        
        
    <?php
    } //foreach
    ?>
        
            <tfoot>
                <tr>
                    <td>
                        <?php echo "<input type=\"checkbox\" id=\"basket_check_all\" />"; ?>
                    </td>
                    <td>
                        <input type="button" class="click btn_basket_vintage_remove big_button" value="Remove">
                    </td>
                </tr>
            </tfoot>
        </tbody>
    </table>
    
<?php

} else {
    
    echo "<p style=\"color:gray;\" >empty</p>";
    
}


?>
