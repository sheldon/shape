<?php
class ShapeBaseUsersController extends ShapeController {
  public $model_class = "ShapeUser";
  public $model_order = "`surname` ASC";
  public $filters = array('firstname', 'surname', 'email');  


  /**
   * reset password function
   * - as the partial used in edit is called with $this the function only 
   *   gets called to handle post requests from the ajax
   */
  public function _reset_password(){
    if(is_numeric($primval = Request::param('id'))) $this->model = new $this->model_class($primval);
    /**
     * check for model posting and save data
     */
    if($this->model && ($new_password = Request::post('new_password') ) ){
      $this->model_posted = true;
      $this->model->password = md5($new_password);
      if($saved = $this->model->save()){
        $this->model = $saved;
        $this->model_saved = true;
      }else $this->model_saved = false;
    }elseif($this->model){
      $this->model_posted = true;
      $this->model_saved = false;
    }else{
      $this->model_posted = $this->model_saved = false;
      $this->use_view = "_not_found";
    }
  }

  /**
   * user permissions
   * - again, partial is first called with $this so this is only posted to later
   */
  public function _permissons(){
    if(($class = Request::param('classname')) && ($act = Request::param('act')) ){
      $current = Request::param('current_value');
      if(is_numeric($primval = Request::param('id'))) $this->model = new $this->model_class($primval);      
      if($existing = $this->model->permissions){
        foreach($existing as $found){
          if($found->classname == $class && $found->action == $act) $permission = $found;
        }
      }
      if(!$permission || !$permission->primval) $permission = new ShapePermission;
      $permission->classname=$class;
      $permission->action=$act;
      if($current) $permission->allowed = 0;
      else $permission->allowed = 1;

      $this->model->permissions = $permission; 
      
    }
    $this->use_view = "_permissions";
  }

}?>