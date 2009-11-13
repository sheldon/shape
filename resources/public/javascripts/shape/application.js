var menu_id = "#main_menu";


function accordion(container, opts){
  if(jQuery(container).length && typeof(opts) != "undefined") jQuery(container).accordion(opts);
  else if(jQuery(container).length) jQuery(container).accordion();
};

/*
Everything for the main menu runs from this function
*/
function main_menu(){    
  //menu accordion
  var acc_id = 'menu-'+jQuery('body').attr('id');
  if(jQuery("#"+acc_id).length) accordion(menu_id, {active: "#"+acc_id+"-title"});
  else accordion(menu_id, {collapsible:true, active:false});

  //add active class for he dashboard
  if(acc_id == 'menu-shape-dashboard') jQuery('#menu-shape-dashboard h3').addClass('ui-state-active');
  
  //hover effects on list items
  jQuery(menu_id+' ul.shape_listing li, #menu-shape-dashboard h3').hover(
    function(){jQuery(this).addClass('ui-state-hover');},
    function(){jQuery(this).removeClass('ui-state-hover');}
  );
  jQuery(menu_id+' ul.shape_listing li .icons a').hover(
    function(){jQuery(this).addClass('active');},
    function(){jQuery(this).removeClass('active');}
  );
  
  
}
/*
Common warning functions
*/
function warnings(){
  jQuery('.delete').click(function(){
    return confirm('Are you sure you want to delete this item?');
  });
}

function widgets(){
  if(jQuery('.widget').length){
    jQuery('.widget').draggable({ snap: true });
  }
}

jQuery(document).ready(function(){
  
  main_menu();
  warnings();
  widgets();
  
});