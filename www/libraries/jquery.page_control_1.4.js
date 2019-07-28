/*
 * page control function
 *
 * page_control object:
 * 
 * page_url:            ui page
 * save_function:       ui page function to commit page changes to db
 * save_session:        ui page function to commit page to session but not db
 * before_close:        ui page function to run before closing page - won't be run on back or browser close,
 *                      must have redirect(url) set as a callback function (see select_image.php for example)
 * _ignore_dirty:       page uses this parameter to prevent the unbeforeunload event triggering, is reset on page reload
 * pop_up:              if true page acts like pop-up and doesn't set this_page(ui page) as return url to avoid
 * set_is_dirty:        function to set or reset is_dirty flag
 * 
 * page_flow_return:    Retrieves calling page from session and then passes it to redirect function to navigate,
 *                      will call before_close function first if it exists
 * page_flow_set:       Sets the return_url for a ui page when being opened
 
 * close_page:          deprecated: use leave_page - 'page_action: close'close ui page, called by btn_close - will force unsaved_dialog if unsaved changes
 * leave_page:          leave ui page opening new page - take obj_dst as arguments
 *                              obj_page.leave_page({
                                    dst_url:        "/destination url",
                                    rtn_url:        "return url",
                                    page_action:    'leave/close',
                                    dst_type:       "grapes", determines data type to load for dst_url
                                    dst_action:     "open",
                                    object_id:      0, id for dst_type object
                                    parent_id:      vintage_id, id for parent of object_id
                                    child:          true, if child form
                                    form_action:    "put_to_session",
                                    form_dest:      "/vintage/rpc_vintage.php",
                                    form_data:      form_data
                                });
 *
 *
 * Example 1: To save and close a page from btn_save
 * 
 *       save_page(function(){
 *           obj_page.leave_page({
 *               page_action: 'close' //save page and then close
 *           });
 *       });
 * 
 * Example 2: 
 *
 *
 *
 */


  function page_control(options) {
        
        this.default_options =  {
            is_dirty:   false, //is_dirty flag
            _ignore_dirty: false, //used to temporarily prevent beforeunload message
            no_dirty: false, //used to permanently disable dirty status for pages with no save
            page_url: null, //url for the current page
            return_url: null, //url for page to return to
            default_url: "http://whatbottle.com/", //default url for redirection
            home_url: "/index.php", //will not have a return_url as it is the home page
            pop_up: null //if true don't set as return_url on close and leave_page just redirect back
        };
        
        //extend default options
        if(typeof options === "object"){
            this.options = $.extend(this.default_options, options);
        }else{
            this.options = this.default_options;
        }     
        
        var self = this;
        var o = this.options;
        
        //default status flags - reset to these values on status_reset
        this.default_status_flags = { //keep track of status between functions
            db_saved: null, //success if get_from_db was successful otherwise fail
            tracker: null
        };
        

        //*** METHODS ***
       
        this.save_function = o.save_function;
        this.save_session = o.save_session;
        

        this.get_return_url = function(){
             //gets url for return page from page_flow array for page_url

            if(o.page_url){
                
                console.log('get_return_url for page_url: '+o.page_url);
                
                return $.post("/admin/rpc_page_control.php", {
                        rpc_action: 'page_flow_return',
                        this_page: o.page_url
                    }, function(data){

                        if(data.success){

                            if(data.src_url){
                                o.return_url = data.src_url;
                            }else{
                                //no return page found - set to default
                                o.return_url = o.default_url;
                            }
                            
                            console.log("> return_url: "+o.return_url);
                            console.log(o);

                        }else{
                            if(o.page_url !== o.home_url){
                                console.log('get_return_url: rpc: page_flow_return failed with warning: '+data.error);
                            }
                        }
                        

                }, "json");

            }else{
                console.log("get_return_url: no page_url provided");
            }

        };
        
        

        
    this.leave_page = function(obj_dst){
            /* Use this function to leave current page and open an existing record (Edit Record)
             * Use page_action: 'close' to close page and return to rtn_url for opening page
             * 
             *  Properties/Options:
             *      child:    'true' won't commit page to db only to session
             *      dst_url:   url string' page to redirect to
             *      rtn_url:  'url string' return url stored for page being left
             *      page_action: 'leave' | 'close'
             *      required by get_from_db:
             *          dst_type:   'vintage/wine'
             *          object_id:  integer - unique db id for object of dst_url
             *          parent_id:  integer - unique db id for parent on dst_url
             *          dst_action: 'open/add/edit'
             *          before_close: function to call before finally closing page
             */     
            

            console.log("> leave_page");
            console.log('obj_dst:');
            console.log(obj_dst);
            
            if(typeof obj_dst !== 'object'){ //check parameter obj is an object
                var msg = 'leave_page: No obj_dst provided - cannot continue';
                console.log(msg);
                alert(msg);
                return false;
            }
            
            
            if((o.is_dirty || $('#is_dirty').val()>0) && !o.no_dirty && !o._ignore_dirty_deprec){
                //check for unsaved changes - does page need to be saved?
              
                if(obj_dst.form_data){
                    //serialized form_data provided - save it to session
                    //form is dirty - if a child no need to save to db yet
                    
                    console.log('form_data provided - save to session');
                    console.log(obj_dst.form_data);
                    
                //TODO: Move to a saveToSession function ****
                    var save_to_session = $.post(obj_dst.form_dest, {
                                            rpc_action: obj_dst.form_action,
                                            json_values: obj_dst.form_data
                                            }, function(data){}, "json");
                    
                //********
                    
                    save_to_session.done(function(data){   
                        if(data.success){
                            console.log('save_to_session successful');
                        }else{
                            console.log('save_to_session returned an error');
                            console.log(data);
                        } 
                    });
                      
                    save_to_session.fail(function(){
                        console.log('save_to_session failed');          
                    });
                    
                } //obj_dst.form_data
                
            
                
                //child page commit page to session but not to db
                if(obj_dst.child){
                    
                    //set _ignore_dirty
                    o._ignore_dirty = true; //TODO: Not sure this is a good way of handling an object variable within the class
                    
                    console.log("page_control:leave_page: dst is child page - commit to session not to db");
                    
                    //wait for save to session to complete before redirecting
                    save_to_session.done(function(data){
                        
                        if(data.success){
                            console.log('save_to_session successful - now do load dst data and redirect');
                            self.load_and_redirect(obj_dst);
                        }
                        
                    }); //save_to_session.done
                    
                    return;
                
                } //obj_dst.child
                

                //form is dirty - prompt user for action - show unsaved dialog and wait for response
                console.log("page_control:leave_page: page is_dirty - show unsaved changes dialog");
                $.when(this.show_dialog_unsaved()).done(function(data){
                    console.log("showUnsavedDialog returned: "+data.value);
                    console.log(data);
                    var response = data.value;

                    if(response==='save'){//call function to save page
                        
                        $.when(def_return = self.save_function()).then(function(status){ //ensure save_function returns false to prevent redirect
                            //TODO: Use status not def_return
                            //save_page function on pages needs to return promise
                            console.log("save function complete response status: "+status);
                            console.log(obj_dst);
                            
                            if(def_return === false || status === false || status === 'false' || status === undefined){
                                //page failed to save - do not continue
                                console.log('page save_page function returned: '+status);
                                return false;
                            }
                            
                            //page saved successfully
                            o.is_dirty = false;
                            //now redirect page
                            self.load_and_redirect(obj_dst);
                            
                            
                        }, function(status){
                            //save function failed
                            console.log("save_function failed with error: "+status);
                        });

                    } else if (response==='cancel'){
                        //stay on page - do nothing

                    } else if (response==='continue'){
                        //leave page without saving
                        self.load_and_redirect(obj_dst);

                    } //response

                }); //when.show_unsaved_dialog
                        
            }else{
                //page has been saved - load dst and redirect
                console.log("page has no unsaved changes");
                self.load_and_redirect(obj_dst);
   
            }//is_dirty

    }; //this.leave_page
    
    
    
    this.load_and_redirect =  function(obj_dst){
        /* Handles loading dst page to memory/session and setting up page flow
         * before redirecting
         * 
         * get_from_db - to set destination page details if required
         * dst_type determines dst_page details to be loaded in get_from_db
         * 
         */
        
        console.log(" > load_and_redirect");
        
        /*** LEAVE ***/
        if(obj_dst.page_action === 'leave'){
            console.log("page_action=leave");
            
            if(!obj_dst.dst_type){ //no dst_type set so no need to get_from_db
                set_page_flow(obj_dst);
                return true;
            }
            
            $.when(self.get_from_db(obj_dst)).then(function(data){ //get dst_page data from db using object_id
    
                    if(data.success === true){ //get_from_db was successful
                        console.log("fn:load_and_redirect: get_from_db - complete");
                        set_page_flow(obj_dst);
                    } else {
                        var msg = "fn:load_and_redirect: get_from_db - failed error="+data.error;
                        console.log(msg);
                        alert(msg);
                        return false;
                    }

                });
      
        } //leave
        
            
        /*** CLOSE ***/
        if(obj_dst.page_action === 'close') {
            //no need to load data for destination page or set page flow - just redirect
            console.log('page_action = close');
            console.log('obj_page options:');
            console.log(o);

            if(typeof obj_dst.before_close == 'function'){ //call before_close function
                console.log('calling before_close function');
                obj_dst.before_close();
            }

            //set default dest/return url if none set
            if(!o.return_url || o.return_url == null){
                obj_dst.dst_url = o.default_url;
                console.log("return_url not set - redirect to obj_dst.dst_url: "+obj_dst.dst_url);
            }else{
                obj_dst.dst_url = o.return_url;
            }

            console.log("redirect to return_url="+obj_dst.dst_url);
            //alert('about to redirect');
            redirect(obj_dst.dst_url);
            return true;

        } 
            
        //*** Undefined ***
        if(obj_dst.page_action === undefined || obj_dst.page_action === null){
            //no page exit action specified - alert
            var msg = "fn:load_and_redirect: page_action undefined!";
            console.log(msg);
            alert(msg);
            return false;
        }
        
        //*** Set Page Flow ***/
        function set_page_flow(obj_dst){
            //update page flow
            $.when(self.page_flow_set(obj_dst)).then(function(data){ //page_flow_set was successful - redirect
                console.log("redirect to dst_url="+obj_dst.dst_url);
                //alert('about to redirect');
                redirect(obj_dst.dst_url);
                return true;

            }, function(data){;//page_flow_set failed/rejected
                msg = "fn:load_and_redirect: page_flow_set failed cannot continue. error: "+data;
                console.log(msg);
                alert(msg);
                return false;
            });
        }
        
                      
    }; //this.load_and_redirect
     

    
        
    this.show_dialog_unsaved = (function(){
        //show unsaved dialog and return promise with value of button clicked
        
        //determine screen size
        var windowWidth = $(window).width();
        if(windowWidth > 500){
            dialogWidth = 470;
        } else {
            dialogWidth = windowWidth;
        }   
        

        var $dialog = $('#unsaved_changes').dialog({
            autoOpen: false,
            modal: true,
            width: dialogWidth,
            dialogClass: "no-close"
        });

        var showDialog = function(){

            var def = $.Deferred();
            
            var windowWidth = $(window).width();
            if(windowWidth > 500){
                dialogWidth = 470;
                positionMy = "center bottom";
                positionAt = "right top";
                positionOf = ".btn_close";
            } else {
                dialogWidth = windowWidth;
                positionMy = "right top+20px";
                positionAt = "right bottom";
                positionOf = "#top_nav";
            }   
            
            $dialog.dialog({
                buttons: {
                    Save: function() {                        
                        var response = {
                            value: 'save',
                            success: true
                        };

                        def.resolve(response);
                        $(this).dialog('close');

                    },
                    Leave: function() {
                        var response = {
                            value: 'continue',
                            success: true
                        };
                        o._ignore_dirty = true; //prevent unsaved page warning being shown
                        def.resolve(response);
                        $(this).dialog('close');

                    },
                    Cancel: function() {
                        var response = {
                            value: 'cancel',
                            success: true
                        };
                        def.resolve(response);
                        $(this).dialog('close');

                    }
                },
                dialogClass: "clean-dialog",
                position: { my: positionMy, at: positionAt, of: positionOf }
            });

            $dialog.dialog('open');
            return def.promise();
        };

        return showDialog;
        
    })();
    
        
    
 
        this.set_is_dirty = function(state){
        //manage dirty state and save button

            if(state){
                if(!o.is_dirty){
                    //set flag to dirty
                    console.log('set is_dirty: True');
                    o.is_dirty = true;
                    $('#is_dirty').val(1); //hidden field used so that dirty status is serialised with json array to server
                    //reset _ignore_dirty flag
                    o._ignore_dirty = false;
                }
            } else {
                //reset dirty flag and set save button to diabled
                o.is_dirty = false;
                $('#is_dirty').val(0);
                console.log("set is_dirty: False (reset)");
            }

        };


        this.is_dirty = function(){
        //get is_dirty status true/false

            if(o.is_dirty){
                return true;
            } else {
               return false;
            }

        };
        
        this.set_ignore_dirty = function(state){
        //manage ignore dirty state

            if(state){
                console.log('set _ignore_dirty: True');
                o._ignore_dirty = true;
            } else {
                //reset
                o._ignore_dirty = false;
                console.log("set _ignore_dirty: False (reset)");
            }

        };

        
        
        this.close_page = function(option){
        //close page function retuning to rtn_url for ui page
        // arguments:
        //  '_ignore_dirty' to prevent is-dirty flag raising unsaved changes warning
        //get return url details and then use leave_page function
        //Do NOT set return details for return page
           
           console.log ('page_control:close_page');
           
           //get return url
           console.log ('this page = '+o.page_url);
           console.log ('return page = '+o.return_url);
           
           //call leave_page function with return_url as object
            this.leave_page({
                dst_url:        o.return_url,
                page_action:    'close'           
            });      
  
        };
                
        
        function redirect(url){
            //redirect window to provided url
            
            if(url){
                console.log(">> redirecct occurs now <<");
                window.location = url;
            }else{
                console.log("fn:redirect - no url provided");
            }
            
        }

        
        //_CREATE
        this._create = function(){
            console.log("fn:_create");
            console.log("> page_url="+o.page_url);
            //get return url from session
            this.get_return_url();
            
        };
        
        //call constructor function
        this._create();
        
        
        
        this.get_from_db = function(obj_dst){
            //get destination page details from db and put to session
            //preset dst_type allow for simple inclusion in obj_dst to retrieve details from db before
            //redirecting to new page
            
            /*  Properties:  
             *  dst_type:   'vintage,wine'
             *  parent_id:  unique db id for parent on dst_url
             *  dst_action: 'open, add'
             */  
            
            if(typeof obj_dst !== "object"){
                console.log("warning: fn:get_from_db: missing parameter 'obj_dst'");
                return false;
            }
                
            switch (obj_dst.dst_type) {
                
                case 'vintage':
                    console.log("fn:get_from_db - dst_type: vintage");

                    return $.post("/admin/rpc_page_control.php", {
                        rpc_action: 'get_vintage_from_db',
                        vintage_id: obj_dst.object_id,
                        wine_id: obj_dst.parent_id,
                        dst_action: obj_dst.dst_action
                    }, function(data){
                            if(data){
                                console.log('get_vintage_from_db returned:');
                                console.log(data);
                            }else{
                                console.log('get_vintage_from_db didnt return any data');
                            }
                        },"json");

                break;

                case "wine":
                    console.log("fn:get_from_db - dst_type: wine");

                    return $.post("/wine/rpc_wine_db.php", {
                        rpc_action: 'get_from_db',
                        wine_id: obj_dst.object_id,
                        dst_action: obj_dst.dst_action
                    }, function(data){
                        if(!data.success){
                            console.log("get_from_db for wine failed with error = "+data.error);
                        }
                        console.log(data);
                    },"json");

                break;


                case "acquisition":

                    console.log("fn:get_from_db - dst_type: acquisition");

                    return $.post("/acquire/rpc_acquire_db.php", {
                        rpc_action: 'get_from_db',
                        acquire_id: obj_dst.object_id,
                        dst_action: obj_dst.dst_action
                    }, function(data){},"json");

                break;


                case "note":
                    console.log("fn:get_from_db - dst_type: note");

                    return $.post("/vintage/rpc_notes.php", {
                        rpc_action: 'get_from_db',
                        note_id: obj_dst.object_id,
                        vintage_id: obj_dst.parent_id,
                        quality_rating: obj_dst.data.quality_rating,
                        value_rating: obj_dst.data.value_rating,
                        dst_action: obj_dst.dst_action
                    }, function(data){},"json");

                break;

                case "grapes":
                    console.log("fn:get_from_db - dst_type: grapes");
                    //nothing to load - handled server side by page, create success object to return
                    var data = {
                        success: true
                    };
                    return data;

                break;

                case "awards":
                    console.log("fn:get_from_db - dst_type: awards");

                    return $.post("/vintage/rpc_vintage.php", {
                        rpc_action: 'put_temp_awards'
                    }, function(data){},"json");

                break;

                case "image":
                    console.log("fn:get_from_db - dst_type: image");

                    return $.post("/vintage/rpc_vintage.php", {
                        rpc_action: 'put_image_vintage',
                        vintage_id: obj_dst.parent_id
                    }, function(data){},"json");

                break;

                default:
                //dst_type not provided or not recognised   
                console.log("fn:get_from_db - dst_type not recognised ="+obj_dst.dst_type);
                return false;

                }
       
            //};

        };
        
        
        
        this.page_flow_set = function(obj_dst)
        {
            //update page_flow session array
            var def = $.Deferred();
            var return_url;
            
            console.log(o.page_url);
         
            var url = o.page_url; //src or parent page
            if( url.length === 0 ){//check source page url
                var msg = "fn:page_flow_set - page_url not set";
                console.log(msg);
                def.reject(msg);
            }
                    
            if(typeof obj_dst !== "object"){ //check obj_dst is provided
                var msg = "fn:page_flow_set - obj_dst not an object cannot continue";
                console.log(msg);
                def.reject(msg);
            }
            
            console.log(obj_dst);
            
            //override page_url with rtn_url if provided
            var rtn_url = obj_dst.rtn_url;
            if(rtn_url){
                console.log("page_url overwritten by rtn_url: "+obj_dst.rtn_url);
                return_url = rtn_url;
            }else{
                return_url = o.page_url;
            }
            
            if(obj_dst.dst_url === undefined || obj_dst.dst_url === null ){ //page_url not set so use default_page
                console.log("fn:page_flow_set - obj_page.dst_url not set using default_url");
                obj_dst.dst_url  = o.default_url;
            }
       
            if(obj_dst.dst_url){ //dst_url update page_flow controls
                console.log("page_flow_set o:");
                console.log(o);
                console.log(obj_dst);
                
               $.post("/admin/rpc_page_control.php", {
                    rpc_action: 'page_flow_set',
                    dst_page: obj_dst.dst_url,
                    src_page: return_url,
                    this_page: o.page_url
                }, function(data){
                    if(data.success){
                        var msg = 'fn:page_flow_set successful';
                        console.log(msg);
                        console.log(data);
                        def.resolve(msg);
                    }else{
                        var msg = 'fn:page_flow_set failed with error = '+data.error;
                        console.log(msg);
                        def.reject(msg);
                    }
                }, "json");
                
                
            } else {
                //no dst_url provided
                msg = "fn:page_flow_set - no dst_url provided cannot continue";
                console.log(msg);
                def.reject(msg);
            }
            
            return def.promise();

        };
        
        
        
        //EVENTS
        
        $(document).on('change', ":input:not('._ignore_dirty')",function(){
            //set is dirty on change of input

            console.log(":input change input="+$(this).attr('id'));
            //console.log(self);
            self.set_is_dirty(true);
            
        });
        
        
        $(document).on('click', "._ignore_dirty", function(){ //was _ignore_dirty
            //set temp flag to prevent unsaved changes warnings, use for child forms
            //apply _ignore_dirty class to link or button to prevent onbeforeunload message

            o._ignore_dirty = true;
            console.log("_ignore_dirty trigger="+$(this).attr('id'));
  
        });
        
        
        window.onbeforeunload = function (e) {
            var e = e || window.event;
            
            console.log('before unload _ignore_dirty='+o._ignore_dirty+' no_dirty='+o.no_dirty+' page_url='+o.page_url);
            
            if(o.no_dirty===true){
                //suppress all unsaved messages for this form
                return undefined;
            }
            
            if(o.is_dirty && o._ignore_dirty===false){
                //form is_dirty so show customised message in unsaved changes dialog

                var message = 'You have unsaved changes! \n\nAre you sure you want to leave?';
                e.returnValue = message;
                return message;

            } else {
                //reset temp _ignore_dirty flag
                o._ignore_dirty = false;
                //suppress unsaved changes dialog
                return undefined;
            }
            
      
        };
        

    };

