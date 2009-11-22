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
  
  public function _get_menu_with_depth($model = false){
    if(!$model) $model = new $this->model_class;
    
    if($target_id = Request::get("target_id")){
      $model = new $this->model_class($target_id);
      $path_from_root = $model->path_from_root();
      array_pop($path_from_root->rowset);
      $children_array = array();
      foreach($path_from_root as $node){
        $children_model = new $this->model_class;
        $children_array[] = $children_model->filter($model->parent_column."_".$model->primary_key,$node->primval)->all();
      }
      print_r($children_array); exit;
    }
  }
  
  public function create($model = false){
    if($this->use_format == "ajax"){
      $this->new_model = $this->new_model();
      $this->new_model->parent_id = Request::get("parent_id");
      $this->new_model->save();
      $this->shape_models = array($this->new_model); //this is so that we can reuse the _menu_list partial to show just 1 item
    }else parent::create($model);
  }
  
  public function _recentpages(){}
  public function _search(){}
}?>