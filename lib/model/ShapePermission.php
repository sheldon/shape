<?php
class ShapePermission extends WaxModel {
  public function setup() {
    $this->define("classname", "CharField");
    $this->define("action", "CharField");    
    $this->define("allowed", "BooleanField");    
  }
  
  public function title(){return $this->controller . " ".$this->action;}
}
?>