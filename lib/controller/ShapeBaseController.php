<?php
/**
* Shape Base Controller
*/
class ShapeBaseController extends WaxController {
  
  public $use_layout = "admin";
  
  public $user_model = "ShapeUser"; //user model
  public $user_table = "shape_user"; //user table
  public $login_path = "/shape/login"; //path to login
  public static $current_user = false; //logged in user object, this set to false means an unauthenticated request and it should be checked on every request
  
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
	  //merge base permissions into extended ones
	  $this->permissions = array_unique(array_merge($this->base_permissions,$this->permissions));
	  if(!$this->auth()) $this->redirect_to($this->login_path);
	}
  /**
   * auth process. first check if a user exists. then check if they are allowed to do what they're trying to.
   *
   * @return void
   */
  public function auth(){
    if(self::$current_user) return true;
		$auth = new WaxAuthDb(array("encrypt"=>true, "db_table"=>$this->user_table, "session_key"=>"shape_user_cookie"));
		if(self::$current_user = $auth->get_user()) return true;
		return false;
  }
}?>