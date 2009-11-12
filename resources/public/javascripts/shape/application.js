var menu_id = "#main_menu";


function accordion(container, opts){
  if(jQuery(container).length && typeof(opts) != "undefined") jQuery(container).accordion(opts);
  else if(jQuery(container).length) jQuery(container).accordion();
};

/*
Everything for the main menu runs from this function
*/
function main_menu(){    
  var acc_id = 'menu-'+jQuery('body').attr('id');
  if(jQuery(acc_id).length) accordion(menu_id, {active: '#'+acc_id});
  else accordion(menu_id);
  
  jQuery(menu_id+' ul.shape_listing li').hover(
    function(){jQuery(this).addClass('ui-state-hover');},
    function(){jQuery(this).removeClass('ui-state-hover');}
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


jQuery(document).ready(function(){
  
  main_menu();
  warnings();
  
});