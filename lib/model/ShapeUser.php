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
	
	public function permissions($controller){
	  $path = CACHE_DIR."/shape/permission-".$this->primval.".cache";
    $cache = new WaxCacheFile(CACHE_DIR."shape", 3600, 'cache', $path);
    $modules = array();
    if(!$found = $cache->valid()){
      foreach($this->permissions as $perm) $modules[$perm->controller][$perm->action] = true;
      $cache->set(serialize($modules));
    }else $modules = unserialize($found);
    return $modules;
	}
	
	public function title(){return $this->fullname ." (".$this->username.")";}
}?>