<?php
/**
* Shape Base Controller
*/
class ShapeBaseController extends WaxController {
  
  public $use_layout = "admin";
  public $use_plugin = "shape"; //this should not be needed - see comment below regarding [DEPRECATION]
  
  public $user_model = "ShapeUser"; //user model
  public $user_table = "shape_user"; //user table
  public $login_path = "/shape/login"; //path to login
  public $login_success = "/shape/dashboard"; //default place to go to
  public $current_user = false; //logged in user object, this set to false means an unauthenticated request and it should be checked on every request
  public $authenticate=false;
  
  public $base_permissions = array("enabled","menu","create","view","delete","edit"); //base permissions to be merged with extended ones
  public $permissions = array(); //stub for extendable permissions, can be added to extended controllers easily
  
  public $model_class;
  public $model_order;  
  public $shape_content;
  public $model; // the working model
  public $wax_form; //wax form for the model  
  public $filters = array();
  
  public $site_name;
  public $widgets = array("shape/_search", "shape/_analytics", "shape/_summary", "shape/_recentpages"); //default widget
  
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
	public function shape_init() {
    $route = rtrim($_SERVER['REQUEST_URI'], "/"); //remove the / at the end of any url    
	  //merge base permissions into extended ones
	  $this->permissions = array_unique(array_merge($this->base_permissions,$this->permissions));
	  if(!$this->auth() && $route != $this->login_path){
	    Session::set('shape_redirect_to', $route);
	    $this->redirect_to($this->login_path);
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
  public function auth(){
    if($this->current_user) return true;
		$this->authenticate = new WaxAuthDb(array("encrypt"=>true, "db_table"=>$this->user_table, "session_key"=>"shape_user_cookie"));
		if($this->current_user = $this->authenticate->get_user()) return true;
		return false;
  }
  
  /**
   * login function
   *
   * @return void
   */
  public function login(){
    $user = new ShapeUser;
    $this->login_form = new WaxForm($user);
    /**
     * form is posted so check the values
     */
    if($user->is_posted() && ($values = Request::param('shape_user')) ){
      /**
       * Only check the database if both values are set - reduce lookups
       * Inform users what they are missing
       * If all good then redirect to where they where trying to get to, or the default
       */
      if(!$values['username'] && !$values['password']) Session::add_error('Please enter username and password');
      elseif(!$values['username'] && $values['password']) Session::add_error('Please enter a username');
      elseif($values['username'] && !$values['password']) Session::add_error('Please enter a password');
      else{
        if($this->authenticate->verify($values['username'], $values['password'])){
          Session::add_message('Welcome Back '.$values['username']);
          $this->current_user = $this->authenticate->get_user();
          if($redirect = Session::get('shape_redirect_to')) $this->redirect_to($redirect);
          else $this->redirect_to($this->login_success);
        }else Session::add_error('Sorry, those details cannot be found, please try again.');
      }
    }
  }
  
  public function logout(){
    Session::unset_var('shape_redirect_to');
    Session::add_message('You have been logged out.');
		$this->authenticate->logout();		
		$this->redirect_to($this->login_path);  	
	}
  
  /** GENERIC ACTIONS **/
  public function index(){}
  /**
   * remove any layout / view / partial cache files
   */
  public function clear_cache(){
    foreach(glob(CACHE_DIR."layout/*") as $file) @unlink($file);
    foreach(glob(CACHE_DIR."view/*") as $file) @unlink($file);
    foreach(glob(CACHE_DIR."partial/*") as $file) @unlink($file);
  }
  /**
   * create an empty skel object, save it and move along to edit
   */
  public function create(){
    $model = new $this->model_class;
     //find the required fields and give them default values
    foreach($model->columns as $name=>$values){
      if($values[1]['unique']) $model->$name = time();
      elseif($values[1] && $values[1]['required'] && !$values[1]['target_model']) {
        if($values[0] == "FloatField" || $values[0] == "IntegerField" || $values[0] == "BooleanField") $model->$name = 0;
        elseif($values[0] == "EmailField") $model->$name = "fill.me@in.com";
      }else $model->$name = $name;
    }
    if($saved = $model->save()) $this->redirect_to("/".$this->controller."/edit/".$saved->primval);
    else{
      Session::add_message('Could not create!');
      $this->redirect("/".$this->controller."/");
    }
  }
  /**
   * Edit function, doesn't do much, just create the page object
   */
  public function edit(){
    //if no idea is found; add error and redirect
    if(!$primval = Request::param('id')){
      Session::add_message('Could not create!');
      $this->redirect("/".$this->controller."/");
    //otherwise create a model
    }else $this->model = new $this->model_class($primval);
    $this->wax_form = new WaxForm($this->model);
        
  }
  
  public function delete(){}
  
  /** GENERIC PARTIALS **/
  /**
   * fetch everything for this model and spit it out
   */
  public function _menu(){
    $model= new $this->model_class;
    if(!$this->shape_content = $model->order($this->model_order)->all()) $this->shape_content = array();
  }
  
  /**widget partials**/
  public function _search(){}
  public function _summary(){}
  public function _analytics(){}
  public function _recentpages(){}
  
  
  /** protected function to check header & layout type **/
  protected function headers_and_layout(){
    if($this->use_format == "ajax" || $this->use_format == "json" || $this->use_format == "xml") $this->use_layout = false;
    if($this->use_format == "xml") header("Content-Type: application/xml");
    if($this->use_format == "json") header("Content-Type: application/json");
  }
  
}?>