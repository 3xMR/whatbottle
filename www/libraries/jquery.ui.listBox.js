/* 
 * Magnus McDonald 2012
 * Version 1.0
 * 
 * plugin to turn div into listBox with methods
 * originally created for whatbottle
 * depdent on jquery ui widget
 *
 * listBox content rows must have class of .listBox_row
 * clickAdd, clickRemove, and clickEdit to creat buttons for manipulating listbox and use for callbacks
 * TODO: clickSelected - add button to row to open selected item
 * clickTitle event used to clear selection
 * clickRow event triggers when row is clicked and is passed value data from row
 * 
 * 
 * this.selected = id for currently selected div
 * this.selected.data = data.listBox_id - currently selected div
 *                      data.listBox_values[n] - values pulled from children of selected div with class of 'listBox_value click'
 * this.arr_rows = array of all current row ids
 * 
 * 'click' class is added and removed from actionable elements to enable or disable them
 * 
 * PUBLIC METHODS:
 *  refesh
 *  enable
 *  disable
 * 
 */

//TODO clickCustom - allow addition of custom buttom and action
//TODO disabled/enabled - method to disable or enable all actions on listbox (listbox.enabled and listbox.add.enabled)



(function($){

    //listBox prototypes ui widget

    $.widget("ui.listBox", {

        //default options
        options: {
            title: 'listbox title',
            width: 200,
            height: 200,
            listContent: "/vintage/rpc_vintage_acquisitions_html.php",
            scrollTo: true, //scroll to newly added row
            showTitle: true,
            showFilter: false, //show filter box at top of listBox
            showFooter: true,
            showShadow: false,
            showBorder: true,
            showRoundedCorners: true,
            editStatus: 'edit' // (1)'add', (2)'edit', (3)'read'
        },

        
        _create: function() { //private function run at instantiation
                        
            var self = this;
            var o = this.options;
            var el = this.element;
        
            var $this = $(this);
            console.log('listbox');
            console.log($this);
        
            this.listBox_id = el.attr('id');
            this.title_id = this.listBox_id+"_title";
            this.title_text_id = this.title_id+"_text";
            this.body_id = this.listBox_id+"_body";
            this.footer_id = this.listBox_id+"_footer";
            
            //create empty arrays to hold persistant data
            this.arr_rows = new Array();
            this.newRow = undefined;
            this.arrOpenCons = new Array();
            
            this.selected = undefined;
            this.selected_data = undefined;

            el.width(o.width);
            //el.height(o.height); //will be corrected at the end based on options
            el.addClass('listBox');
            
            if(!this.editStatus){ //apply default if no editStatus set
                this.editStatus = o.editStatus;
            }
            
            //add title
            var title = "<div><div style='padding:5px;'><p>"+o.title+"</p></div></div>";
            self.el_title = $(title).appendTo(el)
                .addClass('listBox_title')
                .addClass(o.addClass) //adds class for custom styling
                .attr('id',this.title_id)
                .on("click", $.proxy(this._titleClick, this));
                
            //add filter div
            if(o.showFilter){
                var button_width = 35;
                var filter_input_width = (o.width - button_width)+'px';
                console.log('filter_width='+filter_input_width);
               
                   
                var filter =    "<div class=\"vertical-centre listBox_filter\" >\
                                    <input style=\"width:"+filter_input_width+"; float:left; margin-left:5px;\" class='listBox_filter'  id='"+self.listBox_id+"_filter_input' autocomplete='off' placeholder='filter...' />\
                                    <image src='/images/delete_grey_flat_32.png' style='float:right; margin-right:8px;' class='click filterClear' id='"+self.listBox_id+"_btn_clear_filter' height='12px' width='12px' />\
                                </div>";

                
                self.el_filter = $(filter).appendTo(el)
                        .attr('id',self.listBox_id+"_filter")
                        .addClass("listBox_filter")
                        .addClass(o.addClass); //adds class for custom styling

                //add on_change event to filter input
                $("#"+self.listBox_id+"_filter_input")
                    .on('keyup', function(){
                        //call filter function
                        self.filter($(this).val());
                    })
                    .width(filter_input_width); 
            
                //add on click event to clear filter button
                $("#"+self.listBox_id+"_btn_clear_filter")
                    .on('click', function(){
                        //clear filter
                        $("#"+self.listBox_id+"_filter_input").val('');
                        $(self.el_body).children('.listBox_row').show();
                        //call optional function if provided
                        self._filterClearClick();
                    });
                    
            } //add filter


            //add body
            var body = "<div></div>";
            self.el_body = $(body).appendTo(el);
            self.el_body
                .height(o.height) //height needs to be fixed should be height for total listbox
                .addClass('listBox_body')
                .addClass(o.addClass) //adds class for custom styling
                .attr('id',self.body_id)
                .on('click', '.listBox_row', function(){;
                    //row click event
                    console.log('row click event fired');
                    if( $(this).hasClass("click") ){
                        self._rowClick(this);
                    }
                });

            //footer
            var footer = "<div></div>";
            self.el_footer = $(footer).appendTo(el)
                .addClass('listBox_footer')
                .addClass(o.addClass) //adds class for custom styling
                .attr('id',this.footer_id);
            
            var footer_buttons = "<div id='"+self.listBox_id+"_footer_buttons' class='listBox_footer_buttons'></div>";
            self.el_footer_buttons = $(footer_buttons).appendTo(self.el_footer);
            
       
            //button size
            var button_size = '20px';
            if(o.buttonSize){ //button size overide set in options
                button_size = o.buttonSize;
            }
            
            //buttons
            $.each(o, function(name, value) {
            //console.log(name + ": " + value);
                if(name==='clickAdd'){
                    var btn_add = "<img src='/images/plus_grey_flat_32.png' height='"+button_size+"' width='"+button_size+"' id='"+self.listBox_id+"_btn_add' >";
                    this.btn_add = $(btn_add).appendTo(self.el_footer_buttons)
                        .addClass('click listBox_button')
                        .click(function(){
                            if( $(this).hasClass("click")){ //test for click class used to enable/disable action
                                self._addClick();
                            }
                        });
                }else if(name==='clickRemove'){
                    var btn_remove = "<img src='/images/minus_grey_flat_32.png' height='"+button_size+"' width='"+button_size+"' id='"+self.listBox_id+"_btn_remove' >";
                    this.btn_remove = $(btn_remove).appendTo(self.el_footer_buttons)
                        .addClass('click listBox_button')
                        .click(function(){
                            if( $(this).hasClass("click")){ //test for click class used to enable/disable action
                                self._removeClick();
                            }
                        });
                }else if(name==='clickEdit'){
                    var btn_edit = "<img src='/images/edit_flat_grey_24.png' height='"+button_size+"' width='"+button_size+"' id='"+self.listBox_id+"_btn_remove' >";
                    this.btn_edit = $(btn_edit).appendTo(self.el_footer_buttons)
                        .addClass('click listBox_button')
                        .click(function(){
                            if( $(this).hasClass("click")){ //test for click class used to enable/disable action
                                self._editClick();
                            }
                        });
                }else if(name==='clickFilter'){ //filter button in button bar
                    var btn_filter = "<img src='/images/filter_flat_grey_24.png' height='"+button_size+"' width='"+button_size+"' id='"+self.listBox_id+"_btn_filter' >";
                    this.btn_filter = $(btn_filter).appendTo(self.el_footer_buttons)
                        .addClass('click listBox_button')
                        .click(function(){
                            if( $(this).hasClass("click")){ //test for click class used to enable/disable action
                                self._filterClick();
                            }
                        });
                }
                
            });
            
            //show or hide key elements
            if(!o.showTitle){
                self.el_title.css('display', 'none'); //hide title
            }
            
            if(!o.showFooter){
                self.el_footer.css('display','none'); //hide footer
            }
            
            
            //add border formating
            if(o.showBorder){
                el.addClass('listBox_element_border');
            }else{
                el.removeClass('listBox_element_border');
            }
            
            if(o.showRoundedCorners){
                //console.log(el.children(':visible').first().attr('id'));
                //console.log(el.children(':visible').last().attr('id'));
                el.children(':visible').first().addClass('listBox_top_rounded'); //add class to first visible child element within el;
                el.children(':visible').last().addClass('listBox_bottom_rounded'); //add class to the last visible child element within el;
                el.addClass('listBox_element_rounded'); //apply after top and bottom
            }else{
                el.children(':visible').first().removeClass('listBox_top_rounded');
                el.children(':visible').last().removeClass('listBox_bottom_rounded');
                el.removeClass('listBox_element_rounded');
            }
            
            if(o.showShadow){
                el.addClass('listBox_shadow'); //add class to element to show shadow
            }else{
                el.removeClass('listBox_shadow'); //add class to element to show shadow
            }
            
            //adjust height of listBox
            var hList = el.height();
            console.log('listBox target height = ' + o.height);
            console.log('listBox height = ' + hList);
            console.log(el);
            var hBody = self.el_body.height();
            var hFooter = self.el_footer.css('height');
            console.log('body height = ' + hBody);
            console.log('footer height = ' + hFooter);
            var hDiff = hList - o.height;
            console.log('Difference between listBox height and target height = ' + hDiff);
            var hBody = hBody - hDiff;
            console.log('New Body height = ' + hBody);
            self.el_body.height(hBody);
            
            //load listbox rows
            var def = self.refresh(); 
            
            def.then(function(){
                //once refresh function has completed
                console.log("_create complete");
                self.configStatus(); //set state of controls based on editStatus 
            });
            
        }, //_create
        
        _showFilter: function(){
            var self = this;
            
            //add filter div
            var el = self.el_body.appendTo("<div></div>");
            el.attr('id',self.listbox_id+"_filter").addClass("listBox_filter");
            
        },
        
        
        destroy: function() {
            $("#"+this.title_id).remove();
            $("#"+this.body_id).remove();
            $("#"+this.footer_id).remove();

        },
        
        configStatus: function(status){
            //configure buttons and enable/disable based on status
            // [1]'add' [2]'edit' [3]'read'
            var self = this;
            var s = self.editStatus;
            
            if(status){
                self.editStatus = status;
                s = status;
            }
            
            console.log('*** configStatus = '+s);
            
            if( s=='add' || s==1){ //add/new
                //self.enable();
                self.button('add','disable');
                self.button('edit','disable');
                self.button('remove','disable');  
                $("#"+self.body_id).find(".listBox_row").removeClass("click"); //disable rows
            }
            
            if( s=='edit' || s==2 ){ //edit
                //self.enable();
                self.button('add','enable');
                self.button('edit','enable');
                self.button('remove','enable');   
                $("#"+self.body_id).find(".listBox_row").removeClass("click").addClass("click"); //enable rows
            }
            
            if( s=='read' || s==3 ){ //read-only
                console.log('*** configStatus = read-only');
                //self.disable();
                self.button('add','disable');
                self.button('edit','enable');
                $("#"+self.body_id).find(".listBox_row").removeClass("click").addClass("click"); //enable rows
            }
            
        },
        
        _setOption: function(option, value) {
            $.Widget.prototype._setOption.apply( this, arguments );

            var el = this.element;

            switch (option) {
                case "title":
                    $("#"+this.title_text_id).replaceWith(value);
                    break;
                case "color":
                    el.next().css("color", value);
                    break;
                case "backgroundColor":
                    el.next().css("backgroundColor", value);
                    break;
            }
        },
        
        _titleClick: function(event){
            
            this.selected = null;
            //console.log("_titleClick");
            //console.log(this);
            
            this._clear();
            this._trigger('clickTitle', null, this.options.title);
        },

        _rowClick: function(object){
            //row click event
            var $self = this;
           
            //get data from selected row
            var data = $(object).data();
            
            //put data object to listBox
            $self.selected = object; 
            $self.selected_data = data;
            
            console.log("*** _rowClick function ***");
            console.log($self);
            console.log($self.selected);
            console.log($self.selected_data);
    
            //remove highlight from all rows
            $("#"+this.body_id).find(".listBox_row").removeClass('row_selected');
            
            //add highlight to selected row
            $(object).addClass('row_selected');
            
            $self._displayParents(object);
           
            //identify child_con
            var el = $(object).next('.child_con');
            
            //ignore this code if no child_con found
            if(el){ 
                //identify status indicator (collapse/expand)
                var indicator = $(object).find(".listBox_status");

                if(el.is(':visible')){
                    //con_child is hidden - show
                    el.hide();
                    indicator.addClass('listBox_expand');
                    indicator.removeClass('listBox_collapse');
                }else{
                    el.show();
                    indicator.addClass('listBox_collapse');
                    indicator.removeClass('listBox_expand');
                }
            }else{
                console.log("no child_con found");
            }
            
            //update stored data object to persist state
            var obj_persist = {
                row_selected: object
            };
            
            //call clickSelected event
            this._trigger('clickSelected', null, data);
            
            //call rowClick function if set
            o = $self.options;
            if(this._isFunction(o.clickRow)){
                //o.clickRow(data);
            };
            
        },
        
        _displayParents: function(el){
            //display parents for el(ement)
            //test function used to find and highlight parents when searching a tree using filter - NOT COMPLETE
            $self = this;
            
            $(el).parents('.child_con').each(function(){
                var parent_id = $(this).attr('id');
                //console.log('v '+parent_id);
                var arr = parent_id.split("_");
                //console.log(arr);
                //console.log('array length ='+arr.length);
                var db_id = arr[arr.length - 1];
                var level_id = arr[arr.length - 2];
                //console.log('db_id: '+db_id+' level_id: '+level_id);
                if(level_id == 1){
                    //top level has different format
                    var parent_row = $self.listBox_id+"_row_"+db_id;
                }else{
                    var parent_row = $self.listBox_id+"_row_"+level_id+"_"+db_id;
                }
                
                //console.log('parent_row = '+parent_row);
                //$('#'+parent_row).addClass('showMe').show();
                $('#'+parent_row).show(); //show parents
                
            });
            
        },

        _addClick: function(event){
            //add button click event
            this._trigger('clickAdd', null, this.selected_data);
        },

        _removeClick: function(event){
            //remove button click event
            if(this.selected){
               this._trigger('clickRemove', null, this.selected_data);
                console.log('_removeClick');
                //clear selected data use - clearSelected function
            }
        },

        _editClick: function(){
            //add button click event
            if(this.selected){
                this._trigger('clickEdit', null, this.selected_data);    
            }else{
                console.log('nothing selected');
            }
        },
        
        _filterClick: function(event){
            //click filter button in button bar
            if(this.selected){
                this._trigger('clickFilter', event, this.selected_data);
            }
        },
        
        _filterClearClick: function(){
            //click filter clear button in filter input to remove search filter
            console.log('clickFilterClear');
            this._trigger('clickFilterClear', null, this.selected_data);
            this.clearSelected();
            
        },
        
        _refresh: function(){
            //load listbox content
            var deferred = $.Deferred();
            var o = this.options;
            var $self = this;
            var addedRow = new Array();
            
            console.log("*** listbox _refresh:");
            $self.el_body.addClass("listBox_spinner"); //start activity spinner
            
            $self.new_arr_rows = new Array();
            $self.newRow = undefined; //reset
            
            function diffArray(a, b) { //function to create diff between two arrays
                var seen = [], diff = [];
                for ( var i = 0; i < b.length; i++)
                    seen[b[i]] = true;
                for ( var i = 0; i < a.length; i++)
                    if (!seen[a[i]])
                        diff.push(a[i]);
                return diff;
            }         
            
            //record current position of listbox items
            console.log('element at top of listbox');
            $self.topRow = undefined;
            var $top = $('#'+$self.body_id).offset().top; //determine poistion of top of listbox_body
            $("#"+$self.body_id+ " .listBox_row").each(function() { //identify which row is at the top of the listbox
                if($(this).offset().top >= $top) {
                    var data = $(this).data(); //get embedded data from object
                    if(data.listBox_id >= 1){
                        var top_row_id = data.listBox_id;
                    }
                    console.log('top row id = '+top_row_id);
                    console.log(this);
                    $self.topRow = this;
                    return false;
                }
            });
           
            
            //load content and then process rows
            $self.el_body.load($self.options.listContent, function(){
                
                //walk row to set id and level in .data
                $(this).children('.listBox_row').each(function(){
                    //walk rows and set correct ids for each level
                    var level = 1; //first level
                    var id = $(this).attr('id');
                    var row_id = $self.listBox_id+"_row_"+id;
                    //set new id
                    $(this).attr('id', row_id );
                    //add custom style class
                    $(this).addClass(o.addClass);
                    
                    var arr_values = new Array();
                    $(this).find(".listBox_value").each(function(){
                        arr_values.push($(this).val());
                    });
                    
                    //set level and parent
                    $(this).data({
                        listBox_id: id,
                        listBox_level: level,
                        listBox_parent_id: 0, //set to 0 as this is the top level
                        listBox_values: arr_values
                    });
                    
                    //add row_id to array
                    $self.new_arr_rows.push(row_id);
                    
                    if($(this).next(".child_con").length > 0){
                        //row has children i.e. tree listbox
  
                        var el = $(this).next(".child_con");
                        
                        //rename child_con id
                        var con_id = $self.listBox_id+"_child_con_"+level+"_"+id;
                        el.attr('id',con_id);
                           
                        //start walking
                        var level = 2;
                        var parent_id = id;
                        
                        $self._walkRows(el,level,parent_id);
                    };

                });
                
                
                //close all child_cons
                $("#"+$self.body_id).find(".child_con").hide();
                    
                //set child_con to visible based on persisted array arrOpenCons
                //console.log('arrOpenCons.length='+$self.arrOpenCons.length);
                if($self.arrOpenCons.length > 0){
                    $($self.arrOpenCons).each(function(){
                        //console.log("open persisted objects")
                        var child_con_id = $(this).attr('id');
                        //console.log(child_con_id);
                        //$("#con_listBox_location_child_con_1_7").show();
                       $("#"+child_con_id).show().prev().find(".listBox_status").removeClass('listBox_expand listBox_collapse').addClass('listBox_collapse');
                    });
                }
                
              
                if($self.arr_rows.length > 0){ //only run diff if existing row array exists
                   //determine if a new row has been added
                    addedRow = diffArray($self.new_arr_rows, $self.arr_rows);
                    console.log("added = "+ addedRow);
                    $self.newRow = addedRow; 
                }
                
                //make the new row array the current array
                if($self.new_arr_rows.length > 0){
                    $self.arr_rows = $self.new_arr_rows;
                }
               
                //re-select the currently selected row
                console.log("selected row data:");
                console.log($self.selected_data);
                
                //run callback if present
                if(typeof callback === 'function'){
                    callback();
                }
                
                //stop activity spinner
                $self.el_body.removeClass("listBox_spinner");
                console.log("*** listbox load completed");
                
                deferred.resolve();
                

            });
            
            return deferred.promise();
        },
        
        refresh: function(clear_selected, callback){
            //control refresh of listbox
            $self = this;
            var deferred = $.Deferred();
            var listbox_name = $self.listBox_id;
            
            if(clear_selected === true){ //clear selected item before refreshing - complete refresh
                console.log('clear selected item for a complete refresh');
                $self.selected = undefined;
                $self.selected_data = undefined;
            }
            
            var response = $self._refresh(); //call refresh function and return promise
            
            response.promise().done(function(){
                console.log("_refresh done for "+listbox_name);
                deferred.resolve();
                $self.scrollTo(); //call scrollTo function
            });
            
            $("#"+$self.listBox_id+"_filter_input").val('');
            
            return deferred.promise();
            
        }, //refresh
        
        clearSelected: function(){
            //clear selected item
            $self = this;

            console.log('clearSelected item');
            $self.selected = undefined;
            $self.selected_data = undefined;
            $("#"+$self.body_id).find('.listBox_row').removeClass('row_selected'); //remove selected highlight class
 
        }, //clear_selected
        
        
        disable: function(){
            //disable all buttons, actions and events
      
            //buttons
            $("#"+this.footer_id).find(".listBox_button").removeClass("click");
            $(".listBox_button").hide("fast");
             
            //rows
            $("#"+this.body_id).find(".listBox_row").removeClass("click");
            //remove highlight from all rows
            $("#"+this.body_id).find(".listBox_row").removeClass('highlight');
            
        },
        
        button: function(button_name, action){
            //disable or enable button
            
            var self = this;
            var element;
            
            //buttons
            if(button_name){
                switch (button_name) {
                    case "add":
                        element = "#"+self.listBox_id+"_btn_add";   
                        break;
                    case "edit":
                        element = "#"+self.listBox_id+"_btn_edit";
                        break;
                    case "delete":
                        element = "#"+self.listBox_id+"_btn_remove";
                        break;
                }
            }
            
            if(action === 'enable'){
                $(element).removeClass("click").addClass("click").show("fast");
            }
            
            if(action === 'disable'){
                $(element).removeClass("click").hide();
            }

        },
        
        enable: function(){
            //disable all buttons, actions and events
            
            //buttons
            $("#"+this.footer_id).find(".listBox_button").removeClass("click").addClass("click");
            $(".listBox_button").show("fast");
            
            //rows
            $("#"+this.body_id).find(".listBox_row").removeClass("click").addClass("click");
  
        },
        
        _walkRows: function(element, level, parent_id){
            //process listbox html to provide unique and workable ids
            var o = this.options;
            var self = this;
            
            $(element).children('.listBox_row').each(function(){
                //retrieve id
                var id = $(this).attr('id');
                //create new id
                var row_id = self.listBox_id+"_row_"+level+"_"+id;
                //set new id
                $(this).attr('id', row_id );
                //add custom style class
                $(this).addClass(o.addClass); //adds class for custom styling;
                
                //create array of values for row
                var arr_values = new Array();
                
                $(this).find(".listBox_value").each(function(){
                    arr_values.push($(this).val());
                });
                
                //set level data
                $(this).data({
                    listBox_id: id,
                    listBox_level: level,
                    listBox_parent_id: parent_id,
                    listBox_values: arr_values
                });;
                
                //add row_id to array
                self.new_arr_rows.push(row_id);
                
                //has element got children
                if($(this).next(".child_con").length > 0){   
                    var el = $(this).next(".child_con");
                    var next_parent_id = id;
               
                    //rename child_con id
                    var con_id = self.listBox_id+"_child_con_"+level+"_"+id;
                    el.attr('id',con_id);
                    
                    //increment level
                    var next_level = level + 1;
                    
                    //walk the next level
                    self._walkRows(el, next_level, next_parent_id);
                };
                   
            });
   
        },

        _clear: function(){
            //clear selection
            var self = this;
            
            //remove all highlights
            $("#"+self.body_id+" > .listBox_row").removeClass('highlight');
            
            //reset all status indicators
            $("#"+self.body_id).find(".listBox_status").removeClass("listBox_collapse").removeClass("listBox_expand").addClass("listBox_expand");
            
            //close all child cons
            $("#"+self.body_id).find(".child_con").hide();
            
            //reset selection properties
            this.selected = undefined;
            this.selected_data = undefined;
            
            //scroll to top of list
            $("#"+self.body_id).scrollTop(0);
        },
        
        persist: function(){
            //persist listbox state in cookie
            var $self = this;
            var body = $self.el_Body;
            
            
            //console.log('persist open cons');
            console.log('persist...');
            console.log($('#'+$self.body_id));
            $('#'+$self.body_id).find(".child_con").filter(":visible").each(function(){
                console.log(this);
            });
            

        },
        
        scrollTo: function(element, select){
            // scrollTo row passed as element
            // select is true will higlight and trigger click on row
            
            $self = this;
            
            console.log('scrollTo function');
            
            //element provided - scroll to element
            if(element){
                console.log('scrollTo: element provided');
                $self.scrollToRow(element, select);
                return true;
            }
            
            //Add - newRow defined - scroll to element
            if($self.newRow !== undefined){ //new row added - scroll to new row
                if($self.newRow.length > 0){
                    console.log('scrollTo: new row added');
                    $self.scrollToRow($('#'+$self.newRow),true);
                    return true;
                }
            }
            
            //edit - row is selected - scroll to edited element
            if($self.selected){ //new row added - scroll to new row
                console.log('scrollTo: row edited');
                $self.scrollToRow($self.selected, true);
                return true;
            }
            
            //remove - no row is selected and no newRow - scroll to last position
            if($self.topRow){ //new row added - scroll to new row
                console.log('scrollTo: row removed');
                $self.scrollToRow($self.topRow, false); //do not select row after deletion
                return true;
            }
                          
            
        }, //scrollTo
        
        scrollToRow: function(element, select_row){
            //scroll to provided row, if select_row is true trigger click event on row
            var $self = this;
            
            console.log('scrollToRow');
            
            var container = $('#'+$self.body_id);
            var id = $(element).attr('id');
            var scrollTo = $("#"+$(element).attr('id'));
            
            
            console.log('id, container, scrollTo:');
            console.log(id);
            console.log(container);
            console.log(scrollTo);
            
            
            //is div visible? open divs to show modified row
            if(!$("#"+$(element).attr('id')).is(':visible')){
                $("#"+$(element).attr('id')).parents('.child_con').show(); //show parents
            }
            
            container.animate({
                scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
            });
            
            //select_row if true
            if(select_row){
                $self.selectRow(element); //trigger click event on row
            }

        },
        
        _selected: function(){
            //return data object for selected row
            $self = this;
            
            if($self.selected){
                return $self.selected.data;
            }else{
                return false;
            }
        },
        
        selectRow: function(element){
            //select row and trigger click event

            if(element){ //trigger directly on element if provided
                console.log('selectRow: element provided: ');
                console.log(element);
                var row_div = $(element).attr('id');
                $('#'+row_div).trigger('click');
                return true;
            }

        },
        
        hasChildren: function(){
            //returns number of children that provided element has
            var self = this;
 
            var len = $(self.selected).next('.child_con').children('.listBox_row').length;
            
            if(len > 0){
                //row has children
                return len;
            }else{
                return 0;
            }
            
        },
        
        filter: function(text){
            //filter rows based on text search
            var $self = this;
            var body = $self.el_body;
            
            console.log('filter: text: '+text);
                    
            //extend the jquery selector to make contains not case sensitive
            jQuery.expr[':'].Contains = function(a, i, m) { 
                return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0; 
            };
                 
            $(body).find('.child_con').show();
            
            if(text){
                var filter = ":Contains("+text+")"; //create filter
                $(body).find('.listBox_row').hide(); //hide all rows and then only show those that match filter
                $(body).find('.listBox_row').filter(filter).each(function(){
                    $(this).show();
                    $self._displayParents(this);
                });     
            }else{
                //show all
                console.log('filter: no text');
                $(body).find('.listBox_row').show();
                $(body).find('.child_con').hide();
            }
        }, //filter
        
        _isFunction: function(functionToCheck){
            var getType = {};
            return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
        }
        
        
    });


})(jQuery); //end plugin