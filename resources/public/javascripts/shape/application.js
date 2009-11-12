
function accordion(container, opts){
  if(jQuery(container).length && typeof(opts) != "undefined") jQuery(container).accordion(opts);
  else if(jQuery(container).length) jQuery(container).accordion();
};

function main_menu(){
    
  var acc_id = 'menu-'+jQuery('body').attr('id');
  if(jQuery(acc_id).length) accordion('#main_menu', {active: '#'+acc_id});
  else accordion('#main_menu');
}


jQuery(document).ready(function(){
  
  main_menu();
  
});