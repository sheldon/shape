<?php
class ShapeBaseUsersController extends ShapeController {
  public $model_class = "ShapeUser";
  public $model_order = "`surname` ASC";
  public $filters = array('firstname', 'surname', 'email');  


  /*reset password function*/
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

}?>