<?php
class ShapeUser extends WaxModel {
  public function setup() {
    $this->define("filename", "CharField");
    $this->define("path", "CharField");
    $this->define("type", "CharField");
    $this->define("description", "CharField");

    $this->define("size", "IntegerField");
    $this->define("downloads", "IntegerField");

		$this->define("date_modified", "DateTimeField");
		$this->define("date_created", "DateTimeField");
    
		$this->define("pages", "ForeignKey", array("target_model"=>"ShapePage"));
  }
}?>