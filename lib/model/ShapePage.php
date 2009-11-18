<?php
class ShapePage extends WaxTreeModel {
  public $status_options = array("Draft","Published");
  
  public function setup() {
    $this->define("title", "CharField");
    $this->define("url", "CharField");
    
    $this->define("status", "IntegerField", array("maxlength"=>2));
    $this->define("order_by", "IntegerField", array("maxlength"=>3, "editable"=>false));
    $this->define("pageviews", "IntegerField", array());
    
    $this->define("date_published", "DateTimeField");
    $this->define("date_expires", "DateTimeField");
    $this->define("date_modified", "DateTimeField");
    $this->define("date_created", "DateTimeField");
    
    $this->define("revisions", "HasManyField", array("target_model"=>"ShapeFile"));
    $this->define("users", "ManyToManyField", array("target_model"=>"ShapeUser"));
    //$this->define("snippets", "ManyToManyField", array("target_model"=>"ShapeSnippet"));
    //$this->define("comments", "HasManyField", array("target_model"=>"ShapeComment"));
    //$this->define("categories", "ManyToManyField", array("target_model"=>"CmsCategory"));
  }
}?>