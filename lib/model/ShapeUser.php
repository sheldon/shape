<?php
class ShapeUser extends WaxModel {
  public function setup() {
    $this->define("username", "CharField", array("required"=>true, "unique"=>true));
    $this->define("password", "CharField");
    $this->define("email", "CharField");
    $this->define("firstname", "CharField");
    $this->define("surname", "CharField");
    
    $this->define("pages", "ManyToManyField", array("target_model"=>"ShapePage"));
    //$this->define("permissions", "HasManyField", array("target_model"=>"ShapePermission"));
  }
  
	public function fullname() {
	  return $this->firstname." ".$this->surname;
	}
}?>