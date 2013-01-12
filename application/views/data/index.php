<?php

$points = array();

$y_min = 0;
$y_max = 0;

$used_keys = array();

foreach($data as $d) {
  unset($d['_id']);
  $d['time'] = date('Y-M-d H:i:s', $d['time']->sec);
  foreach($d as $key => $y) {
    if('time' == $key) continue;
    $used_keys[$key] = true;
    if($y < $y_min) $y_min = $y;
    if($y > $y_max) $y_max = $y;
  }
  $points []= $d;

}


$legend = array_intersect_key($legend, $used_keys);

foreach($legend as &$line) {
  $line = array(
    'id' => URL::title($line),
    'text' => $line, 
  );
}

if($y_min<0) $y_min--;

if(count($points) > 0) {
  echo json_encode(array(
    'min' => array(
      'x' => $start->format('Y-M-d H:i:s'),
      'y' => $y_min,
    ),
    'max' => array(
      'x' => $end->format('Y-M-d H:i:s'),
      'y' => $y_max + 1,
    ),
    'legend' => $legend,
    'points' => $points,
  ));
}
else {
  echo json_encode(array('error'=>'no points'));
}