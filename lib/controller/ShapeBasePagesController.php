<?php
class ShapeBasePagesController extends ShapeController {
  public $model_class = "ShapePage";
  public $model_order = "`title` ASC";
  public $filters = array('title');
  public $multi_level = true;

  public function _menu($model = false){
    if(!$model) $model = new $this->model_class;
    
    if($parent_id = Request::post("parent_id"))
      $model->filter($model->parent_column."_".$model->primary_key,$parent_id);
    else{ //choose roots if no id is specified
      $model->filter("({$model->parent_column}_{$model->primary_key} = {$model->primary_key}
                    OR {$model->parent_column}_{$model->primary_key} NOT IN (SELECT {$model->primary_key} FROM `{$model->table}`)
                    OR {$model->parent_column}_{$model->primary_key} IS NULL)");
    }
    
    parent::_menu($model);
  }
  
  public function create($model = false){
    if($this->use_format == "ajax"){
      $this->new_model = $this->new_model();
    }else parent::create($model);
  }
  
  public function _recentpages(){}
  public function _search(){}
}?>