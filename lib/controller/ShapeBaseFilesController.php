<?php
class ShapeBaseFilesController extends ShapeController {
  public $model_class = "ShapeFile";
  public $model_order = "`filename` ASC";
  public $filters = array('filename');  

}?>