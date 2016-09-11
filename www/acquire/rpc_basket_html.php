<?php
/* 
 * Called by Load method on basket panel

 */

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");

//initialise
$var_basket = array();

//create html for basket array
if(count($_SESSION['var_basket']) > 0){
    
    $var_basket = $_SESSION['var_basket'];
    
    ?>
    <table class="basket_table">
        <tbody>
    <?php
    
    foreach($var_basket as $vintage){
        $obj_vintage = new vintage($vintage);
        $vintage_label = $obj_vintage -> vintage_label();
    ?>
       
        <tr>
            <td>
               <?php echo "<input type=\"checkbox\" class=\"basket_vintage_check\" id=\"basket_check=$vintage\" />"; ?>
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
                        <input type="button" class="click btn_basket_vintage_remove" value="Remove">
                    </td>
                </tr>
            </tfoot>
        </tbody>
    </table>
    
<?php

} else {
    
    echo "<p>Basket is empty</p>";
    
}


?>
