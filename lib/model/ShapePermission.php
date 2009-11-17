<?php
class ShapePermission extends WaxModel {
  public function setup() {
    $this->define("controller", "CharField");
    $this->define("action", "CharField");    
  }
  
  public function title(){return $this->controller . " ".$this->action;}
}
?>