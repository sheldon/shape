<?php
class ShapeUser extends WaxModel {
  public function setup() {
    $this->define("username", "CharField", array("required"=>true, "unique"=>true));
    $this->define("password", "PasswordField");
    $this->define("email", "CharField");
    $this->define("firstname", "CharField");
    $this->define("surname", "CharField");
    
    $this->define("pages", "ManyToManyField", array("target_model"=>"ShapePage"));
    $this->define("permissions", "HasManyField", array("target_model"=>"ShapePermission"));
  }
  
	public function fullname() {
	  return $this->firstname." ".$this->surname;
	}
	
	public function permissions($class=false){
    $modules = array();
    foreach($this->permissions as $perm) $modules[$perm->classname][$perm->action] = $perm->allowed;
    if($class) return $modules[$class];
    else return $modules;
	}
	
	public function all_permissions($base_permissions, $controller_list){
	  $all_permissions = array();
	  //cast to an array in case empty
    $user_allowed = (array) $this->permissions();
    $controller_allowed=array();
    foreach($controller_list as $classname=>$controller){
      $obj = new $classname(false);
      //merge the base permissions for this class with the obj permissions
      $perm = array_merge($obj->permissions, (array) $base_permissions[$classname]);
      $ex = $obj->excluded_from_permissions;
      $controller_allowed[$classname] = array();
      foreach($perm as $name=>$val){
        if(!in_array($name, $ex)) $controller_allowed[$classname][$name] = $val; 
      }    
      $all_permissions[$classname] = array_merge((array) $base_permissions[$classname], $controller_allowed[$classname], (array) $user_allowed[$classname]);
    }
    
    return $all_permissions;
	}
	
	public function title(){return $this->fullname ." (".$this->username.")";}
}?>