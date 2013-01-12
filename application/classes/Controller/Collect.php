<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Collect extends Controller {
  public function action_index() {
    $collection = Model_Store::collection();
    $values = $this->request->query();
    $time = null;
    if(array_key_exists('time', $values)) {
      try {
        $time = new DateTime($values['time']);
      }
      catch(Exception $e) {
        // failed - will be treated as default and use current time
      }
    }
    if(is_null($time)) {
      $time = new DateTime();
    }
    
    $values['time'] = new MongoDate($time->getTimestamp());
    
    foreach($values as $key => &$value) {
      if('time' == $key) continue;
      
      if(is_numeric($value)) $value = (float)$value;
//      else if(is_numeric($value)) $value = (int)$value;

    }
    $collection->insert($values);
    $this->response->headers('Content-type', 'application/json');
    $this->response->body(
      View::factory('collect/ok')
    );
  }
}