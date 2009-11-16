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
?>