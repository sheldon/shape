/** CONFIG VARS **/
var menu_config = {"id":"main_menu", "h3_active_class":"active", "h3_hover_class":"ui-state-hover", "active":"ui-state-active"};
var warnings_config = {"class_name":"delete"};
var inline_load_config = {"class_name": "inline_load"};
var filter_config = {"class_name": "filter-form", "timeout":800, "replace_id":"page", "keychange_class":"text_field", "loading_class":"loading", "error_class":"erorr","success_class":"success", "timer":false, "ajax_timeout":1200};

/**
 * function to trigger accordions - menu & page editing
 **/
function accordion(container, opts){
  if(jQuery(container).length && typeof(opts) != "undefined") jQuery(container).accordion(opts);
  else if(jQuery(container).length) jQuery(container).accordion();
};

/**
 * FUNCTIONS FOR FILTERS
 */
/**
 * main handler
 * - checks form for certain attributes to override the presets
 * - filter_form is the form id/class
 */
function filters(filter_form){
  if(typeof(filter_form) == "undefined") var filter_form = "."+filter_config.class_name;
  var timeout = filter_config.timeout;
  if(jQuery(filter_form).attr('timeout')) timeout = jQuery(filter_form).attr('timeout'); //overwrite the timeout with form attribute

  //keypress monitoring
  jQuery(filter_form).find("."+filter_config.keychange_class).keyup(function(e){    
    //only run the filter on certain keys
    if(e.which == 8 || e.which == 32 || (65 <= e.which && e.which <= 65 + 25) || (97 <= e.which && e.which <= 97 + 25) || e.which == 160 || e.which == 127){
      jQuery(this).children(filter_config.keychange_class).removeClass(filter_config.success_class).removeClass(filter_config.error_class).addClass(filter_config.loading_class);
      var obj =jQuery(this);
      var filter_func = function(){submit_filter(obj); };
      filter_config.timer = setTimeout(filter_func, timeout);
    }
  });
  
  //dont allow form submit on filter forms
  jQuery(filter_form).submit(function(){
    return false;
  });
};

/**
 * function to run the filter command
 */
function submit_filter(filter_box){
  var destination = jQuery(filter_box).parents('form').attr('action')+".ajax", //all ajax calls to use the .ajax result
      method = jQuery(filter_box).parents('form').attr('method'),
      replace = "#"+filter_config.replace_id,
      form_data = jQuery(filter_box).parents('form').serialize();
  //clear timeout
  clearTimeout(filter_config.timer);
  
  if(jQuery(filter_box).parents('form').attr('rel')) replace = '#'+jQuery(filter_box).parents('form').attr('rel'); //overwrite the replacement
  //remove classes
  jQuery(filter_box).removeClass(filter_config.success_class).removeClass(filter_config.error_class).addClass(filter_config.loading_class);
  //the ajax call
  jQuery.ajax({
    "timeout": filter_config.ajax_timeout,
    "type":method,
    "url":destination,
    "data": form_data,
    "success":function(result){
      jQuery(filter_box).addClass(filter_config.success_class).removeClass(filter_config.error_class).removeClass(filter_config.loading_class);
      jQuery(replace).html(result);
      clearTimeout(filter_config.timer);
    },
    "error":function(){
      jQuery(filter_box).removeClass(filter_config.success_class).addClass(filter_config.error_class).removeClass(filter_config.loading_class);
      clearTimeout(filter_config.timer);
    }
  });
  
};

/**
 * Everything for the main menu runs from this function
 */
function main_menu(){    
  //menu accordion
  var acc_id = 'menu-'+jQuery('body').attr('id');
  if(jQuery("#"+acc_id).length) accordion("#"+menu_config.id, {active: "#"+acc_id+"-title"});
  else accordion("#"+menu_config.id, {collapsible:true, active:false});

  //add active class for he dashboard
  if(acc_id == 'menu-shape-dashboard') jQuery('#menu-shape-dashboard h3').addClass(menu_config.active);
  
  //hover effects on list items
  jQuery("#"+menu_config.id+' ul.shape_listing li, #menu-shape-dashboard h3').hover(
    function(){jQuery(this).addClass(menu_config.h3_hover_class);},
    function(){jQuery(this).removeClass(menu_config.h3_hover_class);}
  );
  jQuery("#"+menu_config.id+' ul.shape_listing li .icons a').hover(
    function(){jQuery(this).addClass(menu_config.h3_active_class);},
    function(){jQuery(this).removeClass(menu_config.h3_active_class);}
  );

};
/**
 * Common warning functions
 */
function warnings(){
  jQuery("."+warnings_config.class_name).click(function(){
    return confirm('Are you sure you want to do that?');
  });
};

function widgets(){
  
};


/** initialise everything **/
jQuery(document).ready(function(){  
  main_menu();
  warnings();
  widgets();
  filters();
});