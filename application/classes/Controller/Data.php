<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Data extends Controller {
  public function action_index() {
    $period_config =$config = Kohana::$config->load('period');
    $default_period = 'day';
    $period = $this->request->query('period');
    
    if(!array_key_exists($period, $period_config)) {
      $period = $default_period;
    }
    $req_period = new DateInterval($period_config[$period]['period']);
    
    $endTime = new DateTime();
    $startTime = clone $endTime;
    $startTime->sub($req_period);
    
    $maxPoints = $this->request->query('max-points');
    
    $collection = Model_Store::collection();
    $q = array(
      'time' => array(
        '$gt' => new MongoDate($startTime->getTimestamp()),
        '$lte' => new MongoDate($endTime->getTimestamp()),
      )
    );

    $points = $collection
      ->find($q)
      ->sort(array('time'=>1));

    $legend = Model_Store::legend();    
      
    $this->response->headers('Content-type', 'application/json');
    $this->response->body(
      View::factory('data/index')
        ->set('data', $points)
        ->set('legend', $legend)
        ->set('start', $startTime)
        ->set('end', $endTime)
    );
  }
  
  public function action_test() {
    $collection = Model_Store::collection();
    $result = $collection
      ->aggregate(
        array(
          '$match' => array(
            'time' => array(
              '$gt' => new MongoDate(strtotime("2013-01-05 00:00:00")),
            )
          )
        ),
        array(
          '$project' => array(
            '_id' => 0,
            'day' => array(
              '$dayOfYear' => '$time'
            ),
            'r' => 1
          )
        ),
        array(
          '$group' => array(
            '_id' => '$day',
            'r' => array(
              '$avg' => '$r'
            ),
//            'p' => array( '$push' => '$r')
          )
        )
      );
      
      echo debug::vars($result);
  }
}