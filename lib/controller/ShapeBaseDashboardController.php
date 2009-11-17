<?php
class ShapeBaseDashboardController extends ShapeController {
  
  /**
   * login function
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
  /**
   * logout function, clear the sessions etc
   */
  public function logout(){
    Session::unset_var('shape_redirect_to');
    Session::add_message('You have been logged out.');
		$this->authenticate->logout();		
		$this->redirect_to($this->login_path);  	
	}
  
  //blank out the menu function
  public function _menu(){}

  
  
  public function _summary(){}

}?>