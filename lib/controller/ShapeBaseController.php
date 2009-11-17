<?php
/**
* Shape Base Controller
*/
class ShapeBaseController extends WaxController {
  
  public $use_layout = "admin";
  public $use_plugin = "shape"; //this should not be needed - see comment below regarding [DEPRECATION]
  
  public $controller_list=array();
  
  public $user_model = "ShapeUser"; //user model
  public $user_table = "shape_user"; //user table
  public $login_path = "/shape/dashboard/login"; //path to login
  public $login_success = "/shape/dashboard"; //default place to go to
  public $current_user = false; //logged in user object, this set to false means an unauthenticated request and it should be checked on every request
  public $authenticate=false;
  
  public $base_permissions = array(); //base permissions to be merged with extended ones
  public $permissions = array(); //stub for extendable permissions, can be added to extended controllers easily
  public $excluded_from_permissions = array('__construct', 'controller_global');
  
  public $model_class; //class name
  public $model_order;  //order
  public $shape_models; //WaxRecordSet of models
  public $model; // the working model
  public $wax_form=false; //wax form for the model  
  public $filters = array(); //columns to filter on
  public $string_field = "title"; //field used in list
  public $multi_level = false; //this will trigger fetching of multi level nav - mainly designed for the pages system
  public $model_posted=false;
  public $model_saved=false;
  
  public $site_name; //$_SERVER['http_host']
  public $widgets = array("shape/pages/_search", "shape/statistics/_analytics", "shape/dashboard/_summary", "shape/pages/_recentpages"); //default widget
  
  public $this_page=1;
  public $per_page=20;
  
  public function __construct($run_init = true) {
    parent::__construct($run_init);
	  if($run_init) $this->shape_init();
	}
	
	public function controller_global(){
	  $this->headers_and_layout();
	}
	
	/** 
	* Deferred constructor, so that Shape Controllers can be constructed without any code running if necessary
	**/
	protected function shape_init() {
    $route = rtrim($_SERVER['REQUEST_URI'], "/"); //remove the / at the end of any url
    //check user is logged in
	  if(!$this->auth() && $route != $this->login_path){
	    Session::set('shape-redirect-to', $route);
	    $this->redirect_to($this->login_path);
    }
    //fetch all the registered controllers
    if($controller_list = constant("CONTROLLER_LIST")) $this->controller_list = unserialize($controller_list);
    
    if($route != $this->login_path){
      $this->permissions = $this->permissions();
    }
    
    $this->site_name = $_SERVER['HTTP_HOST'];
	  /*
	    although wax is flagging use_plugin as [DEPRECATION] it still uses it everywhere and doesnt use the plugins array
	    so until then this will be commented out and will use the declaration above
	    
	    $this->add_plugin("shape");
	  */
    
    
	}
  /**
   * auth process. first check if a user exists. then check if they are allowed to do what they're trying to.
   *
   * @return void
   */
  protected function auth(){
    if($this->current_user) return true;
		$this->authenticate = new WaxAuthDb(array("encrypt"=>true, "db_table"=>$this->user_table, "session_key"=>"shape_user_cookie"));
		if($this->current_user = $this->authenticate->get_user()) return true;
		return false;
  }
  /**
   * find the permissions for the controller working on
   * - take the base permissions; which are all allowed (value 1)
   * - merge with extra permissions for other actions
   * - merge that with user based permissions, which would override the values - letting you black list actions
   */
  protected function permissions(){
    //get the base permissions for everything
    $this->base_permissions = $this->base_permissions();  
    //class name
    $class = get_class($this);
    //cast to an array in case empty
    $user_allowed_modules = (array) $this->current_user->permissions($class);
    return array_merge($this->base_permissions[$class], $this->permissions, $user_allowed_modules);    
  }
  /**
   * loop over the controllers and find all public methods
   * - this operation could be heavy so cache it
   */
  protected function base_permissions(){
    $class_name = get_class($this);
    $permissions=array();
    $cache = new WaxCacheFile(CACHE_DIR."shape", "forever", 'cache', CACHE_DIR."shape/base-permissions-".$class_name.".cache");
    if($found = $cache->get()) $permissions = unserialize($found);
    else{
      //loop over the controllers that have been found
      foreach($this->controller_list as $name=>$controller){
        //make a new reflection class to inspect its methods
        $obj = new ReflectionClass($controller);
        //make a controller so we can later check the methods status - public / protected etc
        $controller = new $controller(false);
        //loop over all methods
        foreach($obj->getMethods() as $method){
          //if the class name this method is from matches the controllers name or the base controller
          if(str_replace("Base", "", $method->class) == $name || $method->class == "ShapeBaseController"){
            $this_method = new ReflectionMethod($controller, $method->name);
            //and the method is public then add it to the permissions array..
        		if($this_method->isPublic() && !in_array($method->name, $this->excluded_from_permissions)) $permissions[$name][$method->name] = 1;
          }
        }      
      }
      $cache->set(serialize($permissions));
    }
    return $permissions;
  }
  
  
  /** GENERIC ACTIONS **/
  public function index(){}
  /**
   * remove any layout / view / partial cache files
   */
  protected function clear_cache(){
    foreach(glob(CACHE_DIR."layout/*") as $file) @unlink($file);
    foreach(glob(CACHE_DIR."view/*") as $file) @unlink($file);
    foreach(glob(CACHE_DIR."partial/*") as $file) @unlink($file);
  }
  
