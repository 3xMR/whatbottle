<?php
/*Vintage Awards form for new and editing existing wines*/

$root = $_SERVER['DOCUMENT_ROOT'];
require_once("$root/includes/init.inc.php");
require_once("$root/functions/function.php");
require_once("$root/classes/class.db.php");


echo "<html>";
echo "<head>";

require_once("$root/includes/standard_html_head.inc.php");
//include style sheets
require_once("$root/includes/css.inc.php");
//page title
echo "<title>Vintage - What Bottle?</title>";
//include all script libraries
require_once("$root/includes/script_libraries.inc.php");

?>

<script type="text/javascript">

$(document).ready(function(){
    
    //TODO: Convert to pop-up form on vintage page

    //_____Initialise_____
    
    var this_page = "/vintage/select_awards.php";

    //load html on page load
    load_award_html();

     //show ajax activity
    $('#process_indicator')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });

    //initialise basket
    initialise_basket();


    var obj_page = new page_control({
        save_function: function(){
            return save_page('close');  
        },
        page_url: this_page, //set page url
        pop_up: true //return_url remains unchanged
        
    });
    

    //_____Functions_____
    
    function load_award_html(){
       //load award html from remote script
       console.log('load award html');
       $('#awards_container').load('rpc_awards_html.php'); 
    }


    function add_remove_award(key, action){
        //remove award from temp session
        console.log("add_remove: key="+key+" action="+action);
        $.post("/vintage/rpc_add_remove_award.php", {
            key: key,
            action: action
        }, function(data){
            if(data.success){
                //re-load award html
                load_award_html();
                console.log('add_remove action success');
                console.log("array_key="+data.array_key);
                obj_page.set_is_dirty(true);
            } else {
                console.log('add_remove action failed');
                console.log("error_msg="+data.error);
            }
            
        },"json");
    }
    


    //_____Events & Actions_____
    
    
    //Add award and re-load html
    $("#award_select").change(function(){
        //add award to array
        var key = $(this).val();
        console.log('add award_id='+key);
        add_remove_award(key,'add');
    });


    $(document).on('click','.btn_remove', function(){
        var key = $(this).attr('id');
        console.log('remove award_id='+key);
        add_remove_award(key,'remove');
        $(this).parents("div:first").remove();
        set_is_dirty(true);
    });



    $("#award_select").blur(function(){
         $('#award_select').val('0');
    });



    $("#btn_close").click(function(){
        //redirect on cancel
        obj_page.close_page();
  
    });


    $("#btn_save").click(function(){
        //save form to session
        console.log('btn_save...');
        save_page(function(){
            obj_page.leave_page({
                page_action: 'close' //save page and then close
            });
        });
    });
    
    
    function save_page(callback){
        //save page function
        console.log('fnc: save_page');
        
        var def = new jQuery.Deferred();
        
        $.post("/vintage/rpc_vintage.php", {
            action: 'put_awards_session' },
            function(data){
                if(data.success){
                    console.log('save_select_award successful');
                    //redirect to vintage page
                    obj_page.set_is_dirty(false);
                    
                    //display success message
                    $(".con_button_bar").notify("Save Successful",{
                        position: "top left",
                        style: "msg",
                        className: "success",
                        arrowShow: false
                        }
                    );
                    
                    //timeout used to delay callback and allow success msg to be shown
                    var timeoutID = window.setTimeout(delayedFinish,1000);

                    function delayedFinish(){
                        if(typeof callback === 'function'){
                            callback();
                        }
                    }
                    
                    def.resolve(true);
                    
                }else{
                    var msg = 'Save Awards failed with error: '+data.error;
                    console.log(msg);
                    //display error message
                    $(".con_button_bar").notify(msg,{
                        position: "top left",
                        style: "msg",
                        className: "error",
                        arrowShow: false
                        }
                    );
                    
                    def.reject(msg);
                }

        }, "json");
        
        return def.promise();
    }
     

});

</script>
</head>
<body>
    
<?php
//standard dialogs
require_once("$root/includes/standard_dialogs.inc.php");

//page container
echo "<div class=\"page_container\" >";

    //header
    require_once("$root/includes/nav/topheader.php");

    echo "<div class=\"con_single_form rounded\" id=\"award_form_content\" >";
    
    echo "<div class=\"con_form_fields\" >";

        //main heading & ajax process indicator
        echo "<div class=\"con_title_bar\" style=\"float:left;\" >";
            echo "<div style=\"border-bottom: solid 1px darkgray; padding-bottom:5px; margin-top:5px; margin-bottom:5px; \" >";
                echo "<div style=\"float:left; width:58px;\" >";
                    echo "<img src=\"/images/award_flat_grey_64.png\" height=\"48px\" width=\"48px\" >";
                echo "</div>";
                echo "<div style=\"width:auto; float:left; padding-top:5px;\" >";
                    echo "<h1 class=\"inline\" style=\"padding-top:10px;\" >Select Awards</h1>";
                echo "</div>";
                echo "<div class=\"vertical-centre\" style=\"padding-left:15px; float:left; height:50px;\"  >";
                    echo "<img id=\"process_indicator\" src=\"/images/ajax-loader.gif\" height=\"24px\" width=\"24px\" />";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
            echo "</div>";

        echo "</div>"; //con_title_bar

        
        echo "<form action=\"$return_url\" method=\"post\" name=\"frm_edit_awards\" id=\"frm_edit_awards\">";
        
            $wine_id = $_SESSION['var_vintage_temp']['wine_id'];
            $return_url = "/vintage/newvintage.php?action=Return&wine_id=$wine_id";
            
        //select awards
        
        echo "<div class=\"input-main\" style=\"float:left; width:100%; padding-top:20px; padding-bottom:15px;\" >";
            echo "<select name=\"award\" id=\"award_select\" >";
                //set default option
                echo "<option value=\"0\">Select an Award to add...";
                //get award_orgs
                $award_org_obj = new award_org();
                $award_orgs = $award_org_obj -> get();
                foreach($award_orgs as $award_org){
                    //return list of award orgs
                    $item = $award_org['award_org'];
                    $key = $award_org['award_org_id'];
                    echo "<optgroup label=$item>";
                        //retrieve awards for organisation
                        $award_obj = new award();
                        $where = "award_org_id=$key";
                        $sort = " list_position ASC ";
                        $awards = $award_obj -> get($where, $columns=false, $group=false, $sort);
                        foreach($awards as $award){
                        $var_awards = $_SESSION['var_vintage_temp']['var_awards'];
                            $item = $award['award'];
                            $key = $award['award_id'];
                            echo ("<option value=".$key.">".$item);
                        }
                    echo "</optgroup>";
                }
                echo "</select>";
        echo "</div>"; 
     
        //awards html
        echo "<div style=\"clear:left;\" id=\"awards_container\" >";
            //award html loaded here - /vintage/rpc_awards_html.php
        echo "</div>";
        
        echo "<div class=\"clear\"></div>";
                
        //form buttons
        echo "<div class=\"con_button_bar\" >";
            //echo "<hr/>";
            echo "<input type=\"button\" id=\"btn_save\" name=\"btn_save\" value=\"Save\" />";
            echo "<input type=\"button\" id=\"btn_close\" class=\"btn_close\" name=\"btn_close\" value=\"Close\" />";         
        echo "</div>"; //con_form_buttons
        
        //form end
        echo "</form>";

    echo "</div>"; //con_form_fields
    
    echo "<div class=\"clear\"></div>";
    
echo "</div>"; //award_form_content
echo "</div" //page container

?>


</body>
</html>
