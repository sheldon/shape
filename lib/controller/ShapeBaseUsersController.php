<?php
class ShapeBaseUsersController extends ShapeController {
  public $model_class = "ShapeUser";
  public $model_order = "`surname` ASC";
  public $filters = array('firstname', 'surname', 'email');  

}?>