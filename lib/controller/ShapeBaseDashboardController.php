<?php
class ShapeBaseDashboardController extends ShapeController {
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


}?>