  /**
   * Creates an empty version of the model and depending upon
   * result shows edit pages etc
   *
   */
  public function create($model = false){
    if($model instanceof WaxModel) $new_model = $model;
    else $new_model = $this->new_model();
    
    $this->use_view = "edit";
    if($new_model)
      $this->edit($new_model);
  }
  
  /**
   * create an empty skel object, save it and return
   */
  protected function new_model(){
    $model = new $this->model_class;
    //find the required fields and give them default values
    foreach($model->columns as $name=>$values){
      if($name == $model->primary_key || in_array($values[0],array("ForeignKey","HasManyField","ManyToManyField"))) continue;
      if($values[1]['unique']) $model->$name = time();
      elseif($values[1] && $values[1]['required'] && !$values[1]['target_model']) {
        if($values[0] == "FloatField" || $values[0] == "IntegerField" || $values[0] == "BooleanField") $model->$name = 0;
        elseif($values[0] == "EmailField") $model->$name = "fill.me@in.com";
      }else $model->$name = $name;
    }
    if($saved = $model->save()) return $saved;
    else return false;
  }
  /**
   * Edit function, doesn't do much, just call the edit function
   *
   * As the none js version of the page posts to this then
   * it just needs to call the _edit function, which checks
   */
  public function edit($model = false){
    $this->_edit($model);
  }
  /**
   * As the form reference to the edit partial passes in $this,
   * this function should never get called by the partial.
   * 
   * Called via an ajax command, this will check the post data
   * and try to save the model
   */
  public function _edit($model=false){
    
    if($model instanceof WaxModel) $this->model = $model;
    else if(is_numeric($primval = Request::param('id'))) $this->model = new $this->model_class($primval);
    /**
     * check for model posting and save data
     */
    if($this->model){      
      $this->wax_form = new WaxForm($this->model);
      if($this->model->is_posted()){
        $this->model_posted = true;
        if($saved = $this->wax_form->save()){
          $this->model = $saved;
          $this->model_saved = true;
        }else $this->model_saved = false;        
      }      
      
    }else{
      $this->model_posted = $this->model_saved = false;
      $this->use_view = "_not_found";
    }
  }
  
  
  public function delete(){}
  
  /**
   * fetch everything for this model and spit it out
   *
   * Called via partial call without a template passed
   * in & in the case of pages via ajax call
   */
  public function _menu($model = false){
    if(!$model) $model = new $this->model_class;
    $this->shape_models = $model->order($this->model_order)->all();
  }
  
  
  
  
  
  /** protected function to check header & layout type **/
  protected function headers_and_layout(){
    if($this->use_format == "ajax" || $this->use_format == "json" || $this->use_format == "xml") $this->use_layout = false;
    if($this->use_format == "xml") header("Content-Type: application/xml");
    if($this->use_format == "json") header("Content-Type: application/json");
  }
  
}?>