<?
/**
 * Load up some information about the plugin, branch, directory etc 
 */
define("PLUGIN_NAME", "shape");
define('SHAPE_BRANCH', 'v1');
define("SHAPE_DIR", dirname(__FILE__));
if(is_readable(SHAPE_DIR.'/.git/refs/heads/'.SHAPE_BRANCH)) $revision = file_get_contents(SHAPE_DIR.'/.git/refs/heads/'.SHAPE_BRANCH);
else $revision = "";
define('SHAPE_REVISION', $revision);

/**
 * Find and log all the available modules
 * - check cache
 * - glob the current directory
 * - check its readable
 * - make sure its a controller
 * - add to array
 */
$cache = new WaxCacheFile(CACHE_DIR."shape", "forever", 'cache', CACHE_DIR."shape/modules.cache");

if($found = $cache->get()) $found = unserialize($found);
else{
  $found = array();
  $path = SHAPE_DIR."/resources/app/controller/".PLUGIN_NAME."/*.php";
  foreach(glob($path) as $file){
    if(is_readable($file)){
      $name = substr(basename($file),0,-4);
      $model = new $name(false);
      if($model instanceOf ShapeBaseController) $found[$name] = $name; 
    }  
  }
  $cache->set(serialize($found));
}

define("CONTROLLER_LIST", serialize($found));

?>