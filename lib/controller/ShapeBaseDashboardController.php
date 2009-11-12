<?php
class ShapeBaseDashboardController extends ShapeController {
  public $widgets = array("shape/dashboard/_search", "shape/dashboard/_analytics", "shape/dashboard/_summary", "shape/dashboard/_recentpages");

  //blank out the menu function
  public function _menu(){}

  /**widget partials**/
  public function _search(){}
  public function _summary(){}
  public function _analytics(){}
  public function _recentpages(){}
  

}?>