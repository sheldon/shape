/** CONFIG VARS **/
var menu_config = {"id":"main_menu", "h3_active_class":"active", "h3_hover_class":"ui-state-hover", "active":"ui-state-active"};
var warnings_config = {"class_name":"delete"};
var inline_load_config = {"class_name": "inline-load", "replace_id":"page", "loading_class":"loading-inline-load", "error_class":"error-inline-load", "success_class":"success-inline-load", "ajax_timeout":1200};
var filter_config = {"class_name": "filter-form", "timeout":800, "replace_id":"page", "keychange_class":"text_field", "loading_class":"loading-filter", "error_class":"erorr-filter","success_class":"success-filter", "timer":false, "ajax_timeout":1200};

/**
 * function to trigger accordions - menu & page editing
 **/
function accordion(container, opts){
  if(jQuery(container).length && typeof(opts) != "undefined") jQuery(container).accordion(opts);
  else if(jQuery(container).length) jQuery(container).accordion();
};
/**
 * used by loading functions to update the accordion menu
 */
function accordion_alteration(obj){
  jQuery('#menu-shape-dashboard h3').removeClass('ui-state-active');
  var trigger = "#"+jQuery(obj).parents('ul').attr('id').replace('-list', '-title');
  console.log(trigger);
  jQuery('#'+menu_config.id).accordion('activate', trigger);
};
/**
 * FUNCTIONS FOR FILTER FORMS
 * form attributes:
 *   action - where the ajax call will go (it will have a .ajax ext)
 *   method - if the call will be post or get
 *   rel - the id of the div to replace
 *   show_and_hide - if the results div is to be show / hidden - like a drop down filter list
 * form data will be serialsed and sent along
 * content returned is presumed to be ajax
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
  //hide the submit buttons
  jQuery(filter_form).find('input[type=submit]').hide();
  //keypress monitoring
  jQuery(filter_form).find("."+filter_config.keychange_class).keyup(function(e){ 
    //clear timeout
    clearTimeout(filter_config.timer);   
    //if short then clear
    if(jQuery(this).val().length < 1){
      var replace = "#"+filter_config.replace_id;
      if(jQuery(filter_form).attr('rel')) replace = "#"+jQuery(filter_form).attr('rel');
      if(jQuery(filter_form).attr('show_and_hide')) jQuery(replace).hide();
    //only run the filter on certain keys  
    }else if(e.which == 8 || e.which == 32 || (65 <= e.which && e.which <= 65 + 25) || (97 <= e.which && e.which <= 97 + 25) || e.which == 160 || e.which == 127){
      jQuery(this).children(filter_config.keychange_class).removeClass(filter_config.success_class).removeClass(filter_config.error_class).addClass(filter_config.loading_class);
      var obj =jQuery(this);
      var filter_func = function(){submit_filter(obj); };
      filter_config.timer = setTimeout(filter_func, timeout);
    }
  });
  //on blur
  jQuery(filter_form).find("."+filter_config.keychange_class).blur(function(){
    
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
      form_data = jQuery(filter_box).parents('form').serialize(),
      show_hide = jQuery(filter_box).parents('form').attr('show_and_hide');
  
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
      clearTimeout(filter_config.timer);
      jQuery(filter_box).addClass(filter_config.success_class).removeClass(filter_config.error_class).removeClass(filter_config.loading_class);
      if(show_hide) jQuery(replace).show();
      jQuery(replace).html(result);
      page_init();      
    },
    "error":function(){
      clearTimeout(filter_config.timer);
      jQuery(filter_box).removeClass(filter_config.success_class).addClass(filter_config.error_class).removeClass(filter_config.loading_class);
      if(show_hide) jQuery(replace).hide();
      page_init();
    }
  });
  
};

/**
 * FUNCTIONS FOR INLINE LOADING OF CONTENT
 * a href attributes
 *  href - location to get data from (.ajax will be appended)
 *  method - to force switch from post to get
 *  rel - override what div to replace the content with  
 *  title - this will replace the page title on successful load
 */
function inline_load(loader){
  var conf = inline_load_config;
  if(typeof(loader) == "undefined") var loader = conf.class_name;
  jQuery("."+loader).click(function(){
    load_page(this);
    return false;
  });
};
/**
 * This handles the loading of the page from the a tag thats been clicked
 */
function load_page(obj){
  var conf = inline_load_config,
      destination = jQuery(obj).attr('href')+".ajax",
      method = "post"
      replace = conf.replace_id;
  if(jQuery(obj).attr('method')) method = jQuery(obj).attr('method');
  if(jQuery(obj).attr('rel')) replace = jQuery(obj).attr('rel');
  
  jQuery(obj).parents('.list_item').removeClass(conf.error_class).removeClass(conf.success_class).addClass(conf.loading_class);    
  jQuery("#"+replace).removeClass(conf.error_class).removeClass(conf.success_class).addClass(conf.loading_class).html(''); //remove classes & blank the html
  
  jQuery.ajax({
    "timeout":conf.ajax_timeout,
    "type":method,
    "url":destination,
    "success":function(result){
      document.title = obj.title;
      if(window.location.toString().indexOf('#')>0) window.location = window.location.toString().substring(0, window.location.toString().indexOf('#')) + "#"+destination;
      else window.location = window.location.toString()+"#"+destination;
      jQuery(obj).parents('.list_item').removeClass(conf.error_class).addClass(conf.success_class).removeClass(conf.loading_class);    
      jQuery("#"+replace).removeClass(conf.error_class).removeClass(conf.success_class).removeClass(conf.loading_class).html(result); //remove classes & blank the html 
      accordion_alteration(obj);
      page_init();      
    },
    "error":function(){
      jQuery(obj).parents('.list_item').addClass(conf.error_class).removeClass(conf.success_class).removeClass(conf.loading_class);    
      jQuery("#"+replace).addClass(conf.error_class).removeClass(conf.success_class).removeClass(conf.loading_class); //remove classes & blank the html 
      page_init();
    }
  });
};
/**
 * Function checks the address bar of the page and looks for the 
 * # mark, if anything is after this mark is a url then tries to
 * load that page
 */
function check_address_bar_for_page_load(){
  var page_url = window.location.toString(), pos = page_url.indexOf('#'), load="";
  if(pos>0){
    var sub_url = page_url.substring(pos).replace("#", "");
    if(sub_url.indexOf("shape")>0) load = sub_url.replace(".ajax", "");
    if(load){
      load_page(jQuery('a[href='+load+']')[0]);      
    }
  }
  
}

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

/**
 * Page init function - handles all ajax effect js
 */
function page_init(){
  
};

/** initialise everything **/
jQuery(document).ready(function(){  
  /* these all trigger once only on page load and should not be effected by any following ajax calls */
  main_menu();
  warnings();
  widgets();
  filters();
  //nuts function that checks current address bar on page load to see if it can recall that page
  check_address_bar_for_page_load(); 
  inline_load();
  /* functions called from page init are effected by ajax calls; so this function is recalled in each ajax call */
  page_init();
});