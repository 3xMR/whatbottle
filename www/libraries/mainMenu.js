/* 
 * Code to drive main navigation menu
 * Items on menu determined by html on each page
 */



//main menu pop-up
var main_menu = $("#main_menu").menu({
   items: "> :not(.ui-menu-header)",
   select: function( event, ui ) {
        menu_select({ //pass selcted object to menu function
            selected_item: ui.item[0].textContent,
            menu_id: $(this).attr('id')
        });
    }
}).hide();


$('#btn_main_menu').click(function(e) {
    // Make use of the general purpose show and position operations
    // open and place the menu where we want.

    $('.ui-menu:not(#main_menu)' ).hide();

    main_menu.show().position({
          my: "left top",
          at: "left bottom",
          of: this
    });

    $(document).on( "click", function() {
          main_menu.hide();
    });

    //handle touching outside div on iPad
    $(document).on('touchstart', function (event) {
        if (!$(event.target).closest(main_menu).length) {
            main_menu.hide();
        }
    });

    (e).stopPropagation();
    return false;
});



function menu_select(selected_object){
    var selected_item = selected_object['selected_item'];
    var menu_id = selected_object['menu_id'];
    var origin_id = selected_object['origin_id'];
    console.log(origin_id);

    console.log('Menu_Select Function. Item: '+selected_item + ' Menu: '+menu_id + ' origin_id: '+origin_id);

    switch(menu_id){
        case 'main_menu':
            switch(selected_item){
                case 'New Wine':
                    add_wine();
                    break;
                case 'New Acquisition':
                    add_acquisition();
                    break;
                case 'Show Acquisitions': // index.php only
                    $('#panel_right').toggle("slide", { direction: "right" }, 500);
                    break;
                case 'New Note': //tasting_note.php only
                    add_note();
                    break;
                case 'New Vintage': // wine.php only
                    add_vintage();
                    break;
                case 'Wines':
                    open_wines();
                    break;
                case 'Reference Data':
                    open_reference_data();
                    break;
                default:
                    console.log('selected_item not recognised: '+selected_item);
            }
            break;

        default:
            console.log("menu_id not recognised: "+menu_id);
    }

}


function open_wines(){
    //open Wines page
    obj_page.leave_page({
        dst_url: "/index.php",
        dst_action: 'open',
        page_action: 'leave'
    });
}


function open_reference_data(){
    //open ref data page
    obj_page.leave_page({
        dst_url: "/admin/index_admin.php",
        rtn_url: this_page,
        dst_action: 'open',
        page_action: 'leave'
    });
}


function add_wine(){
    //Add new wine
    obj_page.leave_page({
        dst_url:        "/wine/wine.php",
        rtn_url:        "/index.php",
        page_action:    'leave',
        dst_type:       "wine",
        object_id:      null,
        dst_action:     "add"
    });

};

function add_acquisition(){
    //add new acquisition

    obj_page.leave_page({
        dst_url:        "/acquire/acquisition.php",
        rtn_url:        this_page,
        page_action:    'leave',
        dst_type:       "acquisition",
        object_id:      0,
        dst_action:     "add"
    });

};


