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
  public $login_success = "/shape/dashboard";
  public static $current_user = false; //logged in user object, this set to false means an unauthenticated request and it should be checked on every request
  public $authenticate=false;
  
  public $base_permissions = array("enabled","menu","create","view","delete","edit"); //base permissions to be merged with extended ones
  public $permissions = array(); //stub for extendable permissions, can be added to extended controllers easily
  
  
	function __construct($run_init = true) {
    parent::__construct($run_init);
	  if($run_init) $this->shape_init();
	}
	
	/** 
	* Deferred constructor, so that Shape Controllers can be constructed without any code running if necessary
	**/
	public function shape_init() {
    $route = rtrim($_SERVER['REQUEST_URI'], "/"); //remove the / at the end of any url    
	  //merge base permissions into extended ones
	  $this->permissions = array_unique(array_merge($this->base_permissions,$this->permissions));
	  if(!$this->auth() && $route != $this->login_path) $this->redirect_to($this->login_path);
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
    if(self::$current_user) return true;
		$this->authenticate = new WaxAuthDb(array("encrypt"=>true, "db_table"=>$this->user_table, "session_key"=>"shape_user_cookie"));
		if(self::$current_user = $this->authenticate->get_user()) return true;
		return false;
  }
  
  /**
   * basic login funciton
   *
   * @return void
   */
  public function login(){}
  
}?>