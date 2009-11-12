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
  
  public $site_name;
  
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
  
}?>