<?php
class ShapeBasePagesController extends ShapeController {
  public $model_class = "ShapePage";
  public $model_order = "`title` ASC";
  public $filters = array('title');  

}?>